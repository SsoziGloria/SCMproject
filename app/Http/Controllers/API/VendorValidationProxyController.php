<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Vendor;
use App\Models\VendorValidation;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;

class VendorValidationProxyController extends Controller
{
    /**
     * Validate an existing vendor document
     */
    public function validateExistingDocument($vendorId)
    {
        try {
            // Find the vendor
            $vendor = Vendor::findOrFail($vendorId);

            // Make sure vendor has a PDF
            if (!$vendor->pdf_path || !Storage::disk('public')->exists($vendor->pdf_path)) {
                return response()->json([
                    'valid' => false,
                    'message' => 'No document found for this vendor'
                ], 404);
            }

            // Get file content and info
            $fileContent = Storage::disk('public')->get($vendor->pdf_path);
            $filename = basename($vendor->pdf_path);

            // Java validation service URL
            $javaUrl = config('services.vendor_validation.url', 'http://localhost:8080');

            // Make request to Java validation service
            $response = Http::timeout(30)
                ->attach('file', $fileContent, $filename)
                ->post($javaUrl . '/api/v1/vendor/validate', [
                    'vendor_id' => $vendor->vendor_id
                ]);

            // Get the validation result
            $validationResult = $response->json();

            // Store validation result in database
            VendorValidation::create([
                'vendor_id' => $vendor->vendor_id,
                'is_valid' => $validationResult['valid'] ?? false,
                'validation_message' => $validationResult['message'] ?? 'Validation completed',
                'validation_details' => $validationResult['validationResults'] ?? null,
                'original_filename' => $filename,
                'file_path' => $vendor->pdf_path
            ]);

            // Return the validation result
            return response()->json($validationResult);

        } catch (\Exception $e) {
            Log::error('Vendor document validation failed: ' . $e->getMessage());

            return response()->json([
                'valid' => false,
                'message' => 'Validation service error: ' . $e->getMessage()
            ], 500);
        }
    }
}