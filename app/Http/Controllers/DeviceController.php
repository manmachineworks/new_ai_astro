<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\UserDeviceToken;

class DeviceController extends Controller
{
    public function registerToken(Request $request)
    {
        $request->validate([
            'token' => 'required|string',
            'platform' => 'nullable|string'
        ]);

        $user = $request->user();

        UserDeviceToken::updateOrCreate(
            ['user_id' => $user->id, 'token' => $request->token],
            [
                'platform' => $request->platform,
                'status' => 'active',
                'last_seen_at' => now(),
                'last_token_refresh_at' => now()
            ]
        );

        return response()->json(['success' => true, 'message' => 'Token registered']);
    }

    public function revokeToken(Request $request)
    {
        $request->validate(['token' => 'required|string']);

        $user = $request->user();

        UserDeviceToken::where('user_id', $user->id)
            ->where('token', $request->token)
            ->update(['status' => 'revoked']);

        return response()->json(['success' => true, 'message' => 'Token revoked']);
    }
}
