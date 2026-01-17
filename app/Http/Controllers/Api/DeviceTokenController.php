<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\DeviceToken;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class DeviceTokenController extends Controller
{
    public function register(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'fcm_token' => 'required|string',
            'platform' => 'required|in:android,ios,web',
            'device_id' => 'nullable|string',
            'app_version' => 'nullable|string',
            'locale' => 'nullable|string|max:10'
        ]);

        if ($validator->fails()) {
            return response()->json(['error' => $validator->errors()], 422);
        }

        $user = $request->user();

        // Upsert logic: if token exists for this user, update details.
        // If token exists for ANOTHER user (device logout/login), maybe we should claim it.
        // For simplicity, we assume one token = one active user session.

        // Local DB Upsert
        $token = DeviceToken::updateOrCreate(
            ['user_id' => $user->id, 'fcm_token' => $request->fcm_token],
            [
                'role' => $user->getRoleNames()->first() ?? 'user',
                'platform' => $request->platform,
                'device_id' => $request->device_id,
                'app_version' => $request->app_version,
                'locale' => $request->locale ?? 'en',
                'is_enabled' => true,
                'last_seen_at' => now(),
            ]
        );

        // SYNC TO FIRESTORE (For Cloud Functions access)
        try {
            $firestore = app('firebase.firestore')->database();
            $firestore->collection('device_tokens')
                ->document((string) $user->id)
                ->collection('tokens')
                ->document($request->fcm_token) // Use Token as ID for easy dedup
                ->set([
                    'platform' => $request->platform,
                    'is_enabled' => true,
                    'updated_at' => time()
                ]);
        } catch (\Exception $e) {
            // Log but don't fail the request if Firestore sync fails
            \Log::warning("Firestore Token Sync Failed: " . $e->getMessage());
        }

        // SYNC TOPICS (Queue)
        $role = $user->getRoleNames()->first() ?? 'user';
        \App\Jobs\SyncFCMTopicsJob::dispatch($request->fcm_token, $user->id, $role, 'subscribe');

        return response()->json([
            'message' => 'Device token registered successfully',
            'data' => $token
        ]);
    }

    public function unregister(Request $request)
    {
        $request->validate([
            'fcm_token' => 'required|string'
        ]);

        // Disable or delete. Deleting is cleaner for hygiene.
        DeviceToken::where('user_id', $request->user()->id)
            ->where('fcm_token', $request->fcm_token)
            ->delete();

        // 1. Remove from Firestore
        try {
            app('firebase.firestore')->database()
                ->collection('device_tokens')
                ->document((string) $request->user()->id)
                ->collection('tokens')
                ->document($request->fcm_token)
                ->delete();
        } catch (\Exception $e) {
            \Log::warning("Firestore Token Delete Failed: " . $e->getMessage());
        }

        // 2. Unsubscribe from Topics (Queue)
        $role = $request->user()->getRoleNames()->first() ?? 'user';
        \App\Jobs\SyncFCMTopicsJob::dispatch($request->fcm_token, $request->user()->id, $role, 'unsubscribe');

        return response()->json(['message' => 'Device token unregistered']);
    }
}
