<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AvailabilityException extends Model
{
    protected $fillable = [
        'astrologer_profile_id',
        'date',
        'type',
        'start_time_utc',
        'end_time_utc',
        'note',
    ];

    protected $casts = [
        'date' => 'date',
    ];

    public function astrologerProfile(): BelongsTo
    {
        return $this->belongsTo(AstrologerProfile::class);
    }
}
