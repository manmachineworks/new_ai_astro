<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('admin_announcements', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->text('body'); // Plaintext (No PII)
            $table->string('target_role', 20)->default('all'); // user, astrologer, all
            $table->json('segment_rules')->nullable(); // e.g. {"verified": true}

            $table->string('status', 20)->default('draft'); // draft, scheduled, sent, failed
            $table->timestamp('scheduled_at')->nullable();
            $table->timestamp('sent_at')->nullable();

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('admin_announcements');
    }
};
