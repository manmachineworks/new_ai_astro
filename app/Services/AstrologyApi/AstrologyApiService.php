<?php

namespace App\Services\AstrologyApi;

use App\Models\User;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class AstrologyApiService
{
    protected string $baseUrl;
    protected string $apiKey;
    protected string $userId;

    public function __construct()
    {
        $this->baseUrl = config('astrologyapi.base_url');
        $this->apiKey = config('astrologyapi.api_key');
        $this->userId = config('astrologyapi.user_id');
    }

    public function startAiChat(User $user, array $context): array
    {
        return [
            'session_id' => 'AI-' . uniqid(),
            'user_id' => $user->id,
            'status' => 'started',
            'context' => $context,
        ];
    }

    public function sendAiMessage(string $sessionId, string $message): array
    {
        if (app()->environment('testing')) {
            return [
                'reply' => 'Use caution when interpreting astrology. [TEST]',
                'tokens_used' => 1,
                'cost' => 0,
            ];
        }

        try {
            $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . $this->apiKey,
            ])->post("{$this->baseUrl}/ai-chat", [
                'session_id' => $sessionId,
                'message' => $message,
            ]);

            if ($response->successful()) {
                return $response->json();
            }
        } catch (\Throwable $e) {
            Log::error('AstrologyApiService::sendAiMessage failed', ['error' => $e->getMessage()]);
        }

        return [
            'reply' => 'Unable to respond right now.',
            'tokens_used' => 0,
            'cost' => 0,
        ];
    }
}
