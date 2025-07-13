<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Http;
use Exception;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
*/

Route::get('/service-health/vendor-validation', function () {
    try {
        $javaUrl = config('services.vendor_validation.url', 'http://localhost:8080');
        $response = Http::timeout(5)->get($javaUrl . '/api/v1/vendor/health');
        return response()->json(['status' => $response->json('status', 'DOWN')]);
    } catch (Exception $e) {
        // Log the error for debugging
        \Illuminate\Support\Facades\Log::error('Health check proxy failed: ' . $e->getMessage());
        return response()->json(['status' => 'DOWN', 'error' => 'Service unavailable'], 503);
    }
});
