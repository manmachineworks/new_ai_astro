<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('promo_campaigns', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('name');
            $table->string('code', 50)->unique();
            $table->enum('type', ['coupon', 'cashback', 'referral', 'first_time'])->default('coupon');
            $table->enum('status', ['active', 'inactive', 'expired'])->default('active');
            $table->dateTime('start_at_utc')->nullable();
            $table->dateTime('end_at_utc')->nullable();
            $table->integer('usage_limit_total')->nullable();
            $table->integer('usage_limit_per_user')->default(1);
            $table->decimal('min_recharge_amount', 10, 2)->nullable();
            $table->decimal('min_spend_amount', 10, 2)->nullable();
            $table->enum('discount_type', ['flat', 'percent'])->default('flat');
            $table->decimal('discount_value', 10, 2);
            $table->json('applies_to')->nullable(); // ['recharge', 'call', 'chat', 'ai_chat', 'appointment']
            $table->decimal('max_discount_amount', 10, 2)->nullable();
            $table->boolean('first_time_only')->default(false);
            $table->unsignedBigInteger('created_by_admin_id')->nullable();
            $table->timestamps();

            $table->index('code');
            $table->index(['status', 'start_at_utc', 'end_at_utc']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('promo_campaigns');
    }
};
