<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::dropIfExists('appointments');

        Schema::create('appointments', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('astrologer_profile_id')->constrained()->cascadeOnDelete();
            $table->timestamp('start_at_utc');
            $table->timestamp('end_at_utc');
            $table->unsignedInteger('duration_minutes');
            $table->string('status', 30)->default('requested');
            $table->string('pricing_mode', 20);
            $table->decimal('price_total', 12, 2)->default(0);
            $table->decimal('rate_snapshot', 12, 2)->nullable();
            $table->uuid('wallet_hold_id')->nullable();
            $table->foreignId('wallet_transaction_id')->nullable()->constrained('wallet_transactions')->nullOnDelete();
            $table->text('notes_user')->nullable();
            $table->text('notes_astrologer')->nullable();
            $table->timestamps();

            $table->foreign('wallet_hold_id')->references('id')->on('wallet_holds')->nullOnDelete();
            $table->index(['astrologer_profile_id', 'start_at_utc'], 'appointments_astrologer_start_idx');
            $table->index(['user_id', 'created_at'], 'appointments_user_created_idx');
            $table->index(['status', 'start_at_utc'], 'appointments_status_start_idx');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('appointments');
    }
};
