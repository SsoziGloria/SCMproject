package com.chocolatescm.service;

import java.io.BufferedReader;
import java.io.IOException;
import java.io.InputStream;
import java.io.InputStreamReader;
import java.nio.charset.StandardCharsets;
import java.util.HashMap;
import java.util.Map;
import java.util.regex.Matcher;
import java.util.regex.Pattern;

import org.springframework.stereotype.Service;
import org.springframework.web.multipart.MultipartFile;

import com.chocolatescm.model.Vendor;

@Service

public class PdfHandler {

    public static String getVALID_VENDOR_TXT() {
        return VALID_VENDOR_TXT;
    }

    public Vendor extractVendorData(MultipartFile file) throws IOException {
        String text = new String(file.getBytes(), StandardCharsets.UTF_8);
        Map<String, String> data = parseTextData(text);

        Vendor vendor = new Vendor();
        vendor.setName(data.get("Vendor Name"));
        return vendor;
    }

    private static final String VALID_VENDOR_TXT = "valid_vendor.txt";

    public Vendor extractVendorData() throws IOException {
        String text = readTextFromResources();
        Map<String, String> data = parseTextData(text);

        Vendor vendor = new Vendor();
        vendor.setName(data.get("Vendor Name"));
        vendor.setContactPerson(data.get("Contact Person"));
        vendor.setEmail(data.get("Email"));
        vendor.setPhone(data.get("Phone"));
        vendor.setCountry(data.get("Country"));
        vendor.setAddress(data.get("Address"));
        vendor.setMonthlyRevenue(parseRevenue(data.get("Monthly Revenue (UGX)")));
        vendor.setBankName(data.get("Bank Name"));
        vendor.setAccountNumber(data.get("Account Number"));
        vendor.setComplianceStatus(extractComplianceStatus(text));
        vendor.setCertification(extractCertification(text));

        return vendor;
    }

    private String readTextFromResources() throws IOException {
        InputStream inputStream = getClass().getClassLoader().getResourceAsStream(VALID_VENDOR_TXT);
        if (inputStream == null) {
            throw new IOException("File not found: " + VALID_VENDOR_TXT);
        }

        StringBuilder content = new StringBuilder();
        try (BufferedReader reader = new BufferedReader(new InputStreamReader(inputStream))) {
            String line;
            while ((line = reader.readLine()) != null) {
                content.append(line).append("\n");
            }
        }
        return content.toString();
    }

    private Map<String, String> parseTextData(String text) {
        Map<String, String> data = new HashMap<>();
        // Pattern to match "Field    Value" pairs
        Pattern pattern = Pattern.compile("^(.+?)\\t(.+?)$", Pattern.MULTILINE);
        Matcher matcher = pattern.matcher(text);

        while (matcher.find()) {
            String key = matcher.group(1).trim();
            String value = matcher.group(2).trim();
            data.put(key, value);
        }
        return data;
    }

    private double parseRevenue(String revenueStr) {
        if (revenueStr == null) {
            return 0.0;
        }
        try {
            return Double.parseDouble(revenueStr.replaceAll("[^0-9]", ""));
        } catch (NumberFormatException e) {
            return 0.0;
        }
    }

    private String extractComplianceStatus(String text) {
        Pattern pattern = Pattern.compile("Regulatory Compliance: (.+?)\\.");
        Matcher matcher = pattern.matcher(text);
        return matcher.find() ? matcher.group(1).trim() : "Not Certified";
    }

    private String extractCertification(String text) {
        Pattern pattern = Pattern.compile("Certification: (.+?)\\.");
        Matcher matcher = pattern.matcher(text);
        return matcher.find() ? matcher.group(1).trim() : "No Certification";
    }
}
