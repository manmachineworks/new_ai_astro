<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('refunds', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('dispute_id')->nullable()->constrained()->nullOnDelete();
            $table->string('reference_type');
            $table->string('reference_id');
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->decimal('amount', 10, 2);
            $table->string('currency', 3)->default('INR');
            $table->string('reason');
            $table->enum('status', ['initiated', 'completed', 'failed'])->default('initiated');
            $table->unsignedBigInteger('wallet_transaction_id')->nullable();
            $table->string('idempotency_key')->unique();
            $table->unsignedBigInteger('processed_by_admin_id')->nullable();
            $table->timestamps();

            $table->index('idempotency_key');
            $table->index(['user_id', 'status']);
            $table->index('dispute_id');

            $table->foreign('wallet_transaction_id')
                ->references('id')
                ->on('wallet_transactions')
                ->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('refunds');
    }
};
