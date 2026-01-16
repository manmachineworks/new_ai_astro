<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::dropIfExists('availability_rules');

        Schema::create('availability_rules', function (Blueprint $table) {
            $table->id();
            $table->foreignId('astrologer_profile_id')->constrained()->cascadeOnDelete();
            $table->tinyInteger('day_of_week'); // 0=Sunday, 6=Saturday
            $table->time('start_time_utc');
            $table->time('end_time_utc');
            $table->boolean('is_active')->default(true);
            $table->timestamps();

            $table->index(['astrologer_profile_id', 'day_of_week']);
        });

        // Exceptions table in same migration for simplicity if allowed, or separate
        Schema::create('availability_exceptions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('astrologer_profile_id')->constrained()->cascadeOnDelete();
            $table->date('date');
            $table->string('type', 20); // blocked, extra
            $table->time('start_time_utc')->nullable();
            $table->time('end_time_utc')->nullable();
            $table->string('note')->nullable();
            $table->timestamps();

            $table->index(['astrologer_profile_id', 'date']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('availability_exceptions');
        Schema::dropIfExists('availability_rules');
    }
};
