<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AstrologerService extends Model
{
    use HasFactory;

    protected $table = 'astrologer_services';

    protected $fillable = [
        'astrologer_id',
        'call_enabled',
        'chat_enabled',
        'sms_enabled',
        'online_status',
    ];

    protected $casts = [
        'call_enabled' => 'boolean',
        'chat_enabled' => 'boolean',
        'sms_enabled' => 'boolean',
    ];

    public function astrologer(): BelongsTo
    {
        return $this->belongsTo(Astrologer::class);
    }
}
