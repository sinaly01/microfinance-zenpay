package com.microfinance.controller;

import com.microfinance.service.SystemService;
import java.util.Map;
import lombok.Generated;
import org.springframework.http.ResponseEntity;
import org.springframework.security.access.prepost.PreAuthorize;
import org.springframework.web.bind.annotation.GetMapping;
import org.springframework.web.bind.annotation.PostMapping;
import org.springframework.web.bind.annotation.RequestMapping;
import org.springframework.web.bind.annotation.RequestParam;
import org.springframework.web.bind.annotation.RestController;

@RestController
@RequestMapping({"/api/system"})
public class SystemController {
   private final SystemService systemService;

   @GetMapping({"/status"})
   public ResponseEntity getStatus() {
      return ResponseEntity.ok(Map.of("status", this.systemService.getStatus()));
   }

   @PostMapping({"/kill-switch"})
   @PreAuthorize("hasRole('SUPER_ADMIN')")
   public ResponseEntity killSwitch(@RequestParam boolean activer) {
      String newStatus = this.systemService.setStatus(activer);
      String msg = activer ? "Système gelé. Toutes les transactions sont bloquées." : "Système relancé. Service opérationnel.";
      return ResponseEntity.ok(Map.of("status", newStatus, "message", msg));
   }

   @Generated
   public SystemController(final SystemService systemService) {
      this.systemService = systemService;
   }
}
