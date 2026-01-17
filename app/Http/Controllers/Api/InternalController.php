<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;

class InternalController extends Controller
{
    /**
     * Retrieve FCM tokens for a user.
     * Secured by a predefined secret in .env (INTERNAL_API_SECRET).
     */
    public function getFcmTokens(Request $request)
    {
        // Simple security check
        $secret = config('services.internal_api_secret', 'secret-key-change-me');
        if ($request->header('X-Internal-Secret') !== $secret) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        $userId = $request->query('user_id');
        if (!$userId) {
            return response()->json(['error' => 'Missing user_id'], 400);
        }

        $user = User::with([
            'deviceTokens' => function ($q) {
                $q->where('is_enabled', true);
            }
        ])->find($userId);

        if (!$user) {
            return response()->json(['tokens' => []]);
        }

        // We can also check NotificationPreferences here to filter at source!
        // But for "Chat Messages", we usually filter in the Cloud Function or just return tokens.
        // Let's return tokens and let Cloud Function decide or just send.
        // Actually, checking prefs here saves cost/time.

        $prefs = $user->preferences; // or NotificationPreference model directly
        // Note: access via relationship might be slightly different depending on model naming.
        // I created `NotificationPreference` model but User has `preferences` rel to `UserPreference`?
        // Wait, I created `NotificationPreference` but User model has `preferences()` linked to `UserPreference::class`.
        // Discrepancy detected! User model has line 60: `return $this->hasOne(UserPreference::class);`
        // I created `App\Models\NotificationPreference`.
        // I should fix this relationship too or use direct query.

        $muteChat = \App\Models\NotificationPreference::where('user_id', $userId)->value('mute_chat');

        if ($muteChat) {
            return response()->json(['tokens' => [], 'muted' => true]);
        }

        // Also DND check could be here, but timezones are tricky. 
        // Let's just return tokens for now.

        return response()->json([
            'tokens' => $user->deviceTokens->pluck('fcm_token')->toArray()
        ]);
    }
}
