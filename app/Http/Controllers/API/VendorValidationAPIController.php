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


    public function validate(Request $request)
    {
        $request->validate([
            'file' => 'required|file|mimes:pdf|max:10240', // 10MB limit
            'vendor_id' => 'required|exists:vendors,id',
 
        ]);

        $vendor = Vendor::findOrFail($request->vendor_id);
        $file = $request->file('file');


        // Store the uploaded file
        $path = $file->store('uploads', config('vendor_validation.storage_disk'));
        $fullPath = Storage::disk(config('vendor_validation.storage_disk'))->path($path);

        // Call Java validation service
        $validationResult = $this->validationService->validateDocument($fullPath, $vendor->id);

        // Record validation result
        $validation = new VendorValidation();
        $validation->vendor_id = $vendor->id;
        $validation->file_path = $path;
        $validation->original_filename = $file->getClientOriginalName();
        $validation->is_valid = $validationResult['success'] ?? false;
        $validation->validation_message = $validationResult['message'] ?? 'No message';
        $validation->validation_details = $validationResult['validation_results'] ?? null;
        $validation->save();

        return response()->json($validationResult);
    }
    public function health()
    {
        \Log::info('Checking Java service health');
        try {

            $result = $this->validationService->checkHealth();
            \Log::info('Health check result', $result);
            return response()->json($result);
        } catch (\Exception $e) {
            \Log::error('Health check error: ' . $e->getMessage());
            return response()->json(['validation_service_status' => 'DOWN', 'error' => $e->getMessage()], 500);
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

        $validationResult = $this->validationService->validateDocument($fullPath, $vendor->id);

        // Update validation record
        $validation->is_valid = $validationResult['success'] ?? false;
        $validation->validation_message = $validationResult['message'] ?? 'No message';
        $validation->validation_details = $validationResult['validation_results'] ?? null;
        $validation->save();

        return response()->json($validationResult);
    }

}