<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('device_tokens', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->string('role')->nullable(); // user, astrologer, admin
            $table->string('platform')->default('android'); // android, ios, web

            // FCM tokens can be long; 191 keeps the unique index under MySQL key limits.
            $table->string('fcm_token', 191)->unique();

            $table->string('device_id')->nullable(); // Hardware ID
            $table->string('app_version')->nullable();
            $table->string('locale')->default('en');
            $table->boolean('is_enabled')->default(true);
            $table->timestamp('last_seen_at')->nullable();
            $table->timestamps();

            // Composite Index for efficient "Get Active Tokens for User" queries
            $table->index(['user_id', 'is_enabled']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('device_tokens');
    }
};
