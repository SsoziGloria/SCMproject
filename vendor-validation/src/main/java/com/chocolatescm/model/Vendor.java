
package com.chocolatescm.model;

import java.time.LocalDate;

import jakarta.persistence.Entity;
import jakarta.persistence.GeneratedValue;
import jakarta.persistence.GenerationType;
import jakarta.persistence.Id;
import jakarta.persistence.Table;


@Entity
@Table(name = "vendors")

public class Vendor {

     @Id
    @GeneratedValue(strategy = GenerationType.IDENTITY)
    private Long id;

    private String name;
    private String email;
    private double revenue;
    private String certificationStatus;
    private LocalDate visitDate;
    private String contactPerson;
    private String phone;
    private String country;
    private String address;
    private double monthlyRevenue;
    private String bankName;
    private String accountNumber;
    private String complianceStatus;
    private String certification;
    
    // Add all getters and setters here

   

    // Getters and Setters
    public String getName() { return name; }
    public void setName(String name) { this.name = name; }

    public String getEmail() { return email; }
    public void setEmail(String email) { this.email = email; }

    public double getRevenue() { return revenue; }
    public void setRevenue(double revenue) { this.revenue = revenue; }

    public String getCertificationStatus() { return certificationStatus; }
    public void setCertificationStatus(String status) { this.certificationStatus = status; }

    public LocalDate getVisitDate() { return visitDate; }
    public void setVisitDate(LocalDate date) { this.visitDate = date; }

    public String getPhone() {
        return phone;
    }

    public void setPhone(String phone) {
        this.phone = phone;
    }

    public String getCountry() {
        return country;
    }

    public void setCountry(String country) {
        this.country = country;
    }

    public String getAddress() {
        return address;
    }

    public void setAddress(String address) {
        this.address = address;
    }

    public double getMonthlyRevenue() {
        return monthlyRevenue;
    }

    public void setMonthlyRevenue(double monthlyRevenue) {
        this.monthlyRevenue = monthlyRevenue;
    }

    public String getBankName() {
        return bankName;
    }

    public void setBankName(String bankName) {
        this.bankName = bankName;
    }

    public String getAccountNumber() {
        return accountNumber;
    }

    public void setAccountNumber(String accountNumber) {
        this.accountNumber = accountNumber;
    }

    public String getComplianceStatus() {
        return complianceStatus;
    }

    public void setComplianceStatus(String complianceStatus) {
        this.complianceStatus = complianceStatus;
    }

    public String getCertification() {
        return certification;
    }

    public void setCertification(String certification) {
        this.certification = certification;
    }

    public String getContactPerson() {
        return contactPerson;
    }

    public void setContactPerson(String contactPerson) {
        this.contactPerson = contactPerson;
    }

    public Long getId() {
        return id;
    }

    public void setId(Long id) {
        this.id = id;
    }

    

   

}