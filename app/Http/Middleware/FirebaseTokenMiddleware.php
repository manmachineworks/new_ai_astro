<?php

namespace App\Http\Middleware;

use App\Models\User;
use App\Services\FirebaseAuthService;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Spatie\Permission\Models\Role;

class FirebaseTokenMiddleware
{
    public function handle(Request $request, Closure $next)
    {
        if (Auth::check() || Auth::guard('sanctum')->check() || Auth::guard('web')->check()) {
            return $next($request);
        }

        $token = $this->extractToken($request->header('Authorization'));
        if (!$token) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        try {
            /** @var FirebaseAuthService $firebase */
            $firebase = app(FirebaseAuthService::class);
            $claims = $firebase->verifyIdToken($token);
        } catch (\Throwable $e) {
            return response()->json(['error' => 'Invalid token'], 401);
        }

        $uid = $claims['uid'] ?? null;
        $phone = $this->normalizePhone($claims['phone_number'] ?? '');

        if (!$uid && !$phone) {
            return response()->json(['error' => 'Invalid token claims'], 401);
        }

        $user = User::query()
            ->when($uid, fn($q) => $q->orWhere('firebase_uid', $uid))
            ->when($phone, fn($q) => $q->orWhere('phone', $phone))
            ->first();

        if (!$user) {
            $user = User::create([
                'phone' => $phone,
                'firebase_uid' => $uid,
            ]);
            $this->assignDefaultRole($user);
        } else {
            $updates = [];
            if ($uid && empty($user->firebase_uid)) {
                $updates['firebase_uid'] = $uid;
            }
            if ($phone && empty($user->phone)) {
                $updates['phone'] = $phone;
            }
            if (!empty($updates)) {
                $user->fill($updates)->save();
            }
            if ($user->getRoleNames()->isEmpty()) {
                $this->assignDefaultRole($user);
            }
        }

        if ($user->is_active === false) {
            return response()->json(['error' => 'Account inactive'], 403);
        }

        if ($this->isBlocked($user)) {
            return response()->json(['error' => 'Account blocked'], 403);
        }

        Auth::login($user);
        $request->setUserResolver(fn() => $user);

        return $next($request);
    }

    protected function extractToken(?string $header): ?string
    {
        if (!$header) {
            return null;
        }

        if (stripos($header, 'Bearer ') === 0) {
            return trim(substr($header, 7));
        }

        return null;
    }

    protected function normalizePhone(string $phone): string
    {
        $clean = preg_replace('/\s+/', '', $phone);
        if ($clean && str_starts_with($clean, '+')) {
            return $clean;
        }

        return $clean ? '+' . ltrim($clean, '+') : '';
    }

    protected function assignDefaultRole(User $user): void
    {
        if (!Role::where('name', 'User')->exists()) {
            return;
        }

        $user->assignRole('User');
    }

    protected function isBlocked(User $user): bool
    {
        if (!$user->blocked_until) {
            return false;
        }

        return $user->blocked_until->isFuture();
    }
}
