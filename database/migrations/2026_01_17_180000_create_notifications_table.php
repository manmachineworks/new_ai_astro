<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('notifications', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignId('recipient_user_id')->constrained('users')->onDelete('cascade');
            $table->string('type', 50); // e.g. chat_new_message
            $table->string('title');
            $table->text('body')->nullable();
            $table->json('data_json')->nullable(); // Payload
            $table->string('status', 20)->default('unread'); // unread, read, archived
            $table->string('priority', 10)->default('normal');

            $table->timestamp('read_at')->nullable();
            $table->timestamp('archived_at')->nullable();
            $table->timestamps();

            // Indexes for Inbox queries
            // 1. Get Unread
            $table->index(['recipient_user_id', 'status', 'created_at']);
            // 2. Get All by Type
            $table->index(['recipient_user_id', 'type', 'created_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('notifications');
    }
};
