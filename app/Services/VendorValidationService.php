<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class VendorValidationService
{
    protected $baseUrl;
    protected $timeout;

    public function __construct()
    {
        $this->baseUrl = config('vendor_validation.java_service_url');
        $this->timeout = config('vendor_validation.timeout');
    }

    public function checkHealth()
    {
        try {
            $response = Http::timeout($this->timeout)->get("{$this->baseUrl}/api/v1/vendor/health");
            return $response->json();
        } catch (\Exception $e) {
            Log::error('Vendor validation service health check failed: ' . $e->getMessage());
            return ['validation_service_status' => 'DOWN'];
        }
    }

    public function validateDocument($filePath, $vendorId)
    {
        try {
            $response = Http::timeout($this->timeout)
                ->attach('file', file_get_contents($filePath), basename($filePath))

                ->post("{$this->baseUrl}/api/vendors/validate", [

                    'vendorId' => $vendorId
                ]);

            return $response->json();
        } catch (\Exception $e) {
            Log::error('Vendor document validation failed: ' . $e->getMessage());
            return [
                'success' => false,
                'message' => 'Error connecting to validation service: ' . $e->getMessage()
            ];
        }
    }
}