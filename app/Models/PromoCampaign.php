<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class PromoCampaign extends Model
{
    use HasUuids;

    protected $fillable = [
        'name',
        'code',
        'type',
        'status',
        'start_at_utc',
        'end_at_utc',
        'usage_limit_total',
        'usage_limit_per_user',
        'min_recharge_amount',
        'min_spend_amount',
        'discount_type',
        'discount_value',
        'applies_to',
        'max_discount_amount',
        'first_time_only',
        'created_by_admin_id',
    ];

    protected $casts = [
        'start_at_utc' => 'datetime',
        'end_at_utc' => 'datetime',
        'usage_limit_total' => 'integer',
        'usage_limit_per_user' => 'integer',
        'min_recharge_amount' => 'float',
        'min_spend_amount' => 'float',
        'discount_value' => 'float',
        'applies_to' => 'array',
        'max_discount_amount' => 'float',
        'first_time_only' => 'boolean',
    ];

    // Relationships
    public function createdBy(): BelongsTo
    {
        return $this->belongsTo(\App\Models\User::class, 'created_by_admin_id');
    }

    public function redemptions(): HasMany
    {
        return $this->hasMany(PromoRedemption::class);
    }

    // Scopes
    public function scopeActive($query)
    {
        return $query->where('status', 'active')
            ->where(function ($q) {
                $q->whereNull('start_at_utc')
                    ->orWhere('start_at_utc', '<=', now());
            })
            ->where(function ($q) {
                $q->whereNull('end_at_utc')
                    ->orWhere('end_at_utc', '>=', now());
            });
    }

    public function scopeForContext($query, string $context)
    {
        return $query->where(function ($q) use ($context) {
            $q->whereJsonContains('applies_to', $context)
                ->orWhereJsonContains('applies_to', 'all');
        });
    }

    // Business Logic Methods

    /**
     * Check if campaign is currently active
     */
    public function isActive(): bool
    {
        if ($this->status !== 'active') {
            return false;
        }

        $now = now();

        if ($this->start_at_utc && $now->lt($this->start_at_utc)) {
            return false;
        }

        if ($this->end_at_utc && $now->gt($this->end_at_utc)) {
            return false;
        }

        return true;
    }

    /**
     * Check if user can use this promo
     */
    public function canBeUsedBy(User $user): bool
    {
        if (!$this->isActive()) {
            return false;
        }

        // Check user-specific usage limit
        $userUsageCount = $this->redemptions()
            ->where('user_id', $user->id)
            ->where('status', 'applied')
            ->count();

        if ($userUsageCount >= $this->usage_limit_per_user) {
            return false;
        }

        return true;
    }

    /**
     * Get remaining total uses
     */
    public function getRemainingUses(): ?int
    {
        if ($this->usage_limit_total === null) {
            return null; // Unlimited
        }

        $usedCount = $this->redemptions()
            ->where('status', 'applied')
            ->count();

        return max(0, $this->usage_limit_total - $usedCount);
    }

    /**
     * Get user's remaining uses
     */
    public function getUserRemainingUses(User $user): int
    {
        $usedCount = $this->redemptions()
            ->where('user_id', $user->id)
            ->where('status', 'applied')
            ->count();

        return max(0, $this->usage_limit_per_user - $usedCount);
    }

    /**
     * Calculate discount amount
     */
    public function calculateDiscount(float $amount): float
    {
        if ($this->discount_type === 'flat') {
            $discount = $this->discount_value;
        } else { // percent
            $discount = $amount * ($this->discount_value / 100);
        }

        // Apply max discount cap if set
        if ($this->max_discount_amount) {
            $discount = min($discount, $this->max_discount_amount);
        }

        return round($discount, 2);
    }

    /**
     * Check if applies to context
     */
    public function appliesToContext(string $context): bool
    {
        if (!$this->applies_to) {
            return false;
        }

        return in_array($context, $this->applies_to) || in_array('all', $this->applies_to);
    }
}
