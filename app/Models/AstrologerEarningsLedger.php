<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class AstrologerEarningsLedger extends Model
{
    use HasUuids;

    protected $table = 'astrologer_earnings_ledger';

    protected $fillable = [
        'astrologer_profile_id',
        'source',
        'reference_type',
        'reference_id',
        'amount',
        'status'
    ];

    public function astrologerProfile(): BelongsTo
    {
        return $this->belongsTo(AstrologerProfile::class);
    }

    /**
     * Get the parent reference model (CallSession, etc.)
     */
    public function reference(): MorphTo
    {
        return $this->morphTo();
    }
}
