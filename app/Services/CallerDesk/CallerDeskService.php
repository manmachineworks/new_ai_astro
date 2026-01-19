<?php

namespace App\Services\CallerDesk;

use App\Models\Astrologer;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class CallerDeskService
{
    protected string $baseUrl;
    protected string $apiKey;
    protected string $webhookSecret;

    public function __construct()
    {
        $this->baseUrl = config('callerdesk.base_url');
        $this->apiKey = config('callerdesk.api_key');
        $this->webhookSecret = config('callerdesk.webhook_secret', '');
    }

    public function initiateCall(Astrologer $astrologer, string $userPublicId, array $meta = []): array
    {
        Log::debug('Initiating CallerDesk call', ['astrologer_id' => $astrologer->id, 'user_public_id' => $userPublicId]);

        return [
            'callerdesk_call_id' => 'CD-' . uniqid(),
            'status' => 'initiated',
            'meta' => array_merge($meta, [
                'astrologer_id' => $astrologer->id,
                'user_public_id' => $userPublicId,
            ]),
        ];
    }

    public function fetchCallStatus(string $callerdeskId): array
    {
        return [
            'status' => 'ended',
            'duration_seconds' => 0,
        ];
    }

    public function verifyWebhook(array $headers, string $payload): bool
    {
        $signature = $headers['x-callerdesk-signature'] ?? '';
        if (empty($this->webhookSecret) || empty($signature)) {
            return false;
        }

        return hash_hmac('sha256', $payload, $this->webhookSecret) === $signature;
    }

    public function mapWebhookToCallLog(array $payload): array
    {
        return [
            'callerdesk_call_id' => $payload['call_id'] ?? null,
            'status' => $payload['status'] ?? 'failed',
            'duration_seconds' => intval($payload['duration_seconds'] ?? 0),
            'rate_per_minute' => floatval($payload['rate_per_minute'] ?? 0),
            'meta' => $payload,
        ];
    }
}
