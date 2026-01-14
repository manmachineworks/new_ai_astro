<?php

namespace App\Services;

use Kreait\Firebase\Exception\Auth\FailedToVerifyToken;
use Kreait\Laravel\Firebase\Facades\Firebase;
use Psr\Log\LoggerInterface;

class FirebaseService
{
    protected $auth;
    protected $logger;

    public function __construct(LoggerInterface $logger)
    {
        $this->auth = Firebase::auth();
        $this->logger = $logger;
    }

    /**
     * Verify the Firebase ID Token.
     *
     * @param string $idToken
     * @return array|null Returns array with 'uid', 'phone_number' if valid, null otherwise.
     */
    public function verifyToken(string $idToken): ?array
    {
        try {
            $verifiedIdToken = $this->auth->verifyIdToken($idToken);
            $claims = $verifiedIdToken->claims();

            return [
                'uid' => $claims->get('sub'),
                'phone_number' => $claims->get('phone_number'),
            ];
        } catch (FailedToVerifyToken $e) {
            $this->logger->error('Firebase Token Verification Failed: ' . $e->getMessage());
            return null;
        } catch (\Throwable $e) {
            $this->logger->error('Firebase Auth Error: ' . $e->getMessage());
            return null;
        }
    }
}
