<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AstrologerProfile extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'display_name',
        'bio',
        'gender',
        'dob',
        'profile_photo_path',
        'skills',
        'specialties',
        'languages',
        'experience_years',
        'rate_per_minute', // Legacy handling or remove if unused, keeping for safety
        'call_per_minute',
        'chat_per_minute', // Alias or new field? Migration has chat_per_session. sticking to schema.
        'chat_per_session', // Keeping consistency with DB
        'verification_status',
        'is_verified', // Added to fillable
        'visibility',
        'show_on_front',
        'is_enabled',
        'is_call_enabled',
        'is_sms_enabled',
        'is_chat_enabled',
        'rating_avg',
        'rating_count',
        'profile_fields',
        'admin_notes',
        'verified_by_admin_id',
        'verified_at',
        'rejection_reason'
    ];

    protected $casts = [
        'skills' => 'array',
        'specialties' => 'array',
        'languages' => 'array',
        'profile_fields' => 'array',
        'dob' => 'date',
        'visibility' => 'boolean',
        'show_on_front' => 'boolean',
        'is_enabled' => 'boolean',
        'is_call_enabled' => 'boolean',
        'is_sms_enabled' => 'boolean',
        'is_chat_enabled' => 'boolean',
        'call_per_minute' => 'decimal:2',
        'chat_per_session' => 'decimal:2',
        'rating_avg' => 'decimal:2',
        'verified_at' => 'datetime',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function documents()
    {
        return $this->hasMany(AstrologerDocument::class);
    }

    public function availabilityRules()
    {
        return $this->hasMany(AvailabilityRule::class);
    }

    public function reviews(): HasMany
    {
        return $this->hasMany(Review::class);
    }

    public function callSessions(): HasMany
    {
        return $this->hasMany(CallSession::class);
    }

    public function earningsLedger(): HasMany
    {
        return $this->hasMany(AstrologerEarningsLedger::class);
    }
}
