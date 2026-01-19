<?php

namespace App\Jobs;

use App\Models\AstrologerPricing;
use App\Models\CallLog;
use App\Models\Earning;
use App\Models\User;
use App\Models\WebhookEvent;
use App\Services\CallerDesk\CallerDeskService;
use App\Services\WalletService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class ProcessCallerDeskWebhook implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function __construct(
        public array $payload,
        public array $headers
    ) {
    }

    public function handle(CallerDeskService $callerDeskService, WalletService $walletService): void
    {
        $raw = json_encode($this->payload);
        if (!$callerDeskService->verifyWebhook($this->headers, $raw)) {
            return;
        }

        $event = $callerDeskService->mapWebhookToCallLog($this->payload);

        $record = WebhookEvent::firstOrCreate(
            ['event_id' => $event['callerdesk_call_id'] ?? uniqid()],
            [
                'provider' => 'callerdesk',
                'payload' => $this->payload,
            ]
        );

        if ($record->processed_at) {
            return;
        }

        $callLog = CallLog::firstOrNew([
            'callerdesk_call_id' => $event['callerdesk_call_id'],
        ]);

        $callLog->fill(array_merge([
            'user_public_id' => $this->payload['payload']['user_public_id'] ?? 'USER-00000',
            'astrologer_id' => $this->payload['payload']['astrologer_id'] ?? null,
            'user_id' => $this->payload['payload']['user_id'] ?? null,
        ], $event));

        $callLog->save();

        if (!$callLog->user_id || !$callLog->astrologer_id) {
            return;
        }

        $pricing = AstrologerPricing::firstWhere('astrologer_id', $callLog->astrologer_id);
        $rate = $pricing->call_per_minute ?? 0;
        $minutes = max(1, ceil($callLog->duration_seconds / 60));
        $charge = $rate * $minutes;

        $user = User::find($callLog->user_id);

        if (!$user) {
            return;
        }

        try {
            $walletService->debit(
                $user,
                $charge,
                'call',
                "call-{$callLog->id}",
                'CallerDesk call charge',
                ['call_log_id' => $callLog->id],
                'call',
                "call-{$callLog->id}"
            );
        } catch (\Exception $e) {
            $callLog->meta = array_merge($callLog->meta ?? [], ['insufficient_balance' => true]);
            $callLog->save();
            return;
        }

        Earning::create([
            'astrologer_id' => $callLog->astrologer_id,
            'source' => 'call',
            'source_id' => $callLog->id,
            'gross_amount' => $charge,
            'commission_amount' => 0,
            'net_amount' => $charge,
            'status' => 'pending',
        ]);

        $callLog->update([
            'amount_charged' => $charge,
            'status' => $event['status'],
            'rate_per_minute' => $rate,
            'meta' => array_merge($callLog->meta ?? [], ['charge_processed' => true]),
        ]);

        $record->update([
            'processed_at' => now(),
            'payload' => $this->payload,
        ]);
    }
}
