<?php

namespace App\Jobs;

use App\Models\User;
use App\Models\WebhookEvent;
use App\Services\PhonePe\PhonePeService;
use App\Services\WalletService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class ProcessPhonePeWebhook implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function __construct(
        public array $payload,
        public array $headers,
    ) {
    }

    public function handle(PhonePeService $service, WalletService $walletService): void
    {
        $raw = json_encode($this->payload);
        if (!$service->verifyWebhook($this->headers, $raw)) {
            return;
        }

        $event = $service->parseWebhook($this->payload);

        $record = WebhookEvent::firstOrCreate(
            ['event_id' => $event['event_id']],
            [
                'provider' => 'phonepe',
                'payload' => $this->payload,
            ]
        );

        if ($record->processed_at) {
            return;
        }

        $user = User::find($event['user_id']);

        if ($user) {
            $service->creditWalletIfPaid($user, $event, $walletService);
        }

        $record->update([
            'processed_at' => now(),
            'payload' => $this->payload,
        ]);
    }
}
