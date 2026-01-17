<?php

namespace App\Services;

use App\Models\Referral;
use App\Models\ReferralCode;
use App\Models\User;
use App\Models\PaymentOrder;
use Illuminate\Support\Facades\DB;

class ReferralService
{
    protected $walletService;

    public function __construct(WalletService $walletService)
    {
        $this->walletService = $walletService;
    }

    /**
     * Create a referral relationship
     */
    public function createReferral(User $invitee, string $code): Referral
    {
        return DB::transaction(function () use ($invitee, $code) {
            // Find referral code
            $referralCode = ReferralCode::where('code', $code)->first();

            if (!$referralCode) {
                throw new \Exception('Invalid referral code');
            }

            $inviter = $referralCode->user;

            // Prevent self-referral
            if ($inviter->id === $invitee->id) {
                throw new \Exception('Cannot refer yourself');
            }

            // Check if invitee already has a referral
            $existing = Referral::where('invitee_user_id', $invitee->id)->first();
            if ($existing) {
                throw new \Exception('User has already been referred');
            }

            // Create referral
            return Referral::create([
                'inviter_user_id' => $inviter->id,
                'invitee_user_id' => $invitee->id,
                'status' => 'pending',
            ]);
        });
    }

    /**
     * Check if user meets qualification criteria
     */
    public function checkQualification(User $invitee): bool
    {
        $settings = \App\Models\PricingSetting::first();
        if (!$settings) {
            return false;
        }

        $minRecharge = $settings->referral_qualification_min_recharge;

        // Check if user has a successful recharge >= minimum amount
        $qualifyingRecharge = PaymentOrder::where('user_id', $invitee->id)
            ->where('status', 'success')
            ->where('amount', '>=', $minRecharge)
            ->exists();

        if ($qualifyingRecharge) {
            return true;
        }

        // Alternative: check if user has made a paid call/chat/appointment
        $hasPaidAction = \App\Models\WalletTransaction::where('user_id', $invitee->id)
            ->whereIn('transaction_type', ['call_charge', 'chat_charge', 'ai_chat_charge', 'appointment_charge'])
            ->where('amount', '>', 0)
            ->exists();

        return $hasPaidAction;
    }

    /**
     * Process referral qualification and reward both parties
     */
    public function processQualification(Referral $referral): bool
    {
        if ($referral->status !== 'pending') {
            return false; // Already processed
        }

        return DB::transaction(function () use ($referral) {
            $settings = \App\Models\PricingSetting::first();
            if (!$settings) {
                return false;
            }

            $inviterBonus = $settings->referral_inviter_bonus_amount;
            $inviteeBonus = $settings->referral_invitee_bonus_amount;

            // Mark as qualified first
            $referral->markQualified();

            // Credit inviter bonus (idempotent)
            $inviterKey = 'referral:' . $referral->invitee_user_id . ':inviter';
            try {
                $this->walletService->credit(
                    $referral->inviter,
                    $inviterBonus,
                    'Referral bonus: You referred a user',
                    'referral_inviter_bonus',
                    $referral->id,
                    $inviterKey,
                    'bonus'
                );
            } catch (\Exception $e) {
                // Already credited, continue
            }

            // Credit invitee bonus (idempotent)
            $inviteeKey = 'referral:' . $referral->invitee_user_id . ':invitee';
            try {
                $this->walletService->credit(
                    $referral->invitee,
                    $inviteeBonus,
                    'Referral bonus: Welcome bonus',
                    'referral_invitee_bonus',
                    $referral->id,
                    $inviteeKey,
                    'bonus'
                );
            } catch (\Exception $e) {
                // Already credited, continue
            }

            // Mark as rewarded
            $referral->markRewarded($inviterBonus, $inviteeBonus);

            return true;
        });
    }

    /**
     * Check and process qualification for a user (called after qualifying event)
     */
    public function checkAndProcessQualification(User $user): ?Referral
    {
        // Find pending referral
        $referral = Referral::where('invitee_user_id', $user->id)
            ->where('status', 'pending')
            ->first();

        if (!$referral) {
            return null; // No pending referral
        }

        // Check if qualifies
        if (!$this->checkQualification($user)) {
            return null; // Not qualified yet
        }

        // Process qualification
        $this->processQualification($referral);

        return $referral;
    }

    /**
     * Get referral stats for a user
     */
    public function getReferralStats(User $inviter): array
    {
        $referrals = Referral::where('inviter_user_id', $inviter->id)->get();

        return [
            'total' => $referrals->count(),
            'pending' => $referrals->where('status', 'pending')->count(),
            'qualified' => $referrals->where('status', 'qualified')->count(),
            'rewarded' => $referrals->where('status', 'rewarded')->count(),
            'total_earned' => $referrals->where('status', 'rewarded')->sum('inviter_bonus_amount'),
        ];
    }
}
