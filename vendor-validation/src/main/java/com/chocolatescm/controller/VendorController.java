package com.chocolatescm.controller;

import java.io.IOException;

import org.springframework.beans.factory.annotation.Autowired;
import org.springframework.http.MediaType;
import org.springframework.http.ResponseEntity;
import org.springframework.web.bind.annotation.CrossOrigin;
import org.springframework.web.bind.annotation.PostMapping;
import org.springframework.web.bind.annotation.RequestMapping;
import org.springframework.web.bind.annotation.RequestParam;
import org.springframework.web.bind.annotation.RestController;
import org.springframework.web.multipart.MultipartFile;

import com.chocolatescm.model.Vendor;
import com.chocolatescm.service.PdfHandler;
import com.chocolatescm.service.ValidationService;

import io.swagger.v3.oas.annotations.Operation;
import io.swagger.v3.oas.annotations.Parameter;

@CrossOrigin(origins = "*")
@RestController
@RequestMapping("/api/vendors")
public class VendorController {

    @Autowired
    private PdfHandler pdfHandler;

    @Autowired
    private ValidationService validationService;

    @Operation(
            summary = "Validate Vendor PDF",
            description = "Uploads a vendor application PDF and returns whether the vendor is APPROVED or REJECTED"
    )
    @PostMapping(value = "/validate", consumes = MediaType.MULTIPART_FORM_DATA_VALUE)
    public ResponseEntity<String> validateVendor(
            @Parameter(description = "PDF file containing vendor application", required = true)
            @RequestParam("file") MultipartFile file
    ) {
        System.out.println("✅ Vendor validation endpoint hit!");

        try {
            Vendor vendor = pdfHandler.extractVendorData(file);
            String response = validationService.validateVendor(vendor)
                    ? "APPROVED" : "REJECTED";
            return ResponseEntity.ok(response);
        } catch (IOException e) {
            return ResponseEntity.badRequest().body("Error: " + e.getMessage());
        }
    }
}
