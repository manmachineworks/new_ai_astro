<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('disputes', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->string('reference_type'); // CallSession, ChatSession, etc.
            $table->string('reference_id'); // UUID or bigint
            $table->enum('reason_code', [
                'poor_quality',
                'technical_issue',
                'no_service',
                'overcharged',
                'other'
            ]);
            $table->text('description')->nullable();
            $table->enum('status', [
                'submitted',
                'under_review',
                'needs_info',
                'approved_full',
                'approved_partial',
                'rejected',
                'closed'
            ])->default('submitted');
            $table->decimal('requested_refund_amount', 10, 2)->nullable();
            $table->decimal('approved_refund_amount', 10, 2)->nullable();
            $table->text('admin_notes')->nullable();
            $table->timestamps();

            $table->index(['user_id', 'status']);
            $table->index(['status', 'created_at']);
        });

        // Create unique constraint and index with prefix to fit MySQL 1000-byte limit
        \DB::statement('CREATE UNIQUE INDEX unique_dispute_per_transaction ON disputes (user_id, reference_type(100), reference_id(100))');
        \DB::statement('CREATE INDEX idx_dispute_ref ON disputes (reference_type(100), reference_id(100))');
    }

    public function down(): void
    {
        Schema::dropIfExists('disputes');
    }
};
