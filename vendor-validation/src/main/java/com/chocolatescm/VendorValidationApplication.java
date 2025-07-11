package com.chocolatescm;

import org.springframework.boot.SpringApplication;
import org.springframework.boot.autoconfigure.SpringBootApplication;
import org.springframework.boot.autoconfigure.domain.EntityScan;
import org.springframework.data.jpa.repository.config.EnableJpaRepositories;

import io.swagger.v3.oas.annotations.OpenAPIDefinition;
import io.swagger.v3.oas.annotations.info.Info;

@SpringBootApplication
@EnableJpaRepositories(basePackages = "com.chocolatescm.repository")
@EntityScan(basePackages = "com.chocolatescm.model")

// Add OpenAPI metadata here
@OpenAPIDefinition(
        info = @Info(
                title = "Chocolate SCM Vendor API",
                version = "v1",
                description = "API for validating vendors in the Chocolate Supply Chain Management system"
        )
)
public class VendorValidationApplication {

    public static void main(String[] args) {
        SpringApplication.run(VendorValidationApplication.class, args);

        System.out.println("""
            #############################################
            # Vendor Validation System Started Successfully!
            # Swagger UI: http://localhost:8080/swagger-ui.html
            # API Endpoint: http://localhost:8080/api/vendors
            #############################################
            """);
    }
}
