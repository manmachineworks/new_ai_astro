<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('astrologer_profiles', function (Blueprint $table) {
            $table->boolean('is_appointment_enabled')->default(false)->after('is_chat_enabled');
        });
    }

    public function down(): void
    {
        Schema::table('astrologer_profiles', function (Blueprint $table) {
            $table->dropColumn('is_appointment_enabled');
        });
    }
};
