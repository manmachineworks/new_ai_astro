<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('chat_reports', function (Blueprint $table) {
            $table->id();
            $table->foreignId('chat_session_id')->constrained()->cascadeOnDelete();
            $table->foreignId('reported_by_user_id')->constrained('users')->cascadeOnDelete();
            $table->string('reason', 100);
            $table->text('details')->nullable();
            $table->timestamps();

            $table->index(['chat_session_id', 'reported_by_user_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('chat_reports');
    }
};
