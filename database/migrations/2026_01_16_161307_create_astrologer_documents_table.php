<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('astrologer_documents', function (Blueprint $table) {
            $table->id();
            $table->foreignId('astrologer_profile_id')->constrained()->cascadeOnDelete();
            $table->string('doc_type', 50); // id_proof, address_proof, certificate
            $table->string('file_path');
            $table->string('status', 20)->default('uploaded'); // uploaded, approved, rejected
            $table->foreignId('reviewed_by_admin_id')->nullable()->constrained('users');
            $table->timestamp('reviewed_at')->nullable();
            $table->text('rejection_reason')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('astrologer_documents');
    }
};
