package com.microfinance.model;

import com.microfinance.model.enums.StatutTicket;
import jakarta.persistence.Column;
import jakarta.persistence.Entity;
import jakarta.persistence.EnumType;
import jakarta.persistence.Enumerated;
import jakarta.persistence.FetchType;
import jakarta.persistence.GeneratedValue;
import jakarta.persistence.GenerationType;
import jakarta.persistence.Id;
import jakarta.persistence.JoinColumn;
import jakarta.persistence.Lob;
import jakarta.persistence.ManyToOne;
import jakarta.persistence.PrePersist;
import jakarta.persistence.SequenceGenerator;
import jakarta.persistence.Table;
import java.time.LocalDateTime;
import lombok.Generated;

@Entity
@Table(
   name = "TICKETS_RECLAMATION"
)
public class TicketReclamation {
   @Id
   @GeneratedValue(
      strategy = GenerationType.SEQUENCE,
      generator = "ticket_seq"
   )
   @SequenceGenerator(
      name = "ticket_seq",
      sequenceName = "TICKET_SEQ",
      allocationSize = 1
   )
   @Column(
      name = "ID_TICKET"
   )
   private Long idTicket;
   @ManyToOne(
      fetch = FetchType.LAZY
   )
   @JoinColumn(
      name = "ID_CLIENT",
      nullable = false
   )
   private Client client;
   @ManyToOne(
      fetch = FetchType.LAZY
   )
   @JoinColumn(
      name = "ID_GESTIONNAIRE_CHARGE"
   )
   private Gestionnaire gestionnaire;
   @Column(
      name = "TITRE_SUJET",
      nullable = false,
      length = 150
   )
   private String titreObjet;
   @Lob
   @Column(
      name = "DESCRIPTION_PROBLEME",
      nullable = false
   )
   private String descriptionProbleme;
   @Enumerated(EnumType.STRING)
   @Column(
      name = "STATUT_TICKET",
      nullable = false,
      length = 20
   )
   private StatutTicket statut;
   @Column(
      name = "DATE_CREATION",
      nullable = false
   )
   private LocalDateTime dateCreation;
   @Column(
      name = "DATE_RESOLUTION"
   )
   private LocalDateTime dateResolution;

   @PrePersist
   public void prePersist() {
      this.dateCreation = LocalDateTime.now();
   }

   @Generated
   public static TicketReclamationBuilder builder() {
      return new TicketReclamationBuilder();
   }

   @Generated
   public Long getIdTicket() {
      return this.idTicket;
   }

   @Generated
   public Client getClient() {
      return this.client;
   }

   @Generated
   public Gestionnaire getGestionnaire() {
      return this.gestionnaire;
   }

   @Generated
   public String getTitreObjet() {
      return this.titreObjet;
   }

   @Generated
   public String getDescriptionProbleme() {
      return this.descriptionProbleme;
   }

   @Generated
   public StatutTicket getStatut() {
      return this.statut;
   }

   @Generated
   public LocalDateTime getDateCreation() {
      return this.dateCreation;
   }

   @Generated
   public LocalDateTime getDateResolution() {
      return this.dateResolution;
   }

   @Generated
   public void setIdTicket(final Long idTicket) {
      this.idTicket = idTicket;
   }

   @Generated
   public void setClient(final Client client) {
      this.client = client;
   }

   @Generated
   public void setGestionnaire(final Gestionnaire gestionnaire) {
      this.gestionnaire = gestionnaire;
   }

   @Generated
   public void setTitreObjet(final String titreObjet) {
      this.titreObjet = titreObjet;
   }

   @Generated
   public void setDescriptionProbleme(final String descriptionProbleme) {
      this.descriptionProbleme = descriptionProbleme;
   }

   @Generated
   public void setStatut(final StatutTicket statut) {
      this.statut = statut;
   }

   @Generated
   public void setDateCreation(final LocalDateTime dateCreation) {
      this.dateCreation = dateCreation;
   }

   @Generated
   public void setDateResolution(final LocalDateTime dateResolution) {
      this.dateResolution = dateResolution;
   }

   @Generated
   public TicketReclamation() {
      this.statut = StatutTicket.OUVERT;
   }

   @Generated
   public TicketReclamation(final Long idTicket, final Client client, final Gestionnaire gestionnaire, final String titreObjet, final String descriptionProbleme, final StatutTicket statut, final LocalDateTime dateCreation, final LocalDateTime dateResolution) {
      this.statut = StatutTicket.OUVERT;
      this.idTicket = idTicket;
      this.client = client;
      this.gestionnaire = gestionnaire;
      this.titreObjet = titreObjet;
      this.descriptionProbleme = descriptionProbleme;
      this.statut = statut;
      this.dateCreation = dateCreation;
      this.dateResolution = dateResolution;
   }

   @Generated
   public static class TicketReclamationBuilder {
      @Generated
      private Long idTicket;
      @Generated
      private Client client;
      @Generated
      private Gestionnaire gestionnaire;
      @Generated
      private String titreObjet;
      @Generated
      private String descriptionProbleme;
      @Generated
      private StatutTicket statut;
      @Generated
      private LocalDateTime dateCreation;
      @Generated
      private LocalDateTime dateResolution;

      @Generated
      TicketReclamationBuilder() {
      }

      @Generated
      public TicketReclamationBuilder idTicket(final Long idTicket) {
         this.idTicket = idTicket;
         return this;
      }

      @Generated
      public TicketReclamationBuilder client(final Client client) {
         this.client = client;
         return this;
      }

      @Generated
      public TicketReclamationBuilder gestionnaire(final Gestionnaire gestionnaire) {
         this.gestionnaire = gestionnaire;
         return this;
      }

      @Generated
      public TicketReclamationBuilder titreObjet(final String titreObjet) {
         this.titreObjet = titreObjet;
         return this;
      }

      @Generated
      public TicketReclamationBuilder descriptionProbleme(final String descriptionProbleme) {
         this.descriptionProbleme = descriptionProbleme;
         return this;
      }

      @Generated
      public TicketReclamationBuilder statut(final StatutTicket statut) {
         this.statut = statut;
         return this;
      }

      @Generated
      public TicketReclamationBuilder dateCreation(final LocalDateTime dateCreation) {
         this.dateCreation = dateCreation;
         return this;
      }

      @Generated
      public TicketReclamationBuilder dateResolution(final LocalDateTime dateResolution) {
         this.dateResolution = dateResolution;
         return this;
      }

      @Generated
      public TicketReclamation build() {
         return new TicketReclamation(this.idTicket, this.client, this.gestionnaire, this.titreObjet, this.descriptionProbleme, this.statut, this.dateCreation, this.dateResolution);
      }

      @Generated
      public String toString() {
         Long var10000 = this.idTicket;
         return "TicketReclamation.TicketReclamationBuilder(idTicket=" + var10000 + ", client=" + String.valueOf(this.client) + ", gestionnaire=" + String.valueOf(this.gestionnaire) + ", titreObjet=" + this.titreObjet + ", descriptionProbleme=" + this.descriptionProbleme + ", statut=" + String.valueOf(this.statut) + ", dateCreation=" + String.valueOf(this.dateCreation) + ", dateResolution=" + String.valueOf(this.dateResolution) + ")";
      }
   }
}
