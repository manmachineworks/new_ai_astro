<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class NotificationLog extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'type',
        'payload_json',
        'provider_message_id',
        'status',
        'error_message'
    ];

    protected $casts = [
        'payload_json' => 'array'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
