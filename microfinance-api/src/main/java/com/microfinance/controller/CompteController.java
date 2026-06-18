package com.microfinance.controller;

import com.microfinance.service.CompteService;
import java.math.BigDecimal;
import lombok.Generated;
import org.springframework.http.HttpStatus;
import org.springframework.http.ResponseEntity;
import org.springframework.security.access.prepost.PreAuthorize;
import org.springframework.security.core.Authentication;
import org.springframework.web.bind.annotation.GetMapping;
import org.springframework.web.bind.annotation.PathVariable;
import org.springframework.web.bind.annotation.PostMapping;
import org.springframework.web.bind.annotation.PutMapping;
import org.springframework.web.bind.annotation.RequestMapping;
import org.springframework.web.bind.annotation.RequestParam;
import org.springframework.web.bind.annotation.RestController;

@RestController
@RequestMapping({"/api/comptes"})
public class CompteController {
   private final CompteService compteService;

   @PostMapping({"/auto-init"})
   @PreAuthorize("hasRole('CLIENT')")
   public ResponseEntity autoInit(Authentication auth) {
      return ResponseEntity.ok(this.compteService.creerCompteAutoClient(auth.getName()));
   }

   @PostMapping({"/ouvrir"})
   @PreAuthorize("hasAnyRole('GESTIONNAIRE', 'ADMIN_SYSTEME')")
   public ResponseEntity ouvrirCompte(@RequestParam Long idClient, @RequestParam BigDecimal depotInitial) {
      return ResponseEntity.status(HttpStatus.CREATED).body(this.compteService.ouvrirCompte(idClient, depotInitial));
   }

   @PutMapping({"/{id}/valider"})
   @PreAuthorize("hasRole('GESTIONNAIRE')")
   public ResponseEntity validerOuverture(@PathVariable Long id) {
      return ResponseEntity.ok(this.compteService.validerOuverture(id));
   }

   @PutMapping({"/{id}/bloquer"})
   @PreAuthorize("hasRole('GESTIONNAIRE')")
   public ResponseEntity bloquer(@PathVariable Long id) {
      return ResponseEntity.ok(this.compteService.bloquerCompte(id));
   }

   @PutMapping({"/{id}/debloquer"})
   @PreAuthorize("hasRole('GESTIONNAIRE')")
   public ResponseEntity debloquer(@PathVariable Long id) {
      return ResponseEntity.ok(this.compteService.debloquerCompte(id));
   }

   @PutMapping({"/{id}/suspendre"})
   @PreAuthorize("hasRole('GESTIONNAIRE')")
   public ResponseEntity suspendre(@PathVariable Long id) {
      return ResponseEntity.ok(this.compteService.suspendreCompte(id));
   }

   @PutMapping({"/{id}/fermer"})
   @PreAuthorize("hasAnyRole('GESTIONNAIRE', 'ADMIN_SYSTEME')")
   public ResponseEntity fermer(@PathVariable Long id) {
      return ResponseEntity.ok(this.compteService.fermerCompte(id));
   }

   @GetMapping({"/{id}"})
   @PreAuthorize("hasAnyRole('CLIENT', 'GESTIONNAIRE', 'ADMIN_BD', 'ADMIN_SYSTEME')")
   public ResponseEntity getCompte(@PathVariable Long id) {
      return ResponseEntity.ok(this.compteService.getCompte(id));
   }

   @GetMapping({"/client/{idClient}"})
   @PreAuthorize("hasAnyRole('CLIENT', 'GESTIONNAIRE', 'ADMIN_BD')")
   public ResponseEntity getComptesClient(@PathVariable Long idClient) {
      return ResponseEntity.ok(this.compteService.getComptesClient(idClient));
   }

   @GetMapping
   @PreAuthorize("hasAnyRole('GESTIONNAIRE', 'ADMIN_BD', 'ADMIN_SYSTEME')")
   public ResponseEntity listerTousComptes() {
      return ResponseEntity.ok(this.compteService.listerTousComptes());
   }

   @Generated
   public CompteController(final CompteService compteService) {
      this.compteService = compteService;
   }
}
