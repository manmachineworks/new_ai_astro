<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AstrologerProfile;
use App\Models\Review;
use Illuminate\Http\Request;

class AdminReviewController extends Controller
{
    public function index(Request $request)
    {
        $query = Review::with(['user', 'astrologerProfile']);

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('rating')) {
            $query->where('rating', $request->rating);
        }

        if ($request->filled('astrologer_id')) {
            $query->where('astrologer_profile_id', $request->astrologer_id);
        }

        if ($search = $request->input('search')) {
            $query->where(function ($q) use ($search) {
                $q->whereHas('user', function ($sub) use ($search) {
                    $sub->where('name', 'like', "%{$search}%");
                })->orWhereHas('astrologerProfile', function ($sub) use ($search) {
                    $sub->where('display_name', 'like', "%{$search}%");
                });
            });
        }

        $reviews = $query->latest()->paginate(20)->withQueryString();
        $astrologers = AstrologerProfile::orderBy('display_name')->get();

        return view('admin.reviews.index', compact('reviews', 'astrologers'));
    }

    public function update(Request $request, Review $review)
    {
        $validated = $request->validate([
            'status' => 'required|in:published,hidden',
        ]);

        $review->update(['status' => $validated['status']]);

        return back()->with('success', 'Review status updated.');
    }

    public function destroy(Review $review)
    {
        $review->delete();

        return back()->with('success', 'Review deleted.');
    }
}
