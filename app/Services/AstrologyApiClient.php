<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use App\Models\PricingSetting;

class AstrologyApiClient
{
    protected $baseUrl;
    protected $userId;
    protected $apiKey;
    protected $timeout;

    public function __construct()
    {
        $this->baseUrl = PricingSetting::get('astrology_api_base_url', config('astrologyapi.base_url'));
        $this->userId = PricingSetting::get('astrology_api_user_id', config('astrologyapi.user_id'));
        $this->apiKey = PricingSetting::get('astrology_api_key', config('astrologyapi.api_key'));
        $this->timeout = (int) PricingSetting::get('astrology_api_timeout', config('astrologyapi.timeout'));
    }

    /**
     * Get Daily Horoscope
     */
    public function getDailyHoroscope(string $sign, string $date, string $lang = 'en')
    {
        return $this->request('sun_sign_prediction/daily/' . $sign, [
            'date' => $date,
            'language' => $lang,
        ]);
    }

    /**
     * Get Weekly Horoscope
     */
    public function getWeeklyHoroscope(string $sign, string $lang = 'en')
    {
        return $this->request('sun_sign_prediction/weekly/' . $sign, [
            'language' => $lang,
        ]);
    }

    /**
     * Get Kundli (Basic Birth Details / Planets)
     */
    public function getKundli(array $birthDetails)
    {
        // birthDetails: day, month, year, hour, min, lat, lon, tzone
        return $this->request('planets', $birthDetails);
    }

    /**
     * AI Chat / Ask / Prediction
     * Note: If a specific AI endpoint exists, use it. 
     * Otherwise, this is a placeholder for the logic.
     */
    public function aiChat(array $messages, array $userContext = [])
    {
        // Example structure for AstrologyAPI AI if supported
        // If not, we might use a generic prediction endpoint or a specialized AI wrapper.
        return $this->request('chat_ai', [
            'messages' => $messages,
            'context' => $userContext,
        ]);
    }

    /**
     * Generic Request Handler
     */
    protected function request(string $endpoint, array $data = [])
    {
        if (empty($this->userId) || empty($this->apiKey)) {
            Log::error("AstrologyAPI credentials missing.");
            throw new \Exception("AstrologyAPI service not configured.");
        }

        try {
            $response = Http::withBasicAuth($this->userId, $this->apiKey)
                ->timeout($this->timeout)
                ->post($this->baseUrl . $endpoint, $data);

            if ($response->failed()) {
                Log::error("AstrologyAPI request failed", [
                    'endpoint' => $endpoint,
                    'status' => $response->status(),
                    'body' => $response->body()
                ]);
                throw new \Exception("AstrologyAPI error: " . ($response->json('msg') ?? 'Unknown error'));
            }

            return $response->json();
        } catch (\Exception $e) {
            Log::error("AstrologyAPI exception", ['msg' => $e->getMessage()]);
            throw $e;
        }
    }
}
