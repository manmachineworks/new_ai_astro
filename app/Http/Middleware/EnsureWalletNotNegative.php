<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureWalletNotNegative
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        // Only check for authenticated users
        if ($user && $user->wallet_balance < 0) {
            // Log the anomaly
            \Log::critical('Negative wallet balance detected', [
                'user_id' => $user->id,
                'balance' => $user->wallet_balance,
                'route' => $request->route()->getName(),
            ]);

            // Force refresh from DB to prevent race condition artifacts
            $user->refresh();

            // If still negative, block the operation
            if ($user->wallet_balance < 0) {
                return response()->json([
                    'error' => 'Wallet integrity issue detected. Please contact support.'
                ], 500);
            }
        }

        return $next($request);
    }
}
