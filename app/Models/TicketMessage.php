<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TicketMessage extends Model
{
    use HasUuids;

    const UPDATED_AT = null;

    protected $fillable = [
        'support_ticket_id',
        'sender_type',
        'sender_id',
        'message',
        'attachments_json',
        'created_at',
    ];

    protected $casts = [
        'attachments_json' => 'array',
        'created_at' => 'datetime',
    ];

    // Relationships
    public function ticket(): BelongsTo
    {
        return $this->belongsTo(SupportTicket::class, 'support_ticket_id');
    }

    public function sender()
    {
        if ($this->sender_type === 'user' && $this->sender_id) {
            return User::find($this->sender_id);
        }
        return null;
    }
}
