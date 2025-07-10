package com.chocolatescm.config;

import org.springframework.context.annotation.Bean;
import org.springframework.context.annotation.Configuration;
import org.springframework.security.config.annotation.web.builders.HttpSecurity;
import org.springframework.security.config.annotation.web.configuration.EnableWebSecurity;
import org.springframework.security.web.SecurityFilterChain;

@Configuration
@EnableWebSecurity
public class SecurityConfig {

    @Bean
    public String debugSecurityBean() {
        System.out.println("SecurityConfig loaded correctly!");
        return "OK";
    }

    public SecurityFilterChain securityFilterChain(HttpSecurity http) throws Exception {

        http
                .csrf(csrf -> csrf.disable())
                .authorizeHttpRequests(auth -> auth
                .requestMatchers(
                        "/swagger-ui/**",
                        "/v3/api-docs/**",
                        "/actuator/**",
                        "/api/**"
                ).permitAll()
                .anyRequest().permitAll()
                )
                .formLogin(form -> form.disable())
                .httpBasic(httpBasic -> {
                });

        return http.build();
    }
}
