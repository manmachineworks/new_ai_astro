<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use App\Models\User;
use App\Models\AstrologerProfile;
use Carbon\Carbon;

class AppointmentSlot extends Model
{
    use HasUuids;

    public const STATUS_AVAILABLE = 'available';
    public const STATUS_HELD = 'held';
    public const STATUS_BOOKED = 'booked';
    public const STATUS_BLOCKED = 'blocked';

    protected $fillable = [
        'astrologer_profile_id',
        'start_at_utc',
        'end_at_utc',
        'duration_minutes',
        'status',
        'held_by_user_id',
        'hold_expires_at_utc',
    ];

    protected $casts = [
        'start_at_utc' => 'datetime',
        'end_at_utc' => 'datetime',
        'hold_expires_at_utc' => 'datetime',
        'duration_minutes' => 'integer',
    ];

    // Relationships
    public function astrologerProfile(): BelongsTo
    {
        return $this->belongsTo(AstrologerProfile::class);
    }

    public function heldByUser(): BelongsTo
    {
        return $this->belongsTo(User::class, 'held_by_user_id');
    }

    // Scopes
    public function scopeAvailable($query)
    {
        return $query->where('status', 'available')
            ->where(function ($q) {
                $q->whereNull('hold_expires_at_utc')
                    ->orWhere('hold_expires_at_utc', '<', now());
            });
    }

    public function scopeForAstrologer($query, $profileId)
    {
        return $query->where('astrologer_profile_id', $profileId);
    }

    public function scopeInDateRange($query, Carbon $from, Carbon $to)
    {
        return $query->whereBetween('start_at_utc', [$from, $to]);
    }

    public function scopeUpcoming($query)
    {
        return $query->where('start_at_utc', '>=', now());
    }

    /**
     * Hold this slot for a user with row-level locking
     * Returns false when the same user already holds the slot.
     */
    public function hold(User $user, int $minutes = 10): bool
    {
        return \DB::transaction(function () use ($user, $minutes) {
            $slot = self::where('id', $this->id)
                ->lockForUpdate()
                ->first();

            if (!$slot) {
                throw new \Exception('Slot not found');
            }

            if ($slot->status === 'held') {
                if ($slot->held_by_user_id === $user->id && $slot->hold_expires_at_utc?->isFuture()) {
                    return false;
                }

                if ($slot->hold_expires_at_utc && $slot->hold_expires_at_utc->isPast()) {
                    $slot->update([
                        'status' => 'available',
                        'held_by_user_id' => null,
                        'hold_expires_at_utc' => null,
                    ]);
                }
            }

            if ($slot->status !== 'available') {
                throw new \Exception('Slot is not available');
            }

            $slot->update([
                'status' => 'held',
                'held_by_user_id' => $user->id,
                'hold_expires_at_utc' => now()->addMinutes($minutes),
            ]);

            return true;
        });
    }

    /**
     * Release the hold on this slot
     */
    public function release(): bool
    {
        if (!in_array($this->status, ['held', 'booked'], true)) {
            return false;
        }

        return $this->update([
            'status' => 'available',
            'held_by_user_id' => null,
            'hold_expires_at_utc' => null,
        ]);
    }

    /**
     * Mark slot as booked
     */
    public function book(Appointment $appointment): bool
    {
        return $this->update([
            'status' => 'booked',
            'held_by_user_id' => null,
            'hold_expires_at_utc' => null,
        ]);
    }

    /**
     * Block this slot (astrologer action)
     */
    public function block(): bool
    {
        if ($this->status === 'booked') {
            throw new \Exception('Cannot block a booked slot');
        }

        return $this->update(['status' => 'blocked']);
    }

    /**
     * Unblock this slot
     */
    public function unblock(): bool
    {
        if ($this->status !== 'blocked') {
            return false;
        }

        return $this->update(['status' => 'available']);
    }

    /**
     * Check if hold has expired
     */
    public function isHoldExpired(): bool
    {
        return $this->status === 'held'
            && $this->hold_expires_at_utc
            && $this->hold_expires_at_utc->isPast();
    }
}
