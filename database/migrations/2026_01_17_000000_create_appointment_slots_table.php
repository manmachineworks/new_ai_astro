<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        if (Schema::hasTable('appointment_slots')) {
            return;
        }

        Schema::create('appointment_slots', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignId('astrologer_profile_id')->constrained()->cascadeOnDelete();
            $table->timestamp('start_at_utc');
            $table->timestamp('end_at_utc');
            $table->unsignedInteger('duration_minutes')->nullable();
            $table->string('status', 20)->default('available'); // available, held, booked, blocked
            $table->foreignId('held_by_user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('hold_expires_at_utc')->nullable();
            $table->timestamps();

            $table->unique(['astrologer_profile_id', 'start_at_utc'], 'appointment_slots_astrologer_start_unique');
            $table->index(['status', 'start_at_utc'], 'appointment_slots_status_start_idx');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('appointment_slots');
    }
};
