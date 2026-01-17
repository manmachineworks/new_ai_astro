<?php

namespace App\Services;

class PrivacyGuard
{
    protected static $blacklist = [
        'email',
        'phone',
        'mobile',
        'password',
        'token',
        'auth',
        'otp',
        'credit_card'
    ];

    /**
     * Assert that variables are safe (no PII keys).
     * @throws \Exception if unsafe.
     */
    public static function assertSafe(array $variables, string $role = 'user'): void
    {
        foreach ($variables as $key => $val) {
            if (in_array(strtolower($key), self::$blacklist)) {
                throw new \Exception("Privacy Violation: Variable '{$key}' contains potential PII.");
            }
        }
    }

    /**
     * Mask a string for display (e.g. "John D***").
     */
    public static function mask(string $input): string
    {
        if (strlen($input) <= 2)
            return $input;
        return substr($input, 0, 1) . str_repeat('*', strlen($input) - 2) . substr($input, -1);
    }
}
