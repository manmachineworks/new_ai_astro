<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CallSession extends Model
{
    protected $fillable = [
        'user_id',
        'astrologer_user_id',
        'status',
        'started_at',
        'ended_at',
        'duration_seconds',
        'rate_per_minute',
        'cost',
        'callerdesk_call_id',
        'meta',
    ];

    protected $casts = [
        'started_at' => 'datetime',
        'ended_at' => 'datetime',
        'duration_seconds' => 'integer',
        'rate_per_minute' => 'integer',
        'cost' => 'integer',
        'meta' => 'array',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function astrologer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'astrologer_user_id');
    }
}
