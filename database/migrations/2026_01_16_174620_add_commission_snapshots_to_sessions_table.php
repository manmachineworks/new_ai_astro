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
        Schema::table('call_sessions', function (Blueprint $table) {
            $table->decimal('commission_percent_snapshot', 5, 2)->nullable()->after('platform_commission_amount');
        });

        Schema::table('chat_sessions', function (Blueprint $table) {
            $table->decimal('commission_percent_snapshot', 5, 2)->nullable()->after('total_charged');
            $table->decimal('commission_amount_total', 15, 2)->default(0)->after('commission_percent_snapshot');
        });

        Schema::table('ai_chat_sessions', function (Blueprint $table) {
            $table->decimal('commission_percent_snapshot', 5, 2)->nullable()->after('total_charged');
            $table->decimal('commission_amount_total', 15, 2)->default(0)->after('commission_percent_snapshot');
        });
    }

    public function down(): void
    {
        Schema::table('call_sessions', function (Blueprint $table) {
            $table->dropColumn('commission_percent_snapshot');
        });
        Schema::table('chat_sessions', function (Blueprint $table) {
            $table->dropColumn(['commission_percent_snapshot', 'commission_amount_total']);
        });
        Schema::table('ai_chat_sessions', function (Blueprint $table) {
            $table->dropColumn(['commission_percent_snapshot', 'commission_amount_total']);
        });
    }
};
