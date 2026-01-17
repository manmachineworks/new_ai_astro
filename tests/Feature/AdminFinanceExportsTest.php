<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Spatie\Permission\PermissionRegistrar;
use Tests\TestCase;

class AdminFinanceExportsTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_view_finance_exports_page()
    {
        $this->app->make(PermissionRegistrar::class)->forgetCachedPermissions();

        $role = Role::create(['name' => 'Super Admin', 'guard_name' => 'web']);
        $permissions = [
            Permission::create(['name' => 'view_finance', 'guard_name' => 'web']),
            Permission::create(['name' => 'export_finance', 'guard_name' => 'web']),
        ];

        $role->givePermissionTo($permissions);

        $admin = User::factory()->create();
        $admin->assignRole($role);

        $response = $this->actingAs($admin)->get(route('admin.finance.exports.index'));

        $response->assertOk();
        $response->assertSee('Finance Exports');
    }
}
