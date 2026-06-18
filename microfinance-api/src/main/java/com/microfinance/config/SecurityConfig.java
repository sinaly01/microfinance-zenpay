package com.microfinance.config;

import com.microfinance.security.JwtFilter;
import java.util.List;
import lombok.Generated;
import org.springframework.context.annotation.Bean;
import org.springframework.context.annotation.Configuration;
import org.springframework.security.authentication.AuthenticationManager;
import org.springframework.security.config.annotation.authentication.configuration.AuthenticationConfiguration;
import org.springframework.security.config.annotation.method.configuration.EnableMethodSecurity;
import org.springframework.security.config.annotation.web.builders.HttpSecurity;
import org.springframework.security.config.annotation.web.configuration.EnableWebSecurity;
import org.springframework.security.config.annotation.web.configurers.AuthorizeHttpRequestsConfigurer;
import org.springframework.security.config.http.SessionCreationPolicy;
import org.springframework.security.crypto.bcrypt.BCryptPasswordEncoder;
import org.springframework.security.crypto.password.PasswordEncoder;
import org.springframework.security.web.SecurityFilterChain;
import org.springframework.security.web.authentication.UsernamePasswordAuthenticationFilter;
import org.springframework.web.cors.CorsConfiguration;
import org.springframework.web.cors.CorsConfigurationSource;
import org.springframework.web.cors.UrlBasedCorsConfigurationSource;

@Configuration
@EnableWebSecurity
@EnableMethodSecurity
public class SecurityConfig {
   private final JwtFilter jwtFilter;

   @Bean
   public SecurityFilterChain filterChain(HttpSecurity http) throws Exception {
      http.csrf((csrf) -> csrf.disable()).cors((cors) -> cors.configurationSource(this.corsConfigurationSource())).sessionManagement((sm) -> sm.sessionCreationPolicy(SessionCreationPolicy.STATELESS)).authorizeHttpRequests((auth) -> ((AuthorizeHttpRequestsConfigurer.AuthorizedUrl)((AuthorizeHttpRequestsConfigurer.AuthorizedUrl)((AuthorizeHttpRequestsConfigurer.AuthorizedUrl)auth.requestMatchers(new String[]{"/api/auth/login", "/api/auth/register", "/api/auth/verify-otp", "/api/auth/resend-otp", "/api/auth/my-ip", "/api/auth/forgot-password", "/api/auth/reset-password", "/api/auth/superadmin/verify-key", "/api/kyc/file/**"})).permitAll().requestMatchers(new String[]{"/swagger-ui/**", "/swagger-ui.html", "/v3/api-docs/**", "/api-docs/**", "/webjars/**"})).permitAll().anyRequest()).authenticated()).addFilterBefore(this.jwtFilter, UsernamePasswordAuthenticationFilter.class);
      return (SecurityFilterChain)http.build();
   }

   @Bean
   public CorsConfigurationSource corsConfigurationSource() {
      CorsConfiguration config = new CorsConfiguration();
      config.setAllowedOriginPatterns(List.of("*"));
      config.setAllowedMethods(List.of("GET", "POST", "PUT", "DELETE", "OPTIONS"));
      config.setAllowedHeaders(List.of("*"));
      config.setAllowCredentials(true);
      UrlBasedCorsConfigurationSource source = new UrlBasedCorsConfigurationSource();
      source.registerCorsConfiguration("/**", config);
      return source;
   }

   @Bean
   public PasswordEncoder passwordEncoder() {
      return new BCryptPasswordEncoder();
   }

   @Bean
   public AuthenticationManager authenticationManager(AuthenticationConfiguration config) throws Exception {
      return config.getAuthenticationManager();
   }

   @Generated
   public SecurityConfig(final JwtFilter jwtFilter) {
      this.jwtFilter = jwtFilter;
   }
}
