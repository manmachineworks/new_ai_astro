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
        Schema::create('calls', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained();
            $table->foreignId('astrologer_id')->constrained('users');
            $table->string('call_sid')->nullable();
            $table->string('status')->default('initiated'); // initiated, active, completed, failed
            $table->integer('duration')->default(0); // seconds
            $table->decimal('rate', 8, 2);
            $table->decimal('total_cost', 10, 2)->default(0);
            $table->string('recording_url')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('calls');
    }
};
