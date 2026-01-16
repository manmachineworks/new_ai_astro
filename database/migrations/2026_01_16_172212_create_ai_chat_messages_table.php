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
        Schema::create('ai_chat_messages', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('ai_chat_session_id')->index();
            $table->enum('role', ['user', 'assistant', 'system']);
            $table->text('content');
            $table->string('provider_message_id')->nullable();
            $table->integer('tokens_used')->nullable();
            $table->timestamps();

            $table->index(['ai_chat_session_id', 'created_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('ai_chat_messages');
    }
};
