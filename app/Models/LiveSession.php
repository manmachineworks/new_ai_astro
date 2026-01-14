<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LiveSession extends Model
{
    use HasFactory;

    protected $fillable = [
        'astrologer_id',
        'title',
        'description',
        'scheduled_at',
        'status',
        'entry_fee',
        'image',
    ];

    protected $casts = [
        'scheduled_at' => 'datetime',
        'entry_fee' => 'decimal:2',
    ];

    public function astrologer()
    {
        return $this->belongsTo(User::class, 'astrologer_id');
    }
}
