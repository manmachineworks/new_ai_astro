<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\WebhookEvent;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class AdminWebhookDeadLetterTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_view_dead_letter_webhooks()
    {
        $this->app->make(\Spatie\Permission\PermissionRegistrar::class)->forgetCachedPermissions();

        $permission = Permission::create(['name' => 'view_webhooks', 'guard_name' => 'web']);
        $role = Role::create(['name' => 'Super Admin', 'guard_name' => 'web']);
        $role->givePermissionTo($permission);

        $admin = User::factory()->create();
        $admin->assignRole($role);

        WebhookEvent::create([
            'provider' => 'phonepe',
            'event_type' => 'dead_letter_test',
            'external_id' => 'txn_dead_letter',
            'signature_valid' => true,
            'payload' => ['data' => ['merchantTransactionId' => 'txn_dead_letter']],
            'headers' => [],
            'processing_status' => 'failed',
            'error_message' => 'Test failure',
            'attempts' => 3,
        ]);

        $response = $this->actingAs($admin)->get(route('admin.system.webhooks.index', ['filter' => 'dead_letter']));

        $response->assertOk();
        $response->assertSee('dead_letter_test');
    }
}
