<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Appointment extends Model
{
    protected $fillable = [
        'user_id',
        'astrologer_user_id',
        'start_at',
        'end_at',
        'status',
        'payment_status',
        'price',
        'notes',
    ];

    protected $casts = [
        'start_at' => 'datetime',
        'end_at' => 'datetime',
        'price' => 'integer',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function astrologer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'astrologer_user_id');
    }
}
