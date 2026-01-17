<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use App\Models\User;

class RolePermissionSeeder extends Seeder
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
        $roleAdmin = Role::firstOrCreate(['name' => 'Super Admin']);
        $roleAdmin->givePermissionTo(Permission::all());

        // Also create Admin if needed, or just alias
        if (!Role::where('name', 'Admin')->exists()) {
            Role::create(['name' => 'Admin'])->givePermissionTo(Permission::all());
        }

        $roleAstrologer = Role::firstOrCreate(['name' => 'Astrologer']);

        $roleUser = Role::firstOrCreate(['name' => 'User']);
    }
}
