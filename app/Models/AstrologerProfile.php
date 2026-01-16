<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AstrologerProfile extends Model
{
    protected $fillable = [
        'user_id',
        'bio',
        'skills',
        'languages',
        'experience_years',
        'verification_status',
        'visibility',
        'is_call_enabled',
        'is_sms_enabled',
        'is_chat_enabled',
        'call_per_minute',
        'chat_per_session',
        'profile_fields',
        'admin_notes',
    ];

    protected $casts = [
        'skills' => 'array',
        'languages' => 'array',
        'profile_fields' => 'array',
        'visibility' => 'boolean',
        'is_call_enabled' => 'boolean',
        'is_sms_enabled' => 'boolean',
        'is_chat_enabled' => 'boolean',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
