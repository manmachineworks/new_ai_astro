<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class NotificationPreference extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'mute_chat',
        'mute_calls',
        'mute_wallet',
        'dnd_start',
        'dnd_end',
        'timezone'
    ];

    protected $casts = [
        'mute_chat' => 'boolean',
        'mute_calls' => 'boolean',
        'mute_wallet' => 'boolean',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
