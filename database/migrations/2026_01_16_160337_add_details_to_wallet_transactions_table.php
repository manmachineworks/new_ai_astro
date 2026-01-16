<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('wallet_transactions', function (Blueprint $table) {
            $table->string('currency', 3)->default('INR')->after('amount');
            $table->string('source', 50)->nullable()->after('type'); // phonepe, call_charge, manual_admin etc
            $table->string('idempotency_key', 191)->nullable()->unique()->after('meta');
            $table->foreignId('created_by')->nullable()->constrained('users')->after('meta');
            // Assuming 'meta' exists from previous migration
        });
    }

    public function down(): void
    {
        Schema::table('wallet_transactions', function (Blueprint $table) {
            $table->dropColumn(['currency', 'source', 'idempotency_key', 'created_by']);
        });
    }
};
