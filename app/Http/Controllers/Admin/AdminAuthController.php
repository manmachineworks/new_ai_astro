<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;

class AdminAuthController extends Controller
{
    public function showLogin(Request $request)
    {
        if ($request->user()?->hasAnyRole(['Super Admin', 'Admin'])) {
            return redirect()->route('admin.dashboard');
        }

        return view('admin.auth.login');
    }

    public function login(Request $request)
    {
        $validated = $request->validate([
            'login' => ['required', 'string'],
            'password' => ['required', 'string'],
        ]);

        $login = $validated['login'];
        $field = filter_var($login, FILTER_VALIDATE_EMAIL) ? 'email' : 'phone';

        if (!Auth::attempt([$field => $login, 'password' => $validated['password']], $request->boolean('remember'))) {
            throw ValidationException::withMessages([
                'login' => 'Invalid credentials.',
            ]);
        }

        $request->session()->regenerate();
        $user = $request->user();

        if (!$user || !$user->hasAnyRole(['Super Admin', 'Admin'])) {
            Auth::logout();
            return back()->withErrors(['login' => 'You do not have admin access.']);
        }

        if (!$user->is_active) {
            Auth::logout();
            return back()->withErrors(['login' => 'Your account is inactive.']);
        }

        return redirect()->route('admin.dashboard');
    }

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('admin.login');
    }
}
