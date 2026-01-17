<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Cookie;
use Symfony\Component\HttpFoundation\Response;

class SetLocale
{
    /**
     * Handle an incoming request.
     *
     * Priority: Query param → User preference → Cookie → Session → Fallback
     */
    public function handle(Request $request, Closure $next): Response
    {
        $availableLocales = ['en', 'hi'];
        $locale = 'en'; // Fallback

        // 1. Check query parameter (highest priority)
        if ($request->has('lang') && in_array($request->lang, $availableLocales)) {
            $locale = $request->lang;
        }
        // 2. Check authenticated user preference
        elseif ($request->user() && in_array($request->user()->preferred_locale ?? 'en', $availableLocales)) {
            $locale = $request->user()->preferred_locale;
        }
        // 3. Check cookie
        elseif ($request->cookie('locale') && in_array($request->cookie('locale'), $availableLocales)) {
            $locale = $request->cookie('locale');
        }
        // 4. Check session
        elseif ($request->session()->has('locale') && in_array($request->session()->get('locale'), $availableLocales)) {
            $locale = $request->session()->get('locale');
        }

        // Set application locale
        App::setLocale($locale);

        // Store in session
        $request->session()->put('locale', $locale);

        $response = $next($request);

        // Set cookie (1 year expiry)
        return $response->withCookie(cookie('locale', $locale, 525600, '/', null, false, false));
    }
}
