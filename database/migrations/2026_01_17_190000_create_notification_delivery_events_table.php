<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('notification_delivery_events', function (Blueprint $table) {
            $table->uuid('id')->primary(); // Event ID

            // Link to Notification Table (nullable because push-only might not have inbox entry?)
            // Requirement says unified, so usually there is an inbox entry.
            // But let's allow nullable if we ever do push-only.
            $table->uuid('notification_id')->nullable();

            $table->foreignId('recipient_user_id')->nullable(); // Helper index
            $table->string('channel', 20); // push, inbox, email
            $table->string('event', 30); // queued, sent, failed, opened

            $table->string('provider_message_id')->nullable(); // FCM message ID
            $table->string('error_code')->nullable();
            $table->text('error_message')->nullable();

            $table->timestamps();

            $table->index(['recipient_user_id', 'event']);
            $table->index('notification_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('notification_delivery_events');
    }
};
