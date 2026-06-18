package com.microfinance.service;

import com.microfinance.dto.response.CompteResponse;
import com.microfinance.exception.BusinessException;
import com.microfinance.exception.ResourceNotFoundException;
import com.microfinance.model.Client;
import com.microfinance.model.Compte;
import com.microfinance.model.enums.StatutCompte;
import com.microfinance.repository.ClientRepository;
import com.microfinance.repository.CompteRepository;
import java.math.BigDecimal;
import java.util.List;
import lombok.Generated;
import org.springframework.stereotype.Service;
import org.springframework.transaction.annotation.Transactional;

@Service
@Transactional
public class CompteService {
   private final CompteRepository compteRepository;
   private final ClientRepository clientRepository;

   public CompteResponse ouvrirCompte(Long idClient, BigDecimal depotInitial) {
      Client client = (Client)this.clientRepository.findById(idClient).orElseThrow(() -> new ResourceNotFoundException("Client", idClient));
      if (!client.isActif()) {
         throw new BusinessException("Le client n'est pas actif");
      } else if (depotInitial.compareTo(new BigDecimal("5000")) < 0) {
         throw new BusinessException("Le dépôt initial minimum est de 5 000 FCFA");
      } else {
         Compte compte = Compte.builder().client(client).solde(depotInitial).statut(StatutCompte.EN_ATTENTE).build();
         return CompteResponse.from((Compte)this.compteRepository.save(compte));
      }
   }

   public CompteResponse validerOuverture(Long idCompte) {
      Compte compte = this.findOrThrow(idCompte);
      if (!StatutCompte.EN_ATTENTE.equals(compte.getStatut())) {
         throw new BusinessException("Seul un compte en attente peut être activé");
      } else {
         compte.activer();
         return CompteResponse.from((Compte)this.compteRepository.save(compte));
      }
   }

   public CompteResponse bloquerCompte(Long idCompte) {
      Compte compte = this.findOrThrow(idCompte);
      if (StatutCompte.FERME.equals(compte.getStatut())) {
         throw new BusinessException("Impossible de bloquer un compte fermé");
      } else {
         compte.bloquer();
         return CompteResponse.from((Compte)this.compteRepository.save(compte));
      }
   }

   public CompteResponse debloquerCompte(Long idCompte) {
      Compte compte = this.findOrThrow(idCompte);
      if (!StatutCompte.BLOQUE.equals(compte.getStatut())) {
         throw new BusinessException("Le compte n'est pas bloqué");
      } else {
         compte.debloquer();
         return CompteResponse.from((Compte)this.compteRepository.save(compte));
      }
   }

   public CompteResponse suspendreCompte(Long idCompte) {
      Compte compte = this.findOrThrow(idCompte);
      if (!StatutCompte.ACTIF.equals(compte.getStatut())) {
         throw new BusinessException("Seul un compte actif peut être suspendu");
      } else {
         compte.suspendre();
         return CompteResponse.from((Compte)this.compteRepository.save(compte));
      }
   }

   public CompteResponse fermerCompte(Long idCompte) {
      Compte compte = this.findOrThrow(idCompte);
      if (StatutCompte.FERME.equals(compte.getStatut())) {
         throw new BusinessException("Le compte est déjà fermé");
      } else if (compte.getSolde().compareTo(BigDecimal.ZERO) > 0) {
         throw new BusinessException("Impossible de fermer un compte avec un solde positif. Solde : " + String.valueOf(compte.getSolde()) + " FCFA");
      } else {
         compte.fermer();
         return CompteResponse.from((Compte)this.compteRepository.save(compte));
      }
   }

   @Transactional(
      readOnly = true
   )
   public CompteResponse getCompte(Long idCompte) {
      return CompteResponse.from(this.findOrThrow(idCompte));
   }

   @Transactional(
      readOnly = true
   )
   public List getComptesClient(Long idClient) {
      return this.compteRepository.findByClientIdClient(idClient).stream().map(CompteResponse::from).toList();
   }

   @Transactional(
      readOnly = true
   )
   public List listerTousComptes() {
      return this.compteRepository.findAll().stream().map(CompteResponse::from).toList();
   }

   @Transactional
   public CompteResponse creerCompteAutoClient(String emailClient) {
      Client client = (Client)this.clientRepository.findByEmail(emailClient).orElseThrow(() -> new ResourceNotFoundException("Client", 0L));
      List<Compte> existants = this.compteRepository.findByClientIdClient(client.getIdClient());
      if (!existants.isEmpty()) {
         return CompteResponse.from((Compte)existants.stream().filter((c) -> StatutCompte.ACTIF.equals(c.getStatut())).findFirst().orElse((Compte)existants.get(0)));
      } else {
         Compte compte = Compte.builder().client(client).solde(BigDecimal.ZERO).statut(StatutCompte.ACTIF).montantMinSolde(BigDecimal.ZERO).plafondRetrait(new BigDecimal("500000")).build();
         return CompteResponse.from((Compte)this.compteRepository.save(compte));
      }
   }

   private Compte findOrThrow(Long idCompte) {
      return (Compte)this.compteRepository.findById(idCompte).orElseThrow(() -> new ResourceNotFoundException("Compte", idCompte));
   }

   @Generated
   public CompteService(final CompteRepository compteRepository, final ClientRepository clientRepository) {
      this.compteRepository = compteRepository;
      this.clientRepository = clientRepository;
   }
}
