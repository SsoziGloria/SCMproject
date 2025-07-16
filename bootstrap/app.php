<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__ . '/../routes/web.php',
        commands: __DIR__ . '/../routes/console.php',
        channels: __DIR__ . '/../routes/channels.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware) {
        // By defining the 'alias' array, you take responsibility for ALL aliases.
        // You must include the defaults you need (auth, guest, etc.) plus your own.
        $middleware->alias([
            // --- Default Aliases You Need ---
            'auth' => \Illuminate\Auth\Middleware\Authenticate::class,
            'guest' => \Illuminate\Auth\Middleware\RedirectIfAuthenticated::class,
            'verified' => \Illuminate\Auth\Middleware\EnsureEmailIsVerified::class,
            'throttle' => \Illuminate\Routing\Middleware\ThrottleRequests::class,
            'signed' => \Illuminate\Routing\Middleware\ValidateSignature::class,
            'can' => \Illuminate\Auth\Middleware\Authorize::class,

            // --- Your Custom Aliases ---
            'is_admin' => \App\Http\Middleware\IsAdmin::class,
            'role' => \App\Http\Middleware\CheckRole::class,
            'vendor.verified' => \App\Http\Middleware\EnsureVendorVerified::class,
        ]);

        // Note: For global middleware that runs on every request, you would use:
        // $middleware->web(append: [ ... ]);
        // $middleware->api(append: [ ... ]);
    })
    ->withExceptions(function (Exceptions $exceptions) {
        // ...
    })->create();

