<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class UserEvent extends Model
{
    protected $fillable = [
        'user_id',
        'event_type',
        'entity_type',
        'entity_id',
        'meta_json',
    ];

    protected $casts = [
        'meta_json' => 'array',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
