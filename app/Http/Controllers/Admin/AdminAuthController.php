<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

class AdminAuthController extends Controller
{
    public function showLogin(Request $request)
    {
        $user = $request->user();
        if ($user) {
            if ($user->hasAnyRole(['Super Admin', 'Admin'])) {
                return redirect()->route('admin.dashboard');
            }

            Auth::logout();
            $request->session()->invalidate();
            $request->session()->regenerateToken();
        }

        return view('admin.auth.login');
    }

    public function login(Request $request)
    {
        $validated = $request->validate([
            'login' => ['required', 'string'],
            'password' => ['required', 'string'],
        ]);

        $login = trim($validated['login']);
        $password = $validated['password'];
        $user = $this->resolveLoginUser($login);

        if (!$user || empty($user->password) || !Hash::check($password, $user->password)) {
            throw ValidationException::withMessages([
                'login' => 'Invalid credentials.',
            ]);
        }

        if (!$user->hasAnyRole(['Super Admin', 'Admin'])) {
            throw ValidationException::withMessages([
                'login' => 'You do not have admin access.',
            ]);
        }

        if ($user->is_active === false) {
            throw ValidationException::withMessages([
                'login' => 'Your account is inactive.',
            ]);
        }

        Auth::login($user, $request->boolean('remember'));

        $request->session()->regenerate();

        return redirect()->route('admin.dashboard');
    }

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('admin.login');
    }

    protected function resolveLoginUser(string $login): ?User
    {
        if (filter_var($login, FILTER_VALIDATE_EMAIL)) {
            return User::where('email', strtolower($login))->first();
        }

        $normalized = preg_replace('/\s+/', '', $login);
        $variants = array_values(array_unique([
            $normalized,
            ltrim($normalized, '+'),
            '+' . ltrim($normalized, '+'),
        ]));

        return User::whereIn('phone', $variants)->first();
    }
}
