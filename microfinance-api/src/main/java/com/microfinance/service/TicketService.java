package com.microfinance.service;

import com.microfinance.exception.BusinessException;
import com.microfinance.exception.ResourceNotFoundException;
import com.microfinance.model.Client;
import com.microfinance.model.Gestionnaire;
import com.microfinance.model.TicketReclamation;
import com.microfinance.model.enums.StatutTicket;
import com.microfinance.repository.ClientRepository;
import com.microfinance.repository.GestionnaireRepository;
import com.microfinance.repository.TicketReclamationRepository;
import java.time.LocalDateTime;
import java.util.List;
import lombok.Generated;
import org.springframework.stereotype.Service;
import org.springframework.transaction.annotation.Transactional;

@Service
@Transactional
public class TicketService {
   private final TicketReclamationRepository ticketRepo;
   private final ClientRepository clientRepository;
   private final GestionnaireRepository gestionnaireRepository;

   public TicketReclamation creerTicket(Long idClient, String titreObjet, String description) {
      Client client = (Client)this.clientRepository.findById(idClient).orElseThrow(() -> new ResourceNotFoundException("Client", idClient));
      TicketReclamation ticket = TicketReclamation.builder().client(client).titreObjet(titreObjet).descriptionProbleme(description).statut(StatutTicket.OUVERT).build();
      return (TicketReclamation)this.ticketRepo.save(ticket);
   }

   @Transactional(
      readOnly = true
   )
   public List getMesTickets(Long idClient) {
      return this.ticketRepo.findByClientIdClientOrderByDateCreationDesc(idClient);
   }

   @Transactional(
      readOnly = true
   )
   public List getTousTickets() {
      return this.ticketRepo.findAllByOrderByDateCreationDesc();
   }

   @Transactional(
      readOnly = true
   )
   public List getTicketsOuverts() {
      return this.ticketRepo.findByStatutOrderByDateCreationDesc(StatutTicket.OUVERT);
   }

   public TicketReclamation prendreEnCharge(Long idTicket, Long idGestionnaire) {
      TicketReclamation ticket = (TicketReclamation)this.ticketRepo.findById(idTicket).orElseThrow(() -> new ResourceNotFoundException("Ticket", idTicket));
      Gestionnaire g = (Gestionnaire)this.gestionnaireRepository.findById(idGestionnaire).orElseThrow(() -> new ResourceNotFoundException("Gestionnaire", idGestionnaire));
      ticket.setGestionnaire(g);
      ticket.setStatut(StatutTicket.EN_COURS);
      return (TicketReclamation)this.ticketRepo.save(ticket);
   }

   public TicketReclamation resoudre(Long idTicket) {
      TicketReclamation ticket = (TicketReclamation)this.ticketRepo.findById(idTicket).orElseThrow(() -> new ResourceNotFoundException("Ticket", idTicket));
      if (ticket.getStatut() == StatutTicket.RESOLU) {
         throw new BusinessException("Ce ticket est déjà résolu.");
      } else {
         ticket.setStatut(StatutTicket.RESOLU);
         ticket.setDateResolution(LocalDateTime.now());
         return (TicketReclamation)this.ticketRepo.save(ticket);
      }
   }

   @Generated
   public TicketService(final TicketReclamationRepository ticketRepo, final ClientRepository clientRepository, final GestionnaireRepository gestionnaireRepository) {
      this.ticketRepo = ticketRepo;
      this.clientRepository = clientRepository;
      this.gestionnaireRepository = gestionnaireRepository;
   }
}
