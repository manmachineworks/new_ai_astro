<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('earnings', function (Blueprint $table) {
            $table->id();
            $table->foreignId('astrologer_id')->constrained('astrologers')->cascadeOnDelete();
            $table->enum('source', ['call', 'chat', 'appointment']);
            $table->unsignedBigInteger('source_id')->nullable();
            $table->decimal('gross_amount', 12, 2);
            $table->decimal('commission_amount', 12, 2)->default(0);
            $table->decimal('net_amount', 12, 2);
            $table->enum('status', ['pending', 'settled'])->default('pending');
            $table->timestamps();
            $table->index(['astrologer_id', 'status']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('earnings');
    }
};
