<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Cookie;
use Symfony\Component\HttpFoundation\Response;

class SetLocale
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $locale = config('app.locale'); // Default fallback

        // 1. Query Parameter (?lang=hi)
        if ($request->has('lang')) {
            $lang = $request->query('lang');
            if (in_array($lang, ['en', 'hi'])) {
                $locale = $lang;
                Session::put('locale', $locale);
                // We'll let the controller handle cookie setting to keep middleware clean,
                // or we could set it on the response, but query param usually implies a switch action or temporary override.
            }
        }
        // 2. User Preference (if logged in)
        elseif (auth()->check() && auth()->user()->preferred_locale) {
            $locale = auth()->user()->preferred_locale;
        }
        // 3. Session
        elseif (Session::has('locale')) {
            $locale = Session::get('locale');
        }
        // 4. Cookie
        elseif ($request->hasCookie('locale')) {
            $locale = $request->cookie('locale');
        }

        // Validate allowed locales
        if (!in_array($locale, ['en', 'hi'])) {
            $locale = config('app.fallback_locale', 'en');
        }

        App::setLocale($locale);

        return $next($request);
    }
}
