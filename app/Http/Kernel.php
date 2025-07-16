<?php

namespace App\Http;

class Kernel
{
    protected $routeMiddleware = [
        'is_admin' => \App\Http\Middleware\IsAdmin::class,
        'role' => \App\Http\Middleware\CheckRole::class,
        'vendor.verified' => \App\Http\Middleware\EnsureVendorVerified::class,
    ];
}
