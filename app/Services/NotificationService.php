<?php

namespace App\Services;

use App\Models\User;
use App\Models\UserDeviceToken;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class NotificationService
{
    /**
     * Send push notification to a user's active devices.
     */
    public function sendToUser(User $user, string $title, string $body, ?string $deepLink = null, array $extraData = [])
    {
        $tokens = UserDeviceToken::where('user_id', $user->id)
            ->where('status', 'active')
            ->pluck('token')
            ->toArray();

        if (empty($tokens)) {
            return; // No active devices
        }

        $payload = [
            'title' => $title,
            'body' => $body,
            'data' => array_merge($extraData, [
                'deep_link' => $deepLink,
                'click_action' => 'FLUTTER_NOTIFICATION_CLICK' // Standard for Flutter/Generic
            ])
        ];

        $this->sendToTokens($tokens, $payload);
    }

    protected function sendToTokens(array $tokens, array $payload)
    {
        // Get Service Account credentials from config or file
        // For this milestone, we assume a helper or direct FCM API call
        // This is a placeholder for the actual FCM HTTP v1 API implementation

        $serverKey = config('firebase.fcm_server_key'); // Legacy API for simplicity in this demo, or use Google Client

        if (!$serverKey) {
            Log::warning('FCM Server Key not configured');
            return;
        }

        $response = Http::withHeaders([
            'Authorization' => 'key=' . $serverKey,
            'Content-Type' => 'application/json',
        ])->post('https://fcm.googleapis.com/fcm/send', [
                    'registration_ids' => $tokens,
                    'notification' => [
                        'title' => $payload['title'],
                        'body' => $payload['body'],
                    ],
                    'data' => $payload['data'] ?? [],
                    'priority' => 'high'
                ]);

        // Log::info('FCM Response', ['response' => $response->json()]);

        // Handle invalid tokens (cleanup)
        if ($response->successful()) {
            $results = $response->json()['results'] ?? [];
            foreach ($results as $index => $result) {
                if (isset($result['error']) && ($result['error'] == 'NotRegistered' || $result['error'] == 'InvalidRegistration')) {
                    // Mark token as revoked
                    $tokenToRevoke = $tokens[$index] ?? null;
                    if ($tokenToRevoke) {
                        UserDeviceToken::where('token', $tokenToRevoke)->update(['status' => 'revoked']);
                    }
                }
            }
        }
    }
}
