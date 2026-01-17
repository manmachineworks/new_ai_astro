<?php

namespace App\Services;

use App\Models\User;
use App\Models\WalletTransaction;
use Illuminate\Support\Facades\DB;
use Exception;

class WalletService
{
    /**
     * Credit the wallet.
     */
    /**
     * Credit the wallet.
     */
    public function credit(User $user, float|int|string $amount, string $referenceType = null, string $referenceId = null, string $description = null, array $meta = null, string $idempotencyKey = null, string $source = 'system'): WalletTransaction
    {
        // 1. Check Idempotency
        if ($idempotencyKey) {
            $existing = WalletTransaction::where('idempotency_key', $idempotencyKey)->first();
            if ($existing) {
                return $existing;
            }
        }

        return DB::transaction(function () use ($user, $amount, $referenceType, $referenceId, $description, $meta, $idempotencyKey, $source) {
            $lockedUser = User::where('id', $user->id)->lockForUpdate()->first();

            $beforeBalance = $lockedUser->wallet_balance;
            $afterBalance = $beforeBalance + $amount;

            $lockedUser->wallet_balance = $afterBalance;
            $lockedUser->save();

            return WalletTransaction::create([
                'user_id' => $user->id,
                'amount' => $amount,
                'type' => 'credit',
                'balance_after' => $afterBalance,
                'currency' => 'INR',
                'source' => $source,
                'reference_type' => $referenceType,
                'reference_id' => $referenceId,
                'description' => $description,
                'meta' => $meta,
                'idempotency_key' => $idempotencyKey,
            ]);

            // Notification Logic (Recharge Success)
            if ($source == 'phonepe' || $description == 'Wallet Recharge') {
                \App\Jobs\SendPushNotificationJob::dispatch(
                    $user->id,
                    'wallet_recharge_success',
                    ['amount' => (string) $amount, 'balance' => (string) $afterBalance, 'deeplink' => 'app://wallet'],
                    'Recharge Successful',
                    'Your wallet has been credited with INR ' . number_format($amount, 2)
                );
            }

            return $txn;
        });
    }

    /**
     * Debit the wallet.
     */
    public function debit(User $user, float|int|string $amount, string $referenceType = null, string $referenceId = null, string $description = null, array $meta = null, string $source = 'system', string $idempotencyKey = null): WalletTransaction
    {
        if ($idempotencyKey) {
            $existing = WalletTransaction::where('idempotency_key', $idempotencyKey)->first();
            if ($existing) {
                return $existing;
            }
        }

        return DB::transaction(function () use ($user, $amount, $referenceType, $referenceId, $description, $meta, $source, $idempotencyKey) {
            $lockedUser = User::where('id', $user->id)->lockForUpdate()->first();

            if ($lockedUser->wallet_balance < $amount) {
                throw new Exception('Insufficient wallet balance.');
            }

            $beforeBalance = $lockedUser->wallet_balance;
            $afterBalance = $beforeBalance - $amount;

            $lockedUser->wallet_balance = $afterBalance;
            $lockedUser->save();

            $txn = WalletTransaction::create([
                'user_id' => $user->id,
                'amount' => $amount,
                'type' => 'debit',
                'balance_after' => $afterBalance,
                'currency' => 'INR',
                'source' => $source,
                'reference_type' => $referenceType,
                'reference_id' => $referenceId,
                'description' => $description,
                'meta' => $meta,
                'idempotency_key' => $idempotencyKey,
            ]);

            // Notification Logic (Post-transaction)
            if ($source !== 'system_adjustment') {
                if ($afterBalance <= 0) {
                    \App\Jobs\SendPushNotificationJob::dispatch(
                        $user->id,
                        'wallet_exhausted',
                        ['balance' => (string) $afterBalance, 'deeplink' => 'app://wallet/recharge'],
                        'Wallet Empty',
                        'Your balance is zero. Recharge to continue services.'
                    );
                } elseif ($afterBalance < 100) { // LOW THRESHOLD = 100
                    \App\Jobs\SendPushNotificationJob::dispatch(
                        $user->id,
                        'wallet_low',
                        ['balance' => (string) $afterBalance, 'deeplink' => 'app://wallet/recharge'],
                        'Low Balance',
                        'Your wallet is running low (INR ' . number_format($afterBalance, 2) . ').'
                    );
                }
            }

            return $txn;
        });
    }

    public function hold(User $user, float $amount, string $purpose, string $referenceType = null, string $referenceId = null, int $durationMinutes = 10): \App\Models\WalletHold
    {
        return DB::transaction(function () use ($user, $amount, $purpose, $referenceType, $referenceId, $durationMinutes) {
            // Debit the amount first (to reserve it) or just check balance?
            // "Hold" strategy: Deduct from balance, move to 'hold' state. This is safest.

            $lockedUser = User::where('id', $user->id)->lockForUpdate()->first();

            if ($lockedUser->wallet_balance < $amount) {
                throw new Exception('Insufficient wallet balance to place hold.');
            }

            $lockedUser->decrement('wallet_balance', $amount);

            // Create Hold Record
            return \App\Models\WalletHold::create([
                'user_id' => $user->id,
                'amount' => $amount,
                'purpose' => $purpose,
                'status' => 'active',
                'reference_type' => $referenceType,
                'reference_id' => $referenceId,
                'expires_at' => now()->addMinutes($durationMinutes),
            ]);
        });
    }

    public function releaseHold(\App\Models\WalletHold $hold)
    {
        return DB::transaction(function () use ($hold) {
            $hold->refresh();
            if ($hold->status !== 'active') {
                return; // Already processed
            }

            $hold->update(['status' => 'released']);

            // Refund to user
            $this->credit(
                $hold->user,
                $hold->amount,
                'refund_hold',
                $hold->id,
                "Release: {$hold->purpose}",
                null,
                null,
                'refund'
            );
        });
    }

    public function consumeHold(\App\Models\WalletHold $hold, float $finalAmount)
    {
        return DB::transaction(function () use ($hold, $finalAmount) {
            $hold->refresh();
            if ($hold->status !== 'active') {
                throw new Exception('Hold is not active');
            }

            if ($finalAmount > $hold->amount) {
                // Needs extra debit? Or fail? Usually, we just cap at hold, or debit extra. 
                // Simple logic for now: Debit extra directly if needed.
                $extra = $finalAmount - $hold->amount;
                if ($extra > 0) {
                    $this->debit($hold->user, $extra, 'adjustment', $hold->id, 'Extra charge above hold');
                }
                $refund = 0;
            } else {
                $refund = $hold->amount - $finalAmount;
            }

            $hold->update(['status' => 'consumed']);

            if (isset($refund) && $refund > 0) {
                $this->credit(
                    $hold->user,
                    $refund,
                    'refund_partial_hold',
                    $hold->id,
                    "Partial Refund: {$hold->purpose}",
                    null,
                    null,
                    'refund'
                );
            }
        });
    }

    /**
     * Check if user has minimum balance.
     */
    public function hasBalance(User $user, float|int|string $amount): bool
    {
        return $user->wallet_balance >= $amount;
    }
}
