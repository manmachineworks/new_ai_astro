<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CallSession extends Model
{
    use HasUuids;

    protected $fillable = [
        'user_id',
        'astrologer_profile_id',
        'provider',
        'provider_call_id',
        'status',
        'started_at_utc',
        'connected_at_utc',
        'ended_at_utc',
        'duration_seconds',
        'billable_minutes',
        'rate_per_minute',
        'gross_amount',
        'platform_commission_amount',
        'astrologer_earnings_amount',
        'wallet_hold_id',
        'user_masked_identifier',
        'astrologer_masked_identifier',
        'meta_json',
        'settled_at'
    ];

    protected $casts = [
        'started_at_utc' => 'datetime',
        'connected_at_utc' => 'datetime',
        'ended_at_utc' => 'datetime',
        'meta_json' => 'array',
        'settled_at' => 'datetime',
    ];

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
        return $this->belongsTo(WalletHold::class);
    }
}
