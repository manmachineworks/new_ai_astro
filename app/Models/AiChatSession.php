<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;

class AiChatSession extends Model
{
    use HasFactory, HasUuids;

    protected $fillable = [
        'user_id',
        'pricing_mode',
        'price_per_message',
        'session_price',
        'status',
        'started_at',
        'ended_at',
        'total_messages',
        'total_charged',
        'commission_percent_snapshot',
        'commission_amount_total',
    ];

    protected $casts = [
        'started_at' => 'datetime',
        'ended_at' => 'datetime',
        'commission_percent_snapshot' => 'float',
        'commission_amount_total' => 'float',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function messages()
    {
        return $this->hasMany(AiChatMessage::class);
    }

    public function charges()
    {
        return $this->hasMany(AiMessageCharge::class);
    }
}
