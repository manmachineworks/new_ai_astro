<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class RolePermissionSeeder extends Seeder
{
    public function run(): void
    {
        $permissions = [
            'view_users',
            'create_users',
            'edit_users',
            'delete_users',
            'block_users',
            'view_astrologers',
            'verify_astrologers',
            'edit_astrologers',
            'enable_disable_astrologers',
            'view_kyc_documents',
            'view_call_logs',
            'view_chat_logs',
            'export_call_reports',
            'view_wallets',
            'credit_wallet',
            'debit_wallet',
            'view_phonepe_transactions',
            'refund_payments',
            'manage_ai_chat_price',
            'manage_platform_settings',
            'manage_roles_permissions',
            'view_reports',
            'export_reports',
        ];

        foreach ($permissions as $permission) {
            Permission::firstOrCreate(
                ['name' => $permission, 'guard_name' => 'web'],
                ['name' => $permission, 'guard_name' => 'web']
            );
        }

        $roles = [
            'Super Admin' => $permissions,
            'Admin' => [
                'view_users',
                'create_users',
                'edit_users',
                'block_users',
                'view_astrologers',
                'verify_astrologers',
                'edit_astrologers',
                'enable_disable_astrologers',
                'view_call_logs',
                'view_chat_logs',
                'view_wallets',
                'view_phonepe_transactions',
                'view_reports',
                'export_reports',
            ],
            'Astrologer' => [],
            'User' => [],
            'Support Staff' => [
                'view_users',
                'view_call_logs',
                'view_chat_logs',
                'view_wallets',
                'view_reports',
            ],
        ];

        foreach ($roles as $roleName => $rolePermissions) {
            $role = Role::firstOrCreate(
                ['name' => $roleName, 'guard_name' => 'web'],
                ['name' => $roleName, 'guard_name' => 'web']
            );

            if (!empty($rolePermissions)) {
                $role->syncPermissions($rolePermissions);
            }
        }
    }
}
