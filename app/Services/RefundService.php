<?php

namespace App\Services;

use App\Models\EarningsAdjustment;
use App\Models\Refund;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class RefundService
{
    protected $walletService;

    public function __construct(WalletService $walletService)
    {
        $this->walletService = $walletService;
    }

    /**
     * Issue a refund to user's wallet
     */
    public function issueRefund(
        $reference,
        User $user,
        float $amount,
        string $reason,
        ?string $disputeId = null
    ): Refund {
        return DB::transaction(function () use ($reference, $user, $amount, $reason, $disputeId) {
            $referenceType = get_class($reference);
            $referenceId = $reference->id;

            // Create idempotency key
            $idempotencyKey = 'refund:' . $referenceType . ':' . $referenceId . ':' . $amount;

            // Check if refund already exists
            $existing = Refund::where('idempotency_key', $idempotencyKey)->first();
            if ($existing) {
                return $existing; // Idempotent
            }

            // Credit wallet
            $walletTransaction = $this->walletService->credit(
                $user,
                $amount,
                'Refund: ' . $reason,
                'refund',
                $referenceId,
                $idempotencyKey,
                'cash' // Refunds go to cash bucket
            );

            // Create refund record
            $refund = Refund::create([
                'dispute_id' => $disputeId,
                'reference_type' => $referenceType,
                'reference_id' => $referenceId,
                'user_id' => $user->id,
                'amount' => $amount,
                'currency' => 'INR',
                'reason' => $reason,
                'status' => 'completed',
                'wallet_transaction_id' => $walletTransaction->id,
                'idempotency_key' => $idempotencyKey,
                'processed_by_admin_id' => auth()->id(),
            ]);

            // Reverse astrologer earnings
            $this->reverseEarnings($reference, $amount, $refund);

            return $refund;
        });
    }

    /**
     * Reverse astrologer earnings proportionally
     */
    protected function reverseEarnings($reference, float $refundAmount, Refund $refund): ?EarningsAdjustment
    {
        $referenceType = get_class($reference);

        // Only reverse earnings for service transactions
        if (
            !in_array($referenceType, [
                'App\\Models\\CallSession',
                'App\\Models\\ChatSession',
                'App\\Models\\Appointment',
            ])
        ) {
            return null; // No earnings to reverse for payment orders
        }

        // Get astrologer profile
        $astrologerProfile = $reference->astrologerProfile ?? null;
        if (!$astrologerProfile) {
            return null; // No astrologer involved
        }

        // Calculate original transaction details
        $originalAmount = $this->getOriginalChargedAmount($reference);
        $astrologerEarnings = $this->getAstrologerEarnings($reference, $originalAmount);

        // Calculate proportional reversal
        $reversalAmount = $this->calculateProportionalReversal(
            $originalAmount,
            $astrologerEarnings,
            $refundAmount
        );

        if ($reversalAmount <= 0) {
            return null;
        }

        // Check if adjustment already exists (idempotency)
        $existing = EarningsAdjustment::where('refund_id', $refund->id)
            ->where('astrologer_profile_id', $astrologerProfile->id)
            ->first();

        if ($existing) {
            return $existing;
        }

        // Create earnings adjustment (negative)
        return EarningsAdjustment::create([
            'astrologer_profile_id' => $astrologerProfile->id,
            'reference_type' => $referenceType,
            'reference_id' => $reference->id,
            'refund_id' => $refund->id,
            'amount' => -$reversalAmount, // Negative for reversal
            'reason' => 'Earnings reversal due to refund: ' . $refund->reason,
            'status' => 'applied',
        ]);
    }

    /**
     * Get original charged amount
     */
    protected function getOriginalChargedAmount($reference): float
    {
        $type = get_class($reference);

        switch ($type) {
            case 'App\\Models\\CallSession':
                return (float) $reference->total_charge;

            case 'App\\Models\\ChatSession':
                return (float) $reference->total_charge;

            case 'App\\Models\\Appointment':
                return (float) $reference->price_total;

            default:
                return 0;
        }
    }

    /**
     * Get astrologer earnings from transaction
     */
    protected function getAstrologerEarnings($reference, float $originalAmount): float
    {
        $type = get_class($reference);

        // Get platform commission rate
        $pricingSettings = \App\Models\PricingSetting::first();
        $commissionRate = 0.3; // Default 30%

        switch ($type) {
            case 'App\\Models\\CallSession':
                $commissionRate = ($pricingSettings->platform_call_commission_percent ?? 30) / 100;
                break;

            case 'App\\Models\\ChatSession':
                $commissionRate = ($pricingSettings->platform_chat_commission_percent ?? 30) / 100;
                break;

            case 'App\\Models\\Appointment':
                $commissionRate = ($pricingSettings->platform_appointment_commission_percent ?? 30) / 100;
                break;
        }

        // Astrologer gets (100 - commission)%
        return $originalAmount * (1 - $commissionRate);
    }

    /**
     * Calculate proportional earnings reversal
     */
    protected function calculateProportionalReversal(
        float $originalAmount,
        float $astrologerEarnings,
        float $refundAmount
    ): float {
        if ($originalAmount <= 0) {
            return 0;
        }

        // Calculate refund percentage
        $refundPercent = $refundAmount / $originalAmount;

        // Apply same percentage to astrologer's earnings
        return round($astrologerEarnings * $refundPercent, 2);
    }
}
