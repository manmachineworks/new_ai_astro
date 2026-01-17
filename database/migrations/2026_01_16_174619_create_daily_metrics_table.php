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
        Schema::create('daily_metrics', function (Blueprint $table) {
            $table->id();
            $table->date('date_ist')->unique();

            // Calls
            $table->decimal('call_gross', 15, 2)->default(0);
            $table->decimal('call_commission', 15, 2)->default(0);
            $table->decimal('call_earnings', 15, 2)->default(0);

            // Human Chat
            $table->decimal('chat_gross', 15, 2)->default(0);
            $table->decimal('chat_commission', 15, 2)->default(0);
            $table->decimal('chat_earnings', 15, 2)->default(0);

            // AI Chat
            $table->decimal('ai_gross', 15, 2)->default(0);
            $table->decimal('ai_commission', 15, 2)->default(0);
            $table->decimal('ai_earnings', 15, 2)->default(0);

            // Recharges
            $table->decimal('wallet_recharge_success', 15, 2)->default(0);
            $table->integer('wallet_recharge_count_success')->default(0);
            $table->integer('wallet_recharge_count_failed')->default(0);

            // User stats
            $table->integer('new_users')->default(0);
            $table->integer('active_users')->default(0);

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('daily_metrics');
    }
};
