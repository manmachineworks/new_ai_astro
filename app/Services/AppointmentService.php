<?php

namespace App\Services;

use App\Models\Appointment;
use App\Models\AppointmentSlot;
use App\Models\AstrologerProfile;
use App\Models\AstrologerEarningsLedger;
use App\Models\MeetingLink;
use App\Models\NotificationJob;
use App\Models\PricingSetting;
use App\Models\User;
use App\Models\WalletHold;
use Illuminate\Support\Facades\DB;

class AppointmentService
{
    protected WalletService $walletService;

    public function __construct(WalletService $walletService)
    {
        $this->walletService = $walletService;
    }

    public function calculatePricing(AstrologerProfile $astrologer, int $durationMinutes): array
    {
        $pricingMode = config('appointments.pricing.mode', 'per_minute');
        $rate = null;

        if ($pricingMode === 'fixed') {
            $total = (float) config('appointments.pricing.fixed_price', 0);
        } else {
            $rate = (float) ($astrologer->call_per_minute ?: config('appointments.pricing.fallback_rate_per_minute', 0));
            $total = $rate * $durationMinutes;
        }

        return [$pricingMode, round($total, 2), $rate];
    }

    /**
     * Hold a slot for a user and create a pending appointment record.
     */
    public function holdSlot(AppointmentSlot $slot, User $user): array
    {
        return DB::transaction(function () use ($slot, $user) {
            $slot = AppointmentSlot::where('id', $slot->id)->lockForUpdate()->firstOrFail();

            if ($slot->status === 'held' && $slot->hold_expires_at_utc?->isFuture()) {
                if ($slot->held_by_user_id === $user->id) {
                    $existing = Appointment::where('user_id', $user->id)
                        ->where('astrologer_profile_id', $slot->astrologer_profile_id)
                        ->where('start_at_utc', $slot->start_at_utc)
                        ->where('status', 'requested')
                        ->latest()
                        ->first();

                    $existingHold = $existing?->walletHold;
                    if (!$existingHold) {
                        $existingHold = WalletHold::where('user_id', $user->id)
                            ->where('reference_type', 'appointment_slot')
                            ->where('reference_id', $slot->id)
                            ->where('status', 'active')
                            ->first();
                    }

                    return [
                        'appointment' => $existing,
                        'hold_id' => $existingHold?->id ?? $existing?->wallet_hold_id,
                        'slot_id' => $slot->id,
                        'expires_at' => $slot->hold_expires_at_utc,
                        'amount_held' => $existing?->price_total ?? $existingHold?->amount,
                        'already_held' => true,
                    ];
                }

                throw new \Exception('Slot is currently held by another user.');
            }

            if ($slot->status === 'held' && $slot->hold_expires_at_utc?->isPast()) {
                $slot->update([
                    'status' => 'available',
                    'held_by_user_id' => null,
                    'hold_expires_at_utc' => null,
                ]);
            }

            if ($slot->status !== 'available') {
                throw new \Exception('Slot is not available.');
            }

            $durationMinutes = $slot->duration_minutes ?: $slot->start_at_utc->diffInMinutes($slot->end_at_utc);
            [$pricingMode, $priceTotal, $rateSnapshot] = $this->calculatePricing($slot->astrologerProfile, $durationMinutes);

            $existingActive = Appointment::where('astrologer_profile_id', $slot->astrologer_profile_id)
                ->where('start_at_utc', $slot->start_at_utc)
                ->whereNotIn('status', ['declined', 'cancelled_by_user', 'cancelled_by_astrologer', 'cancelled_by_admin', 'expired'])
                ->exists();

            if ($existingActive) {
                throw new \Exception('Appointment already exists for this slot.');
            }

            $walletHold = $this->walletService->hold(
                $user,
                $priceTotal,
                'appointment_hold',
                'appointment_slot',
                $slot->id,
                (int) config('appointments.hold_minutes', 10)
            );

            $slot->update([
                'status' => 'held',
                'held_by_user_id' => $user->id,
                'hold_expires_at_utc' => now()->addMinutes((int) config('appointments.hold_minutes', 10)),
            ]);

            $appointment = Appointment::create([
                'user_id' => $user->id,
                'astrologer_profile_id' => $slot->astrologer_profile_id,
                'start_at_utc' => $slot->start_at_utc,
                'end_at_utc' => $slot->end_at_utc,
                'duration_minutes' => $durationMinutes,
                'status' => 'requested',
                'pricing_mode' => $pricingMode,
                'price_total' => $priceTotal,
                'rate_snapshot' => $rateSnapshot,
                'wallet_hold_id' => $walletHold->id,
            ]);

            $appointment->logEvent('held', 'user', $user->id, [
                'wallet_hold_id' => $walletHold->id,
                'slot_id' => $slot->id,
            ]);

            return [
                'appointment' => $appointment,
                'hold_id' => $walletHold->id,
                'slot_id' => $slot->id,
                'expires_at' => $slot->hold_expires_at_utc,
                'amount_held' => $walletHold->amount,
                'already_held' => false,
            ];
        });
    }

