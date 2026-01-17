<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('user_device_tokens', function (Blueprint $table) {
            $table->enum('status', ['active', 'revoked'])->default('active')->after('last_seen_at');
            $table->timestamp('last_token_refresh_at')->nullable()->after('status');
        });
    }

    public function down(): void
    {
        Schema::table('user_device_tokens', function (Blueprint $table) {
            $table->dropColumn(['status', 'last_token_refresh_at']);
        });
    }
};
