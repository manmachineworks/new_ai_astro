<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class CorrelationIdMiddleware
{
    public function handle(Request $request, Closure $next)
    {
        $correlationId = $request->header('X-Correlation-ID') ?? (string) Str::uuid();

        // Store in Request/Container for easy access
        $request->headers->set('X-Correlation-ID', $correlationId);

        // Optionally bind to a singleton context for Processors
        app()->instance('correlation_id', $correlationId);

        $response = $next($request);

        $response->headers->set('X-Correlation-ID', $correlationId);

        return $response;
    }
}
