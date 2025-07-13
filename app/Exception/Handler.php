<?php

namespace App\Exceptions;

use Exception;
use Illuminate\Http\Client\ConnectionException;
use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Throwable;

class Handler extends ExceptionHandler
{
    // ... other code ...

    public function render($request, Throwable $exception)
    {
        // Handle HTTP client connection issues (Java service unavailable)
        if ($exception instanceof ConnectionException) {
            if ($request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Validation service is currently unavailable. Please try again later.'
                ], 503);
            }

            return redirect()->back()->with('error', 'Validation service is currently unavailable. Please try again later.');
        }

        return parent::render($request, $exception);
    }
}