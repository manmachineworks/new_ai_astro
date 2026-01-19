<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->timestamp('blocked_at')->nullable()->after('is_active');
            $table->timestamp('blocked_until')->nullable()->after('blocked_at');
            $table->text('blocked_reason')->nullable()->after('blocked_until');
            $table->foreignId('blocked_by_admin_id')->nullable()->after('blocked_reason')->constrained('users')->nullOnDelete();
            $table->timestamp('unblocked_at')->nullable()->after('blocked_by_admin_id');

            $table->index(['blocked_until']);
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropIndex(['blocked_until']);
            $table->dropConstrainedForeignId('blocked_by_admin_id');
            $table->dropColumn(['blocked_at', 'blocked_until', 'blocked_reason', 'unblocked_at']);
        });
    }
};
