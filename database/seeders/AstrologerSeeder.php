<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\AstrologerProfile;
use App\Models\AvailabilityRule;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;
use Carbon\Carbon;

class AstrologerSeeder extends Seeder
{
    public function run(): void
    {
        $names = [
            'Aarav Sharma',
            'Priya Verma',
            'Rohan Gupta',
            'Ananya Singh',
            'Vikram Malhotra',
            'Neha Kapoor',
            'Suresh Iyer',
            'Meera Reddy',
            'Rajesh Kumar',
            'Simran Kaur'
        ];

        foreach ($names as $index => $name) {
            $email = strtolower(str_replace(' ', '.', $name)) . '@astrologer.com';

            // Create User
            $user = User::firstOrCreate(
                ['email' => $email],
                [
                    'name' => $name,
                    'password' => bcrypt('password'),
                    'phone' => '90000000' . $index,
                ]
            );
            $user->assignRole('Astrologer');

            // Determine Status
            $isVerified = $index < 6; // First 6 verified
            $showOnFront = $index < 4; // Top 4 shown
            $status = $isVerified ? 'approved' : ($index == 8 ? 'rejected' : 'pending');

            // Create Profile
            $profile = AstrologerProfile::updateOrCreate(
                ['user_id' => $user->id],
                [
                    'display_name' => $name,
                    'bio' => "Professional Vedic Astrologer with over " . ($index + 2) . " years of experience.",
                    'gender' => $index % 2 == 0 ? 'male' : 'female',
                    'dob' => '1990-01-01',
                    'experience_years' => 5 + $index,
                    'skills' => ['Vedic', 'Numerology', 'Tarot'],
                    'languages' => ['English', 'Hindi'],
                    'specialties' => ['Love', 'Career', 'Finance'],
                    'call_per_minute' => 10 + ($index * 5),
                    'chat_per_session' => 20 + ($index * 2), // Rate for chat per min
                    'verification_status' => $status,
                    'is_verified' => $isVerified,
                    'show_on_front' => $showOnFront && $isVerified,
                    'is_enabled' => true,
                    'is_call_enabled' => $isVerified,
                    'is_chat_enabled' => $isVerified,
                    'rating_avg' => $isVerified ? (4 + ($index * 0.1)) : 0,
                    'rating_count' => $isVerified ? (10 + $index * 5) : 0,
                ]
            );

            // Add Availability (M-F, 9-5 UTC)
            if ($isVerified) {
                for ($day = 1; $day <= 5; $day++) {
                    AvailabilityRule::create([
                        'astrologer_profile_id' => $profile->id,
                        'day_of_week' => $day,
                        'start_time_utc' => '09:00:00',
                        'end_time_utc' => '17:00:00',
                        'is_active' => true,
                    ]);
                }
            }
        }
    }
}
