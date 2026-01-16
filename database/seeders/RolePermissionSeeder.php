<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class RolePermissionSeeder extends Seeder
{
    public function run(): void
    {
        $adminPermissions = [
            'manage_users',
            'manage_astrologers',
            'verify_astrologers',
            'manage_reports',
            'manage_pricing',
            'manage_visibility',
            'manage_roles_permissions',
            'manage_platform_settings',
            'view_wallets',
            'view_phonepe_transactions',
            'refund_payments',
            'credit_wallet',
            'debit_wallet',
            'view_call_logs',
            'view_chat_logs',
        ];

        $astrologerPermissions = [
            'manage_profile',
            'manage_pricing',
            'manage_availability',
            'manage_toggles',
            'view_call_logs',
            'view_chat_logs',
        ];

        $userPermissions = [
            'browse_astrologers',
            'initiate_chat',
            'initiate_call',
            'manage_appointments',
            'recharge_wallet',
            'view_history',
        ];

        $legacyPermissions = [
            'view_users',
            'create_users',
            'edit_users',
            'delete_users',
            'block_users',
            'view_astrologers',
            'edit_astrologers',
            'enable_disable_astrologers',
            'view_kyc_documents',
            'export_call_reports',
            'view_reports',
            'export_reports',
            'manage_ai_chat_price',
        ];

        $permissions = array_values(array_unique(array_merge(
            $adminPermissions,
            $astrologerPermissions,
            $userPermissions,
            $legacyPermissions
        )));

        foreach ($permissions as $permission) {
            Permission::firstOrCreate(
                ['name' => $permission, 'guard_name' => 'web'],
                ['name' => $permission, 'guard_name' => 'web']
            );
        }

        $roles = [
            'Super Admin' => $permissions,
            'Admin' => array_values(array_unique(array_merge(
                $adminPermissions,
                [
                    'view_users',
                    'create_users',
                    'edit_users',
                    'block_users',
                    'view_astrologers',
                    'edit_astrologers',
                    'enable_disable_astrologers',
                    'view_reports',
                    'export_reports',
                ]
            ))),
            'Astrologer' => $astrologerPermissions,
            'User' => $userPermissions,
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
