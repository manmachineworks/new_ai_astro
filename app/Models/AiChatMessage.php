<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;

class AiChatMessage extends Model
{
    use HasFactory, HasUuids;

    protected $fillable = [
        'ai_chat_session_id',
        'role',
        'content',
        'provider_message_id',
        'tokens_used',
    ];

    public function session()
    {
        return $this->belongsTo(AiChatSession::class, 'ai_chat_session_id');
    }
}
