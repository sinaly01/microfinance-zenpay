package com.microfinance.config;

import com.microfinance.model.Client;
import com.microfinance.repository.BlackListJetonRepository;
import com.microfinance.repository.ClientRepository;
import com.microfinance.service.AbonnementService;
import com.microfinance.service.AuditService;
import java.time.LocalDateTime;
import java.util.List;
import lombok.Generated;
import org.slf4j.Logger;
import org.slf4j.LoggerFactory;
import org.springframework.context.annotation.Configuration;
import org.springframework.scheduling.annotation.EnableScheduling;
import org.springframework.scheduling.annotation.Scheduled;
import org.springframework.transaction.annotation.Transactional;

@Configuration
@EnableScheduling
public class SchedulerConfig {
   @Generated
   private static final Logger log = LoggerFactory.getLogger(SchedulerConfig.class);
   private final ClientRepository clientRepository;
   private final AbonnementService abonnementService;
   private final AuditService auditService;
   private final BlackListJetonRepository blackListJetonRepository;

   @Scheduled(
      cron = "0 0 9 * * *"
   )
   public void traiterPrelevementsMensuels() {
      log.info("[SCHEDULER] Début prélèvements mensuels abonnements");
      LocalDateTime maintenant = LocalDateTime.now();
      List<Client> clientsDus = this.clientRepository.findAll().stream().filter((c) -> c.getOffreAbonnement() != null).filter((c) -> c.getDateProchainPrelevement() != null).filter((c) -> !c.getDateProchainPrelevement().isAfter(maintenant)).filter((c) -> c.getOffreAbonnement().getPrixMensuel() != null && c.getOffreAbonnement().getPrixMensuel().signum() > 0).toList();
      log.info("[SCHEDULER] {} client(s) à prélever", clientsDus.size());

      for(Client client : clientsDus) {
         try {
            this.abonnementService.prelevementMensuel(client.getIdClient());
            AuditService var10000 = this.auditService;
            Long var10001 = client.getIdClient();
            var10000.enregistrerSysteme("PRELEVEMENT_MENSUEL — Client " + var10001 + " (" + client.getEmail() + ") — Offre " + client.getOffreAbonnement().getNomOffre());
         } catch (Exception e) {
            log.error("[SCHEDULER] Échec prélèvement client {} : {}", client.getIdClient(), e.getMessage());
         }
      }

      log.info("[SCHEDULER] Prélèvements mensuels terminés");
   }

   @Scheduled(
      cron = "0 0 2 * * *"
   )
   @Transactional
   public void nettoyerJwtExpires() {
      log.info("[SCHEDULER] Nettoyage blacklist JWT expiré");
      int supprimés = this.blackListJetonRepository.deleteByDateExpirationBefore(LocalDateTime.now());
      this.auditService.enregistrerSysteme("NETTOYAGE_JWT — " + supprimés + " jeton(s) expiré(s) supprimé(s) de la blacklist");
      log.info("[SCHEDULER] {} jeton(s) JWT expiré(s) supprimé(s)", supprimés);
   }

   @Generated
   public SchedulerConfig(final ClientRepository clientRepository, final AbonnementService abonnementService, final AuditService auditService, final BlackListJetonRepository blackListJetonRepository) {
      this.clientRepository = clientRepository;
      this.abonnementService = abonnementService;
      this.auditService = auditService;
      this.blackListJetonRepository = blackListJetonRepository;
   }
}
