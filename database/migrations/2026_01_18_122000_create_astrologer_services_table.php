<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('astrologer_services', function (Blueprint $table) {
            $table->id();
            $table->foreignId('astrologer_id')->constrained('astrologers')->cascadeOnDelete();
            $table->boolean('call_enabled')->default(false);
            $table->boolean('chat_enabled')->default(false);
            $table->boolean('sms_enabled')->default(false);
            $table->enum('online_status', ['online', 'offline'])->default('offline');
            $table->timestamps();
            $table->unique('astrologer_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('astrologer_services');
    }
};
