<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AstrologerPricing extends Model
{
    use HasFactory;

    protected $table = 'astrologer_pricing';

    protected $fillable = [
        'astrologer_id',
        'call_per_minute',
        'chat_price',
        'ai_chat_price',
    ];

    protected $casts = [
        'call_per_minute' => 'decimal:2',
        'chat_price' => 'decimal:2',
        'ai_chat_price' => 'decimal:2',
    ];

    public function astrologer(): BelongsTo
    {
        return $this->belongsTo(Astrologer::class);
    }
}
