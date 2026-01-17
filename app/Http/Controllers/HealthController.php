<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class HealthController extends Controller
{
    /**
     * Basic health check
     */
    public function index()
    {
        return response()->json([
            'status' => 'ok',
            'timestamp' => now()->toIso8601String(),
            'environment' => app()->environment(),
        ]);
    }

    /**
     * Database connectivity check
     */
    public function database()
    {
        try {
            \DB::connection()->getPdo();
            $migrationsUpToDate = \Artisan::call('migrate:status') === 0;

            return response()->json([
                'status' => 'ok',
                'database' => 'connected',
                'migrations' => $migrationsUpToDate ? 'up-to-date' : 'pending',
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'database' => 'disconnected',
                'message' => $e->getMessage(),
            ], 503);
        }
    }

    /**
     * Redis/Queue connectivity check
     */
    public function queue()
    {
        try {
            \Redis::connection()->ping();

            return response()->json([
                'status' => 'ok',
                'redis' => 'connected',
                'queue_driver' => config('queue.default'),
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'redis' => 'disconnected',
                'message' => $e->getMessage(),
            ], 503);
        }
    }
}
