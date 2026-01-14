<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ChatSession extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'astrologer_id',
        'status',
        'rate',
        'total_cost',
        'started_at',
        'ended_at',
    ];

    protected $casts = [
        'rate' => 'decimal:2',
        'total_cost' => 'decimal:2',
        'started_at' => 'datetime',
        'ended_at' => 'datetime',
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function astrologer()
    {
        return $this->belongsTo(User::class, 'astrologer_id');
    }
}
