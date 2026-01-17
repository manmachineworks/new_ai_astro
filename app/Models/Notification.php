<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Notification extends Model
{
    use HasFactory, HasUuids;

    protected $guarded = [];

    protected $casts = [
        'data_json' => 'array',
        'read_at' => 'datetime',
        'archived_at' => 'datetime',
    ];

    public function recipient()
    {
        return $this->belongsTo(User::class, 'recipient_user_id');
    }

    public function scopeUnread($query)
    {
        return $query->where('status', 'unread');
    }
}
