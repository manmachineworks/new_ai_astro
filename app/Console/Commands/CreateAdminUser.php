<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;

class CreateAdminUser extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'make:admin {email} {password} {name=Admin} {phone?}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create a new admin user';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $email = $this->argument('email');
        $password = $this->argument('password');
        $name = $this->argument('name');
        $phone = $this->argument('phone') ?? '9999999999';

        if (User::where('email', $email)->exists()) {
            $this->error('User with this email already exists!');
            return 1;
        }

        $user = User::create([
            'name' => $name,
            'email' => $email,
            'password' => Hash::make($password),
            'phone' => $phone,
            'is_active' => true,
            'email_verified_at' => now(),
        ]);

        // Ensure Role exists
        if (!Role::where('name', 'Super Admin')->exists()) {
            $this->warn('Super Admin role not found. Creating it...');
            Role::create(['name' => 'Super Admin']);
        }

        $user->assignRole('Super Admin');

        // Also assign 'Admin' just in case checks look for that
        if (!Role::where('name', 'Admin')->exists()) {
            Role::create(['name' => 'Admin']);
        }
        $user->assignRole('Admin');

        $this->info("Admin user created successfully!");
        $this->info("Login: {$email}");
        $this->info("Password: {$password}");

        return 0;
    }
}
