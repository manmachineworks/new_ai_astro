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
        Schema::create('export_requests', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignId('admin_id')->constrained('users')->onDelete('cascade');
            $table->string('type'); // e.g., 'calls', 'revenue'
            $table->json('filters_json')->nullable();
            $table->string('status')->default('pending'); // pending, processing, completed, failed
            $table->string('file_path')->nullable();
            $table->text('error_message')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('export_requests');
    }
};