    /**
     * Finalize appointment creation after hold.
     */
    public function createAppointment(AppointmentSlot $slot, User $user, string $walletHoldId, ?string $notes = null): Appointment
    {
        return DB::transaction(function () use ($slot, $user, $walletHoldId, $notes) {
            $slot = AppointmentSlot::where('id', $slot->id)->lockForUpdate()->firstOrFail();

            if ($slot->status !== 'held' || $slot->held_by_user_id !== $user->id) {
                throw new \Exception('Slot is not held by you.');
            }

            if ($slot->isHoldExpired()) {
                throw new \Exception('Hold has expired.');
            }

            $appointment = Appointment::where('wallet_hold_id', $walletHoldId)
                ->where('user_id', $user->id)
                ->lockForUpdate()
                ->first();

            if (!$appointment) {
                $durationMinutes = $slot->duration_minutes ?: $slot->start_at_utc->diffInMinutes($slot->end_at_utc);
                [$pricingMode, $priceTotal, $rateSnapshot] = $this->calculatePricing($slot->astrologerProfile, $durationMinutes);

                $appointment = Appointment::create([
                    'user_id' => $user->id,
                    'astrologer_profile_id' => $slot->astrologer_profile_id,
                    'start_at_utc' => $slot->start_at_utc,
                    'end_at_utc' => $slot->end_at_utc,
                    'duration_minutes' => $durationMinutes,
                    'status' => 'requested',
                    'pricing_mode' => $pricingMode,
                    'price_total' => $priceTotal,
                    'rate_snapshot' => $rateSnapshot,
                    'wallet_hold_id' => $walletHoldId,
                ]);
            }

            if ($notes !== null) {
                $appointment->update(['notes_user' => $notes]);
            }

            $appointment->logEvent('created', 'user', $user->id);

            if (config('appointments.auto_confirm')) {
                $this->confirmAppointment($appointment, $slot->astrologerProfile, true);
            } else {
                $pendingMinutes = (int) config('appointments.pending_hold_minutes', 60);
                $slot->update(['hold_expires_at_utc' => now()->addMinutes($pendingMinutes)]);

                $hold = WalletHold::find($walletHoldId);
                if ($hold) {
                    $hold->update(['expires_at' => now()->addMinutes($pendingMinutes)]);
                }
            }

            return $appointment;
        });
    }

    /**
     * Astrologer confirms appointment
     */
    public function confirmAppointment(Appointment $appointment, AstrologerProfile $astrologer, bool $isAuto = false): Appointment
    {
        return DB::transaction(function () use ($appointment, $astrologer, $isAuto) {
            $appointment = Appointment::where('id', $appointment->id)->lockForUpdate()->firstOrFail();

            if ($appointment->status === 'confirmed') {
                return $appointment;
            }

            if ($appointment->status !== 'requested' && !$isAuto) {
                throw new \Exception('Appointment cannot be confirmed.');
            }

            $slot = AppointmentSlot::where('astrologer_profile_id', $appointment->astrologer_profile_id)
                ->where('start_at_utc', $appointment->start_at_utc)
                ->lockForUpdate()
                ->first();

            if ($slot && $slot->status === 'held' && $slot->held_by_user_id !== $appointment->user_id) {
                throw new \Exception('Slot is held by another user.');
            }

            $hold = $appointment->walletHold;
            if ($hold && $hold->status === 'active') {
                if ($hold->expires_at && $hold->expires_at->isPast()) {
                    $this->expireAppointment($appointment, $slot);
                    throw new \Exception('Hold has expired.');
                }

                $this->walletService->consumeHold($hold, (float) $appointment->price_total);
            }

            $appointment->update(['status' => 'confirmed']);

            if ($slot) {
                $slot->book($appointment);
            }

            $commissionPercent = (float) PricingSetting::get('platform_commission_percent', 20);
            $grossAmount = (float) $appointment->price_total;
            $commissionAmount = ($grossAmount * $commissionPercent) / 100;
            $earningsAmount = $grossAmount - $commissionAmount;

            if ($earningsAmount > 0) {
                AstrologerEarningsLedger::create([
                    'astrologer_profile_id' => $appointment->astrologer_profile_id,
                    'source' => 'appointment',
                    'reference_type' => Appointment::class,
                    'reference_id' => $appointment->id,
                    'amount' => $earningsAmount,
                    'status' => 'available',
                ]);
            }

            $this->ensureMeetingLink($appointment);

            $actorType = $isAuto ? 'system' : 'astrologer';
            $actorId = $isAuto ? null : $astrologer->user_id;
            $appointment->logEvent('confirmed', $actorType, $actorId);

            $this->scheduleReminders($appointment);

            return $appointment;
        });
    }

