<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('dispute_events', function (Blueprint $table) {
            $table->id();
            $table->foreignUuid('dispute_id')->constrained()->cascadeOnDelete();
            $table->enum('actor_type', ['user', 'admin', 'system'])->default('system');
            $table->unsignedBigInteger('actor_id')->nullable();
            $table->enum('event_type', [
                'created',
                'commented',
                'info_requested',
                'evidence_added',
                'approved_full',
                'approved_partial',
                'rejected',
                'closed'
            ]);
            $table->json('meta_json')->nullable();
            $table->timestamp('created_at');

            $table->index(['dispute_id', 'created_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('dispute_events');
    }
};
