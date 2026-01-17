<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('ticket_messages', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('support_ticket_id')->constrained()->cascadeOnDelete();
            $table->enum('sender_type', ['user', 'admin', 'system'])->default('user');
            $table->unsignedBigInteger('sender_id')->nullable();
            $table->text('message');
            $table->json('attachments_json')->nullable();
            $table->timestamp('created_at');

            $table->index(['support_ticket_id', 'created_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('ticket_messages');
    }
};
