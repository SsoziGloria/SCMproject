<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use App\Models\Vendor;
use App\Models\VendorValidation;
use Exception;
use App\Models\Supplier;
use Illuminate\Support\Facades\DB;


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
                Vendor::where('vendor_id', $vendorId)->update(['validation_status' => 'Approved']);
                return response()->json([
                    'success' => true,
                    'message' => 'Document validation successful',
                    'validation_id' => $validationRecord->id,
                    'is_valid' => true,
                    'validation_results' => $validationResult['validationResults'] ?? []
                ], 200);
            } else {
                Vendor::where('vendor_id', $vendorId)->update(['validation_status' => 'Rejected']);
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

    public function validationHistory()
    {
        $validations = VendorValidation::with('vendor')
            ->orderBy('created_at', 'desc')
            ->paginate(15);

        return view('vendor.validation-history', compact('validations'));
    }

    public function downloadValidationDocument1($id)
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

    public function showValidationForm(Request $request)
    {

        $vendors = Vendor::orderBy('name')->get();
        // Check if a vendor_id is provided
        $vendorId = $request->query('vendor_id');
        $vendor = null;

        if ($vendorId) {
            $vendor = Vendor::findOrFail($vendorId);
        }

        // Get pending vendors that need validation
        $pendingVendors = Vendor::where('validation_status', 'Pending')
            ->orderBy('created_at', 'desc')
            ->get();

        return view('vendor.vendor-validation-form', compact('vendor', 'pendingVendors', 'vendors'));
    }

    public function downloadValidationDocument($id)
    {
        $validation = VendorValidation::find($id);
        $vendor = null;

        // If not found as validation ID, try to find as vendor ID
        if (!$validation) {
            $vendor = Vendor::find($id);

            if ($vendor) {
                // Try to get the latest validation for this vendor
                $validation = VendorValidation::where('vendor_id', $vendor->vendor_id)
                    ->latest()
                    ->first();
            }
        }

        // If no validation record exists but vendor has pdf_path, use that
        if (!$validation && $vendor && $vendor->pdf_path) {
            // Determine which disk to use based on path prefix
            $disk = $this->determineDiskFromPath($vendor->pdf_path);

            if (Storage::disk($disk)->exists($this->stripDiskPrefix($vendor->pdf_path, $disk))) {
                return Storage::disk($disk)->download(
                    $this->stripDiskPrefix($vendor->pdf_path, $disk),
                    basename($vendor->pdf_path)
                );
            }

            return back()->with('error', 'Vendor document file not found.');
        }

        // If no validation or vendor found
        if (!$validation) {
            return back()->with('error', 'No document found.');
        }

        // Now we have a validation record, determine which disk to use
        $disk = $this->determineDiskFromPath($validation->file_path);
        $filePath = $this->stripDiskPrefix($validation->file_path, $disk);

        // Check if file exists in the determined disk
        if (Storage::disk($disk)->exists($filePath)) {
            return Storage::disk($disk)->download(
                $filePath,
                $validation->original_filename ?? basename($filePath)
            );
        }

        // If file not found in primary disk, try fallback disks
        foreach (['public', 'vendor_docs', 'local'] as $fallbackDisk) {
            if ($disk !== $fallbackDisk && Storage::disk($fallbackDisk)->exists($filePath)) {
                return Storage::disk($fallbackDisk)->download(
                    $filePath,
                    $validation->original_filename ?? basename($filePath)
                );
            }
        }

        return back()->with('error', 'Document file not found.');
    }

    public function updateVendorStatus(Request $request, $id)
    {
        $request->validate([
            'status' => 'required|in:Approved,Rejected',
            'message' => 'nullable|string|max:500',
        ]);

        $vendor = VendorValidation::findOrFail($id);
        $vendor->validation_message = $request->message;
        $vendor->save();

        $vendor = Vendor::findOrFail($id);
        $vendor->validation_status = $request->status;
        $vendor->save();

        // If vendor is approved, update supplier status if applicable
        if ($request->status === 'Approved' && $vendor->supplier_id) {
            $supplier = Supplier::where('supplier_id', $vendor->supplier_id)->first();
            if ($supplier) {
                $supplier->status = 'active';
                $supplier->save();
            }
        }

        return back()->with('success', "Vendor status updated to {$request->status}");
    }

    public function updateVendorStatusManual(Request $request, $id)
    {
        $request->validate([
            'status' => 'required|in:Approved,Rejected',
            'message' => 'nullable|string|max:500',
        ]);

        $vendor = Vendor::findOrFail($id);

        $vendor->validation_status = $request->status;
        $vendor->save();

        $validation = VendorValidation::updateOrCreate(
            ['vendor_id' => $vendor->vendor_id],
            [
                'original_filename' => basename($vendor->pdf_path ?? 'manual-validation'),
                'file_path' => $vendor->pdf_path,
                'file_size' => 0,
                'is_valid' => $request->status === 'Approved' ? 1 : 0,
                'validation_message' => $request->message ?? 'Manual Validation',
                'validated_at' => now()
            ]
        );

        if ($request->status === 'Approved' && $vendor->vendor_id) {
            if ($vendor->supplier_id) {
                if ($vendor) {
                    DB::table('vendors')
                        ->where('vendor_id', $vendor->vendor_id)
                        ->update([
                            'validation_status' => 'Approved'
                        ]);
                }

                $supplier = DB::table('suppliers')->where('supplier_id', $vendor->supplier_id)->first();
                if ($supplier) {
                    DB::table('suppliers')
                        ->where('supplier_id', $vendor->supplier_id)
                        ->update([
                            'status' => 'active'
                        ]);
                }
            } else {
                if ($vendor) {
                    DB::table('vendors')
                        ->where('vendor_id', $vendor->vendor_id)
                        ->update([
                            'validation_status' => 'Approved'
                        ]);
                }
            }
        }

        return redirect()->route('admin.vendor-validation')
            ->with('success', "Vendor status updated to {$request->status}");
    }

    /**
     * Determine which disk to use based on file path
     *
     * @param string $path
     * @return string
     */
    private function determineDiskFromPath($path)
    {
        // Default disk from config
        $defaultDisk = config('vendor_validation.storage_disk', 'public');

        // Check if path has specific disk prefixes
        if (strpos($path, 'vendor_documents/') === 0) {
            return 'public';
        }

        if (strpos($path, 'uploads/') === 0) {
            return $defaultDisk;
        }

        // Return the configured disk as fallback
        return $defaultDisk;
    }

    /**
     * Remove disk-specific prefixes from paths
     *
     * @param string $path
     * @param string $disk
     * @return string
     */
    private function stripDiskPrefix($path, $disk)
    {
        // No need to modify the path if it's already correct for the disk
        return $path;
    }

    /**
     * Generate document download URL - use this consistently across your app
     *
     * @param mixed $document Vendor or VendorValidation object or ID
     * @param string $routePrefix Optional route prefix (admin., vendor., etc)
     * @return string
     */
    public static function getDocumentUrl($document, $routePrefix = 'admin.')
    {
        if ($document instanceof Vendor) {
            return route($routePrefix . 'vendor-validation.download', $document->vendor_id);
        }

        if ($document instanceof VendorValidation) {
            return route($routePrefix . 'vendor-validation.download', $document->id);
        }

        return route($routePrefix . 'vendor-validation.download', $document);
    }
}
