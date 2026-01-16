<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DeviceController extends Controller
{
    public function registerToken(Request $request)
    {
        $request->validate([
            'token' => 'required',
            'platform' => 'nullable'
        ]);

        $user = $request->user();

        DB::table('user_device_tokens')->updateOrInsert(
            ['user_id' => $user->id, 'token' => $request->token],
            ['platform' => $request->platform, 'last_seen_at' => now(), 'updated_at' => now()]
        );

        return response()->json(['success' => true]);
    }
}
