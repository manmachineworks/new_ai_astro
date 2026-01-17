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

        // 1. Define Permissions
        $permissions = [
            // User Management
            'view_users',
            'manage_users', // Create/Edit/Block
            'export_users',

            // Astrologer Management
            'view_astrologers',
            'manage_astrologers', // Edit Profile
            'verify_astrologers', // Approve/Reject Docs
            'toggle_astrologer_visibility', // Show on Front

            // Communications
            'view_calls', // Read-only history
            'manage_calls', // Dispute checks
            'view_chats',
            'manage_chats',

            // AI Features
            'view_ai_chats',
            'manage_ai_settings',

            // Finance & Payments
            'view_finance', // Transaction History
            'manage_payments', // Retry/Refund
            'wallet_credit', // Add Money
            'wallet_debit',  // Remove Money
            'wallet_adjustments',
            'issue_refunds',
            'manage_payouts',
            'manage_commissions',
            'export_finance',

            // Content & CMS
            'manage_content', // Banners, Pages, Blogs

            // System & Compliance
            'view_audit_logs',
            'view_webhooks',
            'retry_webhooks',
            'manage_roles', // Super Admin only usually
            'view_acp', // Basic Admin Panel Access
        ];

        foreach ($permissions as $permission) {
            Permission::firstOrCreate(['name' => $permission]);
        }

        // 2. Define Roles & Assign Permissions

        // A. Super Admin (God Mode)
        $superAdmin = Role::firstOrCreate(['name' => 'Super Admin']);
        $superAdmin->givePermissionTo(Permission::all());

        // B. Finance Admin
        $financeAdmin = Role::firstOrCreate(['name' => 'Finance Admin']);
        $financeAdmin->givePermissionTo([
            'view_acp',
            'view_users',
            'view_astrologers',
            'view_finance',
            'manage_payments',
            'wallet_credit',
            'wallet_debit',
            'wallet_adjustments',
            'issue_refunds',
            'manage_payouts',
            'manage_commissions',
            'export_finance',
            'view_calls', // For verification
            'view_chats', // For verification
            'export_users',
        ]);

        // C. Support Admin
        $supportAdmin = Role::firstOrCreate(['name' => 'Support Admin']);
        $supportAdmin->givePermissionTo([
            'view_acp',
            'view_users',
            'manage_users', // Reset pass, block
            'view_astrologers',
            'view_calls',
            'view_chats',
            'view_finance', // Read-only to answer questions
        ]);

        // D. Ops Admin (Astrologer Manager)
        $opsAdmin = Role::firstOrCreate(['name' => 'Ops Admin']);
        $opsAdmin->givePermissionTo([
            'view_acp',
            'view_users',
            'view_astrologers',
            'manage_astrologers',
            'verify_astrologers',
            'toggle_astrologer_visibility',
            'manage_content',
        ]);

        // Legacy/User Roles
        if (!Role::where('name', 'Admin')->exists()) {
            // Default Admin - give broad access but maybe not everything
            $adminNode = Role::create(['name' => 'Admin']);
            $adminNode->givePermissionTo(Permission::all());
        }

        Role::firstOrCreate(['name' => 'Astrologer']);
        Role::firstOrCreate(['name' => 'User']);
    }
}
