<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        if (Schema::hasTable('notification_jobs')) {
            return;
        }

        Schema::create('notification_jobs', function (Blueprint $table) {
            $table->id();
            $table->string('type', 50);
            $table->string('reference_type', 100)->nullable();
            $table->string('reference_id', 64);
            $table->foreignId('recipient_user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('scheduled_at');
            $table->string('status', 20)->default('pending');
            $table->unsignedInteger('attempts')->default(0);
            $table->text('last_error')->nullable();
            $table->timestamp('sent_at')->nullable();
            $table->timestamps();

            $table->index(['status', 'scheduled_at'], 'notification_jobs_status_scheduled_idx');
            $table->index(['reference_id', 'type'], 'notification_jobs_reference_type_idx');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('notification_jobs');
    }
};
