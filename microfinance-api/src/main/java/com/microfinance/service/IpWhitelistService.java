package com.microfinance.service;

import com.microfinance.model.AdresseIpAutorisee;
import com.microfinance.model.DemandeAccesExterieur;
import com.microfinance.model.Gestionnaire;
import com.microfinance.repository.AdresseIpAutoriseeRepository;
import com.microfinance.repository.DemandeAccesExterieurRepository;
import com.microfinance.repository.GestionnaireRepository;
import java.time.LocalDateTime;
import java.util.List;
import lombok.Generated;
import org.springframework.stereotype.Service;
import org.springframework.transaction.annotation.Transactional;

@Service
public class IpWhitelistService {
   private final AdresseIpAutoriseeRepository ipRepo;
   private final DemandeAccesExterieurRepository demandeRepo;
   private final GestionnaireRepository gestionnaireRepo;

   public String normaliserIp(String ip) {
      if (ip != null && !ip.isBlank()) {
         if (!ip.startsWith("::ffff:") && !ip.startsWith("::FFFF:")) {
            return "0:0:0:0:0:0:0:1".equals(ip) ? "127.0.0.1" : ip;
         } else {
            return ip.substring(7);
         }
      } else {
         return ip;
      }
   }

   @Transactional(
      readOnly = true
   )
   public boolean estAutorisee(String adresseIp) {
      String normalized = this.normaliserIp(adresseIp);
      if (!"127.0.0.1".equals(normalized) && !"::1".equals(normalized)) {
         return this.ipRepo.existsByAdresseIpAndEstActiveTrue(normalized) || this.ipRepo.existsByAdresseIpAndEstActiveTrue(adresseIp);
      } else {
         return true;
      }
   }

   @Transactional(
      readOnly = true
   )
   public boolean estAutoriseTemporairement(String adresseIp, Long idGestionnaire) {
      String normalized = this.normaliserIp(adresseIp);
      boolean byRaw = (Boolean)this.demandeRepo.findTopByGestionnaire_IdGestionnaireAndAdresseIpOrderByDateCreationDesc(idGestionnaire, adresseIp).map(DemandeAccesExterieur::estApprouveEtValide).orElse(false);
      return byRaw ? true : (Boolean)this.demandeRepo.findTopByGestionnaire_IdGestionnaireAndAdresseIpOrderByDateCreationDesc(idGestionnaire, normalized).map(DemandeAccesExterieur::estApprouveEtValide).orElse(false);
   }

   @Transactional
   public DemandeAccesExterieur creerDemandeAcces(String emailGestionnaire, String adresseIp) {
      Gestionnaire gestionnaire = (Gestionnaire)this.gestionnaireRepo.findByEmail(emailGestionnaire).orElseThrow(() -> new RuntimeException("Gestionnaire introuvable"));
      DemandeAccesExterieur demande = DemandeAccesExterieur.builder().gestionnaire(gestionnaire).adresseIp(adresseIp).nomReseau("Réseau inconnu").statut("SUSPENDU").build();
      return (DemandeAccesExterieur)this.demandeRepo.save(demande);
   }

   @Transactional
   public DemandeAccesExterieur approuverDemande(Long idDemande, int heuresValidite, String emailApprovateur) {
      DemandeAccesExterieur demande = (DemandeAccesExterieur)this.demandeRepo.findById(idDemande).orElseThrow(() -> new RuntimeException("Demande introuvable"));
      Gestionnaire approvateur = (Gestionnaire)this.gestionnaireRepo.findByEmail(emailApprovateur).orElseThrow(() -> new RuntimeException("Approvateur introuvable"));
      demande.setStatut("APPROUVE");
      demande.setDateValidite(LocalDateTime.now().plusHours((long)heuresValidite));
      demande.setApprouvePar(approvateur);
      return (DemandeAccesExterieur)this.demandeRepo.save(demande);
   }

   @Transactional
   public DemandeAccesExterieur rejeterDemande(Long idDemande) {
      DemandeAccesExterieur demande = (DemandeAccesExterieur)this.demandeRepo.findById(idDemande).orElseThrow(() -> new RuntimeException("Demande introuvable"));
      demande.setStatut("REJETE");
      return (DemandeAccesExterieur)this.demandeRepo.save(demande);
   }

   @Transactional(
      readOnly = true
   )
   public List getDemandesEnAttente() {
      return this.demandeRepo.findByStatutOrderByDateCreationDesc("SUSPENDU");
   }

   @Transactional(
      readOnly = true
   )
   public List listerIps() {
      return this.ipRepo.findAll();
   }

   @Transactional
   public AdresseIpAutorisee ajouterIp(String adresseIp, String nomMachine) {
      return (AdresseIpAutorisee)this.ipRepo.save(AdresseIpAutorisee.builder().adresseIp(adresseIp).nomMachine(nomMachine).estActive(true).build());
   }

   @Transactional
   public void desactiverIp(Long idIp) {
      this.ipRepo.findById(idIp).ifPresent((ip) -> {
         ip.setEstActive(false);
         this.ipRepo.save(ip);
      });
   }

   @Transactional
   public void activerIp(Long idIp) {
      this.ipRepo.findById(idIp).ifPresent((ip) -> {
         ip.setEstActive(true);
         this.ipRepo.save(ip);
      });
   }

   @Generated
   public IpWhitelistService(final AdresseIpAutoriseeRepository ipRepo, final DemandeAccesExterieurRepository demandeRepo, final GestionnaireRepository gestionnaireRepo) {
      this.ipRepo = ipRepo;
      this.demandeRepo = demandeRepo;
      this.gestionnaireRepo = gestionnaireRepo;
   }
}
