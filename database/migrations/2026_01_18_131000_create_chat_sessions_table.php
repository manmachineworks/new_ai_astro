<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('chat_sessions')) {
            Schema::create('chat_sessions', function (Blueprint $table) {
                $table->id();
                $table->foreignId('astrologer_id')->constrained('astrologers')->cascadeOnDelete();
                $table->foreignId('user_id')->constrained()->cascadeOnDelete();
                $table->string('user_public_id');
                $table->string('firebase_chat_id')->unique();
                $table->enum('status', ['active', 'closed', 'blocked', 'expired'])->default('active');
                $table->dateTime('started_at');
                $table->dateTime('ended_at')->nullable();
                $table->decimal('chat_price', 10, 2)->default(0);
                $table->decimal('amount_charged', 12, 2)->default(0);
                $table->decimal('commission_percent_snapshot', 5, 2)->nullable();
                $table->decimal('commission_amount_total', 15, 2)->default(0);
                $table->json('meta')->nullable();
                $table->timestamps();
                $table->index(['astrologer_id', 'user_id']);
            });
            return;
        }

        Schema::table('chat_sessions', function (Blueprint $table) {
            if (!Schema::hasColumn('chat_sessions', 'astrologer_id')) {
                $table->foreignId('astrologer_id')->nullable()->constrained('astrologers')->nullOnDelete();
            }
            if (!Schema::hasColumn('chat_sessions', 'user_public_id')) {
                $table->string('user_public_id')->nullable();
            }
            if (!Schema::hasColumn('chat_sessions', 'chat_price')) {
                $table->decimal('chat_price', 10, 2)->default(0);
            }
            if (!Schema::hasColumn('chat_sessions', 'amount_charged')) {
                $table->decimal('amount_charged', 12, 2)->default(0);
            }
            if (!Schema::hasColumn('chat_sessions', 'commission_percent_snapshot')) {
                $table->decimal('commission_percent_snapshot', 5, 2)->nullable();
            }
            if (!Schema::hasColumn('chat_sessions', 'commission_amount_total')) {
                $table->decimal('commission_amount_total', 15, 2)->default(0);
            }
            if (!Schema::hasColumn('chat_sessions', 'meta')) {
                $table->json('meta')->nullable();
            }
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('chat_sessions');
    }
};
