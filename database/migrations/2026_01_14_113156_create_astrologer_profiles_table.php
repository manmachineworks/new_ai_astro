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
        Schema::create('astrologer_profiles', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->string('verification_status')->default('pending'); // pending, verified, rejected
            $table->boolean('is_visible')->default(false);
            $table->boolean('call_enabled')->default(true);
            $table->boolean('chat_enabled')->default(true);
            $table->decimal('per_minute_rate', 8, 2)->default(0);
            $table->decimal('per_chat_rate', 8, 2)->default(0);
            $table->json('languages')->nullable();
            $table->json('expertise')->nullable();
            $table->integer('experience_years')->default(0);
            $table->text('bio')->nullable();
            $table->decimal('rating', 3, 2)->default(0);
            $table->integer('review_count')->default(0);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('astrologer_profiles');
    }
};
