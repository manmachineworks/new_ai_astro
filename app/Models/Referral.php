<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Referral extends Model
{
    use HasUuids;

    protected $fillable = [
        'inviter_user_id',
        'invitee_user_id',
        'status',
        'qualified_at',
        'rewarded_at',
        'inviter_bonus_amount',
        'invitee_bonus_amount',
        'meta_json',
    ];

    protected $casts = [
        'qualified_at' => 'datetime',
        'rewarded_at' => 'datetime',
        'inviter_bonus_amount' => 'float',
        'invitee_bonus_amount' => 'float',
        'meta_json' => 'array',
    ];

    // Relationships
    public function inviter(): BelongsTo
    {
        return $this->belongsTo(User::class, 'inviter_user_id');
    }

    public function invitee(): BelongsTo
    {
        return $this->belongsTo(User::class, 'invitee_user_id');
    }

    // Scopes
    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    public function scopeQualified($query)
    {
        return $query->where('status', 'qualified');
    }

    public function scopeRewarded($query)
    {
        return $query->where('status', 'rewarded');
    }

    // Business Logic Methods

    /**
     * Mark referral as qualified
     */
    public function markQualified(): bool
    {
        return $this->update([
            'status' => 'qualified',
            'qualified_at' => now(),
        ]);
    }

    /**
     * Mark referral as rewarded
     */
    public function markRewarded(float $inviterAmount, float $inviteeAmount): bool
    {
        return $this->update([
            'status' => 'rewarded',
            'rewarded_at' => now(),
            'inviter_bonus_amount' => $inviterAmount,
            'invitee_bonus_amount' => $inviteeAmount,
        ]);
    }

    /**
     * Mark referral as rejected
     */
    public function markRejected(string $reason = null): bool
    {
        return $this->update([
            'status' => 'rejected',
            'meta_json' => array_merge($this->meta_json ?? [], [
                'rejection_reason' => $reason,
                'rejected_at' => now()->toIso8601String(),
            ]),
        ]);
    }
}
