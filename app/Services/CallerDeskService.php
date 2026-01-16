<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class CallerDeskService
{
    protected $apiKey;
    protected $baseUrl;
    protected $route;

    public function __construct()
    {
        $this->apiKey = config('services.callerdesk.api_key');
        $this->baseUrl = config('services.callerdesk.url');
        $this->route = config('services.callerdesk.route');
    }

    public function initiateCall(string $from, string $to, string $callId)
    {
        // Mocking structure for standard Click2Call
        // Actual params depend on CallerDesk API docs: usually auth_key, from, to, route, ref_id
        $response = Http::post("{$this->baseUrl}/create-call", [
            'auth_key' => $this->apiKey,
            'from' => $from,
            'to' => $to,
            'route' => $this->route,
            'reference_id' => $callId,
        ]);

        if ($response->successful()) {
            return $response->json();
        }

        Log::error('CallerDesk Call Init Failed', ['response' => $response->body()]);
        return null;
    }
}
