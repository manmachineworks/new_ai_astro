<?php

namespace App\Services;

use Kreait\Firebase\Contract\Messaging;
use Illuminate\Support\Facades\Log;

class TopicSubscriptionService
{
    protected $messaging;

    public function __construct(Messaging $messaging)
    {
        $this->messaging = $messaging;
    }

    /**
     * Subscribe a token to a specific user topic.
     * Topic format: user_{userId}
     */
    public function subscribeToUserTopic(string $token, int $userId): void
    {
        $topic = "user_{$userId}";
        $this->subscribe($token, $topic);
    }

    /**
     * Unsubscribe a token from a specific user topic.
     */
    public function unsubscribeFromUserTopic(string $token, int $userId): void
    {
        $topic = "user_{$userId}";
        $this->unsubscribe($token, $topic);
    }

    /**
     * Subscribe a token to a role-based topic.
     * e.g. astrologer_{id} or role_user / role_astrologer (if broadcast needed)
     * Requirement: astrologer_{astrologerId}
     */
    public function subscribeToAstrologerTopic(string $token, int $userId): void
    {
        $topic = "astrologer_{$userId}";
        $this->subscribe($token, $topic);
    }

    public function unsubscribeFromAstrologerTopic(string $token, int $userId): void
    {
        $topic = "astrologer_{$userId}";
        $this->unsubscribe($token, $topic);
    }

    /**
     * Subscribe to Admin Broadcasts (optional, for admin users)
     */
    public function subscribeToAdminBroadcast(string $token): void
    {
        $this->subscribe($token, 'admin_broadcast');
    }

    protected function subscribe(string $token, string $topic): void
    {
        try {
            $this->messaging->subscribeToTopic($topic, $token);
            Log::info("Subscribed token to topic: {$topic}");
        } catch (\Throwable $e) {
            Log::error("Failed to subscribe token to {$topic}: " . $e->getMessage());
        }
    }

    protected function unsubscribe(string $token, string $topic): void
    {
        try {
            $this->messaging->unsubscribeFromTopic($topic, $token);
            Log::info("Unsubscribed token from topic: {$topic}");
        } catch (\Throwable $e) {
            // Token might already be invalid, log warning
            Log::warning("Failed to unsubscribe token from {$topic}: " . $e->getMessage());
        }
    }
}
