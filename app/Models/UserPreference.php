<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class UserPreference extends Model
{
    protected $fillable = [
        'user_id',
        'preferred_languages',
        'preferred_specialties',
        'preferred_price_range',
        'zodiac_sign',
        'onboarding_completed',
    ];

    protected $casts = [
        'preferred_languages' => 'array',
        'preferred_specialties' => 'array',
        'preferred_price_range' => 'array',
        'onboarding_completed' => 'boolean',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
