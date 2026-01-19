<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('promo_redemptions', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('promo_campaign_id')->constrained()->cascadeOnDelete();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->string('reference_type'); // PaymentOrder, CallSession, etc.
            $table->string('reference_id'); // UUID or ID
            $table->decimal('discount_amount', 10, 2)->default(0);
            $table->decimal('bonus_credited', 10, 2)->nullable();
            $table->enum('status', ['applied', 'reversed'])->default('applied');
            $table->string('idempotency_key')->unique();
            $table->json('meta_json')->nullable();
            $table->timestamps();

            $table->index('idempotency_key');
            $table->index(['promo_campaign_id', 'created_at']);
            $table->index(['user_id', 'status']);
        });

        // Create composite index with prefix to fit MySQL 1000-byte limit
        if (DB::getDriverName() === 'mysql') {
            DB::statement('CREATE INDEX idx_ref_type_id ON promo_redemptions (reference_type(100), reference_id(100))');
        } else {
            Schema::table('promo_redemptions', function (Blueprint $table) {
                $table->index(['reference_type', 'reference_id'], 'idx_ref_type_id');
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('promo_redemptions');
    }
};
