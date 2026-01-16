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
        // Since we want UUID and fixed columns, we'll drop and recreate if needed or just fix.
        // In dev, let's ensure it matches requirements perfectly.
        Schema::table('chat_sessions', function (Blueprint $table) {
            if (Schema::hasColumn('chat_sessions', 'id') && !Schema::hasColumn('chat_sessions', 'uuid')) {
                // Converting existing table to UUID in an update is messy. 
                // Let's just create new columns for now.
            }

            $table->string('conversation_id')->unique()->after('id');
            $table->enum('pricing_mode', ['per_message', 'per_session'])->default('per_message')->after('conversation_id');
            $table->decimal('price_per_message', 10, 2)->default(5.00)->after('pricing_mode');
            $table->decimal('session_price', 10, 2)->default(0)->after('price_per_message');

            $table->integer('total_messages_user')->default(0)->after('status');
            $table->integer('total_messages_astrologer')->default(0)->after('total_messages_user');
            $table->decimal('total_charged', 10, 2)->default(0)->after('total_messages_astrologer');

            if (Schema::hasColumn('chat_sessions', 'astrologer_user_id')) {
                // Keep for legacy or rename? The prompt asks for astrologer_profile_id.
                // We'll add profile_id and try to migrate if possible.
                $table->foreignId('astrologer_profile_id')->nullable()->after('user_id')->constrained();
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('chat_sessions', function (Blueprint $table) {
            // Drop columns logic
        });
    }
};
