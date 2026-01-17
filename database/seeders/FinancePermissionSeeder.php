<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class FinancePermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // 1. Define Permissions
        $permissions = [
            'manage_payments',    // View/Edit Payments
            'export_finance',     // Export CSVs
            'wallet_adjustments', // Credit/Debit Users
            'manage_payouts',     // View Earnings/Payouts
            'issue_refunds',      // Process Refunds
            'manage_commissions', // Edit Commission Rates
        ];

        foreach ($permissions as $permission) {
            Permission::firstOrCreate(['name' => $permission, 'guard_name' => 'web']);
        }

        // 2. Assign to Super Admin (All Access)
        $superAdmin = Role::firstOrCreate(['name' => 'Super Admin']);
        $superAdmin->givePermissionTo($permissions);

        // 3. Setup Finance Admin
        $financeAdmin = Role::firstOrCreate(['name' => 'Finance Admin']);
        $financeAdmin->givePermissionTo([
            'manage_payments',
            'export_finance',
            'wallet_adjustments',
            'manage_payouts',
            'issue_refunds',
            // 'manage_commissions' -> Optional: Keep exclusive to Super Admin, or share?
            // Let's grant it for now as "Finance Admin" manages money settings.
            'manage_commissions',
        ]);

        // 4. Update Support Admin (Read Only for Payments? Maybe not)
        // Support Admin generally doesn't touch money.
    }
}
