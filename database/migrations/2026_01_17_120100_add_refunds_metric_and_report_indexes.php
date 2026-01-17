<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('daily_metrics', function (Blueprint $table) {
            if (!Schema::hasColumn('daily_metrics', 'refunds_amount')) {
                $table->decimal('refunds_amount', 15, 2)->default(0)->after('wallet_recharge_count_failed');
            }
        });

        Schema::table('chat_message_charges', function (Blueprint $table) {
            $table->index('created_at');
        });

        Schema::table('ai_message_charges', function (Blueprint $table) {
            $table->index('created_at');
        });

        Schema::table('refunds', function (Blueprint $table) {
            $table->index(['status', 'updated_at']);
        });
    }

    public function down(): void
    {
        Schema::table('daily_metrics', function (Blueprint $table) {
            if (Schema::hasColumn('daily_metrics', 'refunds_amount')) {
                $table->dropColumn('refunds_amount');
            }
        });

        Schema::table('chat_message_charges', function (Blueprint $table) {
            $table->dropIndex(['created_at']);
        });

        Schema::table('ai_message_charges', function (Blueprint $table) {
            $table->dropIndex(['created_at']);
        });

        Schema::table('refunds', function (Blueprint $table) {
            $table->dropIndex(['status', 'updated_at']);
        });
    }
};
