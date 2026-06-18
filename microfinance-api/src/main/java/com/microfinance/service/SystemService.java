package com.microfinance.service;

import com.microfinance.exception.BusinessException;
import com.microfinance.model.ConfigurationSysteme;
import com.microfinance.repository.ConfigurationSystemeRepository;
import lombok.Generated;
import org.springframework.stereotype.Service;
import org.springframework.transaction.annotation.Transactional;

@Service
public class SystemService {
   private static final String KEY_STATUS = "STATUS_SYSTEME";
   public static final String STATUS_OPERATIONNEL = "OPERATIONNEL";
   public static final String STATUS_MAINTENANCE = "MAINTENANCE_CRITIQUE";
   private final ConfigurationSystemeRepository configRepo;

   @Transactional(
      readOnly = true
   )
   public String getStatus() {
      return (String)this.configRepo.findById("STATUS_SYSTEME").map(ConfigurationSysteme::getValeurConfiguration).orElse("OPERATIONNEL");
   }

   @Transactional
   public String setStatus(boolean activer) {
      String newStatus = activer ? "MAINTENANCE_CRITIQUE" : "OPERATIONNEL";
      this.configRepo.save(new ConfigurationSysteme("STATUS_SYSTEME", newStatus));
      return newStatus;
   }

   public void verifierSystemeOperationnel() {
      if ("MAINTENANCE_CRITIQUE".equals(this.getStatus())) {
         throw new BusinessException("Service momentanément indisponible pour maintenance d'urgence.");
      }
   }

   @Generated
   public SystemService(final ConfigurationSystemeRepository configRepo) {
      this.configRepo = configRepo;
   }
}
