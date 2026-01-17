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
        Schema::table('phonepe_payments', function (Blueprint $table) {
            $table->string('type')->default('recharge')->after('status'); // recharge, membership
            $table->json('meta_json')->nullable()->after('type'); // store plan_id etc
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('phonepe_payments', function (Blueprint $table) {
            $table->dropColumn(['type', 'meta_json']);
        });
    }
};
