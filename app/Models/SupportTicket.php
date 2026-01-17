<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class SupportTicket extends Model
{
    use HasUuids;

    protected $fillable = [
        'user_id',
        'subject',
        'category',
        'status',
        'priority',
    ];

    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    // Relationships
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function messages(): HasMany
    {
        return $this->hasMany(TicketMessage::class);
    }

    // Scopes
    public function scopeOpen($query)
    {
        return $query->whereIn('status', ['open', 'pending']);
    }

    public function scopeByCategory($query, string $category)
    {
        return $query->where('category', $category);
    }

    public function scopeByStatus($query, string $status)
    {
        return $query->where('status', $status);
    }

    // Business Logic
    public function addMessage(string $senderType, ?int $senderId, string $message): TicketMessage
    {
        return $this->messages()->create([
            'sender_type' => $senderType,
            'sender_id' => $senderId,
            'message' => $message,
            'created_at' => now(),
        ]);
    }

    public function markResolved(): bool
    {
        return $this->update(['status' => 'resolved']);
    }

    public function close(): bool
    {
        return $this->update(['status' => 'closed']);
    }
}
