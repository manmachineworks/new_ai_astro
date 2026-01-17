<?php

namespace App\Services;

use App\Models\PromoCampaign;
use App\Models\PromoRedemption;
use App\Models\User;
use App\Models\PaymentOrder;
use Illuminate\Support\Facades\DB;

class PromoService
{
    protected $walletService;

    public function __construct(WalletService $walletService)
    {
        $this->walletService = $walletService;
    }

    /**
     * Validate promo code for a given context
     *
     * @throws \Exception
     */
    public function validateCode(string $code, User $user, string $context, ?float $amount = null): PromoCampaign
    {
        $promo = PromoCampaign::where('code', $code)->first();

        if (!$promo) {
            throw new \Exception('Invalid promo code');
        }

        if (!$promo->isActive()) {
            throw new \Exception('Promo code is not active or has expired');
        }

        if (!$promo->appliesToContext($context)) {
            throw new \Exception('Promo code is not applicable to this service');
        }

        // Check global usage limit
        $remainingUses = $promo->getRemainingUses();
        if ($remainingUses !== null && $remainingUses <= 0) {
            throw new \Exception('Promo code usage limit reached');
        }

        // Check user-specific usage limit
        if (!$promo->canBeUsedBy($user)) {
            throw new \Exception('You have already used this promo code');
        }

        // Check minimum amount requirements
        if ($context === 'recharge' && $promo->min_recharge_amount) {
            if (!$amount || $amount < $promo->min_recharge_amount) {
                throw new \Exception('Minimum recharge amount of ₹' . $promo->min_recharge_amount . ' required');
            }
        }

        if ($context !== 'recharge' && $promo->min_spend_amount) {
            if (!$amount || $amount < $promo->min_spend_amount) {
                throw new \Exception('Minimum spend amount of ₹' . $promo->min_spend_amount . ' required');
            }
        }

        // Check first-time only requirement
        if ($promo->first_time_only) {
            if (!$this->isFirstTimeUser($user, $context)) {
                throw new \Exception('This promo is for first-time users only');
            }
        }

        return $promo;
    }

    /**
     * Compute discount amount
     */
    public function computeDiscount(PromoCampaign $promo, float $amount): float
    {
        return $promo->calculateDiscount($amount);
    }

    /**
     * Apply promo to recharge (after payment success)
     */
    public function applyPromoToRecharge(string $code, PaymentOrder $order): PromoRedemption
    {
        return DB::transaction(function () use ($code, $order) {
            // Validate promo
            $promo = $this->validateCode($code, $order->user, 'recharge', $order->amount);

            // Create idempotency key
            $idempotencyKey = 'promo:recharge:' . $order->id;

            // Check if already redeemed
            $existing = PromoRedemption::where('idempotency_key', $idempotencyKey)->first();
            if ($existing) {
                return $existing; // Idempotent
            }

            // Calculate bonus
            $bonusAmount = $promo->calculateDiscount($order->amount);

            // Credit bonus to wallet
            $transaction = $this->walletService->credit(
                $order->user,
                $bonusAmount,
                'Promo bonus: ' . $promo->name,
                'promo_bonus',
                $order->id,
                $idempotencyKey,
                'bonus' // bucket
            );

            // Record redemption
            $redemption = PromoRedemption::create([
                'promo_campaign_id' => $promo->id,
                'user_id' => $order->user_id,
                'reference_type' => 'App\\Models\\PaymentOrder',
                'reference_id' => $order->id,
                'discount_amount' => 0, // For cashback, discount is 0
                'bonus_credited' => $bonusAmount,
                'status' => 'applied',
                'idempotency_key' => $idempotencyKey,
                'meta_json' => [
                    'promo_code' => $code,
                    'promo_name' => $promo->name,
                    'order_amount' => $order->amount,
                    'wallet_transaction_id' => $transaction->id,
                ],
            ]);

            return $redemption;
        });
    }

