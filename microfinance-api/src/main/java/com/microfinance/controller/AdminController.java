package com.microfinance.controller;

import com.microfinance.exception.ResourceNotFoundException;
import com.microfinance.model.AdresseIpAutorisee;
import com.microfinance.model.Compte;
import com.microfinance.model.SessionConnexion;
import com.microfinance.model.enums.StatutCompte;
import com.microfinance.repository.CompteRepository;
import com.microfinance.repository.GestionnaireRepository;
import com.microfinance.repository.SessionConnexionRepository;
import com.microfinance.repository.TransactionRepository;
import com.microfinance.service.AuditService;
import com.microfinance.service.IpWhitelistService;
import com.microfinance.service.SessionService;
import java.util.HashMap;
import java.util.List;
import java.util.Map;
import lombok.Generated;
import org.springframework.data.domain.Sort;
import org.springframework.data.domain.Sort.Direction;
import org.springframework.http.ResponseEntity;
import org.springframework.security.access.prepost.PreAuthorize;
import org.springframework.web.bind.annotation.DeleteMapping;
import org.springframework.web.bind.annotation.GetMapping;
import org.springframework.web.bind.annotation.PathVariable;
import org.springframework.web.bind.annotation.PostMapping;
import org.springframework.web.bind.annotation.PutMapping;
import org.springframework.web.bind.annotation.RequestMapping;
import org.springframework.web.bind.annotation.RequestParam;
import org.springframework.web.bind.annotation.RestController;

@RestController
@RequestMapping({"/api/admin"})
public class AdminController {
   private final IpWhitelistService ipWhitelistService;
   private final CompteRepository compteRepository;
   private final AuditService auditService;
   private final SessionService sessionService;
   private final SessionConnexionRepository sessionConnexionRepo;
   private final GestionnaireRepository gestionnaireRepo;
   private final TransactionRepository transactionRepo;

   @GetMapping({"/sessions"})
   @PreAuthorize("hasAnyRole('SUPER_ADMIN', 'ADMIN_SYSTEME')")
   public ResponseEntity sessionsActives() {
      List<SessionConnexion> sessions = this.sessionService.getSessionsActives();
      List<Map<String, Object>> result = sessions.stream().map((s) -> {
         Map<String, Object> m = new HashMap();
         m.put("idSession", s.getIdSession());
         m.put("adresseIp", s.getAdresseIp());
         m.put("dateConnexion", s.getDateConnexion());
         m.put("statutSession", s.getStatutSession());
         if (s.getGestionnaire() != null) {
            m.put("type", "GESTIONNAIRE");
            m.put("email", s.getGestionnaire().getEmail());
            String var10002 = s.getGestionnaire().getPrenom();
            m.put("nom", var10002 + " " + s.getGestionnaire().getNom());
            m.put("role", s.getGestionnaire().getRole().name());
         } else if (s.getClient() != null) {
            m.put("type", "CLIENT");
            m.put("email", s.getClient().getEmail());
            String var2 = s.getClient().getPrenom();
            m.put("nom", var2 + " " + s.getClient().getNom());
            m.put("role", "ROLE_CLIENT");
         } else {
            m.put("type", "INCONNU");
            m.put("email", "—");
            m.put("nom", "—");
            m.put("role", "—");
         }

         return m;
      }).toList();
      return ResponseEntity.ok(result);
   }

   @GetMapping({"/transactions"})
   @PreAuthorize("hasAnyRole('SUPER_ADMIN', 'ADMIN_SYSTEME', 'ADMIN_BD', 'SUPERVISOR')")
   public ResponseEntity toutesTransactions() {
      return ResponseEntity.ok(this.transactionRepo.findAll(Sort.by(Direction.DESC, new String[]{"dateHeure"})));
   }

   @GetMapping({"/ips"})
   @PreAuthorize("hasAnyRole('SUPER_ADMIN', 'ADMIN_SYSTEME')")
   public ResponseEntity listerIps() {
      return ResponseEntity.ok(this.ipWhitelistService.listerIps());
   }

   @PostMapping({"/ips"})
   @PreAuthorize("hasRole('SUPER_ADMIN')")
   public ResponseEntity ajouterIp(@RequestParam String adresseIp, @RequestParam String nomMachine) {
      AdresseIpAutorisee ip = this.ipWhitelistService.ajouterIp(adresseIp, nomMachine);
      this.auditService.enregistrerSysteme("IP_AJOUTEE — " + adresseIp + " (" + nomMachine + ")");
      return ResponseEntity.ok(ip);
   }

   @DeleteMapping({"/ips/{idIp}"})
   @PreAuthorize("hasRole('SUPER_ADMIN')")
   public ResponseEntity desactiverIp(@PathVariable Long idIp) {
      this.ipWhitelistService.desactiverIp(idIp);
      this.auditService.enregistrerSysteme("IP_DESACTIVEE — id=" + idIp);
      return ResponseEntity.ok(Map.of("message", "Adresse IP désactivée."));
   }

   @PutMapping({"/ips/{idIp}/activer"})
   @PreAuthorize("hasRole('SUPER_ADMIN')")
   public ResponseEntity activerIp(@PathVariable Long idIp) {
      this.ipWhitelistService.activerIp(idIp);
      this.auditService.enregistrerSysteme("IP_ACTIVEE — id=" + idIp);
      return ResponseEntity.ok(Map.of("message", "Adresse IP activée."));
   }

