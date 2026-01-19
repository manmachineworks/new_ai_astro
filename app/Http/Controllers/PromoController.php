<?php

namespace App\Http\Controllers;

use App\Models\PromoCampaign;
use App\Services\PromoService;
use Illuminate\Http\Request;

class PromoController extends Controller
{
    protected $promoService;

    public function __construct(PromoService $promoService)
    {
        $this->promoService = $promoService;
    }

    /**
     * Validate a promo code
     */
    public function validatePromo(Request $request)
    {
        $request->validate([
            'code' => 'required|string',
            'context' => 'required|string|in:recharge,call,chat,ai_chat,appointment',
            'amount' => 'nullable|numeric|min:0',
        ]);

        try {
            $promo = $this->promoService->validateCode(
                $request->code,
                $request->user(),
                $request->context,
                $request->amount
            );

            $discountAmount = $request->amount
                ? $this->promoService->computeDiscount($promo, $request->amount)
                : 0;

            return response()->json([
                'valid' => true,
                'promo' => [
                    'name' => $promo->name,
                    'type' => $promo->type,
                    'discount_type' => $promo->discount_type,
                    'discount_value' => $promo->discount_value,
                ],
                'discount_amount' => $discountAmount,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'valid' => false,
                'error' => $e->getMessage(),
            ], 400);
        }
    }

    /**
     * Check first-time eligibility
     */
    public function checkFirstTimeEligible(Request $request)
    {
        $user = $request->user();
        $settings = \App\Models\PricingSetting::first();

        if (!$settings) {
            return response()->json(['eligible' => false]);
        }

        // Check if user has any successful recharges
        $hasRecharge = \App\Models\PaymentOrder::where('user_id', $user->id)
            ->where('status', 'success')
            ->exists();

        return response()->json([
            'eligible' => !$hasRecharge,
            'bonus_amount' => $settings->first_time_recharge_bonus_amount,
            'min_recharge' => $settings->first_time_recharge_min_amount,
        ]);
    }

    /**
     * Get user's referral code
     */
    public function getReferralCode(Request $request)
    {
        $user = $request->user();
        $referralCode = $user->referralCode;

        if (!$referralCode) {
            // Generate if doesn't exist
            $referralCode = \App\Models\ReferralCode::createForUser($user);
        }

        return response()->json([
            'code' => $referralCode->code,
            'share_url' => url('/register?ref=' . $referralCode->code),
        ]);
    }

    /**
     * Get user's referral stats
     */
    public function getReferralStats(Request $request)
    {
        $user = $request->user();
        $stats = app(\App\Services\ReferralService::class)->getReferralStats($user);

        return response()->json($stats);
    }
}
