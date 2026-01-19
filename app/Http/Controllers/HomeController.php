<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;

class HomeController extends Controller
{
    protected $recommendationService;

    public function __construct(\App\Services\RecommendationService $recommendationService)
    {
        $this->recommendationService = $recommendationService;
    }

    public function index()
    {
        $user = auth()->user();

        if ($user) {
            // Personalize for logged-in user
            $featuredAstrologers = $this->recommendationService->getRecommendations($user, 6);
            $sectionTitle = "Recommended For You";
        } else {
            // Guest: Show Featured/Random
            // Note: RecommendationService returns AstrologerProfile, existing code returned User.
            // We should align to return AstrologerProfile for consistency in view.

            $featuredAstrologers = \App\Models\AstrologerProfile::where('is_verified', true)
                ->where('show_on_front', true)
                ->where('is_enabled', true)
                ->with('user')
                ->inRandomOrder()
                ->take(6)
                ->get();
            $sectionTitle = "Top Astrologers";
        }

        $latestBlogs = \App\Models\BlogPost::where('status', 'published')
            ->orderByDesc('published_at')
            ->take(3)
            ->with('author')
            ->get();

        return view('welcome', compact('featuredAstrologers', 'sectionTitle', 'latestBlogs'));
    }
}