   @GetMapping({"/demandes-acces"})
   @PreAuthorize("hasRole('SUPER_ADMIN')")
   public ResponseEntity demandesEnAttente() {
      return ResponseEntity.ok(this.ipWhitelistService.getDemandesEnAttente().stream().map((d) -> {
         Map<String, Object> m = new HashMap();
         m.put("idDemande", d.getIdDemande());
         m.put("adresseIp", d.getAdresseIp());
         m.put("nomReseau", d.getNomReseau());
         m.put("statut", d.getStatut());
         m.put("dateCreation", d.getDateCreation());
         m.put("dateValidite", d.getDateValidite());
         if (d.getGestionnaire() != null) {
            Map<String, Object> g = new HashMap();
            g.put("idGestionnaire", d.getGestionnaire().getIdGestionnaire());
            g.put("nom", d.getGestionnaire().getNom());
            g.put("prenom", d.getGestionnaire().getPrenom());
            g.put("email", d.getGestionnaire().getEmail());
            g.put("role", String.valueOf(d.getGestionnaire().getRole()));
            m.put("gestionnaire", g);
         }

         return m;
      }).toList());
   }

   @PutMapping({"/demandes-acces/{idDemande}/approuver"})
   @PreAuthorize("hasRole('SUPER_ADMIN')")
   public ResponseEntity approuverDemande(@PathVariable Long idDemande, @RequestParam(defaultValue = "4") int heuresValidite, @RequestParam String emailApprovateur) {
      this.ipWhitelistService.approuverDemande(idDemande, heuresValidite, emailApprovateur);
      this.auditService.enregistrerSysteme("ACCES_EXTERIEUR_APPROUVE — demande=" + idDemande + " — valide " + heuresValidite + "h — par " + emailApprovateur);
      return ResponseEntity.ok(Map.of("message", "Accès approuvé pour " + heuresValidite + "h"));
   }

   @PutMapping({"/demandes-acces/{idDemande}/rejeter"})
   @PreAuthorize("hasRole('SUPER_ADMIN')")
   public ResponseEntity rejeterDemande(@PathVariable Long idDemande) {
      this.ipWhitelistService.rejeterDemande(idDemande);
      this.auditService.enregistrerSysteme("ACCES_EXTERIEUR_REJETE — demande=" + idDemande);
      return ResponseEntity.ok(Map.of("message", "Demande rejetée"));
   }

   @PutMapping({"/comptes/{idCompte}/bloquer"})
   @PreAuthorize("hasAnyRole('SUPER_ADMIN', 'SUPERVISOR')")
   public ResponseEntity bloquerCompte(@PathVariable Long idCompte, @RequestParam(required = false,defaultValue = "Fraude suspectée") String motif) {
      Compte compte = (Compte)this.compteRepository.findById(idCompte).orElseThrow(() -> new ResourceNotFoundException("Compte", idCompte));
      compte.bloquer();
      this.compteRepository.save(compte);
      AuditService var10000 = this.auditService;
      String var10001 = compte.getNumeroCompte();
      var10000.enregistrerSysteme("COMPTE_BLOQUE — " + var10001 + " — Motif: " + motif);
      return ResponseEntity.ok(Map.of("message", "Compte bloqué avec succès.", "numeroCompte", compte.getNumeroCompte(), "statut", StatutCompte.BLOQUE.name()));
   }

   @PutMapping({"/comptes/{idCompte}/debloquer"})
   @PreAuthorize("hasAnyRole('SUPER_ADMIN', 'SUPERVISOR')")
   public ResponseEntity debloquerCompte(@PathVariable Long idCompte) {
      Compte compte = (Compte)this.compteRepository.findById(idCompte).orElseThrow(() -> new ResourceNotFoundException("Compte", idCompte));
      compte.debloquer();
      this.compteRepository.save(compte);
      this.auditService.enregistrerSysteme("COMPTE_DEBLOQUE — " + compte.getNumeroCompte());
      return ResponseEntity.ok(Map.of("message", "Compte débloqué avec succès.", "numeroCompte", compte.getNumeroCompte(), "statut", StatutCompte.ACTIF.name()));
   }

   @GetMapping({"/audit-logs"})
   @PreAuthorize("hasAnyRole('SUPER_ADMIN', 'ADMIN_SYSTEME', 'SUPERVISOR')")
   public ResponseEntity listerLogs() {
      return ResponseEntity.ok(this.auditService.listerLogs());
   }

   @GetMapping({"/audit-logs/{idGestionnaire}"})
   @PreAuthorize("hasAnyRole('SUPER_ADMIN', 'ADMIN_SYSTEME')")
   public ResponseEntity logsParActeur(@PathVariable Long idGestionnaire) {
      return ResponseEntity.ok(this.auditService.listerLogsParActeur(idGestionnaire));
   }

   @Generated
   public AdminController(final IpWhitelistService ipWhitelistService, final CompteRepository compteRepository, final AuditService auditService, final SessionService sessionService, final SessionConnexionRepository sessionConnexionRepo, final GestionnaireRepository gestionnaireRepo, final TransactionRepository transactionRepo) {
      this.ipWhitelistService = ipWhitelistService;
      this.compteRepository = compteRepository;
      this.auditService = auditService;
      this.sessionService = sessionService;
      this.sessionConnexionRepo = sessionConnexionRepo;
      this.gestionnaireRepo = gestionnaireRepo;
      this.transactionRepo = transactionRepo;
   }
}
