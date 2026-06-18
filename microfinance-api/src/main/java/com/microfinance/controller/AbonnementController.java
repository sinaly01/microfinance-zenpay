package com.microfinance.controller;

import com.microfinance.service.AbonnementService;
import java.math.BigDecimal;
import lombok.Generated;
import org.springframework.http.ResponseEntity;
import org.springframework.security.access.prepost.PreAuthorize;
import org.springframework.web.bind.annotation.GetMapping;
import org.springframework.web.bind.annotation.PathVariable;
import org.springframework.web.bind.annotation.PostMapping;
import org.springframework.web.bind.annotation.PutMapping;
import org.springframework.web.bind.annotation.RequestMapping;
import org.springframework.web.bind.annotation.RequestParam;
import org.springframework.web.bind.annotation.RestController;

@RestController
@RequestMapping({"/api/abonnements"})
public class AbonnementController {
   private final AbonnementService abonnementService;

   @GetMapping
   public ResponseEntity listerOffres() {
      return ResponseEntity.ok(this.abonnementService.listerOffres());
   }

   @PutMapping({"/changer/{idClient}"})
   @PreAuthorize("hasAnyRole('CLIENT', 'GESTIONNAIRE', 'ADMIN_SYSTEME')")
   public ResponseEntity changerOffre(@PathVariable Long idClient, @RequestParam Long idOffre) {
      return ResponseEntity.ok(this.abonnementService.changerOffre(idClient, idOffre));
   }

   @GetMapping({"/simuler-frais"})
   @PreAuthorize("hasAnyRole('CLIENT', 'GESTIONNAIRE')")
   public ResponseEntity simulerFrais(@RequestParam Long idClient, @RequestParam BigDecimal montant, @RequestParam(defaultValue = "MOMO") String typeOperation) {
      return ResponseEntity.ok(this.abonnementService.simulerFrais(idClient, montant, typeOperation));
   }

   @PostMapping({"/prelever/{idClient}"})
   @PreAuthorize("hasAnyRole('ADMIN_SYSTEME', 'SUPER_ADMIN')")
   public ResponseEntity declencherPrelevement(@PathVariable Long idClient) {
      this.abonnementService.prelevementMensuel(idClient);
      return ResponseEntity.ok("Prélèvement mensuel effectué pour le client " + idClient);
   }

   @Generated
   public AbonnementController(final AbonnementService abonnementService) {
      this.abonnementService = abonnementService;
   }
}
