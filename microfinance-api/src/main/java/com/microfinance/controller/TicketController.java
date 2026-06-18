package com.microfinance.controller;

import com.microfinance.service.TicketService;
import lombok.Generated;
import org.springframework.http.HttpStatus;
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
@RequestMapping({"/api/tickets"})
public class TicketController {
   private final TicketService ticketService;

   @PostMapping
   @PreAuthorize("hasAnyRole('CLIENT', 'GESTIONNAIRE')")
   public ResponseEntity creerTicket(@RequestParam Long idClient, @RequestParam String titreObjet, @RequestParam String description) {
      return ResponseEntity.status(HttpStatus.CREATED).body(this.ticketService.creerTicket(idClient, titreObjet, description));
   }

   @GetMapping({"/mes-tickets/{idClient}"})
   @PreAuthorize("hasAnyRole('CLIENT', 'GESTIONNAIRE')")
   public ResponseEntity mesTickets(@PathVariable Long idClient) {
      return ResponseEntity.ok(this.ticketService.getMesTickets(idClient));
   }

   @GetMapping
   @PreAuthorize("hasAnyRole('GESTIONNAIRE', 'SUPERVISOR', 'ADMIN_SYSTEME', 'SUPER_ADMIN')")
   public ResponseEntity tousTickets() {
      return ResponseEntity.ok(this.ticketService.getTousTickets());
   }

   @GetMapping({"/ouverts"})
   @PreAuthorize("hasAnyRole('GESTIONNAIRE', 'SUPERVISOR', 'SUPER_ADMIN')")
   public ResponseEntity ticketsOuverts() {
      return ResponseEntity.ok(this.ticketService.getTicketsOuverts());
   }

   @PutMapping({"/{idTicket}/prendre-en-charge"})
   @PreAuthorize("hasAnyRole('GESTIONNAIRE', 'SUPERVISOR', 'SUPER_ADMIN')")
   public ResponseEntity prendreEnCharge(@PathVariable Long idTicket, @RequestParam Long idGestionnaire) {
      return ResponseEntity.ok(this.ticketService.prendreEnCharge(idTicket, idGestionnaire));
   }

   @PutMapping({"/{idTicket}/resoudre"})
   @PreAuthorize("hasAnyRole('GESTIONNAIRE', 'SUPERVISOR', 'SUPER_ADMIN')")
   public ResponseEntity resoudre(@PathVariable Long idTicket) {
      return ResponseEntity.ok(this.ticketService.resoudre(idTicket));
   }

   @Generated
   public TicketController(final TicketService ticketService) {
      this.ticketService = ticketService;
   }
}
