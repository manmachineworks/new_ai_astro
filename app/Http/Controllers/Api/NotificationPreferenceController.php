<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\NotificationPreference;
use Illuminate\Http\Request;

class NotificationPreferenceController extends Controller
{
    public function show(Request $request)
    {
        $prefs = NotificationPreference::firstOrCreate(
            ['user_id' => $request->user()->id],
            ['timezone' => 'Asia/Kolkata']
        );
        return response()->json($prefs);
    }

    public function update(Request $request)
    {
        $data = $request->validate([
            'mute_chat' => 'boolean',
            'mute_calls' => 'boolean',
            'mute_wallet' => 'boolean',
            'dnd_start' => 'nullable|date_format:H:i',
            'dnd_end' => 'nullable|date_format:H:i',
            'timezone' => 'nullable|timezone',
        ]);

        $prefs = NotificationPreference::updateOrCreate(
            ['user_id' => $request->user()->id],
            $data
        );

        return response()->json($prefs);
    }
}
