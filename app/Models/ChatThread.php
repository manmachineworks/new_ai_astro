<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ChatThread extends Model
{
    protected $fillable = [
        'user_id',
        'astrologer_user_id',
        'status',
        'last_message_at',
        'meta',
    ];

    protected $casts = [
        'last_message_at' => 'datetime',
        'meta' => 'array',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function astrologer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'astrologer_user_id');
    }

    public function messages(): HasMany
    {
        return $this->hasMany(ChatMessage::class, 'thread_id');
    }
}
