<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('webhook_events', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('provider', 50); // phonepe
            $table->string('event_type', 100)->nullable();
            $table->string('external_id', 100)->nullable()->index(); // e.g. merchantTransactionId
            $table->boolean('signature_valid')->default(false);
            $table->json('payload')->nullable();
            $table->json('headers')->nullable();
            $table->timestamp('processed_at')->nullable();
            $table->string('processing_status', 20)->default('pending'); // pending, processed, failed
            $table->text('error_message')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('webhook_events');
    }
};
