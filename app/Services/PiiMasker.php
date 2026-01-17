<?php

namespace App\Services;

class PiiMasker
{
    /**
     * Mask email address (j***@example.com)
     */
    public static function maskEmail($email)
    {
        if (empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
            return $email;
        }

        $parts = explode('@', $email);
        $name = $parts[0];
        $domain = $parts[1];

        $maskedName = substr($name, 0, 1) . str_repeat('*', max(strlen($name) - 2, 3)) . substr($name, -1);

        return $maskedName . '@' . $domain;
    }

    /**
     * Mask phone number (******8901)
     */
    public static function maskPhone($phone)
    {
        if (empty($phone)) {
            return $phone;
        }

        // Keep last 4 digits
        return str_repeat('*', max(strlen($phone) - 4, 6)) . substr($phone, -4);
    }
}
