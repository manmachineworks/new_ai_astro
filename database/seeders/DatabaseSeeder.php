<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call([
            RolePermissionSeeder::class,
            UserSeeder::class,
        ]);

        $adminEmail = 'admin@example.com';
        $adminPhone = '9999999999';
        $adminPassword = 'password'; // Default password

        // Create Super Admin
        $superAdmin = User::firstOrCreate(
            ['email' => $adminEmail],
            [
                'name' => 'Super Admin',
                'phone' => $adminPhone,
                'password' => $adminPassword, // Mutator handles hashing
                'wallet_balance' => 0,
                'is_active' => true,
            ]
        );

        // Assign Role
        $superAdmin->assignRole('Super Admin');

        // Create a Test User
        $user = User::firstOrCreate(
            ['email' => 'user@example.com'],
            [
                'name' => 'Test User',
                'phone' => '8888888888',
                'password' => 'password',
                'wallet_balance' => 100.00,
                'is_active' => true,
            ]
        );
        $user->assignRole('User');

        // Create a Test Astrologer
        $astrologer = User::firstOrCreate(
            ['email' => 'astro@example.com'],
            [
                'name' => 'Test Astrologer',
                'phone' => '7777777777',
                'password' => 'password',
                'wallet_balance' => 0.00,
                'is_active' => true,
            ]
        );
        $astrologer->assignRole('Astrologer');

        // Create Astrologer Profile
        \App\Models\AstrologerProfile::updateOrCreate(
            ['user_id' => $astrologer->id],
            [
                'bio' => 'Expert Vedic Astrologer',
                'experience_years' => 10,
                'call_per_minute' => 10.00,
                'chat_per_session' => 50.00,
                'visibility' => true,
                'is_call_enabled' => true,
                'verification_status' => 'approved',
                'skills' => ['Vedic', 'Vastu'],
                'languages' => ['English', 'Hindi']
            ]
        );

        // Seed 10 more Astrologers for UI Demo
        $skills = ['Vedic', 'Nadi', 'Tarot', 'Numerology', 'Vastu', 'Psychic'];
        $languages = ['English', 'Hindi', 'Tamil', 'Marathi', 'Bengali'];

        for ($i = 1; $i <= 10; $i++) {
            $fakeAstro = User::firstOrCreate([
                'email' => "astro{$i}@example.com"
            ], [
                'name' => "Astrologer {$i}",
                'phone' => "777777770{$i}",
                'password' => 'password',
                'wallet_balance' => 0,
                'is_active' => true
            ]);
            $fakeAstro->assignRole('Astrologer');

            \App\Models\AstrologerProfile::updateOrCreate(
                ['user_id' => $fakeAstro->id],
                [
                    'bio' => "Experienced in " . $skills[array_rand($skills)],
                    'experience_years' => rand(5, 25),
                    'call_per_minute' => rand(10, 50),
                    'chat_per_session' => rand(20, 100),
                    'visibility' => true,
                    'verification_status' => 'approved',
                    'skills' => [$skills[array_rand($skills)], $skills[array_rand($skills)]],
                    'languages' => [$languages[array_rand($languages)], 'English']
                ]
            );
        }

        $this->call(AstrologerModuleSeeder::class);

    }
}
