package com.microfinance.model;

import com.fasterxml.jackson.annotation.JsonIgnoreProperties;
import jakarta.persistence.Column;
import jakarta.persistence.Entity;
import jakarta.persistence.FetchType;
import jakarta.persistence.GeneratedValue;
import jakarta.persistence.GenerationType;
import jakarta.persistence.Id;
import jakarta.persistence.JoinColumn;
import jakarta.persistence.ManyToOne;
import jakarta.persistence.PrePersist;
import jakarta.persistence.SequenceGenerator;
import jakarta.persistence.Table;
import java.time.LocalDateTime;
import lombok.Generated;

@Entity
@Table(
   name = "DEMANDES_ABONNEMENT"
)
public class DemandeChangementOffre {
   @Id
   @GeneratedValue(
      strategy = GenerationType.SEQUENCE,
      generator = "dem_abo_seq"
   )
   @SequenceGenerator(
      name = "dem_abo_seq",
      sequenceName = "DEM_ABO_SEQ",
      allocationSize = 1
   )
   @Column(
      name = "ID_DEMANDE"
   )
   private Long idDemande;
   @ManyToOne(
      fetch = FetchType.LAZY
   )
   @JoinColumn(
      name = "ID_CLIENT",
      nullable = false
   )
   @JsonIgnoreProperties({"hibernateLazyInitializer", "handler", "motDePasse", "comptes"})
   private Client client;
   @ManyToOne(
      fetch = FetchType.LAZY
   )
   @JoinColumn(
      name = "ID_OFFRE_DEMANDEE",
      nullable = false
   )
   @JsonIgnoreProperties({"hibernateLazyInitializer", "handler"})
   private OffreAbonnement offreDemandee;
   @Column(
      name = "STATUT",
      nullable = false,
      length = 20
   )
   private String statut = "EN_ATTENTE";
   @Column(
      name = "MESSAGE_CLIENT",
      length = 500
   )
   private String messageClient;
   @Column(
      name = "DATE_CREATION",
      nullable = false
   )
   private LocalDateTime dateCreation;
   @Column(
      name = "DATE_TRAITEMENT"
   )
   private LocalDateTime dateTraitement;
   @ManyToOne(
      fetch = FetchType.LAZY
   )
   @JoinColumn(
      name = "ID_TRAITE_PAR"
   )
   @JsonIgnoreProperties({"hibernateLazyInitializer", "handler", "motDePasse", "otpCode", "otpExpiration"})
   private Gestionnaire traitePar;

   @PrePersist
   public void prePersist() {
      this.dateCreation = LocalDateTime.now();
   }

   @Generated
   public static DemandeChangementOffreBuilder builder() {
      return new DemandeChangementOffreBuilder();
   }

   @Generated
   public Long getIdDemande() {
      return this.idDemande;
   }

   @Generated
   public Client getClient() {
      return this.client;
   }

   @Generated
   public OffreAbonnement getOffreDemandee() {
      return this.offreDemandee;
   }

   @Generated
   public String getStatut() {
      return this.statut;
   }

   @Generated
   public String getMessageClient() {
      return this.messageClient;
   }

   @Generated
   public LocalDateTime getDateCreation() {
      return this.dateCreation;
   }

   @Generated
   public LocalDateTime getDateTraitement() {
      return this.dateTraitement;
   }

   @Generated
   public Gestionnaire getTraitePar() {
      return this.traitePar;
   }

   @Generated
   public void setIdDemande(final Long idDemande) {
      this.idDemande = idDemande;
   }

   @Generated
   public void setClient(final Client client) {
      this.client = client;
   }

   @Generated
   public void setOffreDemandee(final OffreAbonnement offreDemandee) {
      this.offreDemandee = offreDemandee;
   }

   @Generated
   public void setStatut(final String statut) {
      this.statut = statut;
   }

   @Generated
   public void setMessageClient(final String messageClient) {
      this.messageClient = messageClient;
   }

