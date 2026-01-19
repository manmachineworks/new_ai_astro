<?php

namespace Database\Seeders;

use App\Models\Appointment;
use App\Models\Astrologer;
use App\Models\AstrologerPricing;
use App\Models\AstrologerService;
use App\Models\AstrologerSchedule;
use App\Models\AstrologerTimeOff;
use App\Models\CallLog;
use App\Models\ChatSession;
use App\Models\Earning;
use App\Models\User;
use App\Models\Wallet;
use App\Models\WalletTransaction;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class AstrologerModuleSeeder extends Seeder
{
    public function run(): void
    {
        $astrologerUser = User::firstOrCreate(
            ['email' => 'astrologer@platform.test'],
            [
                'name' => 'Demo Astrologer',
                'phone' => '7777000000',
                'password' => bcrypt('password'),
                'role' => 'astrologer',
                'wallet_balance' => 1500,
            ]
        );
        $astrologerUser->assignRole('Astrologer');

        $astrologer = Astrologer::updateOrCreate(
            ['user_id' => $astrologerUser->id],
            [
                'public_id' => 'ASTRO-' . Str::padLeft((string) $astrologerUser->id, 5, '0'),
                'display_name' => 'Astro Demo',
                'bio' => 'Vedic astrologer with deep experience in Jyotish.',
                'languages' => ['English', 'Hindi'],
                'specializations' => ['Love', 'Finance', 'Career'],
                'experience_years' => 12,
                'profile_photo_url' => null,
                'is_verified' => true,
                'verification_status' => 'approved',
                'is_listed' => true,
            ]
        );

        AstrologerService::updateOrCreate(
            ['astrologer_id' => $astrologer->id],
            [
                'call_enabled' => true,
                'chat_enabled' => true,
                'sms_enabled' => false,
                'online_status' => 'offline',
            ]
        );

        AstrologerPricing::updateOrCreate(
            ['astrologer_id' => $astrologer->id],
            [
                'call_per_minute' => 20,
                'chat_price' => 120,
                'ai_chat_price' => 60,
            ]
        );

        foreach (range(1, 5) as $dayOfWeek) {
            AstrologerSchedule::firstOrCreate(
                [
                    'astrologer_id' => $astrologer->id,
                    'day_of_week' => $dayOfWeek,
                    'start_time' => '09:00:00',
                    'end_time' => '17:00:00',
                ],
                ['is_active' => true]
            );
        }

        AstrologerTimeOff::firstOrCreate([
            'astrologer_id' => $astrologer->id,
            'start_datetime' => now()->addDays(3)->startOfDay(),
            'end_datetime' => now()->addDays(3)->endOfDay(),
            'reason' => 'Personal leave',
        ]);

        Wallet::firstOrCreate(
            ['user_id' => $astrologerUser->id],
            ['balance' => 1500]
        );

        $client = User::firstOrCreate(
            ['email' => 'user@platform.test'],
            [
                'name' => 'Demo User',
                'phone' => '8888000000',
                'password' => bcrypt('password'),
                'role' => 'user',
                'wallet_balance' => 1000,
            ]
        );

        Wallet::firstOrCreate(['user_id' => $client->id], ['balance' => 1000]);

        WalletTransaction::firstOrCreate(
            [
                'user_id' => $client->id,
                'reference_id' => 'seed-recharge-1',
            ],
            [
                'amount' => 1000,
                'type' => 'credit',
                'balance_after' => 1000,
                'currency' => 'INR',
                'source' => 'seed',
                'description' => 'Seed credit',
                'meta' => ['seed' => true],
                'idempotency_key' => Str::uuid(),
            ]
        );

        CallLog::firstOrCreate(
            ['callerdesk_call_id' => 'seed-call-001'],
            [
                'astrologer_id' => $astrologer->id,
                'user_id' => $client->id,
                'user_public_id' => $client->publicId(),
                'status' => 'ended',
                'started_at' => now()->subDay()->subMinutes(15),
                'ended_at' => now()->subDay(),
                'duration_seconds' => 900,
                'rate_per_minute' => 20,
                'amount_charged' => 300,
            ]
        );

        ChatSession::firstOrCreate(
            ['firebase_chat_id' => 'seed-chat-001'],
            [
                'astrologer_id' => $astrologer->id,
                'user_id' => $client->id,
                'user_public_id' => $client->publicId(),
                'status' => 'closed',
                'started_at' => now()->subDays(2),
                'ended_at' => now()->subDays(1),
                'chat_price' => 120,
                'amount_charged' => 120,
            ]
        );

        Appointment::updateOrCreate(
            [
                'user_id' => $client->id,
                'astrologer_user_id' => $astrologerUser->id,
                'start_at' => now()->addDays(2),
            ],
            [
                'end_at' => now()->addDays(2)->addHour(),
                'status' => 'pending',
                'payment_status' => 'unpaid',
                'price' => 900,
                'notes' => 'Booking for career guidance',
            ]
        );

        Earning::firstOrCreate(
            [
                'astrologer_id' => $astrologer->id,
                'source' => 'call',
                'source_id' => 1,
            ],
            [
                'gross_amount' => 300,
                'commission_amount' => 60,
                'net_amount' => 240,
                'status' => 'settled',
            ]
        );
    }
}
