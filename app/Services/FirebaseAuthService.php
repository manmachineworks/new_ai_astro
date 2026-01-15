<?php

namespace App\Services;

use Kreait\Firebase\Auth;
use Kreait\Firebase\Exception\Auth\FailedToVerifyToken;
use Kreait\Firebase\Factory;

class FirebaseAuthService
{
    protected Auth $auth;

    public function __construct(Factory $factory)
    {
        $credentials = config('firebase.projects.app.credentials');
        $this->auth = $factory
            ->withServiceAccount($credentials)
            ->createAuth();
    }

    /**
     * @return array{uid:string|null, phone_number:string|null}
     *
     * @throws FailedToVerifyToken
     */
    public function verifyIdToken(string $token): array
    {
        $verified = $this->auth->verifyIdToken($token);
        $claims = $verified->claims();

        return [
            'uid' => $claims->get('sub'),
            'phone_number' => $claims->get('phone_number'),
        ];
    }
}
