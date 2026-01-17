<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\FeaturedAstrologer;
use App\Models\AstrologerProfile;
use Illuminate\Http\Request;

class FeaturedAstrologerController extends Controller
{
    public function index()
    {
        $featured = FeaturedAstrologer::with('profile')->orderBy('sort_order')->get();
        $astrologers = AstrologerProfile::where('is_verified', true)->get(); // For selection
        return view('admin.cms.featured.index', compact('featured', 'astrologers'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'astrologer_profile_id' => 'required|exists:astrologer_profiles,id',
            'locale' => 'required|in:en,hi',
            'sort_order' => 'integer',
        ]);

        FeaturedAstrologer::create($validated);
        return back()->with('success', 'Astrologer featured.');
    }

    public function destroy(FeaturedAstrologer $featured)
    {
        $featured->delete();
        return back()->with('success', 'Removed from featured.');
    }

    public function updateOrder(Request $request)
    {
        // ... implementation for reordering if needed via AJAX
        return back();
    }
}
