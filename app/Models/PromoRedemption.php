<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class PromoRedemption extends Model
{
    use HasUuids;

    protected $fillable = [
        'promo_campaign_id',
        'user_id',
        'reference_type',
        'reference_id',
        'discount_amount',
        'bonus_credited',
        'status',
        'idempotency_key',
        'meta_json',
    ];

    protected $casts = [
        'discount_amount' => 'float',
        'bonus_credited' => 'float',
        'meta_json' => 'array',
    ];

    // Relationships
    public function promoCampaign(): BelongsTo
    {
        return $this->belongsTo(PromoCampaign::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function reference(): MorphTo
    {
        return $this->morphTo();
    }

    // Scopes
    public function scopeApplied($query)
    {
        return $query->where('status', 'applied');
    }

    public function scopeReversed($query)
    {
        return $query->where('status', 'reversed');
    }

    /**
     * Mark redemption as reversed
     */
    public function reverse(string $reason = null): bool
    {
        return $this->update([
            'status' => 'reversed',
            'meta_json' => array_merge($this->meta_json ?? [], [
                'reversed_at' => now()->toIso8601String(),
                'reversal_reason' => $reason,
            ]),
        ]);
    }
}
