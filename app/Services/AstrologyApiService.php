<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class AstrologyApiService
{
    protected $baseUrl;
    protected $userId;
    protected $apiKey;

    public function __construct()
    {
        $this->baseUrl = config('services.astrology_api.base_url');
        $this->userId = config('services.astrology_api.user_id');
        $this->apiKey = config('services.astrology_api.api_key');
    }

    public function getDailyHoroscope(string $sign)
    {
        if (app()->environment('testing')) {
            return ['prediction' => 'Today is a good day.'];
        }

        $response = Http::withBasicAuth($this->userId, $this->apiKey)
            ->post("{$this->baseUrl}/sun_sign_prediction/daily/" . $sign);

        if ($response->successful()) {
            return $response->json();
        }

        Log::error('AstrologyAPI Error', ['body' => $response->body()]);
        return null;
    }
}