    /**
     * Astrologer declines appointment
     */
    public function declineAppointment(Appointment $appointment, AstrologerProfile $astrologer, ?string $reason = null): Appointment
    {
        if ($appointment->status !== 'requested') {
            throw new \Exception('Only pending appointments can be declined.');
        }

        return DB::transaction(function () use ($appointment, $astrologer, $reason) {
            $appointment = Appointment::where('id', $appointment->id)->lockForUpdate()->firstOrFail();

            if ($appointment->walletHold && $appointment->walletHold->status === 'active') {
                $this->walletService->releaseHold($appointment->walletHold);
            }

            $slot = AppointmentSlot::where('astrologer_profile_id', $appointment->astrologer_profile_id)
                ->where('start_at_utc', $appointment->start_at_utc)
                ->lockForUpdate()
                ->first();

            if ($slot) {
                $slot->release();
            }

            $appointment->update(['status' => 'declined']);

            $appointment->logEvent('declined', 'astrologer', $astrologer->user_id, [
                'reason' => $reason,
            ]);

            return $appointment;
        });
    }

    /**
     * Cancel appointment (by user, astrologer, or admin)
     */
    public function cancelAppointment(Appointment $appointment, $actor, ?string $reason = null): Appointment
    {
        if (!in_array($appointment->status, ['requested', 'confirmed'], true)) {
            return $appointment;
        }

        return DB::transaction(function () use ($appointment, $actor, $reason) {
            $appointment = Appointment::where('id', $appointment->id)->lockForUpdate()->firstOrFail();
            $actorType = $this->resolveActorType($appointment, $actor);

            $refundAmount = $this->calculateRefundAmount($appointment, $actorType);
            $penaltyAmount = max(0, (float) $appointment->price_total - $refundAmount);

            if ($appointment->walletHold && $appointment->walletHold->status === 'active') {
                if ($refundAmount >= (float) $appointment->price_total) {
                    $this->walletService->releaseHold($appointment->walletHold);
                } else {
                    $this->walletService->consumeHold($appointment->walletHold, $penaltyAmount);
                }
            } elseif ($refundAmount > 0) {
                $this->walletService->credit(
                    $appointment->user,
                    $refundAmount,
                    'appointment_refund',
                    $appointment->id,
                    "Appointment #{$appointment->id} refund",
                    ['actor_type' => $actorType],
                    "appointment-refund-{$appointment->id}-{$actorType}",
                    'refund'
                );
            }

            $slot = AppointmentSlot::where('astrologer_profile_id', $appointment->astrologer_profile_id)
                ->where('start_at_utc', $appointment->start_at_utc)
                ->lockForUpdate()
                ->first();

            if ($slot && in_array($slot->status, ['booked', 'held'], true)) {
                $slot->release();
            }

            $status = match ($actorType) {
                'user' => 'cancelled_by_user',
                'astrologer' => 'cancelled_by_astrologer',
                default => 'cancelled_by_admin',
            };
            $appointment->update(['status' => $status]);

            $appointment->logEvent('cancelled', $actorType, is_object($actor) ? $actor->id : null, [
                'reason' => $reason,
                'refund_amount' => $refundAmount,
            ]);

            return $appointment;
        });
    }

