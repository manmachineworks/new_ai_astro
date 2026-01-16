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
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->text('bio')->nullable();
            $table->json('skills')->nullable();
            $table->json('languages')->nullable();
            $table->unsignedSmallInteger('experience_years')->nullable();
            $table->string('verification_status', 20)->default('draft');
            $table->boolean('visibility')->default(false);
            $table->boolean('is_call_enabled')->default(false);
            $table->boolean('is_sms_enabled')->default(false);
            $table->boolean('is_chat_enabled')->default(false);
            $table->decimal('call_per_minute', 8, 2)->default(0);
            $table->decimal('chat_per_session', 8, 2)->default(0);
            $table->json('profile_fields')->nullable();
            $table->text('admin_notes')->nullable();
            $table->timestamps();

            $table->unique('user_id');
            $table->index(['verification_status', 'visibility']);
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
