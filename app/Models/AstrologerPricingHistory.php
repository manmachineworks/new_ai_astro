<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AstrologerPricingHistory extends Model
{
    protected $fillable = [
        'astrologer_profile_id',
        'old_call_per_minute',
        'new_call_per_minute',
        'old_chat_per_session',
        'new_chat_per_session',
        'changed_by_user_id',
        'change_source',
        'notes',
    ];

    public function astrologerProfile(): BelongsTo
    {
        return $this->belongsTo(AstrologerProfile::class);
    }

    public function changedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'changed_by_user_id');
    }
}
