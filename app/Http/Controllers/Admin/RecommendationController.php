<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\RecommendationSetting;
use App\Models\User;
use App\Services\RecommendationService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

class RecommendationController extends Controller
{
    protected $recommendationService;

    public function __construct(RecommendationService $recommendationService)
    {
        $this->recommendationService = $recommendationService;
    }

    public function index()
    {
        $settings = RecommendationSetting::pluck('value_json', 'key')->toArray();
        $defaults = [
            'weight_language' => 30,
            'weight_specialty' => 25,
            'weight_rating' => 20,
            'weight_availability' => 15,
            'weight_price' => 10,
            'exploration_percent' => 20,
        ];

        $settings = array_merge($defaults, $settings);

        return view('admin.recommendations.index', compact('settings'));
    }

    public function update(Request $request)
    {
        $data = $request->except('_token');

        foreach ($data as $key => $value) {
            RecommendationSetting::updateOrCreate(
                ['key' => $key],
                ['value_json' => (int) $value]
            );
        }

        Cache::forget('recommendation_weights');

        return redirect()->route('admin.recommendations.index')->with('success', 'Settings updated.');
    }

    public function preview(Request $request)
    {
        $userId = $request->input('user_id');
        $previewData = null;

        if ($userId) {
            $user = User::find($userId);
            if ($user && $user->hasRole('User')) {
                $previewData = $this->recommendationService->getRecommendations($user, 10);
            }
        }

        return view('admin.recommendations.preview', compact('previewData', 'userId'));
    }
}
