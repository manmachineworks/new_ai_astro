<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('pricing_settings', function (Blueprint $table) {
            $table->id();
            $table->string('key')->unique();
            $table->json('value_json'); // Stores mixed types as JSON
            $table->foreignId('updated_by_admin_id')->nullable()->constrained('users');
            $table->timestamps();
        });

        // Seed Defaults
        DB::table('pricing_settings')->insert([
            ['key' => 'min_wallet_to_start_call', 'value_json' => json_encode(50.00), 'created_at' => now(), 'updated_at' => now()],
            ['key' => 'min_wallet_to_start_chat', 'value_json' => json_encode(30.00), 'created_at' => now(), 'updated_at' => now()],
            ['key' => 'ai_chat_pricing_mode', 'value_json' => json_encode('per_message'), 'created_at' => now(), 'updated_at' => now()],
            ['key' => 'ai_chat_price_per_message', 'value_json' => json_encode(5.00), 'created_at' => now(), 'updated_at' => now()],
            ['key' => 'call_hold_duration_minutes', 'value_json' => json_encode(5), 'created_at' => now(), 'updated_at' => now()], // Hold for 5 mins
        ]);
    }

    public function down(): void
    {
        Schema::dropIfExists('pricing_settings');
    }
};
