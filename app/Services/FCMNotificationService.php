<?php

namespace App\Services;

use App\Models\DeviceToken;
use App\Models\NotificationLog;
use App\Models\NotificationPreference;
use App\Models\User;
use Illuminate\Support\Facades\Log;
use Kreait\Firebase\Messaging\CloudMessage;
use Kreait\Firebase\Messaging\Notification;
use Kreait\Firebase\Messaging\AndroidConfig;
use Kreait\Firebase\Messaging\ApnsConfig;

class FCMNotificationService
{
    protected $messaging;

    public function __construct()
    {
        try {
            $this->messaging = app('firebase.messaging');
        } catch (\Throwable $e) {
            Log::warning("Firebase Messaging not initialized: " . $e->getMessage());
            $this->messaging = null;
        }
    }

    public function sendToUser(User $user, string $type, array $payload, ?string $title = null, ?string $body = null)
    {
        if (!$this->shouldSend($user, $type)) {
            Log::info("Notification skipped for User {$user->id} due to preferences/DND.");
            return false;
        }

        $tokens = $user->deviceTokens()->where('is_enabled', true)->pluck('fcm_token')->toArray();

        if (empty($tokens)) {
            Log::info("No active device tokens for User {$user->id}.");
            return false;
        }

        return $this->sendToTokens($tokens, $type, $payload, $title, $body, $user->id);
    }

    public function sendToTokens(array $tokens, string $type, array $payload, ?string $title, ?string $body, ?int $userId = null)
    {
        if (!$this->messaging) {
            Log::error("FCM Send Cancelled: Messaging service not initialized.");
            $this->logNotification($userId, $type, json_encode($payload), 'failed', 'Service not initialized');
            return 0;
        }

        // Sanitize
        $safePayload = $this->sanitizePayload($payload);
        $safePayload['type'] = $type;

        // Build Payload using Helper
        $message = $this->buildPayload(
            $type,
            $title ?? 'Notification',
            $body ?? '',
            $safePayload
        );

        try {
            $report = $this->messaging->sendMulticast($message, $tokens);

            // Log
            $this->logNotification(
                $userId,
                $type,
                json_encode($safePayload),
                'sent',
                $report->successes()->count() . ' sent, ' . $report->failures()->count() . ' failed'
            );

            // Handle Failures
            if ($report->hasFailures()) {
                $this->handleInvalidTokens($report->failures());
            }

            return $report->successes()->count();

        } catch (\Throwable $e) {
            Log::error("FCM Send Error: " . $e->getMessage());
            $this->logNotification($userId, $type, json_encode($safePayload), 'failed', $e->getMessage());
            return 0;
        }
    }

    public function buildPayload(string $type, string $title, string $body, array $data = [], string $deeplink = null, string $collapseKey = null, string $threadId = null): CloudMessage
    {
        $messageData = array_merge($data, [
            'type' => $type,
            'deeplink' => $deeplink ?? 'app://home',
            'click_action' => 'FLUTTER_NOTIFICATION_CLICK',
            'sound' => 'default',
        ]);

        $config = [
            'notification' => [
                'title' => $title,
                'body' => $this->truncatePreview($body),
            ],
            'data' => $messageData,
            'android' => [
                'priority' => 'high',
                'notification' => [
                    'channel_id' => config("firebase.fcm.android_channels.{$this->getChannelForType($type)}.id", 'default_channel'),
                    'click_action' => 'FLUTTER_NOTIFICATION_CLICK',
                ],
            ],
            'apns' => [
                'headers' => [
                    'apns-priority' => '10',
                ],
                'payload' => [
                    'aps' => [
                        'sound' => 'default',
                        'thread-id' => $threadId ?? $type,
                    ],
                ],
            ],
        ];

        if ($collapseKey) {
            $config['android']['collapse_key'] = $collapseKey;
            $config['apns']['headers']['apns-collapse-id'] = $collapseKey;
        }

        return CloudMessage::fromArray($config);
    }

    protected function shouldSend(User $user, string $type): bool
    {
        $prefs = NotificationPreference::where('user_id', $user->id)->first();
        if (!$prefs)
            return true;

        if ($type === 'chat_message' && $prefs->mute_chat)
            return false;
        if (str_contains($type, 'call') && $prefs->mute_calls)
            return false;
        if (str_contains($type, 'wallet') && $prefs->mute_wallet)
            return false;

        if ($prefs->dnd_start && $prefs->dnd_end) {
            try {
                $timezone = $prefs->timezone ?? 'UTC';
                $now = now($timezone);
                $start = $now->copy()->setTimeFromTimeString($prefs->dnd_start);
                $end = $now->copy()->setTimeFromTimeString($prefs->dnd_end);

                if ($start->gt($end)) {
                    if ($now->gte($start) || $now->lt($end))
                        return false;
                } else {
                    if ($now->between($start, $end))
                        return false;
                }
            } catch (\Exception $e) {
                // Invalid timezone or time format, ignore DND
            }
        }
        return true;
    }

    public function truncatePreview(string $text, int $maxLen = 80): string
    {
        return strlen($text) <= $maxLen ? $text : substr($text, 0, $maxLen) . '...';
    }

    public function safeMaskedLabel(User $user): string
    {
        return $user->hasRole('Astrologer')
            ? "Astrologer " . $user->name
            : "User #U" . (1000 + $user->id);
    }

    private function getChannelForType($type)
    {
        if (str_contains($type, 'chat'))
            return 'chat_messages';
        if (str_contains($type, 'call'))
            return 'calls';
        if (str_contains($type, 'wallet'))
            return 'wallet';
        return 'default';
    }

    protected function sanitizePayload(array $payload): array
    {
        unset($payload['email'], $payload['phone'], $payload['mobile'], $payload['user_phone']);
        return array_map(function ($value) {
            return is_array($value) ? json_encode($value) : (string) $value;
        }, $payload);
    }

    public function handleInvalidTokens($failures)
    {
        $tokensToDelete = [];
        foreach ($failures as $failure) {
            // Basic check, invalid argument or unregistered
            $tokensToDelete[] = $failure->target()->value();
        }

        if (!empty($tokensToDelete)) {
            DeviceToken::whereIn('fcm_token', $tokensToDelete)->delete();

            // Clean Firestore too
            // Note: Efficient batch delete in Firestore requires knowing UIDs, 
            // which we don't strictly have here without querying DB.
            // For now, we rely on the periodic cleanup or lazy deletion.
            // Or we could query Local DB to find User IDs for these tokens.
        }
    }

    protected function logNotification($userId, $type, $payloadJson, $status, $error = null)
    {
        NotificationLog::create([
            'user_id' => $userId,
            'type' => $type,
            'payload_json' => $payloadJson,
            'status' => $status,
            'error_message' => $error ? substr($error, 0, 500) : null
        ]);
    }
}
