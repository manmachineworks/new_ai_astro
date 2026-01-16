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
            // Rename existing col if needed
            if (Schema::hasColumn('call_sessions', 'astrologer_user_id')) {
                $table->renameColumn('astrologer_user_id', 'astrologer_profile_id');
            }
            if (Schema::hasColumn('call_sessions', 'callerdesk_call_id')) {
                $table->renameColumn('callerdesk_call_id', 'provider_call_id');
            }
            if (Schema::hasColumn('call_sessions', 'cost')) {
                $table->renameColumn('cost', 'gross_amount');
            }
            if (Schema::hasColumn('call_sessions', 'started_at')) {
                $table->renameColumn('started_at', 'started_at_utc');
            }
            if (Schema::hasColumn('call_sessions', 'ended_at')) {
                $table->renameColumn('ended_at', 'ended_at_utc');
            }
            if (Schema::hasColumn('call_sessions', 'meta')) {
                $table->renameColumn('meta', 'meta_json');
            }

            // New Columns
            $table->string('provider')->default('callerdesk')->after('astrologer_profile_id');
            $table->timestamp('connected_at_utc')->nullable()->after('started_at_utc');
            $table->integer('billable_minutes')->default(0)->after('duration_seconds');
            $table->decimal('platform_commission_amount', 10, 2)->default(0)->after('gross_amount');
            $table->decimal('astrologer_earnings_amount', 10, 2)->default(0)->after('platform_commission_amount');
            $table->foreignId('wallet_hold_id')->nullable()->after('astrologer_earnings_amount')->constrained('wallet_holds');
            $table->string('user_masked_identifier')->nullable()->after('wallet_hold_id');
            $table->string('astrologer_masked_identifier')->nullable()->after('user_masked_identifier');
            $table->timestamp('settled_at')->nullable()->after('meta_json');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('call_sessions', function (Blueprint $table) {
            // Rollback logic omitted for brevity as it's a dev step, 
            // but normally you'd reverse renames and drop new cols.
        });
    }
};
