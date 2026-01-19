<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('astrologers', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->string('public_id')->unique();
            $table->string('display_name');
            $table->text('bio')->nullable();
            $table->json('languages')->nullable();
            $table->json('specializations')->nullable();
            $table->unsignedTinyInteger('experience_years')->default(0);
            $table->string('profile_photo_url')->nullable();
            $table->boolean('is_verified')->default(false);
            $table->enum('verification_status', ['pending', 'approved', 'rejected'])->default('pending');
            $table->text('verification_remark')->nullable();
            $table->boolean('is_listed')->default(true);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('astrologers');
    }
};
