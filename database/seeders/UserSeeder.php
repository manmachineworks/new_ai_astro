<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\AstrologerProfile;
use App\Models\Wallet;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Admin
        User::create([
            'name' => 'Admin User',
            'email' => 'admin@example.com',
            'password' => Hash::make('password'),
            'role' => 'admin',
        ]);

        // Astrologer
        $astrologer = User::create([
            'name' => 'Astrologer User',
            'email' => 'astrologer@example.com',
            'password' => Hash::make('password'),
            'role' => 'astrologer',
        ]);

        AstrologerProfile::create([
            'user_id' => $astrologer->id,
            'verification_status' => 'verified',
            'is_visible' => true,
            'call_enabled' => true,
            'chat_enabled' => true,
            'per_minute_rate' => 10.00,
            'per_chat_rate' => 5.00,
            'languages' => ['English', 'Hindi'],
            'expertise' => ['Vedic', 'Numerology'],
            'experience_years' => 5,
            'bio' => 'Experienced astrologer.',
        ]);

        // User
        $user = User::create([
            'name' => 'Regular User',
            'email' => 'user@example.com',
            'password' => Hash::make('password'),
            'role' => 'user',
        ]);

        Wallet::create([
            'user_id' => $user->id,
            'balance' => 100.00,
        ]);
    }
}
