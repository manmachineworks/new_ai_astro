<?php

namespace App\Services;

use App\Models\User;
use App\Models\UserMembership;
use App\Models\MembershipPlan;
use App\Models\MembershipEvent;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class MembershipService
{
    public function activate(User $user, $planId, $paymentOrderId = null)
    {
        $plan = MembershipPlan::findOrFail($planId);

        // Check if user already has an active membership
        // Logic: Cancel/Expire old one or Extend?
        // For MVP: Cancel old active ones instantly (switch plan).
        $activeMemberships = $user->memberships()
            ->where('status', 'active')
            ->where(function ($q) {
                $q->whereNull('ends_at_utc')
                    ->orWhere('ends_at_utc', '>', now());
            })
            ->get();

        foreach ($activeMemberships as $membership) {
            $membership->update(['status' => 'cancelled']);
            MembershipEvent::create([
                'user_membership_id' => $membership->id,
                'event_type' => 'cancelled',
                'meta_json' => ['reason' => 'upgraded_plan']
            ]);
        }

        // Create new membership
        $membership = UserMembership::create([
            'user_id' => $user->id,
            'membership_plan_id' => $plan->id,
            'status' => 'active',
            'starts_at_utc' => now(),
            'ends_at_utc' => now()->addDays($plan->duration_days),
            'payment_order_id' => $paymentOrderId,
        ]);

        MembershipEvent::create([
            'user_membership_id' => $membership->id,
            'event_type' => 'purchased',
            'meta_json' => ['plan_name' => $plan->name, 'price' => $plan->price_amount]
        ]);

        return $membership;
    }

    public function getActiveMembership(User $user)
    {
        return $user->activeMembership;
    }

    /**
     * Calculate discount for a service
     * @param User $user
     * @param string $serviceType 'call', 'chat'
     * @param float $amount
     * @return array ['final_amount' => x, 'discount' => y, 'plan_name' => z]
     */
    public function calculateDiscount(User $user, $serviceType, $amount)
    {
        $membership = $this->getActiveMembership($user);

        if (!$membership) {
            return ['final_amount' => $amount, 'discount' => 0, 'plan_name' => null];
        }

        $plan = $membership->plan;
        if (!$plan) {
            return ['final_amount' => $amount, 'discount' => 0, 'plan_name' => null];
        }

        $benefits = $plan->benefits_json;
        $discountPercent = 0;

        if ($serviceType === 'call') {
            $discountPercent = $benefits['call_discount_percent'] ?? 0;
        } elseif ($serviceType === 'chat') {
            $discountPercent = $benefits['chat_discount_percent'] ?? 0;
        }

        $discountAmount = ($amount * $discountPercent) / 100;
        $finalAmount = max(0, $amount - $discountAmount);

        return [
            'final_amount' => $finalAmount,
            'discount' => $discountAmount,
            'plan_name' => $plan->name,
            'membership_id' => $membership->id
        ];
    }

    public function consumeBenefit(User $user, $benefitKey, $amount = 1)
    {
        $membership = $this->getActiveMembership($user);
        if (!$membership)
            return false;

        $plan = $membership->plan;
        $maxLimit = $plan->benefits_json[$benefitKey] ?? 0;

        if ($maxLimit <= 0)
            return false; // No benefit

        // Track usage per period (assume Monthly reset for now, or total per plan duration?)
        // Let's assume Total per membership Duration for simplicity in MVP, 
        // OR period resets monthly. Let's do Total Per Duration for MVP simplicity unless 'monthly_coupons' logic needed.
        // Re-reading task: "monthly_coupons (array)". So likely monthly.
        // Let's implement Period Logic: 30 days cycles.

        $periodStart = $membership->starts_at_utc->copy();
        while ($periodStart->addDays(30)->isPast()) {
            $periodStart->addDays(30);
        }
        $periodEnd = $periodStart->copy()->addDays(30);

        $usage = \App\Models\MembershipBenefitUsage::firstOrCreate(
            [
                'user_membership_id' => $membership->id,
                'benefit_key' => $benefitKey,
                'period_start_utc' => $periodStart,
            ],
            [
                'period_end_utc' => $periodEnd,
                'used_count' => 0
            ]
        );

        if ($usage->used_count + $amount <= $maxLimit) {
            $usage->increment('used_count', $amount);
            MembershipEvent::create([
                'user_membership_id' => $membership->id,
                'event_type' => 'benefit_used',
                'meta_json' => ['benefit' => $benefitKey, 'amount' => $amount]
            ]);
            return true;
        }

        return false;
    }
}
