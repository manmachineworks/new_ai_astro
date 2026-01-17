<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('referrals', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignId('inviter_user_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('invitee_user_id')->unique()->constrained('users')->cascadeOnDelete();
            $table->enum('status', ['pending', 'qualified', 'rewarded', 'rejected'])->default('pending');
            $table->dateTime('qualified_at')->nullable();
            $table->dateTime('rewarded_at')->nullable();
            $table->decimal('inviter_bonus_amount', 10, 2)->nullable();
            $table->decimal('invitee_bonus_amount', 10, 2)->nullable();
            $table->json('meta_json')->nullable();
            $table->timestamps();

            $table->index(['inviter_user_id', 'status']);
            $table->index('status');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('referrals');
    }
};
