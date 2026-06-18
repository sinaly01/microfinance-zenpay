package com.microfinance.service;

import com.microfinance.dto.request.TransactionRequest;
import com.microfinance.dto.response.TransactionResponse;
import com.microfinance.exception.BusinessException;
import com.microfinance.exception.ResourceNotFoundException;
import com.microfinance.model.Compte;
import com.microfinance.model.Retrait;
import com.microfinance.model.Transaction;
import com.microfinance.model.Versement;
import com.microfinance.model.Virement;
import com.microfinance.model.enums.StatutTransaction;
import com.microfinance.repository.CompteRepository;
import com.microfinance.repository.TransactionRepository;
import java.time.LocalDateTime;
import java.util.List;
import lombok.Generated;
import org.springframework.stereotype.Service;
import org.springframework.transaction.annotation.Transactional;

@Service
@Transactional
public class TransactionService {
   private final TransactionRepository transactionRepository;
   private final CompteRepository compteRepository;
   private final AuditService auditService;
   private final FraudeService fraudeService;

   public TransactionResponse effectuerVersement(TransactionRequest req) {
      Compte compte = this.getCompteActif(req.idCompte());
      Versement v = new Versement();
      v.setCompte(compte);
      v.setMontant(req.montant());
      v.setDescription(req.description());
      v.executer();
      this.compteRepository.save(compte);
      TransactionResponse response = TransactionResponse.from((Transaction)this.transactionRepository.save(v));
      this.auditService.enregistrerSysteme(String.format("VERSEMENT — Compte %s — Montant %.2f FCFA — Réf %s", compte.getNumeroCompte(), req.montant(), response.reference()));
      this.fraudeService.detecterEtBloquerSiSuspect(compte.getIdCompte());
      return response;
   }

   public TransactionResponse effectuerRetrait(TransactionRequest req) {
      Compte compte = this.getCompteActif(req.idCompte());
      Retrait r = new Retrait();
      r.setCompte(compte);
      r.setMontant(req.montant());
      r.setCanal(req.canal() != null ? req.canal() : "GUICHET");
      r.setDescription(req.description());
      r.executer();
      this.compteRepository.save(compte);
      TransactionResponse response = TransactionResponse.from((Transaction)this.transactionRepository.save(r));
      this.auditService.enregistrerSysteme(String.format("RETRAIT — Compte %s — Montant %.2f FCFA — Canal %s — Réf %s", compte.getNumeroCompte(), req.montant(), r.getCanal(), response.reference()));
      this.fraudeService.detecterEtBloquerSiSuspect(compte.getIdCompte());
      return response;
   }

   public TransactionResponse effectuerVirement(TransactionRequest req) {
      if (req.idCompteDestination() == null) {
         throw new BusinessException("Le compte destinataire est obligatoire pour un virement");
      } else if (req.idCompte().equals(req.idCompteDestination())) {
         throw new BusinessException("Le compte source et le compte destinataire ne peuvent pas être identiques");
      } else {
         Compte source = this.getCompteActif(req.idCompte());
         Compte destination = (Compte)this.compteRepository.findById(req.idCompteDestination()).orElseThrow(() -> new ResourceNotFoundException("Compte destinataire", req.idCompteDestination()));
         Virement virement = new Virement();
         virement.setCompte(source);
         virement.setCompteDestination(destination);
         virement.setMontant(req.montant());
         virement.setDescription(req.description());
         virement.executer();
         this.compteRepository.save(source);
         this.compteRepository.save(destination);
         TransactionResponse response = TransactionResponse.from((Transaction)this.transactionRepository.save(virement));
         this.auditService.enregistrerSysteme(String.format("VIREMENT — Compte source %s → dest %s — Montant %.2f FCFA — Réf %s", source.getNumeroCompte(), destination.getNumeroCompte(), req.montant(), response.reference()));
         this.fraudeService.detecterEtBloquerSiSuspect(source.getIdCompte());
         return response;
      }
   }

   @Transactional(
      readOnly = true
   )
   public List getReleve(Long idCompte) {
      return this.transactionRepository.findByCompteIdCompteOrderByDateHeureDesc(idCompte).stream().map(TransactionResponse::from).toList();
   }

   @Transactional(
      readOnly = true
   )
   public List getReleveParPeriode(Long idCompte, LocalDateTime debut, LocalDateTime fin) {
      return this.transactionRepository.findByCompteAndPeriode(idCompte, debut, fin).stream().map(TransactionResponse::from).toList();
   }

   @Transactional(
      readOnly = true
   )
   public List survellerOperations() {
      return this.transactionRepository.findByStatut(StatutTransaction.REJETEE).stream().map(TransactionResponse::from).toList();
   }

   private Compte getCompteActif(Long idCompte) {
      Compte compte = (Compte)this.compteRepository.findById(idCompte).orElseThrow(() -> new ResourceNotFoundException("Compte", idCompte));
      if (!compte.isActif()) {
         String var10002 = compte.getNumeroCompte();
         throw new BusinessException("Le compte " + var10002 + " n'est pas actif (statut : " + String.valueOf(compte.getStatut()) + ")");
      } else {
         return compte;
      }
   }

   @Generated
   public TransactionService(final TransactionRepository transactionRepository, final CompteRepository compteRepository, final AuditService auditService, final FraudeService fraudeService) {
      this.transactionRepository = transactionRepository;
      this.compteRepository = compteRepository;
      this.auditService = auditService;
      this.fraudeService = fraudeService;
   }
}
