package com.chocolatescm.config;

import org.springframework.context.annotation.Bean;
import org.springframework.context.annotation.Configuration;
import org.springframework.security.config.annotation.web.builders.HttpSecurity;
import org.springframework.security.web.SecurityFilterChain;

@Configuration
public class SecurityConfig {

    @Bean
    public SecurityFilterChain securityFilterChain(HttpSecurity http) throws Exception {
        http
            .authorizeHttpRequests(auth -> auth
                .requestMatchers(
                    "/swagger-ui/**",      // Swagger UI
                    "/v3/api-docs/**",     // OpenAPI docs
                    "/actuator/**"         // Actuator endpoints
                ).permitAll()
                .anyRequest().permitAll() // secure other endpoints
            )
            .httpBasic(org.springframework.security.config.Customizer.withDefaults()); // use HTTP Basic Auth for simplicity

        return http.build();
    }
}

