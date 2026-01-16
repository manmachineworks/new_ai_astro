<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class AiChatSession extends Model
{
    protected $fillable = [
        'user_id',
        'astrologer_user_id',
        'status',
        'per_chat_price',
        'total_cost',
        'meta',
    ];

    protected $casts = [
        'per_chat_price' => 'integer',
        'total_cost' => 'integer',
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
        return $this->hasMany(AiChatMessage::class, 'session_id');
    }
}
