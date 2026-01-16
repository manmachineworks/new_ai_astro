<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AiChatReport extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'ai_chat_message_id',
        'reason',
        'details',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function message()
    {
        return $this->belongsTo(AiChatMessage::class, 'ai_chat_message_id');
    }
}
