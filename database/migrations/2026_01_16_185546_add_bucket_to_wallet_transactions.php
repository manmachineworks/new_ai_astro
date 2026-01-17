<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('wallet_transactions', function (Blueprint $table) {
            $table->enum('bucket', ['cash', 'bonus'])->default('cash')->after('amount');
            $table->foreignUuid('promo_campaign_id')->nullable()->constrained()->nullOnDelete()->after('bucket');

            $table->index(['user_id', 'bucket']);
        });
    }

    public function down(): void
    {
        Schema::table('wallet_transactions', function (Blueprint $table) {
            $table->dropForeign(['promo_campaign_id']);
            $table->dropColumn(['bucket', 'promo_campaign_id']);
        });
    }
};
