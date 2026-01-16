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
        Schema::create('ai_chat_sessions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('astrologer_user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->string('status', 20)->default('open');
            $table->unsignedBigInteger('per_chat_price')->default(0);
            $table->unsignedBigInteger('total_cost')->default(0);
            $table->json('meta')->nullable();
            $table->timestamps();

            $table->index(['user_id', 'status']);
            $table->index('astrologer_user_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('ai_chat_sessions');
    }
};
