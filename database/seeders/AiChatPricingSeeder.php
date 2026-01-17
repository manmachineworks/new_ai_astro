<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class AiChatPricingSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $settings = [
            'ai_chat_enabled' => 1,
            'ai_chat_pricing_mode' => 'per_message',
            'ai_chat_price_per_message' => 10.00,
            'ai_chat_price_per_session' => 150.00,
            'ai_chat_min_wallet_to_start' => 50.00,
            'ai_chat_max_messages_per_day' => 50,
            'ai_chat_disclaimer_text' => 'This AI astrologer provides guidance based on astrology. It is not a substitute for professional legal, medical, or financial advice.',
        ];

        foreach ($settings as $key => $value) {
            \App\Models\PricingSetting::updateOrCreate(
                ['key' => $key],
                ['value_json' => $val = $value]
            );
        }
    }
}
