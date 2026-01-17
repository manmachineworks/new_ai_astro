<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class AdminChatAggregatesTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_chats_index_has_total_messages_aggregate()
    {
        $this->app->make(\Spatie\Permission\PermissionRegistrar::class)->forgetCachedPermissions();

        $permission = Permission::create(['name' => 'view_chats', 'guard_name' => 'web']);
        $role = Role::create(['name' => 'Super Admin', 'guard_name' => 'web']);
        $role->givePermissionTo($permission);

        $admin = User::factory()->create();
        $admin->assignRole($role);

        $response = $this->actingAs($admin)->get('/admin/chats');

        $response->assertOk();
        $response->assertViewHas('aggregates', function ($aggregates) {
            return is_array($aggregates) && array_key_exists('total_messages', $aggregates);
        });
    }
}
