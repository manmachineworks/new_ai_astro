<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class CallerDeskClient
{
    protected string $baseUrl;
    protected string $apiKey;
    protected ?string $webhookSecret;

    public function __construct()
    {
        $this->baseUrl = config('callerdesk.base_url');
        $this->apiKey = config('callerdesk.api_key');
        $this->webhookSecret = config('callerdesk.webhook_secret');
    }

    /**
     * Initiate a masked call between user and astrologer
     * 
     * @param string $userPhone E.164 format
     * @param string $astrologerPhone E.164 format
     * @param array $metadata
     * @return array [provider_call_id, status]
     */
    public function initiateMaskedCall(string $userPhone, string $astrologerPhone, array $metadata = [])
    {
        Log::info('CallerDesk: Initiating masked call', [
            'meta' => $metadata
        ]);

        // Mocking API call as per requirements for integration
        // Real implementation would use Http::withHeaders(['Authorization' => 'Bearer ' . $this->apiKey])

        $response = Http::withHeaders([
            'x-api-key' => $this->apiKey,
            'Accept' => 'application/json'
        ])->post($this->baseUrl . '/calls/mask', [
                    'pool_id' => config('callerdesk.masking_pool_id'),
                    'from' => $userPhone,
                    'to' => $astrologerPhone,
                    'metadata' => $metadata
                ]);

        if ($response->failed()) {
            Log::error('CallerDesk: API call failed', [
                'status' => $response->status(),
                'body' => $response->body()
            ]);
            throw new \Exception('CallerDesk API failed: ' . $response->reason());
        }

        $data = $response->json();

        return [
            'provider_call_id' => $data['call_id'] ?? 'CD_MOCK_' . uniqid(),
            'status' => $data['status'] ?? 'initiated'
        ];
    }

    /**
     * Fetch call details from Provider
     */
    public function fetchCallDetails(string $providerCallId)
    {
        $response = Http::withHeaders([
            'x-api-key' => $this->apiKey
        ])->get($this->baseUrl . "/calls/{$providerCallId}");

        return $response->json();
    }

    /**
     * Verify Webhook Signature
     */
    public function verifyWebhookSignature(string $payload, array $headers): bool
    {
        if (!$this->webhookSecret) {
            return true; // Or log warning if secret is recommended
        }

        $receivedSignature = $headers['x-callerdesk-signature'] ?? '';
        if (is_array($receivedSignature)) {
            $receivedSignature = $receivedSignature[0] ?? '';
        }
        $expectedSignature = hash_hmac('sha256', $payload, $this->webhookSecret);

        return hash_equals($expectedSignature, $receivedSignature);
    }
}
