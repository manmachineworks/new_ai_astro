<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class RolesAndPermissionsSeeder extends Seeder
{
    public function run()
    {
        // Reset cached roles and permissions
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        // Create Permissions
        $permissions = [
            'view admin dashboard',
            'manage cms',
            'manage blog',
            'manage users',
            'manage astrologers',
        ];

        foreach ($permissions as $permission) {
            Permission::firstOrCreate(['name' => $permission]);
        }

        // Create Roles and assign existing permissions
        $roleAdmin = Role::firstOrCreate(['name' => 'Admin']);
        $roleAdmin->givePermissionTo(Permission::all());

        Role::firstOrCreate(['name' => 'Moderator']);

        $roleAstrologer = Role::firstOrCreate(['name' => 'Astrologer']);
        // Assign specific permissions to Astrologer if needed
        // $roleAstrologer->givePermissionTo('view some dashboard');

        $roleUser = Role::firstOrCreate(['name' => 'User']);

        // Create a default Admin user if not exists
        $adminEmail = 'admin@example.com';
        $adminUser = \App\Models\User::where('email', $adminEmail)->first();
        if (!$adminUser) {
            $adminUser = \App\Models\User::create([
                'name' => 'Super Admin',
                'email' => $adminEmail,
                'password' => bcrypt('password'), // Change validation in production
                'role' => 'admin', // Legacy column support
            ]);
            $adminUser->assignRole($roleAdmin);
        } else {
            if (!$adminUser->hasRole('Admin')) {
                $adminUser->assignRole($roleAdmin);
            }
        }
    }
}
