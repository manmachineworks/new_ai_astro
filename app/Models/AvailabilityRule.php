<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AvailabilityRule extends Model
{
    protected $fillable = [
        'astrologer_user_id',
        'weekday',
        'start_time',
        'end_time',
        'slot_minutes',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    public function astrologer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'astrologer_user_id');
    }
}
