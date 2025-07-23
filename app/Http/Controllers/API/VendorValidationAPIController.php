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
use Illuminate\Support\Facades\DB;

class VendorValidationAPIController extends Controller
{
    protected $validationService;

    public function __construct(VendorValidationService $validationService)
    {
        $this->validationService = $validationService;
    }


    public function validateDocument(Request $request)
    {
        $request->validate([
            'vendor_id' => 'required|exists:vendors,vendor_id',
            'file' => 'required|file|mimes:pdf|max:10240',
        ]);

        $vendor = Vendor::findOrFail($request->vendor_id);
        $file = $request->file('file');


        // Store the uploaded file
        $path = $file->store('uploads', config('vendor_validation.storage_disk'));
        $fullPath = Storage::disk(config('vendor_validation.storage_disk'))->path($path);

        $validationResult = $this->validationService->validateDocument($fullPath, $vendor->vendor_id);

        // Record validation result
        $validation = new VendorValidation();
        $validation->vendor_id = $vendor->vendor_id;
        $validation->file_path = $path;
        $validation->file_size = $file->getSize();
        $validation->original_filename = $file->getClientOriginalName();

        $validation->is_valid = $validationResult['valid'] ?? false;

        $validation->validation_message = $validationResult['message'] ?? 'No message from service';

        $validation->validation_results = $validationResult['validationResults'] ?? null;

        $validation->save();

        if (!$validation->is_valid) {
            Log::warning('Validation failed for vendor ' . $vendor->vendor_id, [
                'validation_id' => $validation->id,
                'message' => $validation->validation_message,
                'results' => $validation->validation_results
            ]);
        } else {
            Log::info('Validation successful for vendor ' . $vendor->vendor_id, [
                'validation_id' => $validation->id,
                'results' => $validation->validation_results
            ]);
        }

        if ($validation->is_valid === 1) {
            DB::table('vendors')
                ->where('vendor_id', $validation->vendor_id)
                ->update([
                    'validation_status' => 'Approved',
                    'certification_status' => 'Certified',
                    'compliance_status' => 'Compliant'
                ]);

            $supplier = DB::table('suppliers')->where('supplier_id', $vendor->supplier_id)->first();
            if ($supplier) {
                DB::table('suppliers')
                    ->where('supplier_id', $vendor->supplier_id)
                    ->update([
                        'status' => 'active'
                    ]);
            }
        } else {
            DB::table('vendors')
                ->where('vendor_id', $validation->vendor_id)
                ->update([
                    'validation_status' => 'Rejected'
                ]);
        }

        return response()->json($validationResult);
    }
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
        } catch (Exception $e) {
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


    public function revalidate($id)
    {
        $validation = VendorValidation::findOrFail($id);
        $vendor = Vendor::findOrFail($validation->vendor_id);


        if (!Storage::disk(config('vendor_validation.storage_disk'))->exists($validation->file_path)) {
            return response()->json([
                'success' => false,
                'message' => 'Original document no longer exists'
            ], 404);
        }

        $fullPath = Storage::disk(config('vendor_validation.storage_disk'))->path($validation->file_path);

        // Call Java validation service
        $validationResult = $this->validationService->validateDocument($fullPath, $validation->vendor_id);

        $validation->is_valid = $validationResult['valid'] ?? false;

        $validation->validation_message = $validationResult['message'] ?? 'No message from service';

        $validation->validation_results = $validationResult['validationResults'] ?? null;

        $validation->validated_at = now(); // Update the timestamp

        $validation->save();

        if (!$validation->is_valid) {
            Log::warning('Validation failed for vendor ' . $vendor->vendor_id, [
                'validation_id' => $validation->id,
                'message' => $validation->validation_message,
                'results' => $validation->validation_results
            ]);
        } else {
            Log::info('Validation successful for vendor ' . $vendor->vendor_id, [
                'validation_id' => $validation->id,
                'results' => $validation->validation_results
            ]);
        }

        if ($validation->is_valid === 1) {
            DB::table('vendors')
                ->where('vendor_id', $validation->vendor_id)
                ->update([
                    'validation_status' => 'Approved',
                    'certification_status' => 'Certified',
                    'compliance_status' => 'Compliant'
                ]);

            $supplier = DB::table('suppliers')->where('supplier_id', $vendor->supplier_id)->first();
            if ($supplier) {
                DB::table('suppliers')
                    ->where('supplier_id', $vendor->supplier_id)
                    ->update([
                        'status' => 'active'
                    ]);
            }
        } else {
            DB::table('vendors')
                ->where('vendor_id', $validation->vendor_id)
                ->update([
                    'validation_status' => 'Rejected'
                ]);
        }

        return response()->json($validationResult);
    }

    /**
     * Validates a document that already exists for a given vendor.
     *
     * @param \App\Models\Vendor $vendor
     * @return \Illuminate\Http\JsonResponse
     */
    public function validateExistingDocument(Vendor $vendor)
    {
        try {
            if (empty($vendor->pdf_path)) {
                return response()->json([
                    'valid' => false,
                    'message' => 'Error: No document path is registered for this vendor.'
                ], 404);
            }

            $disk = config('vendor_validation.storage_disk', 'public');
            if (!Storage::disk($disk)->exists($vendor->pdf_path)) {
                Log::error('File not found for validation', ['path' => $vendor->pdf_path, 'disk' => $disk]);
                return response()->json([
                    'valid' => false,
                    'message' => 'Error: The document file could not be found in storage.'
                ], 404);
            }

            $fullPath = Storage::disk($disk)->path($vendor->pdf_path);

            $validationResult = $this->validationService->validateDocument($fullPath, $vendor->vendor_id);

            $validationLog = new \App\Models\VendorValidation();
            $validationLog->vendor_id = $vendor->vendor_id;
            $validationLog->file_path = $vendor->pdf_path;
            $validationLog->original_filename = basename($vendor->pdf_path);
            $validationLog->file_size = Storage::disk($disk)->size($vendor->pdf_path);
            $validationLog->is_valid = $validationResult['valid'] ?? false;
            $validationLog->validation_message = $validationResult['message'] ?? 'Validation service did not provide a message.';
            $validationLog->validation_results = $validationResult['validationResults'] ?? null;
            $validationLog->save();

            if ($validationLog->is_valid) {
                $vendor->update(['validation_status' => 'Approved']);
            }

            // 7. Return the successful result to the frontend.
            return response()->json($validationResult);
        } catch (Exception $e) {
            Log::error('Critical error during existing document validation', [
                'vendor_id' => $vendor->vendor_id,
                'error' => $e->getMessage()
            ]);
            return response()->json([
                'valid' => false,
                'message' => 'A server error occurred. Please check the logs.'
            ], 500);
        }
    }

    /**
     * @param int $vendorId
     * @return \Illuminate\Http\JsonResponse
     */
    public function validateExisting($vendorId)
    {
        try {
            $vendor = Vendor::findOrFail($vendorId);

            if (empty($vendor->pdf_path)) {
                return response()->json([
                    'success' => false,
                    'valid' => false,
                    'message' => 'No document found for this vendor'
                ], 404);
            }

            $disk = config('vendor_validation.storage_disk', 'public');

            if (!Storage::disk($disk)->exists($vendor->pdf_path)) {
                return response()->json([
                    'success' => false,
                    'valid' => false,
                    'message' => 'Vendor document file not found'
                ], 404);
            }

            // Get the full path to the file
            $fullPath = Storage::disk($disk)->path($vendor->pdf_path);

            // Call the validation service
            $validationResult = $this->validationService->validateDocument($fullPath, $vendor->vendor_id);

            // Create a new validation record
            $validation = new VendorValidation();
            $validation->vendor_id = $vendor->vendor_id;
            $validation->file_path = $vendor->pdf_path;
            $validation->file_size = Storage::disk($disk)->size($vendor->pdf_path);
            $validation->original_filename = basename($vendor->pdf_path);
            $validation->is_valid = $validationResult['valid'] ?? false;
            $validation->validation_message = $validationResult['message'] ?? 'No message from service';
            $validation->validation_results = $validationResult['validationResults'] ?? null;
            $validation->save();

            // Update vendor status if validation succeeded
            if ($validation->is_valid) {
                DB::table('vendors')
                    ->where('vendor_id', $validation->vendor_id)
                    ->update(['validation_status' => 'Approved']);

                // If there's a supplier associated with this vendor, update their status too
                if ($vendor->supplier_id) {
                    DB::table('users')
                        ->where('id', $vendor->supplier_id)
                        ->update(['is_active' => 1]);
                }
            }

            Log::info('API validation for existing document', [
                'vendor_id' => $vendorId,
                'validation_id' => $validation->id,
                'success' => $validation->is_valid
            ]);

            return response()->json($validationResult);
        } catch (\Exception $e) {
            Log::error('Error validating existing document', [
                'vendor_id' => $vendorId,
                'error' => $e->getMessage()
            ]);

            return response()->json([
                'success' => false,
                'valid' => false,
                'message' => 'Error validating document: ' . $e->getMessage()
            ], 500);
        }
    }
}
