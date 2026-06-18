package com.microfinance.controller;

import com.microfinance.exception.ResourceNotFoundException;
import com.microfinance.model.Client;
import com.microfinance.model.DemandeChangementOffre;
import com.microfinance.model.Gestionnaire;
import com.microfinance.model.OffreAbonnement;
import com.microfinance.repository.ClientRepository;
import com.microfinance.repository.DemandeChangementOffreRepository;
import com.microfinance.repository.GestionnaireRepository;
import com.microfinance.repository.OffreAbonnementRepository;
import com.microfinance.service.AuditService;
import java.time.LocalDateTime;
import java.util.HashMap;
import java.util.Map;
import lombok.Generated;
import org.springframework.http.HttpStatus;
import org.springframework.http.ResponseEntity;
import org.springframework.security.access.prepost.PreAuthorize;
import org.springframework.security.core.Authentication;
import org.springframework.security.core.context.SecurityContextHolder;
import org.springframework.web.bind.annotation.GetMapping;
import org.springframework.web.bind.annotation.PathVariable;
import org.springframework.web.bind.annotation.PostMapping;
import org.springframework.web.bind.annotation.PutMapping;
import org.springframework.web.bind.annotation.RequestMapping;
import org.springframework.web.bind.annotation.RequestParam;
import org.springframework.web.bind.annotation.RestController;

@RestController
@RequestMapping({"/api/demandes-abonnement"})
public class DemandeAbonnementController {
   private final DemandeChangementOffreRepository demandeRepo;
   private final ClientRepository clientRepo;
   private final OffreAbonnementRepository offreRepo;
   private final GestionnaireRepository gestionnaireRepo;
   private final AuditService auditService;

   private Map toMap(DemandeChangementOffre d) {
      Map<String, Object> m = new HashMap();
      m.put("idDemande", d.getIdDemande());
      m.put("statut", d.getStatut());
      m.put("messageClient", d.getMessageClient());
      m.put("dateCreation", d.getDateCreation());
      m.put("dateTraitement", d.getDateTraitement());
      if (d.getOffreDemandee() != null) {
         Map<String, Object> offre = new HashMap();
         offre.put("idOffre", d.getOffreDemandee().getIdOffre());
         offre.put("nomOffre", d.getOffreDemandee().getNomOffre());
         offre.put("prixMensuel", d.getOffreDemandee().getPrixMensuel());
         m.put("offreDemandee", offre);
      }

      if (d.getClient() != null) {
         m.put("idClient", d.getClient().getIdClient());
         String var10002 = d.getClient().getPrenom();
         m.put("nomClient", var10002 + " " + d.getClient().getNom());
      }

      if (d.getTraitePar() != null) {
         String var4 = d.getTraitePar().getPrenom();
         m.put("traitePar", var4 + " " + d.getTraitePar().getNom());
      }

      return m;
   }

   @PostMapping
   @PreAuthorize("hasAnyRole('CLIENT', 'GESTIONNAIRE', 'ADMIN_SYSTEME', 'SUPER_ADMIN')")
   public ResponseEntity creerDemande(@RequestParam Long idClient, @RequestParam Long idOffre, @RequestParam(required = false) String messageClient) {
      Client client = (Client)this.clientRepo.findById(idClient).orElseThrow(() -> new ResourceNotFoundException("Client", idClient));
      OffreAbonnement offre = (OffreAbonnement)this.offreRepo.findById(idOffre).orElseThrow(() -> new ResourceNotFoundException("OffreAbonnement", idOffre));
      DemandeChangementOffre demande = DemandeChangementOffre.builder().client(client).offreDemandee(offre).statut("EN_ATTENTE").messageClient(messageClient).build();
      return ResponseEntity.status(HttpStatus.CREATED).body(this.toMap((DemandeChangementOffre)this.demandeRepo.save(demande)));
   }

   @GetMapping
   @PreAuthorize("hasAnyRole('GESTIONNAIRE', 'SUPERVISOR', 'ADMIN_SYSTEME', 'SUPER_ADMIN')")
   public ResponseEntity listerTout() {
      return ResponseEntity.ok(this.demandeRepo.findAllByOrderByDateCreationDesc().stream().map(this::toMap).toList());
   }

