<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('webhook_events', function (Blueprint $table) {
            $table->id();
            $table->enum('provider', ['phonepe', 'callerdesk']);
            $table->string('event_id')->unique();
            $table->json('payload');
            $table->timestamp('processed_at')->nullable();
            $table->timestamps();
            $table->index('provider');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('webhook_events');
    }
};
