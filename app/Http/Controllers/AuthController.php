<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;

use App\Models\User;
use App\Services\FirebaseService;

class AuthController extends Controller
{
    protected $firebaseService;

    public function __construct(FirebaseService $firebaseService)
    {
        $this->firebaseService = $firebaseService;
    }

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

            return $this->redirectBasedOnRole($user);
        }

        throw ValidationException::withMessages([
            'email' => __('auth.failed'),
        ]);
    }

    public function loginWithFirebase(Request $request)
    {
        $request->validate([
            'id_token' => 'required|string',
        ]);

        $verifiedData = $this->firebaseService->verifyToken($request->id_token);

        if (!$verifiedData) {
            return response()->json(['error' => 'Invalid Token'], 401);
        }

        $uid = $verifiedData['uid'];
        $phone = $verifiedData['phone_number'];

        // Find or create user
        $user = User::where('firebase_uid', $uid)
            ->orWhere('phone', $phone)
            ->first();

        if (!$user) {
            $user = User::create([
                'name' => 'User ' . substr($phone, -4), // Default name
                'email' => null, // Email is optional for phone auth initially
                'password' => bcrypt(str()->random(16)), // Random password
                'phone' => $phone,
                'firebase_uid' => $uid,
                'role' => 'user',
                'phone_verified_at' => now(),
                'is_active' => true,
            ]);
        } else {
            // Update firebase_uid if missing (e.g. if matched by phone)
            if (!$user->firebase_uid) {
                $user->update(['firebase_uid' => $uid, 'phone_verified_at' => now()]);
            }
        }

        Auth::login($user);
        $request->session()->regenerate();

        return response()->json([
            'success' => true,
            'redirect_url' => route('dashboard')
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
