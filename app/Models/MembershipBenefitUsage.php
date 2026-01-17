<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MembershipBenefitUsage extends Model
{
    use HasFactory;

    protected $table = 'membership_benefit_usage';

    protected $fillable = [
        'user_membership_id',
        'benefit_key',
        'period_start_utc',
        'period_end_utc',
        'used_count',
    ];

    protected $casts = [
        'period_start_utc' => 'datetime',
        'period_end_utc' => 'datetime',
        'used_count' => 'integer',
    ];

    public function membership()
    {
        return $this->belongsTo(UserMembership::class, 'user_membership_id');
    }
}
