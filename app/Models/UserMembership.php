<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class UserMembership extends Model
{
    use HasFactory;

    protected $keyType = 'string';
    public $incrementing = false;

    protected $fillable = [
        'user_id',
        'membership_plan_id',
        'status',
        'starts_at_utc',
        'ends_at_utc',
        'auto_renew',
        'next_renewal_at_utc',
        'payment_order_id',
    ];

    protected $casts = [
        'starts_at_utc' => 'datetime',
        'ends_at_utc' => 'datetime',
        'next_renewal_at_utc' => 'datetime',
        'auto_renew' => 'boolean',
    ];

    protected static function boot()
    {
        parent::boot();
        static::creating(function ($model) {
            if (empty($model->id)) {
                $model->id = (string) Str::uuid();
            }
        });
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function plan()
    {
        return $this->belongsTo(MembershipPlan::class, 'membership_plan_id');
    }

    public function usage()
    {
        return $this->hasMany(MembershipBenefitUsage::class, 'user_membership_id');
    }
}
