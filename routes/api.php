<?php

use Illuminate\Support\Facades\Route;

use App\Http\Controllers\API\VendorValidationAPIController;

// Vendor validation routes
Route::prefix('vendor-validation')->group(function () {
    // Upload and validate document
    Route::post('/validate', [VendorValidationAPIController::class, 'validateDocument'])
        ->name('vendor.validate');

    // Get validation history for a vendor
    Route::get('/vendor/{vendorId}/history', [VendorValidationAPIController::class, 'getValidationHistory'])
        ->name('vendor.validation.history');

    // Get specific validation details
    Route::get('/validation/{validationId}', [VendorValidationAPIController::class, 'getValidationDetails'])
        ->name('vendor.validation.details');

    // Revalidate existing document
    Route::post('/validation/{validationId}/revalidate', [VendorValidationAPIController::class, 'revalidateDocument'])
        ->name('vendor.validation.revalidate');

    // Health check for validation service
    Route::prefix('vendor-validation')->group(function () {
        Route::get('/health', [VendorValidationAPIController::class, 'health']);
        Route::post('/validate', [VendorValidationAPIController::class, 'validate']);
        // Other routes
    });
});

// Alternative RESTful approach (if you prefer)
Route::apiResource('vendors.validations', VendorValidationAPIController::class)
    ->except(['update', 'destroy']);

