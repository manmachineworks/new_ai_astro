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
        // 1. User Preferences
        Schema::create('user_preferences', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id')->unique();
            $table->json('preferred_languages')->nullable();
            $table->json('preferred_specialties')->nullable();
            $table->json('preferred_price_range')->nullable(); // {min: 0, max: 100}
            $table->string('zodiac_sign', 20)->nullable();
            $table->boolean('onboarding_completed')->default(false);
            $table->timestamps();

            // $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
        });

        // 2. User Events (Analytics Signals)
        Schema::create('user_events', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id')->nullable();
            $table->string('event_type', 50); // Adjusted length
            $table->string('entity_type', 50)->nullable(); // Adjusted length
            $table->string('entity_id', 64)->nullable(); // Adjusted length
            $table->json('meta_json')->nullable();
            $table->timestamps();

            $table->index(['user_id', 'created_at']);
            $table->index(['event_type', 'created_at']);
            $table->index(['entity_type', 'entity_id']);
        });

        // 3. Bookmarks
        Schema::create('bookmarks', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id');
            $table->unsignedBigInteger('astrologer_profile_id');
            $table->timestamps();

            $table->unique(['user_id', 'astrologer_profile_id']);
            // $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            // $table->foreign('astrologer_profile_id')->references('id')->on('astrologer_profiles')->onDelete('cascade');
        });

        // 4. Recommendation Settings (Admin Config)
        Schema::create('recommendation_settings', function (Blueprint $table) {
            $table->id();
            $table->string('key', 100)->unique();
            $table->json('value_json');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('recommendation_settings');
        Schema::dropIfExists('bookmarks');
        Schema::dropIfExists('user_events');
        Schema::dropIfExists('user_preferences');
    }
};