   @Generated
   public void setDateCreation(final LocalDateTime dateCreation) {
      this.dateCreation = dateCreation;
   }

   @Generated
   public void setDateTraitement(final LocalDateTime dateTraitement) {
      this.dateTraitement = dateTraitement;
   }

   @Generated
   public void setTraitePar(final Gestionnaire traitePar) {
      this.traitePar = traitePar;
   }

   @Generated
   public DemandeChangementOffre() {
   }

   @Generated
   public DemandeChangementOffre(final Long idDemande, final Client client, final OffreAbonnement offreDemandee, final String statut, final String messageClient, final LocalDateTime dateCreation, final LocalDateTime dateTraitement, final Gestionnaire traitePar) {
      this.idDemande = idDemande;
      this.client = client;
      this.offreDemandee = offreDemandee;
      this.statut = statut;
      this.messageClient = messageClient;
      this.dateCreation = dateCreation;
      this.dateTraitement = dateTraitement;
      this.traitePar = traitePar;
   }

   @Generated
   public static class DemandeChangementOffreBuilder {
      @Generated
      private Long idDemande;
      @Generated
      private Client client;
      @Generated
      private OffreAbonnement offreDemandee;
      @Generated
      private String statut;
      @Generated
      private String messageClient;
      @Generated
      private LocalDateTime dateCreation;
      @Generated
      private LocalDateTime dateTraitement;
      @Generated
      private Gestionnaire traitePar;

      @Generated
      DemandeChangementOffreBuilder() {
      }

      @Generated
      public DemandeChangementOffreBuilder idDemande(final Long idDemande) {
         this.idDemande = idDemande;
         return this;
      }

      @JsonIgnoreProperties({"hibernateLazyInitializer", "handler", "motDePasse", "comptes"})
      @Generated
      public DemandeChangementOffreBuilder client(final Client client) {
         this.client = client;
         return this;
      }

      @JsonIgnoreProperties({"hibernateLazyInitializer", "handler"})
      @Generated
      public DemandeChangementOffreBuilder offreDemandee(final OffreAbonnement offreDemandee) {
         this.offreDemandee = offreDemandee;
         return this;
      }

      @Generated
      public DemandeChangementOffreBuilder statut(final String statut) {
         this.statut = statut;
         return this;
      }

      @Generated
      public DemandeChangementOffreBuilder messageClient(final String messageClient) {
         this.messageClient = messageClient;
         return this;
      }

      @Generated
      public DemandeChangementOffreBuilder dateCreation(final LocalDateTime dateCreation) {
         this.dateCreation = dateCreation;
         return this;
      }

      @Generated
      public DemandeChangementOffreBuilder dateTraitement(final LocalDateTime dateTraitement) {
         this.dateTraitement = dateTraitement;
         return this;
      }

      @JsonIgnoreProperties({"hibernateLazyInitializer", "handler", "motDePasse", "otpCode", "otpExpiration"})
      @Generated
      public DemandeChangementOffreBuilder traitePar(final Gestionnaire traitePar) {
         this.traitePar = traitePar;
         return this;
      }

      @Generated
      public DemandeChangementOffre build() {
         return new DemandeChangementOffre(this.idDemande, this.client, this.offreDemandee, this.statut, this.messageClient, this.dateCreation, this.dateTraitement, this.traitePar);
      }

      @Generated
      public String toString() {
         Long var10000 = this.idDemande;
         return "DemandeChangementOffre.DemandeChangementOffreBuilder(idDemande=" + var10000 + ", client=" + String.valueOf(this.client) + ", offreDemandee=" + String.valueOf(this.offreDemandee) + ", statut=" + this.statut + ", messageClient=" + this.messageClient + ", dateCreation=" + String.valueOf(this.dateCreation) + ", dateTraitement=" + String.valueOf(this.dateTraitement) + ", traitePar=" + String.valueOf(this.traitePar) + ")";
      }
   }
}
