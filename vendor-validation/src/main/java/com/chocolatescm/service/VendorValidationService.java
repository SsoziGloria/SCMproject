package com.chocolatescm.service;

import java.io.IOException;
import java.time.LocalDateTime;
import java.util.HashMap;
import java.util.Map;
import java.util.regex.Pattern;

import org.apache.pdfbox.pdmodel.PDDocument;
import org.apache.pdfbox.text.PDFTextStripper;
import org.springframework.stereotype.Service;
import org.springframework.web.multipart.MultipartFile;

import com.chocolatescm.dto.ValidationResponse;
import com.chocolatescm.model.Vendor;

@Service
public class VendorValidationService {

    public ValidationResponse validateDocument(MultipartFile file, Long vendorId) throws IOException {
        ValidationResponse response = new ValidationResponse();
        response.setTimestamp(LocalDateTime.now());
        response.setVendorId(vendorId);
        response.setFileName(file.getOriginalFilename());
        response.setFileSize(file.getSize());

        try (PDDocument document = PDDocument.load(file.getInputStream())) {
            // Extract text from PDF
            PDFTextStripper stripper = new PDFTextStripper();
            String text = stripper.getText(document);

            // Perform validation checks
            Map<String, Object> validationResults = performValidationChecks(text);
            response.setValidationResults(validationResults);

            // Determine overall validation status
            boolean isValid = determineValidationStatus(validationResults);
            response.setValid(isValid);
            response.setSuccess(true);

            if (isValid) {
                response.setMessage("Document validation successful");
            } else {
                response.setMessage("Document validation failed - see validation results for details");
            }

        } catch (Exception e) {
            response.setSuccess(false);
            response.setValid(false);
            response.setMessage("Error processing PDF: " + e.getMessage());
            response.setValidationResults(new HashMap<>());
        }

        return response;
    }

    private Map<String, Object> performValidationChecks(String text) {
        Map<String, Object> results = new HashMap<>();

        // Check for required information
        results.put("hasCompanyName", checkForCompanyName(text));
        results.put("hasAddress", checkForAddress(text));
        results.put("hasContactInfo", checkForContactInfo(text));
        results.put("hasCertification", checkForCertification(text));
        results.put("hasFinancialInfo", checkForFinancialInfo(text));
        results.put("hasComplianceInfo", checkForComplianceInfo(text));

        // Additional checks
        results.put("documentLength", text.length());
        results.put("hasValidFormat", text.length() > 100); // Basic check

        return results;
    }

    private boolean checkForCompanyName(String text) {
        // Look for patterns that might indicate company name
        String[] companyIndicators = { "company", "corporation", "corp", "inc", "ltd", "llc", "business" };
        String lowerText = text.toLowerCase();

        for (String indicator : companyIndicators) {
            if (lowerText.contains(indicator)) {
                return true;
            }
        }
        return false;
    }

    private boolean checkForAddress(String text) {
        // Look for address patterns
        String[] addressIndicators = { "address", "street", "avenue", "road", "city", "state", "zip", "postal" };
        String lowerText = text.toLowerCase();

        for (String indicator : addressIndicators) {
            if (lowerText.contains(indicator)) {
                return true;
            }
        }
        return false;
    }

    private boolean checkForContactInfo(String text) {
        // Look for phone numbers or email patterns
        Pattern phonePattern = Pattern
                .compile("\\b\\d{3}-\\d{3}-\\d{4}\\b|\\b\\(\\d{3}\\)\\s*\\d{3}-\\d{4}\\b|\\b\\d{10}\\b");
        Pattern emailPattern = Pattern.compile("\\b[A-Za-z0-9._%+-]+@[A-Za-z0-9.-]+\\.[A-Z|a-z]{2,}\\b");

        return phonePattern.matcher(text).find() || emailPattern.matcher(text).find();
    }

    private boolean checkForCertification(String text) {
        String[] certificationIndicators = { "certificate", "certification", "certified", "license", "accredited",
                "iso", "quality" };
        String lowerText = text.toLowerCase();

        for (String indicator : certificationIndicators) {
            if (lowerText.contains(indicator)) {
                return true;
            }
        }
        return false;
    }

    private boolean checkForFinancialInfo(String text) {
        String[] financialIndicators = { "revenue", "income", "financial", "bank", "account", "tax", "fiscal" };
        String lowerText = text.toLowerCase();

        for (String indicator : financialIndicators) {
            if (lowerText.contains(indicator)) {
                return true;
            }
        }
        return false;
    }

    private boolean checkForComplianceInfo(String text) {
        String[] complianceIndicators = { "compliance", "regulation", "standard", "policy", "procedure", "audit" };
        String lowerText = text.toLowerCase();

        for (String indicator : complianceIndicators) {
            if (lowerText.contains(indicator)) {
                return true;
            }
        }
        return false;
    }

    private boolean determineValidationStatus(Map<String, Object> results) {
        // Define minimum requirements for validation
        boolean hasCompanyName = (Boolean) results.get("hasCompanyName");
        boolean hasAddress = (Boolean) results.get("hasAddress");
        boolean hasContactInfo = (Boolean) results.get("hasContactInfo");
        boolean hasValidFormat = (Boolean) results.get("hasValidFormat");

        // Document is valid if it has basic required information
        return hasCompanyName && hasAddress && hasContactInfo && hasValidFormat;
    }

    public Vendor getVendorById(Long id) {
        // This would typically interact with a repository/database
        // For now, return a dummy vendor or throw exception
        throw new RuntimeException("Vendor not found with ID: " + id);
    }
}