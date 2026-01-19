<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('call_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('astrologer_id')->constrained('astrologers')->cascadeOnDelete();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->string('user_public_id');
            $table->string('callerdesk_call_id')->nullable()->unique();
            $table->enum('status', ['initiated', 'ringing', 'connected', 'missed', 'failed', 'ended']);
            $table->dateTime('started_at')->nullable();
            $table->dateTime('ended_at')->nullable();
            $table->unsignedInteger('duration_seconds')->default(0);
            $table->decimal('rate_per_minute', 10, 2)->default(0);
            $table->decimal('amount_charged', 12, 2)->default(0);
            $table->json('meta')->nullable();
            $table->timestamps();
            $table->index(['astrologer_id', 'user_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('call_logs');
    }
};
