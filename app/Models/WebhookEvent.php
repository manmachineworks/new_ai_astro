<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;

class WebhookEvent extends Model
{
    use HasUuids;

    protected $fillable = [
        'provider',
        'event_type',
        'external_id',
        'signature_valid',
        'payload',
        'headers',
        'processed_at',
        'processing_status',
        'error_message'
    ];

    protected $casts = [
        'payload' => 'json',
        'headers' => 'json',
        'signature_valid' => 'boolean',
        'processed_at' => 'datetime',
    ];
}
