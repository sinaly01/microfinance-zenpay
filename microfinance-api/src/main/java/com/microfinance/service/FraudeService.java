package com.microfinance.service;

import com.microfinance.model.Compte;
import com.microfinance.model.Transaction;
import com.microfinance.repository.ClientRepository;
import com.microfinance.repository.CompteRepository;
import com.microfinance.repository.TransactionRepository;
import java.time.LocalDateTime;
import java.util.List;
import lombok.Generated;
import org.slf4j.Logger;
import org.slf4j.LoggerFactory;
import org.springframework.stereotype.Service;
import org.springframework.transaction.annotation.Transactional;

@Service
public class FraudeService {
   @Generated
   private static final Logger log = LoggerFactory.getLogger(FraudeService.class);
   private static final int SEUIL_TRANSACTIONS = 10;
   private static final int FENETRE_MINUTES = 5;
   private final TransactionRepository transactionRepository;
   private final CompteRepository compteRepository;
   private final ClientRepository clientRepository;
   private final AuditService auditService;

   @Transactional
   public boolean detecterEtBloquerSiSuspect(Long idCompte) {
      LocalDateTime fenetre = LocalDateTime.now().minusMinutes(5L);
      List<Transaction> transactionsRecentes = this.transactionRepository.findByCompteIdCompteOrderByDateHeureDesc(idCompte).stream().filter((t) -> t.getDateHeure() != null && t.getDateHeure().isAfter(fenetre)).toList();
      if (transactionsRecentes.size() >= 10) {
         Compte compte = (Compte)this.compteRepository.findById(idCompte).orElse((Object)null);
         if (compte != null && compte.isActif()) {
            compte.bloquer();
            this.compteRepository.save(compte);
            String alerte = String.format("FRAUDE DETECTEE — Compte %s (client ID %d) bloqué automatiquement : %d transactions en moins de %d minutes.", compte.getNumeroCompte(), compte.getClient().getIdClient(), transactionsRecentes.size(), 5);
            this.auditService.enregistrerSysteme(alerte);
            log.warn(alerte);
            return true;
         }
      }

      return false;
   }

   @Generated
   public FraudeService(final TransactionRepository transactionRepository, final CompteRepository compteRepository, final ClientRepository clientRepository, final AuditService auditService) {
      this.transactionRepository = transactionRepository;
      this.compteRepository = compteRepository;
      this.clientRepository = clientRepository;
      this.auditService = auditService;
   }
}
