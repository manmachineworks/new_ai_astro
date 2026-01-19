<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__ . '/../routes/web.php',
        api: __DIR__ . '/../routes/api.php',
        commands: __DIR__ . '/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        $middleware->alias([
            'admin.auth' => \App\Http\Middleware\AdminAuthenticate::class,
            'firebase.auth' => \App\Http\Middleware\FirebaseTokenMiddleware::class,
            'role' => \Spatie\Permission\Middleware\RoleMiddleware::class,
            'permission' => \Spatie\Permission\Middleware\PermissionMiddleware::class,
            'role_or_permission' => \Spatie\Permission\Middleware\RoleOrPermissionMiddleware::class,
            'wallet.check' => \App\Http\Middleware\EnsureWalletNotNegative::class,
        ]);

        $middleware->web(append: [
            \App\Http\Middleware\SecureHeaders::class,
            \App\Http\Middleware\SetLocale::class,
        ]);

        $middleware->validateCsrfTokens(except: [
            'webhooks/*', // Exclude all webhooks from CSRF
        ]);

        // Enable Stateful Sanctum for SPA-like calls from Web
        $middleware->api(prepend: [
            \Laravel\Sanctum\Http\Middleware\EnsureFrontendRequestsAreStateful::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        //
    })->create();
