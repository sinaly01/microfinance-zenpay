package com.microfinance.service;

import com.microfinance.model.Gestionnaire;
import com.microfinance.model.LogAudit;
import com.microfinance.repository.GestionnaireRepository;
import com.microfinance.repository.LogAuditRepository;
import java.util.List;
import lombok.Generated;
import org.slf4j.Logger;
import org.slf4j.LoggerFactory;
import org.springframework.stereotype.Service;
import org.springframework.transaction.annotation.Propagation;
import org.springframework.transaction.annotation.Transactional;

@Service
public class AuditService {
   @Generated
   private static final Logger log = LoggerFactory.getLogger(AuditService.class);
   private final LogAuditRepository logAuditRepository;
   private final GestionnaireRepository gestionnaireRepository;

   @Transactional(
      propagation = Propagation.REQUIRES_NEW
   )
   public void enregistrer(String emailActeur, String action, String adresseIp) {
      try {
         Gestionnaire acteur = (Gestionnaire)this.gestionnaireRepository.findByEmail(emailActeur).orElse((Object)null);
         LogAudit log = LogAudit.builder().utilisateur(acteur).actionEffectuee(action).adresseIp(adresseIp).build();
         this.logAuditRepository.save(log);
      } catch (Exception e) {
         AuditService.log.warn("Échec écriture audit log [{}] : {}", action, e.getMessage());
      }

   }

   @Transactional(
      propagation = Propagation.REQUIRES_NEW
   )
   public void enregistrerSysteme(String action) {
      try {
         LogAudit auditLog = LogAudit.builder().actionEffectuee(action).adresseIp("SYSTEM").build();
         this.logAuditRepository.save(auditLog);
      } catch (Exception e) {
         log.warn("Échec écriture audit log système [{}] : {}", action, e.getMessage());
      }

   }

   @Transactional(
      readOnly = true
   )
   public List listerLogs() {
      return this.logAuditRepository.findAll();
   }

   @Transactional(
      readOnly = true
   )
   public List listerLogsParActeur(Long idGestionnaire) {
      return this.logAuditRepository.findAll().stream().filter((l) -> l.getUtilisateur() != null && l.getUtilisateur().getIdGestionnaire().equals(idGestionnaire)).toList();
   }

   @Generated
   public AuditService(final LogAuditRepository logAuditRepository, final GestionnaireRepository gestionnaireRepository) {
      this.logAuditRepository = logAuditRepository;
      this.gestionnaireRepository = gestionnaireRepository;
   }
}
