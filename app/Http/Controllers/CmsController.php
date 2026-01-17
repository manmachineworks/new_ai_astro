<?php

namespace App\Http\Controllers;

use App\Models\CmsPage;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Cache;

class CmsController extends Controller
{
    public function show($slug)
    {
        $locale = App::getLocale();
        $cacheKey = "cms_page_{$slug}_{$locale}";

        $page = Cache::remember($cacheKey, 60 * 60, function () use ($slug, $locale) {
            return CmsPage::where('slug', $slug)
                ->where('locale', $locale)
                ->where('status', 'published')
                ->first();
        });

        if (!$page) {
            // Fallback to English? Or 404.
            // Requirement says "cache published cms_pages... invalidate on update" (invalidation not fully implemented yet in Admin, but cache time is set).
            // Let's check EN fallback if desired, but SEO usually prefers strict URL content.
            // If /pages/{slug} is requested, we expect content.
            abort(404);
        }

        return view('cms.page', compact('page'));
    }
}
