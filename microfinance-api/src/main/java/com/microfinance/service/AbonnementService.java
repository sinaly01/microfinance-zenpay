package com.microfinance.service;

import com.microfinance.exception.BusinessException;
import com.microfinance.exception.ResourceNotFoundException;
import com.microfinance.model.Client;
import com.microfinance.model.Compte;
import com.microfinance.model.FactureAbonnement;
import com.microfinance.model.OffreAbonnement;
import com.microfinance.repository.ClientRepository;
import com.microfinance.repository.CompteRepository;
import com.microfinance.repository.FactureAbonnementRepository;
import com.microfinance.repository.OffreAbonnementRepository;
import java.math.BigDecimal;
import java.math.RoundingMode;
import java.time.LocalDateTime;
import java.util.List;
import java.util.Map;
import lombok.Generated;
import org.springframework.stereotype.Service;
import org.springframework.transaction.annotation.Transactional;

@Service
@Transactional
public class AbonnementService {
   private final OffreAbonnementRepository offreRepo;
   private final ClientRepository clientRepository;
   private final CompteRepository compteRepository;
   private final FactureAbonnementRepository factureRepo;

   @Transactional(
      readOnly = true
   )
   public List listerOffres() {
      return this.offreRepo.findAll();
   }

   public Map changerOffre(Long idClient, Long idOffre) {
      Client client = (Client)this.clientRepository.findById(idClient).orElseThrow(() -> new ResourceNotFoundException("Client", idClient));
      OffreAbonnement offre = (OffreAbonnement)this.offreRepo.findById(idOffre).orElseThrow(() -> new ResourceNotFoundException("Offre", idOffre));
      client.setOffreAbonnement(offre);
      client.setDateProchainPrelevement(LocalDateTime.now().plusMonths(1L));
      this.clientRepository.save(client);
      return Map.of("message", "Offre changée avec succès", "nouvelleOffre", offre.getNomOffre());
   }

   @Transactional(
      readOnly = true
   )
   public Map simulerFrais(Long idClient, BigDecimal montant, String typeOperation) {
      Client client = (Client)this.clientRepository.findById(idClient).orElseThrow(() -> new ResourceNotFoundException("Client", idClient));
      OffreAbonnement offre = client.getOffreAbonnement();
      BigDecimal taux = offre != null && "VIREMENT".equalsIgnoreCase(typeOperation) ? offre.getFraisVirementInterne() : (offre != null ? offre.getPourcentageFraisMomo() : new BigDecimal("1.50"));
      BigDecimal frais = montant.multiply(taux).divide(BigDecimal.valueOf(100L), 2, RoundingMode.HALF_UP);
      BigDecimal total = montant.add(frais);
      return Map.of("montant", montant, "taux", taux, "frais", frais, "total", total, "offre", offre != null ? offre.getNomOffre() : "STANDARD");
   }

   public void prelevementMensuel(Long idClient) {
      Client client = (Client)this.clientRepository.findById(idClient).orElseThrow(() -> new ResourceNotFoundException("Client", idClient));
      OffreAbonnement offre = client.getOffreAbonnement();
      if (offre != null && offre.getPrixMensuel().compareTo(BigDecimal.ZERO) != 0) {
         Compte compteActif = (Compte)this.compteRepository.findAll().stream().filter((c) -> c.getClient().getIdClient().equals(idClient) && c.isActif()).findFirst().orElseThrow(() -> new BusinessException("Aucun compte actif trouvé pour ce client"));
         FactureAbonnement facture;
         if (compteActif.getSolde().compareTo(offre.getPrixMensuel()) >= 0) {
            compteActif.setSolde(compteActif.getSolde().subtract(offre.getPrixMensuel()));
            this.compteRepository.save(compteActif);
            client.setDateProchainPrelevement(LocalDateTime.now().plusMonths(1L));
            facture = FactureAbonnement.builder().client(client).offre(offre).montantPreleve(offre.getPrixMensuel()).statutPaiement("PAYE").build();
         } else {
            OffreAbonnement standard = (OffreAbonnement)this.offreRepo.findByNomOffre("STANDARD").orElseThrow(() -> new BusinessException("Offre STANDARD introuvable"));
            client.setOffreAbonnement(standard);
            facture = FactureAbonnement.builder().client(client).offre(offre).montantPreleve(offre.getPrixMensuel()).statutPaiement("ECHEC_SOLDE_INSUFFISANT").build();
         }

         this.factureRepo.save(facture);
         this.clientRepository.save(client);
      }
   }

   @Transactional(
      readOnly = true
   )
   public BigDecimal calculerFrais(Client client, BigDecimal montant, boolean estVirement) {
      OffreAbonnement offre = client.getOffreAbonnement();
      BigDecimal taux;
      if (estVirement) {
         taux = offre != null ? offre.getFraisVirementInterne() : new BigDecimal("1.00");
      } else {
         taux = offre != null ? offre.getPourcentageFraisMomo() : new BigDecimal("1.50");
      }

      return montant.multiply(taux).divide(BigDecimal.valueOf(100L), 2, RoundingMode.HALF_UP);
   }

   @Generated
   public AbonnementService(final OffreAbonnementRepository offreRepo, final ClientRepository clientRepository, final CompteRepository compteRepository, final FactureAbonnementRepository factureRepo) {
      this.offreRepo = offreRepo;
      this.clientRepository = clientRepository;
      this.compteRepository = compteRepository;
      this.factureRepo = factureRepo;
   }
}
