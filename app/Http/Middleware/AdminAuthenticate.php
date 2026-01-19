<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class AdminAuthenticate
{
    private const ADMIN_ROLE_NAMES = [
        'Super Admin',
        'Admin',
        'Finance Admin',
        'Support Admin',
        'Ops Admin',
        'Moderator',
    ];

    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();
        if (!$user) {
            return redirect()->route('admin.login');
        }

        $hasRole = $user->roles()
            ->whereIn('name', self::ADMIN_ROLE_NAMES)
            ->exists();

        if (!$hasRole) {
            Auth::logout();
            $request->session()->invalidate();
            $request->session()->regenerateToken();
            return redirect()->route('admin.login')->withErrors(['login' => 'Admin access required.']);
        }

        if ($user->is_active === false) {
            Auth::logout();
            $request->session()->invalidate();
            $request->session()->regenerateToken();
            return redirect()->route('admin.login')->withErrors(['login' => 'Your account is inactive.']);
        }

        return $next($request);
    }
}
