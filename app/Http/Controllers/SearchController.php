<?php

namespace App\Http\Controllers;

use App\Models\AstrologerProfile;
use App\Models\UserEvent;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class SearchController extends Controller
{
    protected $synonyms = [
        'job' => ['career', 'business', 'job', 'work'],
        'career' => ['career', 'business', 'job', 'work'],
        'love' => ['love', 'relationship', 'marriage', 'divorce'],
        'marriage' => ['marriage', 'love', 'relationship'],
        'finance' => ['finance', 'money', 'wealth', 'investment'],
        'health' => ['health', 'wellness', 'fitness'],
    ];

    public function search(Request $request)
    {
        $query = $request->input('q');

        // Log search event
        if (auth()->check()) {
            UserEvent::create([
                'user_id' => auth()->id(),
                'event_type' => 'search',
                'meta_json' => ['query' => $query]
            ]);
        }

        $keywords = $this->expandKeywords($query);

        $results = AstrologerProfile::where('is_verified', true)
            ->where('show_on_front', true)
            ->where(function ($q) use ($keywords, $query) {
                // Name match
                $q->whereHas('user', function ($uq) use ($query) {
                    $uq->where('name', 'like', "%{$query}%");
                });

                // Skill/Bio match
                foreach ($keywords as $word) {
                    $q->orWhereJsonContains('skills', $word)
                        ->orWhere('bio', 'like', "%{$word}%");
                }
            })
            ->with('user')
            ->limit(20)
            ->get();

        return response()->json($results);
    }

    public function ask(Request $request)
    {
        $question = $request->input('question');
        $keywords = $this->expandKeywords($question);

        // Find astrologers matching ANY keyword
        $astrologers = AstrologerProfile::where('is_verified', true)
            ->where('show_on_front', true)
            ->where(function ($q) use ($keywords) {
                foreach ($keywords as $word) {
                    $q->orWhereJsonContains('skills', $word);
                }
            })
            ->with('user')
            ->limit(5)
            ->get();

        // Mock reason tags
        $astrologers->transform(function ($astro) use ($keywords) {
            // Find which keyword matched
            $matches = array_intersect($astro->skills ?? [], $keywords);
            $astro->match_reason = !empty($matches) ? "Expert in " . reset($matches) : "General Expert";
            return $astro;
        });

        return response()->json([
            'answer' => "Based on your question about '" . implode(' ', array_slice($keywords, 0, 2)) . "', we recommend these experts:",
            'recommendations' => $astrologers
        ]);
    }

    protected function expandKeywords($input)
    {
        if (!$input)
            return [];

        $words = explode(' ', strtolower($input));
        $expanded = [];

        foreach ($words as $word) {
            $expanded[] = $word;
            if (isset($this->synonyms[$word])) {
                $expanded = array_merge($expanded, $this->synonyms[$word]);
            }
        }

        return array_unique($expanded);
    }
}
