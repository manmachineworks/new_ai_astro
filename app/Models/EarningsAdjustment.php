<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class EarningsAdjustment extends Model
{
    use HasUuids;

    protected $fillable = [
        'astrologer_profile_id',
        'reference_type',
        'reference_id',
        'refund_id',
        'amount',
        'reason',
        'status',
    ];

    protected $casts = [
        'amount' => 'float',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    // Relationships
    public function astrologerProfile(): BelongsTo
    {
        return $this->belongsTo(AstrologerProfile::class);
    }

    public function refund(): BelongsTo
    {
        return $this->belongsTo(Refund::class);
    }

    public function reference(): MorphTo
    {
        return $this->morphTo();
    }

    // Scopes
    public function scopeForAstrologer($query, int $astrologerId)
    {
        return $query->where('astrologer_profile_id', $astrologerId);
    }

    public function scopeReversals($query)
    {
        return $query->where('amount', '<', 0);
    }

    public function scopeApplied($query)
    {
        return $query->where('status', 'applied');
    }
}
