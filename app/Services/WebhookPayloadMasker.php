<?php

namespace App\Services;

class WebhookPayloadMasker
{
    protected const SENSITIVE_KEYS = [
        'authorization',
        'token',
        'secret',
        'signature',
        'x-verify',
        'checksum',
        'salt',
        'key',
    ];

    public static function mask(array $payload): array
    {
        $masked = [];

        foreach ($payload as $key => $value) {
            $normalized = strtolower((string) $key);
            $isSensitive = false;

            foreach (self::SENSITIVE_KEYS as $needle) {
                if (str_contains($normalized, $needle)) {
                    $isSensitive = true;
                    break;
                }
            }

            if ($isSensitive) {
                $masked[$key] = '[redacted]';
                continue;
            }

            if (is_array($value)) {
                $masked[$key] = self::mask($value);
                continue;
            }

            $masked[$key] = $value;
        }

        return $masked;
    }
}
