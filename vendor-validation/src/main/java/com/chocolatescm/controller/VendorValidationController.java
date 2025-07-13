package com.chocolatescm.controller;

import java.util.HashMap;
import java.util.Map;

import org.springframework.beans.factory.annotation.Autowired;
import org.springframework.http.HttpStatus;
import org.springframework.http.ResponseEntity;
import org.springframework.web.bind.annotation.CrossOrigin;
import org.springframework.web.bind.annotation.GetMapping;
import org.springframework.web.bind.annotation.PathVariable;
import org.springframework.web.bind.annotation.PostMapping;
import org.springframework.web.bind.annotation.RequestMapping;
import org.springframework.web.bind.annotation.RequestParam;
import org.springframework.web.bind.annotation.RestController;
import org.springframework.web.multipart.MultipartFile;

import com.chocolatescm.dto.ValidationResponse;
import com.chocolatescm.service.VendorValidationService;

@RestController
@RequestMapping("/api")
@CrossOrigin(origins = "*") // Configure this properly for production
public class VendorValidationController {

    @Autowired
    private VendorValidationService vendorValidationService;

    @PostMapping("/validate")
    public ResponseEntity<?> validateVendorDocument(
            @RequestParam("file") MultipartFile file,
            @RequestParam(value = "vendorId", required = false) Long vendorId) {

        try {
            // Validate file
            if (file.isEmpty()) {
                return ResponseEntity.badRequest()
                        .body(createErrorResponse("No file uploaded", "EMPTY_FILE"));
            }

            // Check if it's a PDF
            if (!isPdfFile(file)) {
                return ResponseEntity.badRequest()
                        .body(createErrorResponse("Only PDF files are allowed", "INVALID_FILE_TYPE"));
            }

            // Check file size (e.g., max 10MB)
            if (file.getSize() > 10 * 1024 * 1024) {
                return ResponseEntity.badRequest()
                        .body(createErrorResponse("File size exceeds 10MB limit", "FILE_TOO_LARGE"));
            }

            // Process the PDF validation
            ValidationResponse response = vendorValidationService.validateDocument(file, vendorId);

            return ResponseEntity.ok(response);

        } catch (Exception e) {
            return ResponseEntity.status(HttpStatus.INTERNAL_SERVER_ERROR)
                    .body(createErrorResponse("Error processing file: " + e.getMessage(), "PROCESSING_ERROR"));
        }
    }

    @GetMapping("/health")
    public ResponseEntity<Map<String, String>> healthCheck() {
        Map<String, String> response = new HashMap<>();
        response.put("status", "UP");
        response.put("service", "Vendor Validation Service");
        response.put("timestamp", java.time.LocalDateTime.now().toString());
        return ResponseEntity.ok(response);
    }

    @GetMapping("/vendor/{id}")
    public ResponseEntity<?> getVendorById(@PathVariable Long id) {
        try {
            // This would typically fetch from database
            return ResponseEntity.ok(vendorValidationService.getVendorById(id));
        } catch (Exception e) {
            return ResponseEntity.status(HttpStatus.NOT_FOUND)
                    .body(createErrorResponse("Vendor not found", "VENDOR_NOT_FOUND"));
        }
    }

    private boolean isPdfFile(MultipartFile file) {
        String contentType = file.getContentType();
        String filename = file.getOriginalFilename();

        return (contentType != null && contentType.equals("application/pdf")) ||
                (filename != null && filename.toLowerCase().endsWith(".pdf"));
    }

    private Map<String, Object> createErrorResponse(String message, String errorCode) {
        Map<String, Object> error = new HashMap<>();
        error.put("success", false);
        error.put("message", message);
        error.put("errorCode", errorCode);
        error.put("timestamp", java.time.LocalDateTime.now().toString());
        return error;
    }
}