   @GetMapping({"/en-attente"})
   @PreAuthorize("hasAnyRole('GESTIONNAIRE', 'SUPERVISOR', 'ADMIN_SYSTEME', 'SUPER_ADMIN')")
   public ResponseEntity enAttente() {
      return ResponseEntity.ok(this.demandeRepo.findByStatutOrderByDateCreationDesc("EN_ATTENTE").stream().map(this::toMap).toList());
   }

   @GetMapping({"/client/{idClient}"})
   @PreAuthorize("hasAnyRole('CLIENT', 'GESTIONNAIRE', 'ADMIN_SYSTEME', 'SUPER_ADMIN')")
   public ResponseEntity mesDemandesPourClient(@PathVariable Long idClient) {
      return ResponseEntity.ok(this.demandeRepo.findByClient_IdClientOrderByDateCreationDesc(idClient).stream().map(this::toMap).toList());
   }

   @PutMapping({"/{idDemande}/approuver"})
   @PreAuthorize("hasAnyRole('GESTIONNAIRE', 'SUPERVISOR', 'ADMIN_SYSTEME', 'SUPER_ADMIN')")
   public ResponseEntity approuver(@PathVariable Long idDemande) {
      DemandeChangementOffre demande = (DemandeChangementOffre)this.demandeRepo.findById(idDemande).orElseThrow(() -> new ResourceNotFoundException("DemandeChangementOffre", idDemande));
      Authentication auth = SecurityContextHolder.getContext().getAuthentication();
      Gestionnaire traiteur = (Gestionnaire)this.gestionnaireRepo.findByEmail(auth.getName()).orElse((Object)null);
      Client client = demande.getClient();
      client.setOffreAbonnement(demande.getOffreDemandee());
      this.clientRepo.save(client);
      demande.setStatut("APPROUVE");
      demande.setDateTraitement(LocalDateTime.now());
      demande.setTraitePar(traiteur);
      this.demandeRepo.save(demande);
      String gestName = traiteur != null ? traiteur.getEmail() : "système";
      this.auditService.enregistrer(gestName, "ABONNEMENT_APPROUVE — client=" + client.getIdClient() + " — offre=" + demande.getOffreDemandee().getNomOffre(), "N/A");
      return ResponseEntity.ok(Map.of("message", "Demande approuvée. Offre changée vers " + demande.getOffreDemandee().getNomOffre(), "idClient", client.getIdClient(), "nouvelleOffre", demande.getOffreDemandee().getNomOffre()));
   }

   @PutMapping({"/{idDemande}/rejeter"})
   @PreAuthorize("hasAnyRole('GESTIONNAIRE', 'SUPERVISOR', 'ADMIN_SYSTEME', 'SUPER_ADMIN')")
   public ResponseEntity rejeter(@PathVariable Long idDemande) {
      DemandeChangementOffre demande = (DemandeChangementOffre)this.demandeRepo.findById(idDemande).orElseThrow(() -> new ResourceNotFoundException("DemandeChangementOffre", idDemande));
      Authentication auth = SecurityContextHolder.getContext().getAuthentication();
      Gestionnaire traiteur = (Gestionnaire)this.gestionnaireRepo.findByEmail(auth.getName()).orElse((Object)null);
      demande.setStatut("REJETE");
      demande.setDateTraitement(LocalDateTime.now());
      demande.setTraitePar(traiteur);
      this.demandeRepo.save(demande);
      String gestName = traiteur != null ? traiteur.getEmail() : "système";
      this.auditService.enregistrer(gestName, "ABONNEMENT_REJETE — demande=" + idDemande, "N/A");
      return ResponseEntity.ok(Map.of("message", "Demande rejetée."));
   }

   @Generated
   public DemandeAbonnementController(final DemandeChangementOffreRepository demandeRepo, final ClientRepository clientRepo, final OffreAbonnementRepository offreRepo, final GestionnaireRepository gestionnaireRepo, final AuditService auditService) {
      this.demandeRepo = demandeRepo;
      this.clientRepo = clientRepo;
      this.offreRepo = offreRepo;
      this.gestionnaireRepo = gestionnaireRepo;
      this.auditService = auditService;
   }
}
