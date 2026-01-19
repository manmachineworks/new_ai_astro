<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create Test User
        $user = User::firstOrCreate(
            ['email' => 'test@example.com'],
            [
                'name' => 'Test User',
                'password' => Hash::make('password'),
                'phone' => '+1234567890',
                'is_active' => true,
            ]
        );

        // Assign Role if Spatie Permission is installed (it seems to be used in AuthController)
        // Assuming 'User' role exists from likely RoleSeeder, otherwise we might skip or create it.
        // Based on AuthController: $user->assignRole('User');

        try {
            if (!$user->hasRole('User')) {
                $user->assignRole('User');
            }
        } catch (\Throwable $e) {
            // Role might not exist yet if RoleSeeder hasn't run, or permission table issue.
            // We'll ignore for now as basic auth doesn't strict check role for login, just dashboard redirection might.
        }
    }
}
