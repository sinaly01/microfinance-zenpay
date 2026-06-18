package com.microfinance.controller;

import com.microfinance.dto.request.TransactionRequest;
import com.microfinance.repository.TransactionRepository;
import com.microfinance.service.TransactionService;
import jakarta.validation.Valid;
import java.time.LocalDateTime;
import lombok.Generated;
import org.springframework.data.domain.Sort;
import org.springframework.data.domain.Sort.Direction;
import org.springframework.format.annotation.DateTimeFormat;
import org.springframework.format.annotation.DateTimeFormat.ISO;
import org.springframework.http.HttpStatus;
import org.springframework.http.ResponseEntity;
import org.springframework.security.access.prepost.PreAuthorize;
import org.springframework.web.bind.annotation.GetMapping;
import org.springframework.web.bind.annotation.PathVariable;
import org.springframework.web.bind.annotation.PostMapping;
import org.springframework.web.bind.annotation.RequestBody;
import org.springframework.web.bind.annotation.RequestMapping;
import org.springframework.web.bind.annotation.RequestParam;
import org.springframework.web.bind.annotation.RestController;

@RestController
@RequestMapping({"/api/transactions"})
public class TransactionController {
   private final TransactionService transactionService;
   private final TransactionRepository transactionRepository;

   @PostMapping({"/versement"})
   @PreAuthorize("hasAnyRole('CLIENT', 'GESTIONNAIRE', 'SUPER_ADMIN')")
   public ResponseEntity versement(@RequestBody @Valid TransactionRequest req) {
      return ResponseEntity.status(HttpStatus.CREATED).body(this.transactionService.effectuerVersement(req));
   }

   @PostMapping({"/retrait"})
   @PreAuthorize("hasAnyRole('CLIENT', 'GESTIONNAIRE', 'SUPER_ADMIN')")
   public ResponseEntity retrait(@RequestBody @Valid TransactionRequest req) {
      return ResponseEntity.status(HttpStatus.CREATED).body(this.transactionService.effectuerRetrait(req));
   }

   @PostMapping({"/virement"})
   @PreAuthorize("hasAnyRole('CLIENT', 'GESTIONNAIRE', 'SUPER_ADMIN')")
   public ResponseEntity virement(@RequestBody @Valid TransactionRequest req) {
      return ResponseEntity.status(HttpStatus.CREATED).body(this.transactionService.effectuerVirement(req));
   }

   @GetMapping({"/releve/{idCompte}"})
   @PreAuthorize("hasAnyRole('CLIENT', 'GESTIONNAIRE', 'ADMIN_BD', 'SUPER_ADMIN')")
   public ResponseEntity releve(@PathVariable Long idCompte) {
      return ResponseEntity.ok(this.transactionService.getReleve(idCompte));
   }

   @GetMapping({"/releve/{idCompte}/periode"})
   @PreAuthorize("hasAnyRole('CLIENT', 'GESTIONNAIRE', 'ADMIN_BD', 'SUPER_ADMIN')")
   public ResponseEntity releveParPeriode(@PathVariable Long idCompte, @RequestParam @DateTimeFormat(iso = ISO.DATE_TIME) LocalDateTime debut, @RequestParam @DateTimeFormat(iso = ISO.DATE_TIME) LocalDateTime fin) {
      return ResponseEntity.ok(this.transactionService.getReleveParPeriode(idCompte, debut, fin));
   }

   @GetMapping({"/surveillance"})
   @PreAuthorize("hasAnyRole('GESTIONNAIRE', 'ADMIN_BD', 'ADMIN_SYSTEME', 'SUPER_ADMIN')")
   public ResponseEntity surveillance() {
      return ResponseEntity.ok(this.transactionService.survellerOperations());
   }

   @GetMapping
   @PreAuthorize("hasAnyRole('GESTIONNAIRE', 'ADMIN_BD', 'ADMIN_SYSTEME', 'SUPERVISOR', 'SUPER_ADMIN')")
   public ResponseEntity toutesTransactions() {
      return ResponseEntity.ok(this.transactionRepository.findAll(Sort.by(Direction.DESC, new String[]{"dateHeure"})));
   }

   @Generated
   public TransactionController(final TransactionService transactionService, final TransactionRepository transactionRepository) {
      this.transactionService = transactionService;
      this.transactionRepository = transactionRepository;
   }
}
