<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        if (Schema::hasTable('appointment_events')) {
            return;
        }

        Schema::create('appointment_events', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('appointment_id');
            $table->string('actor_type', 20);
            $table->string('actor_id', 64)->nullable();
            $table->string('event_type', 30);
            $table->json('meta_json')->nullable();
            $table->timestamps();

            $table->foreign('appointment_id')->references('id')->on('appointments')->cascadeOnDelete();
            $table->index(['appointment_id', 'created_at'], 'appointment_events_appointment_created_idx');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('appointment_events');
    }
};
