<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('pricing_settings', function (Blueprint $table) {
            // Referral settings
            $table->decimal('referral_inviter_bonus_amount', 10, 2)->default(100.00);
            $table->decimal('referral_invitee_bonus_amount', 10, 2)->default(50.00);
            $table->decimal('referral_qualification_min_recharge', 10, 2)->default(100.00);

            // First-time offer settings
            $table->decimal('first_time_recharge_bonus_amount', 10, 2)->default(25.00);
            $table->decimal('first_time_recharge_min_amount', 10, 2)->default(500.00);

            // Anti-abuse settings
            $table->boolean('restrict_bonus_to_verified')->default(false);
        });
    }

    public function down(): void
    {
        Schema::table('pricing_settings', function (Blueprint $table) {
            $table->dropColumn([
                'referral_inviter_bonus_amount',
                'referral_invitee_bonus_amount',
                'referral_qualification_min_recharge',
                'first_time_recharge_bonus_amount',
                'first_time_recharge_min_amount',
                'restrict_bonus_to_verified',
            ]);
        });
    }
};
