<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ChatSession extends Model
{
    use HasFactory;

    protected $fillable = [
        'astrologer_id',
        'user_id',
        'user_public_id',
        'firebase_chat_id',
        'status',
        'started_at',
        'ended_at',
        'chat_price',
        'amount_charged',
        'meta',
    ];

    protected $casts = [
        'started_at' => 'datetime',
        'ended_at' => 'datetime',
        'meta' => 'array',
    ];

    public function astrologer(): BelongsTo
    {
        return $this->belongsTo(Astrologer::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
