<?php

namespace App\Support;

use Illuminate\Support\Facades\Cache;

class Dedupe
{
    /**
     * atomic check-and-set.
     * Returns true if the key is new (action allowed).
     * Returns false if key already exists (action deduplicated).
     *
     * @param string $key Unique Idempotency Key
     * @param int $ttlSeconds Time to live
     * @return bool
     */
    public static function once(string $key, int $ttlSeconds = 60): bool
    {
        // Cache::add returns true if key was added, false if it existed.
        return Cache::add("dedupe:{$key}", true, $ttlSeconds);
    }

    /**
     * Generate a standard idempotency key for notifications.
     */
    public static function key(string $type, string $recipientId, string $entityId, ?string $timeBucket = null): string
    {
        return "notif:{$type}:{$recipientId}:{$entityId}" . ($timeBucket ? ":{$timeBucket}" : "");
    }
}
