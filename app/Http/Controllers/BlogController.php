<?php

namespace App\Http\Controllers;

use App\Models\BlogPost;
use App\Models\BlogCategory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;

class BlogController extends Controller
{
    public function index(Request $request)
    {
        $locale = App::getLocale();
        $posts = BlogPost::where('locale', $locale)
            ->where('status', 'published')
            ->latest('published_at')
            ->paginate(9);

        $categories = BlogCategory::where('locale', $locale)->get();

        return view('blog.index', compact('posts', 'categories'));
    }

    public function show($slug)
    {
        // Slug is typically unique per locale, or global unique? Model has unique(['slug', 'locale']).
        // But URL usually doesn't include locale prefix in pure generic form unless prefix is used.
        // We handle locale via middleware or query param.
        // So we search for slug + current locale.

        $locale = App::getLocale();
        $post = BlogPost::where('slug', $slug)
            ->where('locale', $locale)
            ->where('status', 'published')
            ->firstOrFail();

        return view('blog.show', compact('post'));
    }

    public function category($slug)
    {
        $locale = App::getLocale();
        $category = BlogCategory::where('slug', $slug)
            ->where('locale', $locale) // Category slugs also localized?
            ->firstOrFail();

        $posts = $category->posts()
            ->where('locale', $locale)
            ->where('status', 'published')
            ->latest('published_at')
            ->paginate(9);

        $categories = BlogCategory::where('locale', $locale)->get();

        return view('blog.index', compact('posts', 'category', 'categories'));
    }
}
