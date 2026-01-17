<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class DisputeEvent extends Model
{
    const UPDATED_AT = null;

    protected $fillable = [
        'dispute_id',
        'actor_type',
        'actor_id',
        'event_type',
        'meta_json',
        'created_at',
    ];

    protected $casts = [
        'meta_json' => 'array',
        'created_at' => 'datetime',
    ];

    // Relationships
    public function dispute(): BelongsTo
    {
        return $this->belongsTo(Dispute::class);
    }
}
