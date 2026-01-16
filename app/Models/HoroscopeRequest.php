<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;

class HoroscopeRequest extends Model
{
    use HasFactory, HasUuids;

    protected $fillable = [
        'user_id',
        'type',
        'input_json',
        'response_json',
        'provider_request_id',
        'status',
        'error_message',
    ];

    protected $casts = [
        'input_json' => 'array',
        'response_json' => 'array',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
