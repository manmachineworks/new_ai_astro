<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

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
        });

        Schema::table('call_sessions', function (Blueprint $table) {
            // New Columns
            if (!Schema::hasColumn('call_sessions', 'provider')) {
                $table->string('provider')->default('callerdesk')->after('astrologer_profile_id');
            }
            if (!Schema::hasColumn('call_sessions', 'connected_at_utc')) {
                $table->timestamp('connected_at_utc')->nullable()->after('started_at_utc');
            }
            if (!Schema::hasColumn('call_sessions', 'billable_minutes')) {
                $table->integer('billable_minutes')->default(0)->after('duration_seconds');
            }
            if (!Schema::hasColumn('call_sessions', 'platform_commission_amount')) {
                $table->decimal('platform_commission_amount', 10, 2)->default(0)->after('gross_amount');
            }
            if (!Schema::hasColumn('call_sessions', 'astrologer_earnings_amount')) {
                $table->decimal('astrologer_earnings_amount', 10, 2)->default(0)->after('platform_commission_amount');
            }
            if (!Schema::hasColumn('call_sessions', 'wallet_hold_id')) {
                $table->uuid('wallet_hold_id')->nullable()->after('astrologer_earnings_amount');
            }
            if (!Schema::hasColumn('call_sessions', 'user_masked_identifier')) {
                $table->string('user_masked_identifier')->nullable()->after('wallet_hold_id');
            }
            if (!Schema::hasColumn('call_sessions', 'astrologer_masked_identifier')) {
                $table->string('astrologer_masked_identifier')->nullable()->after('user_masked_identifier');
            }
            if (!Schema::hasColumn('call_sessions', 'settled_at')) {
                $table->timestamp('settled_at')->nullable()->after('meta_json');
            }
        });

        if (Schema::hasColumn('call_sessions', 'wallet_hold_id')) {
            DB::statement('ALTER TABLE call_sessions MODIFY wallet_hold_id CHAR(36) NULL');

            $constraint = DB::selectOne(
                "SELECT COUNT(*) AS count
                 FROM information_schema.KEY_COLUMN_USAGE
                 WHERE TABLE_SCHEMA = DATABASE()
                   AND TABLE_NAME = 'call_sessions'
                   AND COLUMN_NAME = 'wallet_hold_id'
                   AND REFERENCED_TABLE_NAME = 'wallet_holds'"
            );

            if ((int) ($constraint->count ?? 0) === 0) {
                DB::statement(
                    'ALTER TABLE call_sessions ADD CONSTRAINT call_sessions_wallet_hold_id_foreign FOREIGN KEY (wallet_hold_id) REFERENCES wallet_holds(id)'
                );
            }
        }
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
