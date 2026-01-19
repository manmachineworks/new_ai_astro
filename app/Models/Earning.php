<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Earning extends Model
{
    use HasFactory;

    protected $fillable = [
        'astrologer_id',
        'source',
        'source_id',
        'gross_amount',
        'commission_amount',
        'net_amount',
        'status',
    ];

    protected $casts = [
        'gross_amount' => 'decimal:2',
        'commission_amount' => 'decimal:2',
        'net_amount' => 'decimal:2',
    ];

    public function astrologer(): BelongsTo
    {
        return $this->belongsTo(Astrologer::class);
    }
}
