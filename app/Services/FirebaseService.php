<?php

namespace App\Services;

use Kreait\Firebase\Auth;
use Kreait\Firebase\Factory;
use RuntimeException;

class FirebaseService
{
    private Auth $auth;

    public function __construct()
    {
        $credentialsJson = config('firebase.credentials_json');
        if ($credentialsJson) {
            $decoded = json_decode($credentialsJson, true);
            if (!is_array($decoded)) {
                throw new RuntimeException('FIREBASE_CREDENTIALS_JSON must be valid JSON.');
            }

            try {
                $this->auth = (new Factory())
                    ->withServiceAccount($decoded)
                    ->createAuth();
                return;
            } catch (\Throwable $e) {
                throw new RuntimeException('Invalid Firebase credentials JSON: '.$e->getMessage(), 0, $e);
            }
        }

        $envPath = env('FIREBASE_CREDENTIALS');
        if (!$envPath) {
            throw new RuntimeException('FIREBASE_CREDENTIALS is not set. Add it to your .env file.');
        }

        $credentialsPath = storage_path($envPath);
        if (!file_exists($credentialsPath)) {
            // Normalize "storage/..." env values to avoid double "storage/storage" paths.
            $credentialsPath = storage_path($this->normalizePath($envPath));
        }

        if (!file_exists($credentialsPath)) {
            throw new RuntimeException("Firebase credentials file not found: {$credentialsPath}");
        }

        if (!is_readable($credentialsPath)) {
            throw new RuntimeException("Firebase credentials file is not readable: {$credentialsPath}");
        }

        try {
            $this->auth = (new Factory())
                ->withServiceAccount($credentialsPath)
                ->createAuth();
        } catch (\Throwable $e) {
            throw new RuntimeException('Invalid Firebase credentials: '.$e->getMessage(), 0, $e);
        }
    }

    public function auth(): Auth
    {
        return $this->auth;
    }

    private function normalizePath(string $path): string
    {
        $clean = ltrim($path, "\\/");
        if (str_starts_with($clean, 'storage/')) {
            $clean = substr($clean, strlen('storage/'));
        }

        return $clean;
    }
}