    /**
     * Release expired holds and mark appointments as expired.
     */
    public function expireStaleHolds(): int
    {
        $expiredSlots = AppointmentSlot::where('status', 'held')
            ->whereNotNull('hold_expires_at_utc')
            ->where('hold_expires_at_utc', '<=', now())
            ->get();

        $count = 0;

        foreach ($expiredSlots as $slot) {
            DB::transaction(function () use ($slot, &$count) {
                $lockedSlot = AppointmentSlot::where('id', $slot->id)->lockForUpdate()->first();
                if (!$lockedSlot || !$lockedSlot->isHoldExpired()) {
                    return;
                }

                $appointment = Appointment::where('astrologer_profile_id', $lockedSlot->astrologer_profile_id)
                    ->where('start_at_utc', $lockedSlot->start_at_utc)
                    ->where('status', 'requested')
                    ->lockForUpdate()
                    ->first();

                if ($appointment) {
                    if ($appointment->walletHold && $appointment->walletHold->status === 'active') {
                        $this->walletService->releaseHold($appointment->walletHold);
                    }
                    $appointment->update(['status' => 'expired']);
                    $appointment->logEvent('expired', 'system');
                }

                $lockedSlot->update([
                    'status' => 'available',
                    'held_by_user_id' => null,
                    'hold_expires_at_utc' => null,
                ]);

                $count++;
            });
        }

        return $count;
    }

    protected function scheduleReminders(Appointment $appointment): void
    {
        $leadMinutes = config('appointments.reminders.lead_minutes', [1440, 60, 10]);
        $startAt = $appointment->start_at_utc;
        $recipients = [
            $appointment->user_id,
            $appointment->astrologerProfile?->user_id,
        ];

        foreach (array_filter($recipients) as $recipientUserId) {
            foreach ($leadMinutes as $minutes) {
                $scheduled = $startAt->copy()->subMinutes((int) $minutes);
                if ($scheduled->isPast()) {
                    continue;
                }

                NotificationJob::updateOrCreate(
                    [
                        'type' => 'appointment_reminder',
                        'reference_type' => Appointment::class,
                        'reference_id' => $appointment->id,
                        'recipient_user_id' => $recipientUserId,
                        'scheduled_at' => $scheduled,
                    ],
                    [
                        'status' => 'pending',
                        'attempts' => 0,
                        'last_error' => null,
                        'sent_at' => null,
                    ]
                );
            }
        }
    }

    protected function ensureMeetingLink(Appointment $appointment): void
    {
        if (!config('appointments.meeting.enabled', true)) {
            return;
        }

        if ($appointment->meetingLink) {
            return;
        }

        MeetingLink::create([
            'appointment_id' => $appointment->id,
            'provider' => config('appointments.meeting.provider', 'jitsi'),
            'join_url' => $appointment->generateMeetingLink(),
        ]);
    }

    protected function resolveActorType(Appointment $appointment, $actor): string
    {
        if (!is_object($actor)) {
            return 'admin';
        }

        if ($actor instanceof User) {
            if (method_exists($actor, 'hasRole') && ($actor->hasRole('Admin') || $actor->hasRole('Super Admin'))) {
                return 'admin';
            }

            return $actor->id === $appointment->user_id ? 'user' : 'astrologer';
        }

        return 'admin';
    }

    protected function calculateRefundAmount(Appointment $appointment, string $actorType): float
    {
        $hoursUntilStart = now()->diffInHours($appointment->start_at_utc, false);

        if ($hoursUntilStart <= 0) {
            return 0;
        }

        if ($actorType === 'admin') {
            return (float) $appointment->price_total;
        }

        if ($actorType === 'astrologer') {
            $percent = (float) config('appointments.cancellation.astrologer_refund_percent', 100);
            return $appointment->price_total * ($percent / 100);
        }

        $fullRefundHours = (int) config('appointments.cancellation.user_full_refund_hours', 6);
        if ($hoursUntilStart >= $fullRefundHours) {
            return (float) $appointment->price_total;
        }

        $partialPercent = (float) config('appointments.cancellation.user_partial_refund_percent', 50);
        return (float) $appointment->price_total * ($partialPercent / 100);
    }

    protected function expireAppointment(Appointment $appointment, ?AppointmentSlot $slot = null): void
    {
        if ($appointment->walletHold && $appointment->walletHold->status === 'active') {
            $this->walletService->releaseHold($appointment->walletHold);
        }

        $appointment->update(['status' => 'expired']);
        $appointment->logEvent('expired', 'system');

        if ($slot) {
            $slot->update([
                'status' => 'available',
                'held_by_user_id' => null,
                'hold_expires_at_utc' => null,
            ]);
        }
    }
}
