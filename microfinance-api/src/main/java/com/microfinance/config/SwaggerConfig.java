package com.microfinance.config;

import io.swagger.v3.oas.models.Components;
import io.swagger.v3.oas.models.OpenAPI;
import io.swagger.v3.oas.models.info.Info;
import io.swagger.v3.oas.models.security.SecurityRequirement;
import io.swagger.v3.oas.models.security.SecurityScheme;
import io.swagger.v3.oas.models.security.SecurityScheme.Type;
import org.springframework.context.annotation.Bean;
import org.springframework.context.annotation.Configuration;

@Configuration
public class SwaggerConfig {
   @Bean
   public OpenAPI openAPI() {
      return (new OpenAPI()).info((new Info()).title("Microfinance API").description("API de gestion des opérations d'une microfinance").version("1.0.0")).addSecurityItem((new SecurityRequirement()).addList("Bearer")).components((new Components()).addSecuritySchemes("Bearer", (new SecurityScheme()).name("Bearer").type(Type.HTTP).scheme("bearer").bearerFormat("JWT")));
   }
}
