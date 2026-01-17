<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class Refund extends Model
{
    use HasUuids;

    protected $fillable = [
        'dispute_id',
        'reference_type',
        'reference_id',
        'user_id',
        'amount',
        'currency',
        'reason',
        'status',
        'wallet_transaction_id',
        'idempotency_key',
        'processed_by_admin_id',
    ];

    protected $casts = [
        'amount' => 'float',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    // Relationships
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function dispute(): BelongsTo
    {
        return $this->belongsTo(Dispute::class);
    }

    public function walletTransaction(): BelongsTo
    {
        return $this->belongsTo(\App\Models\WalletTransaction::class);
    }

    public function processedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'processed_by_admin_id');
    }

    public function reference(): MorphTo
    {
        return $this->morphTo();
    }

    // Scopes
    public function scopeCompleted($query)
    {
        return $query->where('status', 'completed');
    }

    public function scopeByUser($query, int $userId)
    {
        return $query->where('user_id', $userId);
    }

    // Business Logic
    public function markCompleted(): bool
    {
        return $this->update(['status' => 'completed']);
    }

    public function markFailed(): bool
    {
        return $this->update(['status' => 'failed']);
    }
}
