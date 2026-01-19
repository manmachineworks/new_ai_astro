<?php

namespace App\Http\Controllers;

use App\Models\CmsPage;
use App\Models\BlogPost;
use App\Models\AstrologerProfile;
use Illuminate\Http\Response;
use Illuminate\Support\Carbon;

class SitemapController extends Controller
{
    public function index()
    {
        $urls = [];

        // 1. Static Pages
        // Home
        $urls[] = [
            'loc' => route('home'),
            'lastmod' => Carbon::now()->toAtomString(),
            'priority' => '1.0',
            'changefreq' => 'daily',
        ];

        // About, Contact (Example static routes, verify if they exist or just add generically)
        // Ideally should check if route exists, but for now we assume common structure or pull from CMS
        // If they are CMS pages, they will be covered below.

        // 2. CMS Pages
        $cmsPages = CmsPage::where('status', 'published')
            ->whereNotNull('slug')
            ->get();

        foreach ($cmsPages as $page) {
            $urls[] = [
                'loc' => route('cms.page', $page->slug),
                'lastmod' => $page->updated_at->toAtomString(),
                'priority' => '0.8',
                'changefreq' => 'weekly',
            ];
        }

        // 3. Blog Posts
        $posts = BlogPost::where('status', 'published')
            ->whereNotNull('published_at')
            ->get();

        foreach ($posts as $post) {
            $urls[] = [
                'loc' => route('blog.show', $post->slug),
                'lastmod' => $post->updated_at->toAtomString(),
                'priority' => '0.7',
                'changefreq' => 'weekly',
            ];
        }

        // 4. Astrologers
        // Assuming we have a public route for astrologer profile 'astrologers.public_show'
        $astrologers = AstrologerProfile::where('verification_status', 'verified')
            ->where('visibility', 1)
            ->where('is_enabled', 1)
            ->with('user') // to ensure user link logic if needed, but ID is enough usually
            ->get();

        foreach ($astrologers as $astro) {
            $urls[] = [
                'loc' => route('astrologers.public_show', $astro->id), // Or slug if you have it
                'lastmod' => $astro->updated_at->toAtomString(),
                'priority' => '0.9',
                'changefreq' => 'daily', // Status changes often
            ];
        }

        return response()->view('seo.sitemap', compact('urls'))
            ->header('Content-Type', 'text/xml');
    }

    public function robots()
    {
        $content = "User-agent: *\n";
        $content .= "Disallow: /admin/\n";
        $content .= "Disallow: /user/\n"; // Maybe allow some? usually dashboard is private
        $content .= "Disallow: /astrologer/\n";
        $content .= "\n";
        $content .= "Sitemap: " . route('sitemap.xml');

        return response($content, 200)
            ->header('Content-Type', 'text/plain');
    }
}
