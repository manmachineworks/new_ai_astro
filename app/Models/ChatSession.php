<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ChatSession extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'astrologer_user_id',
        'astrologer_profile_id',
        'conversation_id',
        'pricing_mode',
        'price_per_message',
        'session_price',
        'status',
        'started_at',
        'ended_at',
        'last_billed_at',
        'duration_minutes',
        'rate_per_minute',
        'cost',
        'firebase_chat_id',
    ];

    protected $casts = [
        'started_at' => 'datetime',
        'ended_at' => 'datetime',
        'last_billed_at' => 'datetime',
        'rate_per_minute' => 'decimal:2',
        'cost' => 'decimal:2',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function astrologer()
    {
        return $this->belongsTo(User::class, 'astrologer_user_id');
    }
}
