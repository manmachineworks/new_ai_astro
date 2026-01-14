<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Call extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'astrologer_id',
        'call_sid',
        'status',
        'duration',
        'rate',
        'total_cost',
        'recording_url',
    ];

    protected $casts = [
        'rate' => 'decimal:2',
        'total_cost' => 'decimal:2',
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
