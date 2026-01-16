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
        Schema::create('ai_message_charges', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('ai_chat_session_id')->index();
            $table->string('client_message_id')->unique(); // For idempotency
            $table->decimal('amount', 10, 2);
            $table->foreignId('wallet_transaction_id')->nullable()->constrained();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('ai_message_charges');
    }
};