    /**
     * Apply promo to spend (cashback approach)
     */
    public function applyPromoToSpend(string $code, $reference, float $grossAmount): array
    {
        return DB::transaction(function () use ($code, $reference, $grossAmount) {
            $user = $reference->user;
            $context = $this->getContextFromReference($reference);

            // Validate promo
            $promo = $this->validateCode($code, $user, $context, $grossAmount);

            // Create idempotency key
            $referenceType = get_class($reference);
            $idempotencyKey = 'promo:' . $context . ':' . $reference->id;

            // Check if already redeemed
            $existing = PromoRedemption::where('idempotency_key', $idempotencyKey)->first();
            if ($existing) {
                return [
                    'redemption' => $existing,
                    'bonus_credited' => $existing->bonus_credited,
                ];
            }

            // Calculate cashback
            $cashbackAmount = $promo->calculateDiscount($grossAmount);

            // Credit cashback to wallet
            $transaction = $this->walletService->credit(
                $user,
                $cashbackAmount,
                'Cashback: ' . $promo->name,
                'promo_cashback',
                $reference->id,
                $idempotencyKey,
                'bonus' // bucket
            );

            // Record redemption
            $redemption = PromoRedemption::create([
                'promo_campaign_id' => $promo->id,
                'user_id' => $user->id,
                'reference_type' => $referenceType,
                'reference_id' => $reference->id,
                'discount_amount' => 0,
                'bonus_credited' => $cashbackAmount,
                'status' => 'applied',
                'idempotency_key' => $idempotencyKey,
                'meta_json' => [
                    'promo_code' => $code,
                    'promo_name' => $promo->name,
                    'gross_amount' => $grossAmount,
                    'wallet_transaction_id' => $transaction->id,
                ],
            ]);

            return [
                'redemption' => $redemption,
                'bonus_credited' => $cashbackAmount,
            ];
        });
    }

    /**
     * Reverse a promo redemption
     */
    public function reversePromo(PromoRedemption $redemption, string $reason): bool
    {
        return DB::transaction(function () use ($redemption, $reason) {
            if ($redemption->status === 'reversed') {
                return true; // Already reversed
            }

            // Debit the bonus amount if it was credited
            if ($redemption->bonus_credited > 0) {
                $this->walletService->debit(
                    $redemption->user,
                    $redemption->bonus_credited,
                    'Promo reversal: ' . $reason,
                    'promo_reversal',
                    $redemption->id,
                    'promo_reversal:' . $redemption->id,
                    'bonus' // bucket
                );
            }

            // Mark as reversed
            $redemption->reverse($reason);

            return true;
        });
    }

    /**
     * Check if user is first-time for a context
     */
    protected function isFirstTimeUser(User $user, string $context): bool
    {
        if ($context === 'recharge') {
            // Check if user has any successful recharges
            $hasRecharge = PaymentOrder::where('user_id', $user->id)
                ->where('status', 'success')
                ->exists();
            return !$hasRecharge;
        }

        // For other contexts, check wallet transactions
        $transactionTypes = [
            'call' => 'call_charge',
            'chat' => 'chat_charge',
            'ai_chat' => 'ai_chat_charge',
            'appointment' => 'appointment_charge',
        ];

        if (isset($transactionTypes[$context])) {
            $hasTransaction = \App\Models\WalletTransaction::where('user_id', $user->id)
                ->where('transaction_type', $transactionTypes[$context])
                ->exists();
            return !$hasTransaction;
        }

        return false;
    }

    /**
     * Get context from reference model
     */
    protected function getContextFromReference($reference): string
    {
        $class = get_class($reference);

        $map = [
            'App\\Models\\CallSession' => 'call',
            'App\\Models\\ChatSession' => 'chat',
            'App\\Models\\AiChatSession' => 'ai_chat',
            'App\\Models\\Appointment' => 'appointment',
        ];

        return $map[$class] ?? 'unknown';
    }

    /**
     * Apply first-time recharge bonus
     */
    public function applyFirstTimeBonus(PaymentOrder $order): ?PromoRedemption
    {
        $settings = \App\Models\PricingSetting::first();
        if (!$settings) {
            return null;
        }

        // Check if eligible
        if ($order->amount < $settings->first_time_recharge_min_amount) {
            return null;
        }

        if (!$this->isFirstTimeUser($order->user, 'recharge')) {
            return null;
        }

        return DB::transaction(function () use ($order, $settings) {
            $idempotencyKey = 'first_time_bonus:' . $order->user_id;

            // Check if already applied
            $existing = PromoRedemption::where('idempotency_key', $idempotencyKey)->first();
            if ($existing) {
                return $existing;
            }

            // Credit bonus
            $bonusAmount = $settings->first_time_recharge_bonus_amount;
            $transaction = $this->walletService->credit(
                $order->user,
                $bonusAmount,
                'First-time recharge bonus',
                'first_time_bonus',
                $order->id,
                $idempotencyKey,
                'bonus'
            );

            // Record as promo redemption
            return PromoRedemption::create([
                'promo_campaign_id' => null, // System bonus, not a campaign
                'user_id' => $order->user_id,
                'reference_type' => 'App\\Models\\PaymentOrder',
                'reference_id' => $order->id,
                'discount_amount' => 0,
                'bonus_credited' => $bonusAmount,
                'status' => 'applied',
                'idempotency_key' => $idempotencyKey,
                'meta_json' => [
                    'type' => 'first_time_bonus',
                    'order_amount' => $order->amount,
                    'wallet_transaction_id' => $transaction->id,
                ],
            ]);
        });
    }
}
