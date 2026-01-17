<?php

namespace App\Services;

use App\Models\Dispute;
use App\Models\User;
use Illuminate\Support\Facades\DB;

class DisputeService
{
    /**
     * Create a new dispute
     */
    public function createDispute(
        User $user,
        $reference,
        string $reasonCode,
        ?string $description = null,
        ?float $requestedAmount = null
    ): Dispute {
        return DB::transaction(function () use ($user, $reference, $reasonCode, $description, $requestedAmount) {
            // Check eligibility
            $eligibility = $this->checkEligibility($reference, $user);
            if (!$eligibility['eligible']) {
                throw new \Exception($eligibility['reason']);
            }

            // Create dispute
            $dispute = Dispute::create([
                'user_id' => $user->id,
                'reference_type' => get_class($reference),
                'reference_id' => $reference->id,
                'reason_code' => $reasonCode,
                'description' => $description,
                'requested_refund_amount' => $requestedAmount,
                'status' => 'submitted',
            ]);

            // Log creation event
            $dispute->logEvent('created', 'user', $user->id, [
                'reason_code' => $reasonCode,
                'requested_amount' => $requestedAmount,
            ]);

            return $dispute;
        });
    }

    /**
     * Check if a transaction is eligible for dispute
     */
    public function checkEligibility($reference, User $user): array
    {
        $referenceType = get_class($reference);
        $config = config('disputes');

        // Check if already disputed
        $existing = Dispute::where('user_id', $user->id)
            ->where('reference_type', $referenceType)
            ->where('reference_id', $reference->id)
            ->first();

        if ($existing) {
            return [
                'eligible' => false,
                'reason' => 'You have already filed a dispute for this transaction',
            ];
        }

        // Check time window
        $timeWindow = $this->getTimeWindow($referenceType);
        if (!$timeWindow) {
            return [
                'eligible' => false,
                'reason' => 'This type of transaction is not eligible for disputes',
            ];
        }

        $cutoffTime = now()->subHours($timeWindow);
        $transactionTime = $this->getTransactionCompletionTime($reference);

        if ($transactionTime && $transactionTime->lt($cutoffTime)) {
            return [
                'eligible' => false,
                'reason' => 'The dispute window of ' . $timeWindow . ' hours has expired',
            ];
        }

        // Check daily limit
        $todayDisputesCount = Dispute::where('user_id', $user->id)
            ->whereDate('created_at', now())
            ->count();

        if ($todayDisputesCount >= $config['max_disputes_per_day']) {
            return [
                'eligible' => false,
                'reason' => 'You have reached the maximum dispute limit for today',
            ];
        }

        return ['eligible' => true];
    }

    /**
     * Get time window for dispute based on reference type
     */
    protected function getTimeWindow(string $referenceType): ?int
    {
        $config = config('disputes.time_windows');

        $map = [
            'App\\Models\\CallSession' => $config['call'] ?? null,
            'App\\Models\\ChatSession' => $config['chat'] ?? null,
            'App\\Models\\AiChatSession' => $config['ai_chat'] ?? null,
            'App\\Models\\Appointment' => $config['appointment'] ?? null,
            'App\\Models\\PaymentOrder' => $config['payment'] ?? null,
        ];

        return $map[$referenceType] ?? null;
    }

    /**
     * Get transaction completion time
     */
    protected function getTransactionCompletionTime($reference): ?\Carbon\Carbon
    {
        $type = get_class($reference);

        switch ($type) {
            case 'App\\Models\\CallSession':
                return $reference->ended_at;

            case 'App\\Models\\ChatSession':
                return $reference->ended_at;

            case 'App\\Models\\AiChatSession':
                return $reference->ended_at;

            case 'App\\Models\\Appointment':
                return $reference->end_at_utc;

            case 'App\\Models\\PaymentOrder':
                return $reference->updated_at;

            default:
                return null;
        }
    }

    /**
     * Admin: Request more information from user
     */
    public function requestMoreInfo(Dispute $dispute, User $admin, string $message): bool
    {
        DB::transaction(function () use ($dispute, $admin, $message) {
            $dispute->update(['status' => 'needs_info']);

            $dispute->logEvent('info_requested', 'admin', $admin->id, [
                'message' => $message,
            ]);
        });

        return true;
    }

    /**
     * Admin: Approve refund (full or partial)
     */
    public function approveRefund(
        Dispute $dispute,
        User $admin,
        float $amount,
        string $reason
    ): \App\Models\Refund {
        return DB::transaction(function () use ($dispute, $admin, $amount, $reason) {
            // Determine status based on amount
            $isFull = $amount >= ($dispute->requested_refund_amount ?? $amount);
            $status = $isFull ? 'approved_full' : 'approved_partial';

            // Update dispute
            $dispute->update([
                'status' => $status,
                'approved_refund_amount' => $amount,
                'admin_notes' => $reason,
            ]);

            // Execute refund
            $refund = app(RefundService::class)->issueRefund(
                $dispute->reference,
                $dispute->user,
                $amount,
                $reason,
                $dispute->id
            );

            // Log approval
            $dispute->logEvent(
                $isFull ? 'approved_full' : 'approved_partial',
                'admin',
                $admin->id,
                ['approved_amount' => $amount, 'reason' => $reason]
            );

            return $refund;
        });
    }

    /**
     * Admin: Reject dispute
     */
    public function rejectDispute(Dispute $dispute, User $admin, string $reason): bool
    {
        return DB::transaction(function () use ($dispute, $admin, $reason) {
            $dispute->update([
                'status' => 'rejected',
                'admin_notes' => $reason,
            ]);

            $dispute->logEvent('rejected', 'admin', $admin->id, [
                'reason' => $reason,
            ]);

            return true;
        });
    }
}
