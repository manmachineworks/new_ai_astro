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
        Schema::create('wallet_transactions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('wallet_id')->constrained()->onDelete('cascade');
            $table->decimal('amount', 10, 2);
            $table->string('type'); // credit, debit
            $table->string('reference_type')->nullable(); // call, chat, order, recharge, refund
            $table->string('reference_id')->nullable();
            $table->string('description')->nullable();
            $table->decimal('balance_after', 10, 2);
            $table->json('meta')->nullable();
            $table->string('transaction_id')->unique()->nullable(); // External ID like PhonePe
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('wallet_transactions');
    }
};
