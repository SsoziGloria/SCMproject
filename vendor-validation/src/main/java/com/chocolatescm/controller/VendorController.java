package com.chocolatescm.controller;

import java.io.IOException;

import org.springframework.beans.factory.annotation.Autowired;
import org.springframework.http.ResponseEntity;
import org.springframework.web.bind.annotation.PostMapping;
import org.springframework.web.bind.annotation.RequestMapping;
import org.springframework.web.bind.annotation.RequestParam;
import org.springframework.web.bind.annotation.RestController;
import org.springframework.web.multipart.MultipartFile;

import com.chocolatescm.model.Vendor;
import com.chocolatescm.service.PdfHandler;
import com.chocolatescm.service.ValidationService;
@RestController
@RequestMapping("/api/vendors")
public class VendorController {
    
    @Autowired
    private PdfHandler pdfHandler;
    
    @Autowired
    private ValidationService validationService;
    
    @PostMapping("/validate")
    public ResponseEntity<String> validateVendor(
        @RequestParam("file") MultipartFile file  
    ) {
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