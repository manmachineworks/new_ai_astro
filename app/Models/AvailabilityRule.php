<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AvailabilityRule extends Model
{
    protected $fillable = [
        'astrologer_profile_id',
        'day_of_week',
        'start_time_utc',
        'end_time_utc',
        'is_active'
    ];

    // Explicitly define time fields if needed, but string format H:i:s usually works fine.
}
