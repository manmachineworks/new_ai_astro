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
        Schema::dropIfExists('astrologer_earnings_ledger');
        Schema::create('astrologer_earnings_ledger', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignId('astrologer_profile_id')->constrained();
            $table->enum('source', ['call', 'chat', 'ai_chat', 'adjustment']);
            $table->string('reference_type')->nullable(); // CallSession
            $table->uuid('reference_id')->nullable();
            $table->decimal('amount', 10, 2);
            $table->enum('status', ['pending', 'available', 'paid', 'reversed'])->default('available');
            $table->timestamps();

            $table->index(['astrologer_profile_id', 'created_at'], 'ast_earn_prof_id_ca_idx');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('astrologer_earnings_ledger');
    }
};
