<?php

namespace App\Http\Controllers;

use App\Services\FirebaseService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Kreait\Firebase\Factory;
use Kreait\Firebase\Exception\Auth\FailedToVerifyToken;

class FirebaseExampleController extends Controller
{
    public function health(): JsonResponse
    {
        $checks = [
            'credentials_file' => 'pending',
            'firebase_auth' => 'pending',
        ];

        $credentialsPath = config('firebase.credentials');
        if (!is_string($credentialsPath) || $credentialsPath === '') {
            return response()->json([
                'status' => 'fail',
                'checks' => [
                    'credentials_file' => 'FIREBASE_CREDENTIALS is not set or invalid.',
                ],
            ], 500);
        }

        if (!file_exists($credentialsPath)) {
            return response()->json([
                'status' => 'fail',
                'checks' => [
                    'credentials_file' => "Missing credentials file: {$credentialsPath}",
                ],
            ], 500);
        }

        if (!is_readable($credentialsPath)) {
            return response()->json([
                'status' => 'fail',
                'checks' => [
                    'credentials_file' => "Credentials file is not readable: {$credentialsPath}",
                ],
            ], 500);
        }

        $checks['credentials_file'] = 'ok';

        try {
            $auth = (new Factory())
                ->withServiceAccount($credentialsPath)
                ->createAuth();
            $auth->listUsers(1);
            $checks['firebase_auth'] = 'ok';
        } catch (\Throwable $e) {
            $checks['firebase_auth'] = 'fail: '.$e->getMessage();
        }

        $status = $checks['firebase_auth'] === 'ok' ? 'ok' : 'fail';

        return response()->json([
            'status' => $status,
            'checks' => $checks,
        ], $status === 'ok' ? 200 : 500);
    }

    public function verifyPhoneToken(Request $request, FirebaseService $firebaseService): JsonResponse
    {
        $request->validate([
            'firebase_id_token' => ['required', 'string'],
        ]);

        try {
            $verifiedToken = $firebaseService->auth()->verifyIdToken($request->string('firebase_id_token'));
        } catch (FailedToVerifyToken $e) {
            return response()->json([
                'message' => 'Invalid Firebase token.',
            ], 401);
        } catch (\Throwable $e) {
            return response()->json([
                'message' => 'Firebase verification failed.',
            ], 500);
        }

        $phoneNumber = $verifiedToken->claims()->get('phone_number');
        if (!$phoneNumber) {
            return response()->json([
                'message' => 'Phone number not found in token claims.',
            ], 422);
        }

        return response()->json([
            'message' => 'Token verified.',
            'phone_number' => $phoneNumber,
        ]);
    }
}
