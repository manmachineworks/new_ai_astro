<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('chat_sessions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('astrologer_id')->constrained('astrologers')->cascadeOnDelete();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->string('user_public_id');
            $table->string('firebase_chat_id')->unique();
            $table->enum('status', ['active', 'closed', 'blocked', 'expired'])->default('active');
            $table->dateTime('started_at');
            $table->dateTime('ended_at')->nullable();
            $table->decimal('chat_price', 10, 2)->default(0);
            $table->decimal('amount_charged', 12, 2)->default(0);
            $table->json('meta')->nullable();
            $table->timestamps();
            $table->index(['astrologer_id', 'user_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('chat_sessions');
    }
};
