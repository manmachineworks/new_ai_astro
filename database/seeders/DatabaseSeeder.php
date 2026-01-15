<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call(RolePermissionSeeder::class);

        $adminEmail = env('ADMIN_EMAIL', 'admin@example.com');
        $adminPhone = env('ADMIN_PHONE', '9999999999');
        $adminPassword = env('ADMIN_PASSWORD', 'ChangeMe123!');

        $superAdmin = User::firstOrCreate(
            ['phone' => $adminPhone],
            [
                'name' => 'Super Admin',
                'email' => $adminEmail,
                'password' => $adminPassword,
                'firebase_uid' => null,
            ]
        );

        $updates = [];
        if (empty($superAdmin->email)) {
            $updates['email'] = $adminEmail;
        }
        if (empty($superAdmin->password)) {
            $updates['password'] = $adminPassword;
        }
        if (!empty($updates)) {
            $superAdmin->fill($updates)->save();
        }

        $role = Role::where('name', 'Super Admin')->first();
        if ($role) {
            $superAdmin->syncRoles([$role->name]);
        }
    }
}
