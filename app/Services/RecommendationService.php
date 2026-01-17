<?php

namespace App\Services;

use App\Models\AstrologerProfile;
use App\Models\User;
use App\Models\UserPreference;
use App\Models\RecommendationSetting;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Cache;

class RecommendationService
{
    protected $weights;

    public function __construct()
    {
        // Load weights from settings or defaults
        $this->weights = Cache::remember('recommendation_weights', 600, function () {
            $settings = RecommendationSetting::pluck('value_json', 'key')->toArray();
            return array_merge([
                'weight_language' => 30,
                'weight_specialty' => 25,
                'weight_rating' => 20,
                'weight_availability' => 15,
                'weight_price' => 10,
                'exploration_percent' => 20,
            ], $settings);
        });
    }

    public function getRecommendations(User $user, int $limit = 10): Collection
    {
        // Cache key per user preferences
        // In real world, invalidate this cache when preferences change
        return Cache::remember("recommendations_user_{$user->id}", 300, function () use ($user, $limit) {
            return $this->calculateRecommendations($user, $limit);
        });
    }

    protected function calculateRecommendations(User $user, int $limit): Collection
    {
        $preferences = $user->preferences; // Assumes relationship
        $userLanguages = $preferences->preferred_languages ?? ['English']; // Default
        $userSpecialties = $preferences->preferred_specialties ?? [];
        $priceRange = $preferences->preferred_price_range ?? ['min' => 0, 'max' => 9999];

        // 1. Candidate Pool (Verified + Active)
        $candidates = AstrologerProfile::where('is_verified', true)
            ->where('is_enabled', true)
            ->where('show_on_front', true)
            ->with(['user']) // Load relationship
            ->get();

        $scoredCandidates = $candidates->map(function ($astro) use ($userLanguages, $userSpecialties, $priceRange) {
            $score = 0;
            $reasons = [];

            // A. Language Match
            $astroLanguages = $astro->languages ?? [];
            $langIntersect = array_intersect($userLanguages, $astroLanguages);
            if (!empty($langIntersect)) {
                $score += $this->weights['weight_language'];
                $reasons[] = 'Speaks ' . implode(', ', array_slice($langIntersect, 0, 2));
            }

            // B. Specialty Match
            $astroSkills = $astro->skills ?? [];
            $skillIntersect = array_intersect($userSpecialties, $astroSkills);
            if (!empty($skillIntersect)) {
                $score += $this->weights['weight_specialty'];
                $reasons[] = 'Expert in ' . implode(', ', array_slice($skillIntersect, 0, 2));
            }

            // C. Rating (Normalized 0-5 -> 0-1)
            $ratingScore = ($astro->rating_avg / 5) * $this->weights['weight_rating'];
            $score += $ratingScore;
            if ($astro->rating_avg >= 4.5) {
                // Only add reason if it's a key factor? Or always?
                // $reasons[] = 'Top Rated';
            }

            // D. Availablity (Mock logic: Chat/Call enabled)
            if ($astro->is_chat_enabled || $astro->is_call_enabled) {
                $score += $this->weights['weight_availability'];
                $reasons[] = 'Available Now';
            }

            // E. Price Affinity
            $avgPrice = ($astro->call_per_minute + $astro->chat_per_session) / 2;
            if ($avgPrice >= $priceRange['min'] && $avgPrice <= $priceRange['max']) {
                $score += $this->weights['weight_price'];
                $reasons[] = 'Within Budget';
            }

            $astro->recommendation_score = $score;
            $astro->recommendation_reasons = array_slice($reasons, 0, 2); // Top 2 reasons

            return $astro;
        });

        // 2. Sort & Exploration
        $sorted = $scoredCandidates->sortByDesc('recommendation_score')->values();

        $explorationCount = (int) ceil($limit * ($this->weights['exploration_percent'] / 100));
        $topCount = $limit - $explorationCount;

        // Top N
        $topPicks = $sorted->take($topCount);

        // Random Exploration from the rest (if any)
        $others = $sorted->slice($topCount);
        $explorationPicks = $others->isNotEmpty() ? $others->random(min($others->count(), $explorationCount)) : collect();

        $final = $topPicks->merge($explorationPicks);
        // Shuffle slightly to mix exploration in? Or keep top at top?
        // Let's keep top at top for relevance, but maybe append exploration at end.

        return $final;
    }
}
