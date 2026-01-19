<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('astrologer_pricing', function (Blueprint $table) {
            $table->id();
            $table->foreignId('astrologer_id')->constrained('astrologers')->cascadeOnDelete();
            $table->decimal('call_per_minute', 10, 2)->default(0);
            $table->decimal('chat_price', 10, 2)->default(0);
            $table->decimal('ai_chat_price', 10, 2)->default(0);
            $table->timestamps();
            $table->unique('astrologer_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('astrologer_pricing');
    }
};
