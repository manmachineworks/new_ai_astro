<?php

namespace App\Services;

use Illuminate\Support\Facades\Cache;

class NotificationThrottleService
{
    /**
     * Check if a notification specific key should be sent (i.e. not throttled).
     * If true, it means we can send.
     * Note: Does NOT automatically mark as sent.
     */
    public function shouldSend(string $throttleKey): bool
    {
        return !Cache::has($throttleKey);
    }

    /**
     * Mark a key as sent with a TTL.
     */
    public function markSent(string $throttleKey, int $ttlSeconds): void
    {
        Cache::put($throttleKey, true, $ttlSeconds);
    }

    /**
     * Atomic check and set. Returns true if allowed to send, and sets the lock.
     * Use this for strict rate limiting to avoid race conditions.
     */
    public function attempt(string $throttleKey, int $ttlSeconds): bool
    {
        // 'add' returns true if key didn't exist and was added.
        return Cache::add($throttleKey, true, $ttlSeconds);
    }

    // Helper Key Generators
    public function walletLowKey(int $userId): string
    {
        return "throttle:wallet_low:{$userId}";
    }

    public function callMissedKey(int $astrologerId): string
    {
        return "throttle:call_missed:{$astrologerId}";
    }

    public function adminBroadcastKey(string $broadcastId, int $userId): string
    {
        return "throttle:admin_broadcast:{$broadcastId}:{$userId}";
    }
}
