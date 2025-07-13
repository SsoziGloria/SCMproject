<?php

return [
    'java_service_url' => env('VENDOR_VALIDATION_SERVICE_URL', 'http://localhost:8080'),
    'timeout' => env('VENDOR_VALIDATION_TIMEOUT', 30),
    'storage_disk' => 'vendor_docs',
];