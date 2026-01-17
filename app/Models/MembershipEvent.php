<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MembershipEvent extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_membership_id',
        'event_type',
        'meta_json',
    ];

    protected $casts = [
        'meta_json' => 'array',
    ];

    public function membership()
    {
        return $this->belongsTo(UserMembership::class, 'user_membership_id');
    }
}
