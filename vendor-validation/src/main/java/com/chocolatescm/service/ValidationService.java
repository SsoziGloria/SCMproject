package com.chocolatescm.service;

import java.time.LocalDate;

import org.springframework.stereotype.Service;

import com.chocolatescm.model.Vendor;

@Service

public class ValidationService {

    private static final double MIN_REVENUE = 2000000; // 2M UGX (from design doc)

    public boolean validateVendor(Vendor vendor) {
        return vendor.getRevenue() >= MIN_REVENUE
                && "VALID".equals(vendor.getCertificationStatus());
    }

    public LocalDate scheduleVisit() {
        return LocalDate.now().plusWeeks(2); // Visit in 2 weeks (from design doc)
    }
}
