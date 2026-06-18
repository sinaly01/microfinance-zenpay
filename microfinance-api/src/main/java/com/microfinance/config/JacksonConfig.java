package com.microfinance.config;

import com.fasterxml.jackson.datatype.hibernate6.Hibernate6Module;
import com.fasterxml.jackson.datatype.hibernate6.Hibernate6Module.Feature;
import org.springframework.context.annotation.Bean;
import org.springframework.context.annotation.Configuration;

@Configuration
public class JacksonConfig {
   @Bean
   public Hibernate6Module hibernate6Module() {
      Hibernate6Module module = new Hibernate6Module();
      module.enable(Feature.FORCE_LAZY_LOADING);
      module.disable(Feature.USE_TRANSIENT_ANNOTATION);
      return module;
   }
}
