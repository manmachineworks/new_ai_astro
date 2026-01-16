<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('call_sessions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('astrologer_user_id')->constrained('users')->cascadeOnDelete();
            $table->string('status', 20)->default('initiated');
            $table->timestamp('started_at')->nullable();
            $table->timestamp('ended_at')->nullable();
            $table->unsignedInteger('duration_seconds')->nullable();
            $table->unsignedBigInteger('rate_per_minute')->default(0);
            $table->unsignedBigInteger('cost')->default(0);
            $table->string('callerdesk_call_id', 191)->nullable();
            $table->json('meta')->nullable();
            $table->timestamps();

            $table->index(['astrologer_user_id', 'status']);
            $table->index(['user_id', 'created_at']);
            $table->index('callerdesk_call_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('call_sessions');
    }
};
