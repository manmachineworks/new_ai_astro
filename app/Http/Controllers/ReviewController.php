<?php

namespace App\Http\Controllers;

use App\Models\AstrologerProfile;
use App\Models\Review;
use Illuminate\Http\Request;

class ReviewController extends Controller
{
    public function index(Request $request, $astrologerId)
    {
        $profile = AstrologerProfile::findOrFail($astrologerId);

        $reviews = Review::where('astrologer_profile_id', $profile->id)
            ->where('status', 'published')
            ->latest()
            ->paginate(20);

        return response()->json($reviews);
    }

    public function store(Request $request, $astrologerId)
    {
        $validated = $request->validate([
            'rating' => 'required|integer|min:1|max:5',
            'comment' => 'nullable|string|max:1000',
        ]);

        $profile = AstrologerProfile::findOrFail($astrologerId);

        $review = Review::create([
            'user_id' => $request->user()->id,
            'astrologer_profile_id' => $profile->id,
            'rating' => $validated['rating'],
            'comment' => $validated['comment'] ?? null,
            'status' => 'published',
        ]);

        $this->refreshRating($profile);

        return response()->json([
            'review_id' => $review->id,
            'status' => $review->status,
        ], 201);
    }

    public function update(Request $request, Review $review)
    {
        if ($review->user_id !== $request->user()->id) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        $validated = $request->validate([
            'rating' => 'nullable|integer|min:1|max:5',
            'comment' => 'nullable|string|max:1000',
        ]);

        $review->update($validated);

        $this->refreshRating($review->astrologerProfile);

        return response()->json(['status' => 'updated']);
    }

    public function destroy(Request $request, Review $review)
    {
        if ($review->user_id !== $request->user()->id && !$request->user()->hasAnyRole(['Super Admin', 'Admin', 'Moderator', 'Support Admin'])) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        $profile = $review->astrologerProfile;
        $review->delete();

        if ($profile) {
            $this->refreshRating($profile);
        }

        return response()->json(['status' => 'deleted']);
    }

    public function moderate(Request $request, Review $review)
    {
        $validated = $request->validate([
            'status' => 'required|in:published,hidden',
        ]);

        $review->update(['status' => $validated['status']]);

        $this->refreshRating($review->astrologerProfile);

        return response()->json(['status' => 'updated']);
    }

    protected function refreshRating(AstrologerProfile $profile): void
    {
        $query = Review::where('astrologer_profile_id', $profile->id)
            ->where('status', 'published');

        $count = $query->count();
        $avg = $count > 0 ? round((float) $query->avg('rating'), 2) : 0.0;

        $profile->update([
            'rating_avg' => $avg,
            'rating_count' => $count,
        ]);
    }
}
