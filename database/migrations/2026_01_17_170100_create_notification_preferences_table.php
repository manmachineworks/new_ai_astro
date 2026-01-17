<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('notification_preferences', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->unique()->constrained()->onDelete('cascade');
            $table->boolean('mute_chat')->default(false);
            $table->boolean('mute_calls')->default(false);
            $table->boolean('mute_wallet')->default(false);
            $table->time('dnd_start')->nullable();
            $table->time('dnd_end')->nullable();
            $table->string('timezone')->default('Asia/Kolkata');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('notification_preferences');
    }
};
