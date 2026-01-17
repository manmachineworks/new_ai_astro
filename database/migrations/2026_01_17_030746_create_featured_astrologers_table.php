<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('featured_astrologers', function (Blueprint $table) {
            $table->id();
            $table->foreignId('astrologer_profile_id')->constrained('astrologer_profiles')->onDelete('cascade');
            $table->string('locale', 5)->default('en');
            $table->unsignedInteger('sort_order')->default(0);
            $table->boolean('is_active')->default(true);
            $table->timestamps();

            $table->unique(['astrologer_profile_id', 'locale']);
            $table->index(['locale', 'is_active', 'sort_order']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('featured_astrologers');
    }
};
