<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // 1. Membership Plans
        Schema::create('membership_plans', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('name');
            $table->string('slug')->unique();
            $table->decimal('price_amount', 10, 2);
            $table->string('currency', 3)->default('INR');
            $table->integer('duration_days')->comment('Duration in days, e.g. 30, 365');
            $table->json('benefits_json')->comment('JSON store for discounts, free limits etc.');
            $table->enum('status', ['active', 'inactive'])->default('active');
            $table->unsignedBigInteger('created_by_admin_id')->nullable();
            $table->timestamps();
        });

        // 2. User Memberships
        Schema::create('user_memberships', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->unsignedBigInteger('user_id');
            $table->uuid('membership_plan_id');
            $table->enum('status', ['active', 'expired', 'cancelled'])->default('active');
            $table->timestamp('starts_at_utc')->nullable();
            $table->timestamp('ends_at_utc')->nullable();
            $table->boolean('auto_renew')->default(false);
            $table->timestamp('next_renewal_at_utc')->nullable();
            $table->string('payment_order_id')->nullable(); // String to match provider order IDs
            $table->timestamps();

            $table->index(['user_id', 'status']);
            $table->index('ends_at_utc');

            // Foreign keys
            // $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            // $table->foreign('membership_plan_id')->references('id')->on('membership_plans');
        });

        // 3. Benefit Usage Tracking (for capped benefits like Free AI Messages)
        Schema::create('membership_benefit_usage', function (Blueprint $table) {
            $table->id();
            $table->uuid('user_membership_id');
            $table->string('benefit_key'); // e.g., 'ai_free_messages'
            $table->timestamp('period_start_utc')->nullable();
            $table->timestamp('period_end_utc')->nullable();
            $table->integer('used_count')->default(0);
            $table->timestamps();

            $table->unique(['user_membership_id', 'benefit_key', 'period_start_utc'], 'usage_unique_idx');
        });

        // 4. Membership Audit Events
        Schema::create('membership_events', function (Blueprint $table) {
            $table->id();
            $table->uuid('user_membership_id');
            $table->string('event_type'); // purchased, renewed, cancelled, benefit_used
            $table->json('meta_json')->nullable();
            $table->timestamps();

            $table->index('user_membership_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('membership_events');
        Schema::dropIfExists('membership_benefit_usage');
        Schema::dropIfExists('user_memberships');
        Schema::dropIfExists('membership_plans');
    }
};
