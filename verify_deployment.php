<?php

// Quick verification test for M11 & M12 deployment

echo "=== Milestone 11 & 12 Verification ===" . PHP_EOL . PHP_EOL;

// Test database tables
echo "📊 Database Tables:" . PHP_EOL;
echo "- Referral Codes: " . DB::table('referral_codes')->count() . PHP_EOL;
echo "- Referrals: " . DB::table('referrals')->count() . PHP_EOL;
echo "- Promo Campaigns: " . DB::table('promo_campaigns')->count() . PHP_EOL;
echo "- Promo Redemptions: " . DB::table('promo_redemptions')->count() . PHP_EOL;
echo "- Support Tickets: " . DB::table('support_tickets')->count() . PHP_EOL;
echo "- Disputes: " . DB::table('disputes')->count() . PHP_EOL;
echo "- Refunds: " . DB::table('refunds')->count() . PHP_EOL;
echo "- Earnings Adjustments: " . DB::table('earnings_adjustments')->count() . PHP_EOL;

echo PHP_EOL . "⚙️  Configuration:" . PHP_EOL;
$settings = DB::table('pricing_settings')->first();
if ($settings) {
    echo "- Referral Inviter Bonus: ₹" . $settings->referral_inviter_bonus_amount . PHP_EOL;
    echo "- Referral Invitee Bonus: ₹" . $settings->referral_invitee_bonus_amount . PHP_EOL;
    echo "- First Time Bonus: ₹" . $settings->first_time_recharge_bonus_amount . PHP_EOL;
    echo "- Min First Recharge: ₹" . $settings->first_time_recharge_min_amount . PHP_EOL;
} else {
    echo "⚠️  No pricing settings found!" . PHP_EOL;
}

echo PHP_EOL . "✅ All tables verified and accessible!" . PHP_EOL;
echo PHP_EOL . "Ready for functional testing." . PHP_EOL;
