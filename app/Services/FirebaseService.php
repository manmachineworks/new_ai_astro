<?php

namespace App\Services;

use Kreait\Firebase\Factory;
use Kreait\Firebase\Messaging\CloudMessage;
use Kreait\Firebase\Messaging\Notification;
use Illuminate\Support\Facades\Log;

class FirebaseService
{
    protected $auth;
    protected $messaging;
    protected $storage;

    public function __construct()
    {
        try {
            $factory = (new Factory)
                ->withServiceAccount([
                    'project_id' => config('firebase.project_id') ?? 'mock',
                    'client_email' => config('firebase.client_email') ?? 'mock@example.com',
                    'private_key' => config('firebase.private_key') ?? 'mock-key',
                ])
                ->withDefaultStorageBucket(config('firebase.storage_bucket'));

            $this->auth = $factory->createAuth();
            $this->messaging = $factory->createMessaging();
            $this->storage = $factory->createStorage();
        } catch (\Throwable $e) {
            Log::warning('FirebaseService: Failed to initialize. Check credentials.', ['error' => $e->getMessage()]);
        }
    }

    /**
     * Create a custom Firebase token for a Laravel user
     */
    public function createCustomToken(string $uid, array $claims = []): string
    {
        return (string) $this->auth->createCustomToken($uid, $claims);
    }

    /**
     * Send Push Notification via FCM
     */
    public function sendNotification(string $token, string $title, string $body, array $data = [])
    {
        try {
            $message = CloudMessage::withTarget('token', $token)
                ->withNotification(Notification::create($title, $body))
                ->withData($data);

            $this->messaging->send($message);
            return true;
        } catch (\Exception $e) {
            Log::error('FCM Notification failed', ['error' => $e->getMessage()]);
            return false;
        }
    }

    /**
     * Get Public URL for a storage object (for attachments)
     */
    public function getPublicUrl(string $path): string
    {
        $bucket = $this->storage->getBucket();
        $object = $bucket->object($path);

        if ($object->exists()) {
            return $object->signedUrl(now()->addDays(7));
        }

        return '';
    }
}
