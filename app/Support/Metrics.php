<?php

namespace App\Support;

use Illuminate\Support\Facades\Log;

class Metrics
{
    /**
     * Increment a counter metric.
     * In a real system, this would push to StatsD/Prometheus.
     * Here we structure-log it for parsing/cloud watch.
     */
    public static function increment(string $name, array $tags = [], int $value = 1): void
    {
        $payload = [
            'metric' => $name,
            'type' => 'counter',
            'value' => $value,
            'tags' => $tags,
            'timestamp' => now()->toIso8601String()
        ];

        // Log to a specific channel 'metrics' if configured, else default info
        Log::channel('daily')->info("METRIC: {$name}", $payload);
    }

    public static function gauge(string $name, int $value, array $tags = []): void
    {
        $payload = [
            'metric' => $name,
            'type' => 'gauge',
            'value' => $value,
            'tags' => $tags,
            'timestamp' => now()->toIso8601String()
        ];

        Log::channel('daily')->info("METRIC: {$name}", $payload);
    }
}
