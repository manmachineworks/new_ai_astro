<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('earnings_adjustments', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignId('astrologer_profile_id')->constrained()->cascadeOnDelete();
            $table->string('reference_type');
            $table->string('reference_id');
            $table->foreignUuid('refund_id')->nullable()->constrained()->nullOnDelete();
            $table->decimal('amount', 10, 2); // Negative for reversals
            $table->string('reason');
            $table->enum('status', ['applied', 'reversed'])->default('applied');
            $table->timestamps();

            // One adjustment per refund per astrologer
            $table->unique(['refund_id', 'astrologer_profile_id'], 'unique_adjustment_per_refund');

            $table->index(['astrologer_profile_id', 'created_at']);
            $table->index('refund_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('earnings_adjustments');
    }
};
