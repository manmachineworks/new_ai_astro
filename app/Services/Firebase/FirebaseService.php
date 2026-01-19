<?php

namespace App\Services\Firebase;

use App\Models\ChatSession;
use App\Models\User;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Kreait\Firebase\Auth;
use Kreait\Firebase\Factory;
use Kreait\Firebase\Firestore;

class FirebaseService
{
    protected ?Auth $auth = null;
    protected ?Firestore $firestore = null;
    protected bool $enabled = false;

    public function __construct()
    {
        try {
            $factory = (new Factory())
                ->withServiceAccount([
                    'project_id' => config('firebase.project_id'),
                    'client_email' => config('firebase.client_email'),
                    'private_key' => config('firebase.private_key'),
                ]);

            $this->auth = $factory->createAuth();
            $this->firestore = $factory->createFirestore();
            $this->enabled = true;
        } catch (\Throwable $e) {
            Log::warning('FirebaseService failed to boot: ' . $e->getMessage());
        }
    }

    public function createChatThread(ChatSession $session): string
    {
        if (!$this->enabled || !$this->firestore) {
            return $session->firebase_chat_id;
        }

        $doc = $this->firestore->database()->collection('chats')->newDocument();
        $doc->set([
            'participants' => [
                'astrologer_id' => $session->astrologer_id,
                'user_id' => $session->user_id,
            ],
            'status' => $session->status,
            'createdAt' => now(),
        ]);

        return $doc->id();
    }

    public function setPresence(int $astrologerId, bool $online): void
    {
        if (!$this->enabled || !$this->firestore) {
            return;
        }
        $this->firestore
            ->database()
            ->collection('presence')
            ->document("astrologer-{$astrologerId}")
            ->set([
                'online' => $online,
                'lastSeenAt' => now(),
            ], ['merge' => true]);
    }

    public function generateChatTokenForUser(User $user): string
    {
        if (!$this->auth) {
            return 'mock-token-' . Str::random(12);
        }
        return (string) $this->auth->createCustomToken($user->id);
    }

    public function uploadMedia(string $path): string
    {
        return config('firebase.storage_bucket') . '/' . $path;
    }
}
