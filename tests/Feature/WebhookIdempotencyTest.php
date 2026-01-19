<?php

namespace Tests\Feature;

use App\Jobs\ProcessPhonePeWebhook;
use App\Models\User;
use App\Models\WebhookEvent;
use App\Services\WalletService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class WebhookIdempotencyTest extends TestCase
{
    use RefreshDatabase;

    public function test_phonepe_webhook_is_idempotent()
    {
        config(['phonepe.webhook_secret' => 'secret']);

        $user = User::factory()->create(['wallet_balance' => 100]);

        $payload = [
            'merchantTransactionId' => 'tx-123',
            'merchantUserId' => $user->id,
            'status' => 'SUCCESS',
            'amount' => 5000,
        ];

        $signature = hash('sha256', json_encode($payload) . 'secret');

        $job = new ProcessPhonePeWebhook($payload, ['x-verify' => $signature]);
        $job->handle(app()->make(\App\Services\PhonePe\PhonePeService::class), app()->make(WalletService::class));

        $this->assertEquals(1, WebhookEvent::count());
        $this->assertEquals(100 + 50, $user->refresh()->wallet_balance);

        $job->handle(app()->make(\App\Services\PhonePe\PhonePeService::class), app()->make(WalletService::class));
        $this->assertEquals(1, WebhookEvent::count());
        $this->assertEquals(150, $user->refresh()->wallet_balance);
    }
}
