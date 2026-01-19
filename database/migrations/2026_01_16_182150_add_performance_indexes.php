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
        // Wallet Transactions - common queries
        Schema::table('wallet_transactions', function (Blueprint $table) {
            if (!$this->indexExists('wallet_transactions', 'wallet_transactions_user_id_created_at_index')) {
                $table->index(['user_id', 'created_at']);
            }
            if (!$this->indexExists('wallet_transactions', 'wallet_transactions_type_index')) {
                $table->index('type');
            }
        });

        // Payment Orders - webhook lookups
        Schema::table('payment_orders', function (Blueprint $table) {
            if (!$this->indexExists('payment_orders', 'payment_orders_merchant_transaction_id_index')) {
                $table->index('merchant_transaction_id');
            }
            if (!$this->indexExists('payment_orders', 'payment_orders_status_updated_at_index')) {
                $table->index(['status', 'updated_at']);
            }
        });

        // Call Sessions - astrologer dashboard and reporting
        Schema::table('call_sessions', function (Blueprint $table) {
            if (!$this->indexExists('call_sessions', 'call_sessions_provider_call_id_index')) {
                $table->index('provider_call_id');
            }
            if (!$this->indexExists('call_sessions', 'call_sessions_astrologer_profile_id_created_at_index')) {
                $table->index(['astrologer_profile_id', 'created_at']);
            }
            if (!$this->indexExists('call_sessions', 'call_sessions_status_updated_at_index')) {
                $table->index(['status', 'updated_at']);
            }
        });

        // Chat Message Charges - idempotency checks
        Schema::table('chat_message_charges', function (Blueprint $table) {
            if (!$this->indexExists('chat_message_charges', 'chat_message_charges_firestore_message_id_index')) {
                $table->index('firestore_message_id');
            }
        });

        // AI Message Charges - idempotency checks
        Schema::table('ai_message_charges', function (Blueprint $table) {
            if (!$this->indexExists('ai_message_charges', 'ai_message_charges_client_message_id_index')) {
                $table->index('client_message_id');
            }
        });

        // Webhook Events - external ID lookups
        Schema::table('webhook_events', function (Blueprint $table) {
            if (!$this->indexExists('webhook_events', 'webhook_events_external_id_created_at_index')) {
                $table->index(['external_id', 'created_at']);
            }
            // Note: status column doesn't exist in webhook_events table
            // if (!$this->indexExists('webhook_events', 'webhook_events_status_index')) {
            //     $table->index('status');
            // }
        });
    }

    protected function indexExists($table, $index)
    {
        $connection = Schema::getConnection();
        if ($connection->getDriverName() === 'sqlite') {
            $indexes = $connection->select("PRAGMA index_list('{$table}')");
            foreach ($indexes as $idx) {
                if (($idx->name ?? null) === $index) {
                    return true;
                }
            }
            return false;
        }
        $dbName = $connection->getDatabaseName();

        $result = \DB::select("
            SELECT COUNT(*) as count
            FROM information_schema.statistics
            WHERE table_schema = ?
            AND table_name = ?
            AND index_name = ?
        ", [$dbName, $table, $index]);

        return $result[0]->count > 0;
    }

    public function down(): void
    {
        Schema::table('wallet_transactions', function (Blueprint $table) {
            $table->dropIndex(['user_id', 'created_at']);
            $table->dropIndex(['type']);
        });

        Schema::table('payment_orders', function (Blueprint $table) {
            $table->dropIndex(['merchant_transaction_id']);
            $table->dropIndex(['status', 'updated_at']);
        });

        Schema::table('call_sessions', function (Blueprint $table) {
            $table->dropIndex(['provider_call_id']);
            $table->dropIndex(['astrologer_profile_id', 'created_at']);
            $table->dropIndex(['status', 'updated_at']);
        });

        Schema::table('chat_message_charges', function (Blueprint $table) {
            $table->dropIndex(['firestore_message_id']);
        });

        Schema::table('ai_message_charges', function (Blueprint $table) {
            $table->dropIndex(['client_message_id']);
        });

        Schema::table('webhook_events', function (Blueprint $table) {
            $table->dropIndex(['external_id', 'created_at']);
            // $table->dropIndex(['status']);
        });
    }
};
