<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use App\Models\Vendor;
use App\Models\VendorValidation;
use Exception;

class VendorValidationController extends Controller
{
    private $validationServerUrl;

    public function __construct()
    {
        $this->validationServerUrl = config('services.vendor_validation.url', 'http://localhost:8080');
    }

    /**
     * Upload and validate a vendor document
     */
    public function validateDocument(Request $request)
    {
        $request->validate([
            'file' => 'required|file|mimes:pdf|max:10240', // 10MB max
            'vendor_id' => 'required|exists:vendors,id',
        ]);

        try {
            $file = $request->file('file');
            $vendorId = $request->input('vendor_id');

            // Get vendor details
            $vendor = Vendor::findOrFail($vendorId);

            // Call the Java validation service
            $validationResult = $this->callValidationService($file, $vendorId);

            // Process and store the validation result
            $validationRecord = $this->storeValidationResult($validationResult, $vendor, $file);

            // Return response based on validation result
            if ($validationResult['success'] && $validationResult['valid']) {
                return response()->json([
                    'success' => true,
                    'message' => 'Document validation successful',
                    'validation_id' => $validationRecord->id,
                    'is_valid' => true,
                    'validation_results' => $validationResult['validationResults'] ?? []
                ], 200);
            } else {
                return response()->json([
                    'success' => false,
                    'message' => $validationResult['message'] ?? 'Document validation failed',
                    'validation_id' => $validationRecord->id,
                    'is_valid' => false,
                    'validation_results' => $validationResult['validationResults'] ?? []
                ], 422);
            }

        } catch (Exception $e) {
            Log::error('Vendor validation error: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Error processing document validation',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get validation history for a vendor
     */
    public function getValidationHistory($vendorId)
    {
        try {
            $vendor = Vendor::findOrFail($vendorId);
            $validations = VendorValidation::where('vendor_id', $vendorId)
                ->orderBy('created_at', 'desc')
                ->get();

            return response()->json([
                'success' => true,
                'vendor' => $vendor,
                'validations' => $validations
            ]);

        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error fetching validation history',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get specific validation details
     */
    public function getValidationDetails($validationId)
    {
        try {
            $validation = VendorValidation::with('vendor')->findOrFail($validationId);

            return response()->json([
                'success' => true,
                'validation' => $validation
            ]);

        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Validation not found',
                'error' => $e->getMessage()
            ], 404);
        }
    }

    /**
     * Revalidate an existing document
     */
    public function revalidateDocument($validationId)
    {
        try {
            $validation = VendorValidation::findOrFail($validationId);

            if (!Storage::exists($validation->file_path)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Original file not found for revalidation'
                ], 404);
            }

            // Get the stored file
            $filePath = Storage::path($validation->file_path);
            $file = new \Illuminate\Http\UploadedFile(
                $filePath,
                $validation->original_filename,
                'application/pdf',
                null,
                true
            );

            // Call validation service again
            $validationResult = $this->callValidationService($file, $validation->vendor_id);

            // Update the validation record
            $validation->update([
                'is_valid' => $validationResult['success'] && $validationResult['valid'],
                'validation_results' => $validationResult['validationResults'] ?? [],
                'validation_message' => $validationResult['message'] ?? '',
                'validated_at' => now()
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Document revalidated successfully',
                'validation' => $validation->fresh()
            ]);

        } catch (Exception $e) {
            Log::error('Revalidation error: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Error during revalidation',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Call the Java validation service
     */
    private function callValidationService($file, $vendorId)
    {
        try {
            $response = Http::timeout(30)
                ->attach('file', fopen($file->path(), 'r'), $file->getClientOriginalName())
                ->post($this->validationServerUrl . '/api/v1/vendor/validate', [
                    'vendorId' => $vendorId
                ]);

            if ($response->successful()) {
                return $response->json();
            } else {
                throw new Exception('Validation service returned error: ' . $response->body());
            }

        } catch (Exception $e) {
            Log::error('Validation service call failed: ' . $e->getMessage());
            throw new Exception('Unable to connect to validation service: ' . $e->getMessage());
        }
    }

    /**
     * Store validation result in database
     */
    private function storeValidationResult($validationResult, $vendor, $file)
    {
        // Store the uploaded file
        $filePath = $file->store('vendor_documents', 'local');

        // Create validation record
        return VendorValidation::create([
            'vendor_id' => $vendor->vendor_id,
            'original_filename' => $file->getClientOriginalName(),
            'file_path' => $filePath,
            'file_size' => $file->getSize(),
            'is_valid' => $validationResult['success'] && $validationResult['valid'],
            'validation_results' => $validationResult['validationResults'] ?? [],
            'validation_message' => $validationResult['message'] ?? '',
            'validated_at' => now()
        ]);
    }

    /**
     * Check if validation service is available
     */
    public function healthCheck()
    {
        try {
            $response = Http::timeout(5)->get($this->validationServerUrl . '/api/v1/vendor/health');

            return response()->json([
                'validation_service_status' => $response->successful() ? 'UP' : 'DOWN',
                'validation_service_response' => $response->json()
            ]);

        } catch (Exception $e) {
            return response()->json([
                'validation_service_status' => 'DOWN',
                'error' => $e->getMessage()
            ]);
        }
    }

    public function showValidationForm()
    {
        $vendors = Vendor::orderBy('name')->get();
        return view('vendor.vendor-validation-form', compact('vendors'));
    }

    public function validationHistory()
    {
        $validations = VendorValidation::with('vendor')
            ->orderBy('created_at', 'desc')
            ->paginate(15);

        return view('vendor.validation-history', compact('validations'));
    }

    public function downloadValidationDocument($id)
    {
        $validation = VendorValidation::findOrFail($id);

        // Check if file exists
        if (!Storage::disk('vendor_docs')->exists($validation->file_path)) {
            return back()->with('error', 'File not found');
        }

        return Storage::disk('vendor_docs')->download(
            $validation->file_path,
            $validation->original_filename
        );
    }
}