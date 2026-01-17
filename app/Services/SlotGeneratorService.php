<?php

namespace App\Services;

use App\Models\Appointment;
use App\Models\AppointmentSlot;
use App\Models\AstrologerProfile;
use App\Models\AvailabilityException;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class SlotGeneratorService
{
    /**
     * Generate appointment slots for an astrologer based on availability rules.
     */
    public function generateForAstrologer(AstrologerProfile $profile, int $days = 14): int
    {
        $count = 0;
        $slotDuration = (int) config('appointments.slot_length_minutes', 30);
        $startDate = now('UTC')->startOfDay();
        $rangeEnd = $startDate->copy()->addDays(max(0, $days - 1));

        $rules = $profile->availabilityRules()->where('is_active', true)->get();
        if ($rules->isEmpty()) {
            return 0;
        }

        $exceptions = AvailabilityException::where('astrologer_profile_id', $profile->id)
            ->whereBetween('date', [$startDate->toDateString(), $rangeEnd->toDateString()])
            ->get()
            ->groupBy(fn ($exception) => $exception->date->toDateString());

        $inactiveStatuses = [
            Appointment::STATUS_CANCELLED_BY_USER,
            Appointment::STATUS_CANCELLED_BY_ASTROLOGER,
            Appointment::STATUS_CANCELLED_BY_ADMIN,
            Appointment::STATUS_DECLINED,
            Appointment::STATUS_EXPIRED,
        ];

        for ($offset = 0; $offset < $days; $offset++) {
            $dayDate = $startDate->copy()->addDays($offset);
            $dayOfWeek = (int) $dayDate->dayOfWeek;
            $dayRules = $rules->where('day_of_week', $dayOfWeek);
            $dayExceptions = $exceptions->get($dayDate->toDateString(), collect());

            $slotStarts = [];
            foreach ($dayRules as $rule) {
                $this->addSlotsForRange($slotStarts, $dayDate, $rule->start_time_utc, $rule->end_time_utc, $slotDuration);
            }

            foreach ($dayExceptions->where('type', 'extra') as $extra) {
                if ($extra->start_time_utc && $extra->end_time_utc) {
                    $this->addSlotsForRange($slotStarts, $dayDate, $extra->start_time_utc, $extra->end_time_utc, $slotDuration);
                }
            }

            if (empty($slotStarts)) {
                continue;
            }

            $dayStart = $dayDate->copy()->startOfDay();
            $dayEnd = $dayDate->copy()->endOfDay();

            $bookedStarts = Appointment::where('astrologer_profile_id', $profile->id)
                ->whereBetween('start_at_utc', [$dayStart, $dayEnd])
                ->whereNotIn('status', $inactiveStatuses)
                ->get(['start_at_utc'])
                ->mapWithKeys(fn ($appointment) => [$appointment->start_at_utc->format('Y-m-d H:i:s') => true]);

            $existingSlots = AppointmentSlot::where('astrologer_profile_id', $profile->id)
                ->whereBetween('start_at_utc', [$dayStart, $dayEnd])
                ->get()
                ->keyBy(fn (AppointmentSlot $slot) => $slot->start_at_utc->format('Y-m-d H:i:s'));

            $allDayBlocked = $dayExceptions->where('type', 'blocked')
                ->first(fn ($exception) => !$exception->start_time_utc && !$exception->end_time_utc);

            foreach ($slotStarts as $slotKey => $slotStart) {
                if ($slotStart->lt(now('UTC'))) {
                    continue;
                }

                $slotEnd = $slotStart->copy()->addMinutes($slotDuration);
                if ($slotEnd->gt($dayEnd)) {
                    continue;
                }

                $status = AppointmentSlot::STATUS_AVAILABLE;
                if ($allDayBlocked) {
                    $status = AppointmentSlot::STATUS_BLOCKED;
                } else {
                    foreach ($dayExceptions->where('type', 'blocked') as $blocked) {
                        if (!$blocked->start_time_utc || !$blocked->end_time_utc) {
                            continue;
                        }
                        $blockedStart = Carbon::parse($dayDate->toDateString() . ' ' . $blocked->start_time_utc, 'UTC');
                        $blockedEnd = Carbon::parse($dayDate->toDateString() . ' ' . $blocked->end_time_utc, 'UTC');
                        if ($slotStart->lt($blockedEnd) && $slotEnd->gt($blockedStart)) {
                            $status = AppointmentSlot::STATUS_BLOCKED;
                            break;
                        }
                    }
                }

                if (isset($bookedStarts[$slotKey])) {
                    $status = AppointmentSlot::STATUS_BOOKED;
                }

                $existing = $existingSlots->get($slotKey);
                if ($existing) {
                    if (in_array($existing->status, [AppointmentSlot::STATUS_HELD, AppointmentSlot::STATUS_BOOKED, AppointmentSlot::STATUS_BLOCKED], true)) {
                        continue;
                    }

                    $existing->update([
                        'end_at_utc' => $slotEnd,
                        'duration_minutes' => $slotDuration,
                        'status' => $status,
                    ]);
                    continue;
                }

                try {
                    AppointmentSlot::create([
                        'astrologer_profile_id' => $profile->id,
                        'start_at_utc' => $slotStart,
                        'end_at_utc' => $slotEnd,
                        'duration_minutes' => $slotDuration,
                        'status' => $status,
                    ]);
                    $count++;
                } catch (\Exception $e) {
                    // Ignore duplicate slot entries.
                }
            }
        }

        return $count;
    }

    /**
     * Generate slots for all active astrologers.
     */
    public function generateForAllAstrologers(int $days = 14): array
    {
        $results = [];

        $astrologers = AstrologerProfile::where('is_verified', true)
            ->where('visibility', true)
            ->get();

        foreach ($astrologers as $astrologer) {
            $count = $this->generateForAstrologer($astrologer, $days);
            $results[$astrologer->id] = $count;
        }

        return $results;
    }

    /**
     * Clear expired holds and release slots.
     */
    public function clearExpiredHolds(): int
    {
        return DB::transaction(function () {
            $expiredSlots = AppointmentSlot::where('status', AppointmentSlot::STATUS_HELD)
                ->where('hold_expires_at_utc', '<', now())
                ->get();

            $count = 0;
            foreach ($expiredSlots as $slot) {
                $slot->release();
                $count++;
            }

            return $count;
        });
    }

    protected function addSlotsForRange(array &$slotStarts, Carbon $dayDate, string $startTime, string $endTime, int $slotDuration): void
    {
        $rangeStart = Carbon::parse($dayDate->toDateString() . ' ' . $startTime, 'UTC');
        $rangeEnd = Carbon::parse($dayDate->toDateString() . ' ' . $endTime, 'UTC');

        if ($rangeEnd->lt($rangeStart)) {
            $rangeEnd->addDay();
        }

        $cursor = $rangeStart->copy();
        while ($cursor->copy()->addMinutes($slotDuration)->lessThanOrEqualTo($rangeEnd)) {
            $slotKey = $cursor->format('Y-m-d H:i:s');
            $slotStarts[$slotKey] = $cursor->copy();
            $cursor->addMinutes($slotDuration);
        }
    }
}
