
package com.repository;

import java.time.LocalDate;

import org.springframework.data.jpa.repository.JpaRepository;
import org.springframework.data.jpa.repository.Modifying;
import org.springframework.data.jpa.repository.Query;
import org.springframework.data.repository.query.Param;
import org.springframework.stereotype.Repository;

import com.chocolatescm.model.Vendor;

@Repository
public interface VendorRepository extends JpaRepository<Vendor, Long> {

    @Modifying
    @Query("UPDATE Vendor v SET v.certificationStatus = 'APPROVED', v.visitDate = :visitDate WHERE v.id = :id")
    void approveVendor(@Param("id") Long id, @Param("visitDate") LocalDate visitDate);
}