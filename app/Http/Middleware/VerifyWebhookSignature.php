<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class VerifyWebhookSignature
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     * @param  string|null $provider (callerdesk, phonepe, etc.)
     */
    public function handle(Request $request, Closure $next, ?string $provider = null): Response
    {
        if (app()->environment('local', 'testing')) {
            // Optional: Skip verification in local dev if configured
            // return $next($request);
        }

        if ($provider === 'phonepe') {
            if (!$this->verifyPhonePe($request)) {
                return response()->json(['error' => 'Invalid Signature'], 401);
            }
        } elseif ($provider === 'callerdesk') {
            if (!$this->verifyCallerDesk($request)) {
                return response()->json(['error' => 'Invalid Signature'], 401);
            }
        }

        return $next($request);
    }

    protected function verifyPhonePe(Request $request): bool
    {
        // Header: X-VERIFY
        $signature = $request->header('X-VERIFY');
        $payload = $request->input('response'); // PhonePe sends base64 payload

        if (!$signature || !$payload) {
            // Some callbacks might differ, customize as needed
            return false;
        }

        // Verification Logic: SHA256(payload + salt) + ### + SALT_INDEX
        // $saltKey = config('services.phonepe.salt_key');
        // $saltIndex = config('services.phonepe.salt_index');
        // $calculated = hash('sha256', $payload . "/pg/v1/status" . $saltKey) . "###" . $saltIndex;
        // return $signature === $calculated;

        return true; // Placeholder for exact logic implementation
    }

    protected function verifyCallerDesk(Request $request): bool
    {
        // CallerDesk might use IP allowlisting or a secret token in header
        // $secret = config('services.callerdesk.webhook_secret');
        // return $request->header('X-CallerDesk-Token') === $secret;

        return true; // Placeholder
    }
}
