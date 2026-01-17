<?php

namespace App\Http\Controllers;

use App\Http\Requests\VerifyPhoneRequest;
use App\Models\User;
use App\Services\FirebaseAuthService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Spatie\Permission\Models\Role;
use Illuminate\Validation\ValidationException;

class PhoneAuthController extends Controller
{
    public function show(): \Illuminate\View\View
    {
        return view('auth.phone');
    }

    public function verify(VerifyPhoneRequest $request, FirebaseAuthService $firebaseAuthService): JsonResponse|RedirectResponse
    {
        $data = $request->validated();

        try {
            $claims = $firebaseAuthService->verifyIdToken($data['firebase_id_token']);
        } catch (\Throwable $e) {
            Log::warning('Firebase ID token verification failed', ['exception' => $e]);
            return $this->errorResponse($request, 'Unable to verify the phone number. Please request a new code and try again.', 401);
        }

        $uid = $claims['uid'] ?? null;
        $phone = $this->normalizePhone($claims['phone_number'] ?? '');

        if (!$uid || !$phone) {
            return $this->errorResponse($request, 'Verification succeeded but no phone number was returned. Please try again.', 422);
        }

        $user = User::where('firebase_uid', $uid)
            ->orWhere('phone', $phone)
            ->first();

        if (!$user) {
            $user = User::create([
                'name' => $data['name'] ?? null,
                'phone' => $phone,
                'firebase_uid' => $uid,
            ]);
            $this->assignDefaultRole($user);
        } else {
            $updates = [];
            if (empty($user->firebase_uid)) {
                $updates['firebase_uid'] = $uid;
            }
            if (!empty($data['name']) && empty($user->name)) {
                $updates['name'] = $data['name'];
            }
            if (!empty($updates)) {
                $user->fill($updates)->save();
            }
            if ($user->getRoleNames()->isEmpty()) {
                $this->assignDefaultRole($user);
            }
        }

        Auth::login($user);
        $request->session()->regenerate();

        if ($request->expectsJson()) {
            return response()->json([
                'message' => 'Authenticated',
                'redirect' => $this->dashboardRoute($user),
            ]);
        }

        return redirect()->to($this->dashboardRoute($user));
    }

    public function loginWithEmail(Request $request): JsonResponse|RedirectResponse
    {
        $validated = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required', 'string'],
        ]);

        $email = strtolower($validated['email']);
        $password = $validated['password'];
        $user = User::where('email', $email)->first();

        if (!$user || empty($user->password) || !Hash::check($password, $user->password)) {
            throw ValidationException::withMessages([
                'email' => 'Invalid credentials.',
            ]);
        }

        if ($user->is_active === false) {
            throw ValidationException::withMessages([
                'email' => 'Your account is inactive.',
            ]);
        }

        Auth::login($user, $request->boolean('remember'));
        $request->session()->regenerate();

        if ($request->expectsJson()) {
            return response()->json([
                'message' => 'Authenticated',
                'redirect' => $this->dashboardRoute($user),
            ]);
        }

        return redirect()->to($this->dashboardRoute($user));
    }

    public function logout(Request $request): RedirectResponse
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('auth.phone.show');
    }

    protected function errorResponse(Request $request, string $message, int $status = 422): JsonResponse|RedirectResponse
    {
        if ($request->expectsJson()) {
            return response()->json([
                'message' => $message,
                'errors' => ['firebase_id_token' => [$message]],
            ], $status);
        }

        return back()->withInput()->withErrors(['firebase_id_token' => $message]);
    }

    protected function normalizePhone(string $phone): string
    {
        $clean = preg_replace('/\s+/', '', $phone);
        if ($clean && str_starts_with($clean, '+')) {
            return $clean;
        }

        return $clean ? '+' . ltrim($clean, '+') : '';
    }

    protected function dashboardRoute(User $user): string
    {
        if ($user->hasAnyRole(['Super Admin', 'Admin'])) {
            return route('admin.dashboard');
        }

        if ($user->hasRole('Astrologer')) {
            return route('astrologer.dashboard');
        }

        return route('user.dashboard');
    }

    protected function assignDefaultRole(User $user): void
    {
        if (!Role::where('name', 'User')->exists()) {
            return;
        }

        $user->assignRole('User');
    }
}
