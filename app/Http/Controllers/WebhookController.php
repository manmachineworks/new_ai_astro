<?php

namespace App\Http\Controllers;

use App\Models\WebhookEvent;
use App\Services\CallerDeskClient;
use App\Services\WebhookPayloadMasker;
use App\Jobs\ProcessCallerDeskWebhook;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class WebhookController extends Controller
{
    public function handleCallerDesk(Request $request, CallerDeskClient $client)
    {
        $payload = $request->getContent();
        $rawHeaders = $request->headers->all();

        // 1. Verify Signature (if secret provided)
        $isValid = $client->verifyWebhookSignature($payload, $rawHeaders);
        $headers = WebhookPayloadMasker::mask($rawHeaders);

        // 2. Log Webhook Event
        $data = $request->all();
        $providerCallId = $data['call_id'] ?? ($data['provider_call_id'] ?? ($data['reference_id'] ?? null));

        $event = WebhookEvent::create([
            'provider' => 'callerdesk',
            'event_type' => $data['event'] ?? 'status_update',
            'external_id' => $providerCallId,
            'signature_valid' => $isValid,
            'payload' => WebhookPayloadMasker::mask($data),
            'headers' => $headers,
            'processing_status' => 'pending'
        ]);

        if (!$isValid) {
            Log::warning('CallerDesk: Invalid webhook signature', ['id' => $event->id]);
            // Still returning 200 to acknowledge receipt as per provider requirements
            return response()->json(['status' => 'received', 'warning' => 'invalid_signature']);
        }

        // 3. Dispatch processing job
        ProcessCallerDeskWebhook::dispatch($event->id);

        return response()->json(['status' => 'received', 'event_id' => $event->id]);
    }
}
