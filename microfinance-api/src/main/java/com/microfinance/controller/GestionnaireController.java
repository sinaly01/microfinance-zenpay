package com.microfinance.controller;

import com.microfinance.exception.ResourceNotFoundException;
import com.microfinance.model.Gestionnaire;
import com.microfinance.model.enums.RoleUtilisateur;
import com.microfinance.repository.GestionnaireRepository;
import com.microfinance.service.AuditService;
import java.time.LocalDate;
import java.util.Map;
import lombok.Generated;
import org.springframework.http.HttpStatus;
import org.springframework.http.ResponseEntity;
import org.springframework.security.access.prepost.PreAuthorize;
import org.springframework.security.core.Authentication;
import org.springframework.security.crypto.password.PasswordEncoder;
import org.springframework.web.bind.annotation.GetMapping;
import org.springframework.web.bind.annotation.PathVariable;
import org.springframework.web.bind.annotation.PostMapping;
import org.springframework.web.bind.annotation.PutMapping;
import org.springframework.web.bind.annotation.RequestBody;
import org.springframework.web.bind.annotation.RequestMapping;
import org.springframework.web.bind.annotation.RequestParam;
import org.springframework.web.bind.annotation.RestController;

@RestController
@RequestMapping({"/api/gestionnaires"})
public class GestionnaireController {
   private final GestionnaireRepository gestionnaireRepo;
   private final AuditService auditService;
   private final PasswordEncoder passwordEncoder;

   @GetMapping
   @PreAuthorize("hasAnyRole('SUPER_ADMIN', 'ADMIN_SYSTEME')")
   public ResponseEntity listerTout() {
      return ResponseEntity.ok(this.gestionnaireRepo.findAll());
   }

   @GetMapping({"/role/{role}"})
   @PreAuthorize("hasAnyRole('SUPER_ADMIN', 'ADMIN_SYSTEME')")
   public ResponseEntity parRole(@PathVariable String role) {
      try {
         RoleUtilisateur r = RoleUtilisateur.valueOf(role);
         return ResponseEntity.ok(this.gestionnaireRepo.findByRole(r));
      } catch (IllegalArgumentException var3) {
         return ResponseEntity.badRequest().build();
      }
   }

   @PostMapping
   @PreAuthorize("hasRole('SUPER_ADMIN')")
   public ResponseEntity creer(@RequestBody Map body) {
      if (this.gestionnaireRepo.existsByEmail((String)body.get("email"))) {
         return ResponseEntity.status(HttpStatus.CONFLICT).build();
      } else {
         RoleUtilisateur role = RoleUtilisateur.valueOf((String)body.getOrDefault("role", "ROLE_GESTIONNAIRE"));
         Gestionnaire.GestionnaireBuilder builder = Gestionnaire.builder().nom((String)body.get("nom")).prenom((String)body.get("prenom")).email((String)body.get("email")).motDePasse(this.passwordEncoder.encode((CharSequence)body.getOrDefault("motDePasse", "Admin@2024"))).role(role).dateEmbauche(LocalDate.now()).actif(true);
         if (role == RoleUtilisateur.ROLE_SUPER_ADMIN) {
            builder.cleSecrete(this.passwordEncoder.encode("SuperKey@2024"));
         }

         Gestionnaire saved = (Gestionnaire)this.gestionnaireRepo.save(builder.build());
         AuditService var10000 = this.auditService;
         String var10001 = saved.getEmail();
         var10000.enregistrerSysteme("PERSONNEL_CREE — " + var10001 + " — " + String.valueOf(saved.getRole()));
         return ResponseEntity.status(HttpStatus.CREATED).body(saved);
      }
   }

   @PutMapping({"/me/cle-secrete"})
   @PreAuthorize("hasRole('SUPER_ADMIN')")
   public ResponseEntity changerCleSecrete(@RequestBody Map body, Authentication auth) {
      String email = auth.getName();
      String ancienne = (String)body.get("ancienneCle");
      String nouvelle = (String)body.get("nouvelleCle");
      if (ancienne != null && nouvelle != null && nouvelle.length() >= 6) {
         Gestionnaire g = (Gestionnaire)this.gestionnaireRepo.findByEmail(email).orElseThrow(() -> new ResourceNotFoundException("Gestionnaire", 0L));
         if (g.getCleSecrete() != null && this.passwordEncoder.matches(ancienne, g.getCleSecrete())) {
            g.setCleSecrete(this.passwordEncoder.encode(nouvelle));
            this.gestionnaireRepo.save(g);
            this.auditService.enregistrerSysteme("CLE_SECRETE_MODIFIEE — " + email);
            return ResponseEntity.ok(Map.of("message", "Clé secrète modifiée avec succès."));
         } else {
            return ResponseEntity.status(HttpStatus.UNAUTHORIZED).body(Map.of("error", "Clé secrète actuelle incorrecte."));
         }
      } else {
         return ResponseEntity.badRequest().body(Map.of("error", "Données incomplètes (minimum 6 caractères)."));
      }
   }

   @PutMapping({"/{id}/suspendre"})
   @PreAuthorize("hasRole('SUPER_ADMIN')")
   public ResponseEntity suspendre(@PathVariable Long id) {
      Gestionnaire g = (Gestionnaire)this.gestionnaireRepo.findById(id).orElseThrow(() -> new ResourceNotFoundException("Gestionnaire", id));
      g.setActif(false);
      this.gestionnaireRepo.save(g);
      this.auditService.enregistrerSysteme("PERSONNEL_SUSPENDU — " + g.getEmail());
      return ResponseEntity.ok(Map.of("message", "Compte suspendu.", "email", g.getEmail(), "actif", false));
   }

   @PutMapping({"/{id}/activer"})
   @PreAuthorize("hasRole('SUPER_ADMIN')")
   public ResponseEntity activer(@PathVariable Long id) {
      Gestionnaire g = (Gestionnaire)this.gestionnaireRepo.findById(id).orElseThrow(() -> new ResourceNotFoundException("Gestionnaire", id));
      g.setActif(true);
      this.gestionnaireRepo.save(g);
      this.auditService.enregistrerSysteme("PERSONNEL_ACTIVE — " + g.getEmail());
      return ResponseEntity.ok(Map.of("message", "Compte réactivé.", "email", g.getEmail(), "actif", true));
   }

   @PutMapping({"/{id}/reset-password"})
   @PreAuthorize("hasRole('SUPER_ADMIN')")
   public ResponseEntity resetPassword(@PathVariable Long id, @RequestParam(defaultValue = "Admin@2024") String nouveauMdp) {
      Gestionnaire g = (Gestionnaire)this.gestionnaireRepo.findById(id).orElseThrow(() -> new ResourceNotFoundException("Gestionnaire", id));
      g.setMotDePasse(this.passwordEncoder.encode(nouveauMdp));
      this.gestionnaireRepo.save(g);
      this.auditService.enregistrerSysteme("MDP_REINITIALISE — " + g.getEmail());
      return ResponseEntity.ok(Map.of("message", "Mot de passe réinitialisé."));
   }

   @Generated
   public GestionnaireController(final GestionnaireRepository gestionnaireRepo, final AuditService auditService, final PasswordEncoder passwordEncoder) {
      this.gestionnaireRepo = gestionnaireRepo;
      this.auditService = auditService;
      this.passwordEncoder = passwordEncoder;
   }
}
