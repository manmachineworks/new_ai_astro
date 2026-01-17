<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class LocaleController extends Controller
{
    public function switch(Request $request)
    {
        $request->validate([
            'locale' => 'required|in:en,hi',
        ]);

        $locale = $request->input('locale');

        // Update user preference if authenticated
        if ($request->user()) {
            $request->user()->update([
                'preferred_locale' => $locale,
            ]);
        }

        // Store in session
        $request->session()->put('locale', $locale);

        return response()->json([
            'success' => true,
            'locale' => $locale,
            'message' => __('common.language') . ' ' . __('common.' . $locale === 'en' ? 'english' : 'hindi'),
        ])->cookie('locale', $locale, 525600, '/', null, false, false);
    }
}
