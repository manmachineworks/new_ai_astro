<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('astrologer_pricing_histories', function (Blueprint $table) {
            $table->id();
            $table->foreignId('astrologer_profile_id')->constrained()->cascadeOnDelete();
            $table->decimal('old_call_per_minute', 8, 2)->nullable();
            $table->decimal('new_call_per_minute', 8, 2)->nullable();
            $table->decimal('old_chat_per_session', 8, 2)->nullable();
            $table->decimal('new_chat_per_session', 8, 2)->nullable();
            $table->foreignId('changed_by_user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->string('change_source', 20)->default('astrologer'); // astrologer, admin
            $table->text('notes')->nullable();
            $table->timestamps();

            $table->index(['astrologer_profile_id', 'created_at'], 'astro_price_hist_profile_date_idx');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('astrologer_pricing_histories');
    }
};
