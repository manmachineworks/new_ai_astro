<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;

class AuthController extends Controller
{
    public function userLoginView()
    {
        return view('auth.login');
    }

    public function adminLoginView()
    {
        return view('auth.login');
    }

    public function astrologerLoginView()
    {
        return view('auth.login');
    }

    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required'],
        ]);

        if (Auth::attempt($credentials)) {
            $request->session()->regenerate();

            $user = Auth::user();

            // Check if user is trying to access correct portal
            // This is basic; you might want to force redirect to correct dashboard regardless of login page

            return $this->redirectBasedOnRole($user);
        }

        throw ValidationException::withMessages([
            'email' => __('auth.failed'),
        ]);
    }

    public function logout(Request $request)
    {
        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/');
    }

    protected function redirectBasedOnRole($user)
    {
        if ($user->role === 'admin') {
            return redirect()->route('admin.dashboard');
        } elseif ($user->role === 'astrologer') {
            return redirect()->route('astrologer.dashboard');
        } else {
            return redirect()->route('dashboard');
        }
    }
}
