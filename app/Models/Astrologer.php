<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Astrologer extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'public_id',
        'display_name',
        'bio',
        'languages',
        'specializations',
        'experience_years',
        'profile_photo_url',
        'is_verified',
        'verification_status',
        'verification_remark',
        'is_listed',
    ];

    protected $casts = [
        'languages' => 'array',
        'specializations' => 'array',
        'is_verified' => 'boolean',
        'is_listed' => 'boolean',
        'experience_years' => 'int',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function service(): HasOne
    {
        return $this->hasOne(AstrologerService::class);
    }

    public function pricing(): HasOne
    {
        return $this->hasOne(AstrologerPricing::class);
    }

    public function pricingAudits(): HasMany
    {
        return $this->hasMany(AstrologerPricingAudit::class);
    }

    public function schedules(): HasMany
    {
        return $this->hasMany(AstrologerSchedule::class);
    }

    public function timeOff(): HasMany
    {
        return $this->hasMany(AstrologerTimeOff::class);
    }

    public function callLogs(): HasMany
    {
        return $this->hasMany(CallLog::class);
    }

    public function chatSessions(): HasMany
    {
        return $this->hasMany(ChatSession::class);
    }

    public function appointments(): HasMany
    {
        return $this->hasMany(Appointment::class);
    }

    public function earnings(): HasMany
    {
        return $this->hasMany(Earning::class);
    }
}
