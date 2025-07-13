package com.chocolatescm.dto;

import java.time.LocalDateTime;
import java.util.Map;

public class ValidationResponse {
    private boolean success;
    private boolean valid;
    private String message;
    private Long vendorId;
    private String fileName;
    private Long fileSize;
    private LocalDateTime timestamp;
    private Map<String, Object> validationResults;

    // Constructors
    public ValidationResponse() {
    }

    public ValidationResponse(boolean success, boolean valid, String message) {
        this.success = success;
        this.valid = valid;
        this.message = message;
        this.timestamp = LocalDateTime.now();
    }

    // Getters and Setters
    public boolean isSuccess() {
        return success;
    }

    public void setSuccess(boolean success) {
        this.success = success;
    }

    public boolean isValid() {
        return valid;
    }

    public void setValid(boolean valid) {
        this.valid = valid;
    }

    public String getMessage() {
        return message;
    }

    public void setMessage(String message) {
        this.message = message;
    }

    public Long getVendorId() {
        return vendorId;
    }

    public void setVendorId(Long vendorId) {
        this.vendorId = vendorId;
    }

    public String getFileName() {
        return fileName;
    }

    public void setFileName(String fileName) {
        this.fileName = fileName;
    }

    public Long getFileSize() {
        return fileSize;
    }

    public void setFileSize(Long fileSize) {
        this.fileSize = fileSize;
    }

    public LocalDateTime getTimestamp() {
        return timestamp;
    }

    public void setTimestamp(LocalDateTime timestamp) {
        this.timestamp = timestamp;
    }

    public Map<String, Object> getValidationResults() {
        return validationResults;
    }

    public void setValidationResults(Map<String, Object> validationResults) {
        this.validationResults = validationResults;
    }
}