<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AstrologerSchedule extends Model
{
    use HasFactory;

    protected $fillable = [
        'astrologer_id',
        'day_of_week',
        'start_time',
        'end_time',
        'is_active',
    ];

    protected $casts = [
        'day_of_week' => 'int',
        'is_active' => 'boolean',
    ];

    public function astrologer(): BelongsTo
    {
        return $this->belongsTo(Astrologer::class);
    }
}
