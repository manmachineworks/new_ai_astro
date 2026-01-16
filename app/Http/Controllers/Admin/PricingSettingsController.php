<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\PricingSetting;
use Illuminate\Http\Request;

class PricingSettingsController extends Controller
{
    public function index()
    {
        $settings = PricingSetting::all()->mapWithKeys(function ($item) {
            return [$item->key => $item->value_json];
        });

        // Ensure defaults exist for view
        $defaults = [
            'min_wallet_to_start_call' => 50,
            'min_wallet_to_start_chat' => 30,
            'ai_chat_price_per_message' => 5,
        ];

        return view('admin.pricing.index', [
            'settings' => $settings->merge($defaults), // Simple merge for view layer
        ]);
    }

    public function update(Request $request)
    {
        $validated = $request->validate([
            'min_wallet_to_start_call' => 'required|numeric|min:0',
            'min_wallet_to_start_chat' => 'required|numeric|min:0',
            'ai_chat_price_per_message' => 'required|numeric|min:0',
        ]);

        foreach ($validated as $key => $value) {
            PricingSetting::updateOrCreate(
                ['key' => $key],
                [
                    'value_json' => $value,
                    'updated_by_admin_id' => auth()->id()
                ]
            );
        }

        return back()->with('success', 'Pricing settings updated.');
    }
}
