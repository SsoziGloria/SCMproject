<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Vendor;
use App\Models\VendorValidation;
use App\Services\VendorValidationService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;
use Exception;

class VendorValidationAPIController extends Controller
{
    protected $validationService;

    public function __construct(VendorValidationService $validationService)
    {
        $this->validationService = $validationService;
    }

    // In app/Http/Controllers/API/VendorValidationAPIController.php

    public function validateDocument(Request $request)
    {
        Log::info('--- Validation Request Started ---');

        $request->validate([
            'vendor_id' => 'required|exists:vendors,vendor_id',
            'file' => 'required|file|mimes:pdf|max:10240',
        ]);

        $vendor = Vendor::findOrFail($request->vendor_id);
        $file = $request->file('file');

        $path = $file->store('uploads', config('vendor_validation.storage_disk'));
        $fullPath = Storage::disk(config('vendor_validation.storage_disk'))->path($path);

        $validationResult = $this->validationService->validateDocument($fullPath, $vendor->vendor_id);

        Log::info('Response received from ValidationService.', ['result' => $validationResult]);

        // --- Start of Corrected Logic ---

        $validation = new VendorValidation();
        $validation->vendor_id = $vendor->vendor_id;
        $validation->file_path = $path;
        $validation->file_size = $file->getSize();
        $validation->original_filename = $file->getClientOriginalName();

        // FIX #1: Check the 'valid' key for business logic result, not the 'success' key.
        $validation->is_valid = $validationResult['valid'] ?? false;

        $validation->validation_message = $validationResult['message'] ?? 'No message from service';

        // FIX #2: Look for the 'validationResults' (camelCase) key from the Java JSON response.
        // Assign it to your 'validate_results' (snake_case) model property/database column.
        $validation->validation_results = $validationResult['validationResults'] ?? null;

        $validation->save();

        // --- End of Corrected Logic ---

        Log::info('--- Validation Request Finished. ---');

        return response()->json($validationResult);
    }


    // In app/Http/Controllers/API/VendorValidationAPIController.php

    // In app/Http/Controllers/API/VendorValidationAPIController.php

    public function health()
    {
        \Log::info('Checking Java service health');
        try {
            // 1. Get the raw response from the Java service
            $javaResponse = $this->validationService->checkHealth();
            \Log::info('Health check result', $javaResponse);

            // 2. Safely get the status from the Java response.
            //    The '??' operator provides a default 'DOWN' value if the 'status' key doesn't exist.
            $status = $javaResponse['status'] ?? 'DOWN';

            // 3. Build the new, consistent JSON response for the frontend
            return response()->json([
                'validation_service_status' => $status, // This is the key the frontend is looking for
                'source_response' => $javaResponse      // It's good practice to include the original response for debugging
            ]);

        } catch (\Exception $e) {
            \Log::error('Health check error: ' . $e->getMessage());
            return response()->json([
                'validation_service_status' => 'DOWN', // Ensure this key is present even on error
                'error' => $e->getMessage()
            ], 500);
        }
    }



    public function history($vendorId)
    {
        $validations = VendorValidation::where('vendor_id', $vendorId)
            ->orderBy('created_at', 'desc')
            ->get();

        return response()->json(['validations' => $validations]);
    }

    public function show($id)
    {
        $validation = VendorValidation::findOrFail($id);
        return response()->json(['validation' => $validation]);
    }

    // In app/Http/Controllers/API/VendorValidationAPIController.php
    public function revalidate($id)
    {
        $validation = VendorValidation::findOrFail($id);
        // You don't need to find the vendor again unless you need it for the service call

        if (!Storage::disk(config('vendor_validation.storage_disk'))->exists($validation->file_path)) {
            return response()->json([
                'success' => false,
                'message' => 'Original document no longer exists'
            ], 404);
        }

        $fullPath = Storage::disk(config('vendor_validation.storage_disk'))->path($validation->file_path);

        // Call Java validation service
        $validationResult = $this->validationService->validateDocument($fullPath, $validation->vendor_id);

        // --- Start of Corrected Logic ---

        // FIX #1: Check the 'valid' key, not 'success'.
        $validation->is_valid = $validationResult['valid'] ?? false;

        $validation->validation_message = $validationResult['message'] ?? 'No message from service';

        // FIX #2: Use the correct database column 'validate_results' and JSON key 'validationResults'.
        $validation->validation_results = $validationResult['validationResults'] ?? null;

        $validation->validated_at = now(); // Update the timestamp

        $validation->save();

        // --- End of Corrected Logic ---

        return response()->json($validationResult);
    }

}