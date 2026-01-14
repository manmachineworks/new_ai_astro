<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AstrologerProfile extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'verification_status',
        'is_visible',
        'call_enabled',
        'chat_enabled',
        'per_minute_rate',
        'per_chat_rate',
        'languages',
        'expertise',
        'experience_years',
        'bio',
        'rating',
        'review_count',
    ];

    protected $casts = [
        'is_visible' => 'boolean',
        'call_enabled' => 'boolean',
        'chat_enabled' => 'boolean',
        'languages' => 'array',
        'expertise' => 'array',
        'per_minute_rate' => 'decimal:2',
        'per_chat_rate' => 'decimal:2',
        'rating' => 'decimal:2',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
