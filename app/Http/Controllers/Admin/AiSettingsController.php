<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\PricingSetting;
use App\Models\AiChatReport;
use Illuminate\Http\Request;

class AiSettingsController extends Controller
{
    public function index()
    {
        $keys = [
            'ai_chat_enabled',
            'ai_chat_pricing_mode',
            'ai_chat_price_per_message',
            'ai_chat_price_per_session',
            'ai_chat_min_wallet_to_start',
            'ai_chat_max_messages_per_day',
            'ai_chat_disclaimer_text',
            'astrology_api_base_url',
            'astrology_api_user_id',
            'astrology_api_key',
            'astrology_api_timeout',
            'ai_chat_rate_limit_per_min'
        ];

        $settings = [];
        foreach ($keys as $key) {
            $settings[$key] = PricingSetting::get($key);
        }

        return view('admin.ai.settings', compact('settings'));
    }

    public function update(Request $request)
    {
        $validated = $request->validate([
            'ai_chat_enabled' => 'nullable|boolean',
            'ai_chat_pricing_mode' => 'required|in:per_message,per_session',
            'ai_chat_price_per_message' => 'required|numeric|min:0',
            'ai_chat_price_per_session' => 'required|numeric|min:0',
            'ai_chat_min_wallet_to_start' => 'required|numeric|min:0',
            'ai_chat_max_messages_per_day' => 'required|integer|min:1',
            'ai_chat_disclaimer_text' => 'required|string|max:1000',
            'astrology_api_base_url' => 'nullable|string|max:255',
            'astrology_api_user_id' => 'nullable|string|max:255',
            'astrology_api_key' => 'nullable|string|max:255',
            'astrology_api_timeout' => 'nullable|integer|min:1|max:120',
            'ai_chat_rate_limit_per_min' => 'nullable|integer|min:1|max:120',
        ]);

        // Fix boolean checkbox
        $validated['ai_chat_enabled'] = $request->has('ai_chat_enabled') ? 1 : 0;

        if (empty($validated['astrology_api_key'])) {
            unset($validated['astrology_api_key']);
        }

        foreach ($validated as $key => $value) {
            PricingSetting::updateOrCreate(
                ['key' => $key],
                ['value_json' => $value, 'updated_by_admin_id' => auth()->id()]
            );
        }

        return back()->with('success', 'AI settings updated successfully.');
    }

    public function reports()
    {
        $reports = AiChatReport::with(['user', 'message.session'])->latest()->paginate(20);
        return view('admin.ai.reports', compact('reports'));
    }
}
