<?php

namespace App\Jobs;

use App\Models\WebhookEvent;
use App\Models\CallSession;
use App\Models\AstrologerEarningsLedger;
use App\Services\WalletService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;

class ProcessCallerDeskWebhook implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $eventId;

    public function __construct($eventId)
    {
        $this->eventId = $eventId;
    }

    public function handle(WalletService $walletService)
    {
        $event = WebhookEvent::findOrFail($this->eventId);
        /** @var array $payload */
        $payload = (array) $event->payload;
        $providerCallId = $payload['call_id'] ?? null;

        if (!$providerCallId) {
            $event->update(['processing_status' => 'failed', 'error_message' => 'Missing call_id in payload']);
            return;
        }

        $session = CallSession::where('provider_call_id', $providerCallId)->first();

        if (!$session) {
            $event->update(['processing_status' => 'failed', 'error_message' => "Session not found for provider_id: {$providerCallId}"]);
            return;
        }

        try {
            \DB::transaction(function () use ($session, $payload, $walletService) {
                $session = CallSession::where('id', $session->id)->lockForUpdate()->first();

                $status = $payload['status'] ?? ($payload['event'] ?? null);

                switch ($status) {
                    case 'ringing':
                        if ($session->status === 'initiated') {
                            $session->update([
                                'status' => 'ringing',
                                'started_at_utc' => now()
                            ]);
                        }
                        break;

                    case 'connected':
                    case 'answered':
                        if ($session->status !== 'completed' && !$session->connected_at_utc) {
                            $session->update([
                                'status' => 'connected',
                                'connected_at_utc' => now()
                            ]);
                        }
                        break;

                    case 'completed':
                    case 'disconnected':
                    case 'ended':
                        if ($session->settled_at) {
                            Log::info("CallSession {$session->id} already settled.");
                            break;
                        }

                        $this->handleCallCompletion($session, $payload, $walletService);
                        break;

                    case 'missed':
                    case 'failed':
                    case 'rejected':
                        if (!$session->settled_at) {
                            $this->handleCallFailure($session, $status, $walletService);
                        }
                        break;
                }
            });

            $event->update(['processing_status' => 'processed', 'processed_at' => now()]);
        } catch (\Exception $e) {
            Log::error('ProcessCallerDeskWebhook error', ['event_id' => $event->id, 'error' => $e->getMessage()]);
            $event->update(['processing_status' => 'failed', 'error_message' => $e->getMessage()]);
            throw $e;
        }
    }

    protected function handleCallCompletion(CallSession $session, array $payload, WalletService $walletService)
    {
        $endedAt = now();
        $connectedAt = $session->connected_at_utc;

        // Use provider duration if available, else calculate
        $durationSeconds = $payload['duration'] ?? ($connectedAt ? $endedAt->diffInSeconds($connectedAt) : 0);

        // Billing Logic
        $rule = config('callerdesk.billing.rounding_rule', 'ceil');
        $billableMinutes = 0;

        if ($durationSeconds > 0) {
            if ($rule === 'ceil') {
                $billableMinutes = ceil($durationSeconds / 60);
            } else {
                $billableMinutes = floor($durationSeconds / 60);
            }
        }

        $grossAmount = $billableMinutes * $session->rate_per_minute;

        // Platform Commission
        $commissionPercent = \App\Models\PricingSetting::get('platform_commission_percent', 20);
        $commissionAmount = ($grossAmount * $commissionPercent) / 100;
        $earningsAmount = $grossAmount - $commissionAmount;

        // Wallet Settlement
        if ($grossAmount > 0) {
            $walletService->consumeHold($session->walletHold, $grossAmount);
        } else {
            if ($session->walletHold) {
                $walletService->releaseHold($session->walletHold);
            }
        }

        // Update Session
        $session->update([
            'status' => 'completed',
            'ended_at_utc' => $endedAt,
            'duration_seconds' => $durationSeconds,
            'billable_minutes' => $billableMinutes,
            'gross_amount' => $grossAmount,
            'platform_commission_amount' => $commissionAmount,
            'astrologer_earnings_amount' => $earningsAmount,
            'settled_at' => now(),
            'meta_json' => array_merge($session->meta_json ?? [], ['webhook_payload' => $payload])
        ]);

        // Record Earnings
        if ($earningsAmount > 0) {
            AstrologerEarningsLedger::create([
                'astrologer_profile_id' => $session->astrologer_profile_id,
                'source' => 'call',
                'reference_type' => CallSession::class,
                'reference_id' => $session->id,
                'amount' => $earningsAmount,
                'status' => 'available'
            ]);
        }
    }

    protected function handleCallFailure(CallSession $session, string $status, WalletService $walletService)
    {
        if ($session->walletHold) {
            $walletService->releaseHold($session->walletHold);
        }

        $session->update([
            'status' => $status,
            'ended_at_utc' => now(),
            'settled_at' => now(),
            'gross_amount' => 0
        ]);
    }
}
