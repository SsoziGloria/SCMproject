<?php

namespace App\Http;

class Kernel
{
    protected $routeMiddleware = [
        'is_admin' => \App\Http\Middleware\IsAdmin::class,
    ];
}
