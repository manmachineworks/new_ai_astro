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
        Schema::create('chat_message_charges', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('chat_session_id')->index();
            $table->string('firestore_message_id')->unique();
            $table->string('charged_party')->default('user');
            $table->decimal('amount', 10, 2);
            $table->foreignId('wallet_transaction_id')->nullable()->constrained();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('chat_message_charges');
    }
};
