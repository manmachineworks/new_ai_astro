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
        Schema::create('chat_sessions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('astrologer_user_id')->constrained('users')->cascadeOnDelete();
            $table->string('status', 20)->default('initiated'); // initiated, active, completed, failed
            $table->timestamp('started_at')->nullable();
            $table->timestamp('ended_at')->nullable();
            $table->timestamp('last_billed_at')->nullable();
            $table->unsignedInteger('duration_minutes')->default(0);
            $table->decimal('rate_per_minute', 8, 2)->default(0);
            $table->decimal('cost', 10, 2)->default(0);
            $table->string('firebase_chat_id', 191)->nullable();
            $table->timestamps();

            $table->index(['astrologer_user_id', 'status']);
            $table->index(['user_id', 'status']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('chat_sessions');
    }
};
