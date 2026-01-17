<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class Dispute extends Model
{
    use HasUuids;

    protected $fillable = [
        'user_id',
        'reference_type',
        'reference_id',
        'reason_code',
        'description',
        'status',
        'requested_refund_amount',
        'approved_refund_amount',
        'admin_notes',
    ];

    protected $casts = [
        'requested_refund_amount' => 'float',
        'approved_refund_amount' => 'float',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    // Relationships
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function reference(): MorphTo
    {
        return $this->morphTo();
    }

    public function events(): HasMany
    {
        return $this->hasMany(DisputeEvent::class);
    }

    public function refund(): HasOne
    {
        return $this->hasOne(Refund::class);
    }

    // Scopes
    public function scopePending($query)
    {
        return $query->whereIn('status', ['submitted', 'under_review', 'needs_info']);
    }

    public function scopeResolved($query)
    {
        return $query->whereIn('status', ['approved_full', 'approved_partial', 'rejected', 'closed']);
    }

    public function scopeByStatus($query, string $status)
    {
        return $query->where('status', $status);
    }

    // Business Logic

    /**
     * Log a dispute event for audit trail
     */
    public function logEvent(string $eventType, string $actorType, ?int $actorId = null, ?array $meta = null): DisputeEvent
    {
        return $this->events()->create([
            'event_type' => $eventType,
            'actor_type' => $actorType,
            'actor_id' => $actorId,
            'meta_json' => $meta,
            'created_at' => now(),
        ]);
    }

    /**
     * Get transaction details from polymorphic reference
     */
    public function getTransactionDetails(): array
    {
        $reference = $this->reference;

        if (!$reference) {
            return ['error' => 'Reference not found'];
        }

        $details = [
            'type' => $this->reference_type,
            'id' => $this->reference_id,
        ];

        // Add service-specific details
        switch ($this->reference_type) {
            case 'App\\Models\\CallSession':
                $details['astrologer'] = $reference->astrologerProfile->display_name ?? 'Unknown';
                $details['duration'] = $reference->call_duration_seconds;
                $details['amount_charged'] = $reference->total_charge;
                $details['started_at'] = $reference->started_at;
                break;

            case 'App\\Models\\ChatSession':
                $details['astrologer'] = $reference->astrologerProfile->display_name ?? 'Unknown';
                $details['messages_count'] = $reference->messages()->count();
                $details['amount_charged'] = $reference->total_charge;
                break;

            case 'App\\Models\\Appointment':
                $details['astrologer'] = $reference->astrologerProfile->display_name ?? 'Unknown';
                $details['scheduled_at'] = $reference->start_at_utc;
                $details['amount_charged'] = $reference->price_total;
                $details['status'] = $reference->status;
                break;

            case 'App\\Models\\PaymentOrder':
                $details['amount'] = $reference->amount;
                $details['payment_status'] = $reference->status;
                break;
        }

        return $details;
    }
}
