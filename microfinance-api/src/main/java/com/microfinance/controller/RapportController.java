package com.microfinance.controller;

import com.microfinance.service.RapportService;
import java.time.LocalDate;
import lombok.Generated;
import org.springframework.format.annotation.DateTimeFormat;
import org.springframework.format.annotation.DateTimeFormat.ISO;
import org.springframework.http.ResponseEntity;
import org.springframework.security.access.prepost.PreAuthorize;
import org.springframework.web.bind.annotation.GetMapping;
import org.springframework.web.bind.annotation.PostMapping;
import org.springframework.web.bind.annotation.RequestMapping;
import org.springframework.web.bind.annotation.RequestParam;
import org.springframework.web.bind.annotation.RestController;

@RestController
@RequestMapping({"/api/rapports"})
public class RapportController {
   private final RapportService rapportService;

   @PostMapping({"/generer"})
   @PreAuthorize("hasAnyRole('GESTIONNAIRE', 'ADMIN_BD')")
   public ResponseEntity generer(@RequestParam String type, @RequestParam @DateTimeFormat(iso = ISO.DATE) LocalDate debut, @RequestParam @DateTimeFormat(iso = ISO.DATE) LocalDate fin) {
      return ResponseEntity.ok(this.rapportService.genererRapportPeriode(type, debut, fin));
   }

   @GetMapping
   @PreAuthorize("hasAnyRole('GESTIONNAIRE', 'ADMIN_BD', 'ADMIN_SYSTEME')")
   public ResponseEntity lister() {
      return ResponseEntity.ok(this.rapportService.listerRapports());
   }

   @Generated
   public RapportController(final RapportService rapportService) {
      this.rapportService = rapportService;
   }
}
