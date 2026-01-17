<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('meeting_links', function (Blueprint $table) {
            $table->id();
            $table->uuid('appointment_id');
            $table->string('provider', 20)->default('jitsi');
            $table->text('join_url');
            $table->timestamps();

            $table->foreign('appointment_id')->references('id')->on('appointments')->cascadeOnDelete();
            $table->unique('appointment_id', 'meeting_links_appointment_unique');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('meeting_links');
    }
};
