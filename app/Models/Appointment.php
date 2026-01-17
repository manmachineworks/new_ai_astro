<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Appointment extends Model
{
    use HasUuids;

    public const STATUS_REQUESTED = 'requested';
    public const STATUS_CONFIRMED = 'confirmed';
    public const STATUS_DECLINED = 'declined';
    public const STATUS_CANCELLED_BY_USER = 'cancelled_by_user';
    public const STATUS_CANCELLED_BY_ASTROLOGER = 'cancelled_by_astrologer';
    public const STATUS_CANCELLED_BY_ADMIN = 'cancelled_by_admin';
    public const STATUS_COMPLETED = 'completed';
    public const STATUS_EXPIRED = 'expired';
    public const STATUS_NO_SHOW = 'no_show';

    protected $fillable = [
        'user_id',
        'astrologer_profile_id',
        'start_at_utc',
        'end_at_utc',
        'duration_minutes',
        'status',
        'pricing_mode',
        'price_total',
        'rate_snapshot',
        'wallet_hold_id',
        'wallet_transaction_id',
        'notes_user',
        'notes_astrologer',
    ];

    protected $casts = [
        'start_at_utc' => 'datetime',
        'end_at_utc' => 'datetime',
        'duration_minutes' => 'integer',
        'price_total' => 'decimal:2',
        'rate_snapshot' => 'decimal:2',
    ];

    // Relationships
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function astrologerProfile(): BelongsTo
    {
        return $this->belongsTo(AstrologerProfile::class);
    }

    public function walletHold(): BelongsTo
    {
        return $this->belongsTo(\App\Models\WalletHold::class, 'wallet_hold_id');
    }

    public function walletTransaction(): BelongsTo
    {
        return $this->belongsTo(\App\Models\WalletTransaction::class, 'wallet_transaction_id');
    }

    public function events(): HasMany
    {
        return $this->hasMany(AppointmentEvent::class);
    }

    public function meetingLink(): HasOne
    {
        return $this->hasOne(MeetingLink::class);
    }

    // Scopes
    public function scopeForUser($query, $userId)
    {
        return $query->where('user_id', $userId);
    }

    public function scopeForAstrologer($query, $profileId)
    {
        return $query->where('astrologer_profile_id', $profileId);
    }

    public function scopeUpcoming($query)
    {
        return $query->where('start_at_utc', '>=', now())
            ->whereIn('status', ['requested', 'confirmed']);
    }

    public function scopePending($query)
    {
        return $query->where('status', 'requested');
    }

    public function scopeConfirmed($query)
    {
        return $query->where('status', 'confirmed');
    }

    // Business Logic Methods

    /**
     * Check if appointment can be cancelled by user
     */
    public function canBeCancelledByUser(): bool
    {
        return in_array($this->status, ['requested', 'confirmed']);
    }

    /**
     * Check if appointment can be confirmed by astrologer
     */
    public function canBeConfirmed(): bool
    {
        return $this->status === 'requested';
    }

    /**
     * Calculate refund amount based on cancellation policy
     */
    public function calculateRefundAmount(): float
    {
        $hoursUntilStart = now()->diffInHours($this->start_at_utc, false);
        $policy = config('appointments.cancellation');

        // If already started or past, no refund
        if ($hoursUntilStart <= 0) {
            return 0;
        }

        // Full refund if cancelled well in advance
        if ($hoursUntilStart >= ($policy['user_full_refund_hours'] ?? 6)) {
            return $this->price_total;
        }

        $partialPercent = $policy['user_partial_refund_percent'] ?? 50;
        return $this->price_total * ($partialPercent / 100);
    }

    /**
     * Generate meeting link (Jitsi by default)
     */
    public function generateMeetingLink(): string
    {
        $provider = config('appointments.meeting.provider', 'jitsi');

        if ($provider === 'jitsi') {
            $domain = config('appointments.meeting.jitsi_base_url', 'https://meet.jit.si');
            $roomName = 'appointment-' . $this->id;
            return rtrim($domain, '/') . "/{$roomName}";
        }

        // Placeholder for other providers
        return '';
    }

    /**
     * Check if meeting link should be visible
     */
    public function isMeetingLinkVisible(): bool
    {
        if ($this->status !== 'confirmed') {
            return false;
        }

        // Show link 10 minutes before start time
        $minutesUntilStart = now()->diffInMinutes($this->start_at_utc, false);
        $revealMinutes = (int) config('appointments.meeting.reveal_minutes_before', 10);
        return $minutesUntilStart <= $revealMinutes && $minutesUntilStart >= -30;
    }

    /**
     * Log an event
     */
    public function logEvent(string $eventType, string $actorType, $actorId = null, array $meta = [])
    {
        return $this->events()->create([
            'actor_type' => $actorType,
            'actor_id' => $actorId,
            'event_type' => $eventType,
            'meta_json' => $meta,
            'created_at' => now(),
        ]);
    }
}
