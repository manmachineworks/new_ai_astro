<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class NotificationJob extends Model
{
    protected $fillable = [
        'type',
        'reference_type',
        'reference_id',
        'recipient_user_id',
        'scheduled_at',
        'status',
        'attempts',
        'last_error',
        'sent_at',
    ];

    protected $casts = [
        'scheduled_at' => 'datetime',
        'sent_at' => 'datetime',
        'attempts' => 'integer',
    ];

    // Relationships
    public function recipientUser(): BelongsTo
    {
        return $this->belongsTo(User::class, 'recipient_user_id');
    }

    public function reference(): MorphTo
    {
        return $this->morphTo();
    }

    // Scopes
    public function scopePending($query)
    {
        return $query->where('status', 'pending')
            ->where('scheduled_at', '<=', now())
            ->where('attempts', '<', 3); // Max 3 attempts
    }

    /**
     * Mark as sent
     */
    public function markAsSent()
    {
        $this->update([
            'status' => 'sent',
            'sent_at' => now(),
        ]);
    }

    /**
     * Mark as failed with error
     */
    public function markAsFailed(string $error)
    {
        $this->increment('attempts');
        $this->update([
            'status' => $this->attempts >= 3 ? 'failed' : 'pending',
            'last_error' => $error,
        ]);
    }
}
