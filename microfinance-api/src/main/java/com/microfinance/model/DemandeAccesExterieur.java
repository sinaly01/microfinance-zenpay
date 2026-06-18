package com.microfinance.model;

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
   name = "DEMANDES_ACCES_EXTERIEUR"
)
public class DemandeAccesExterieur {
   @Id
   @GeneratedValue(
      strategy = GenerationType.SEQUENCE,
      generator = "demande_seq"
   )
   @SequenceGenerator(
      name = "demande_seq",
      sequenceName = "DEMANDE_SEQ",
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
      name = "ID_GESTIONNAIRE",
      nullable = false
   )
   private Gestionnaire gestionnaire;
   @Column(
      name = "ADRESSE_IP",
      nullable = false,
      length = 45
   )
   private String adresseIp;
   @Column(
      name = "NOM_RESEAU",
      length = 100
   )
   private String nomReseau;
   @Column(
      name = "STATUT",
      nullable = false,
      length = 20
   )
   private String statut = "SUSPENDU";
   @Column(
      name = "DATE_CREATION",
      nullable = false
   )
   private LocalDateTime dateCreation;
   @Column(
      name = "DATE_VALIDITE"
   )
   private LocalDateTime dateValidite;
   @ManyToOne(
      fetch = FetchType.LAZY
   )
   @JoinColumn(
      name = "ID_APPROUVEUR"
   )
   private Gestionnaire approuvePar;

   @PrePersist
   public void prePersist() {
      this.dateCreation = LocalDateTime.now();
   }

   public boolean estApprouveEtValide() {
      return "APPROUVE".equals(this.statut) && this.dateValidite != null && LocalDateTime.now().isBefore(this.dateValidite);
   }

   @Generated
   public static DemandeAccesExterieurBuilder builder() {
      return new DemandeAccesExterieurBuilder();
   }

   @Generated
   public Long getIdDemande() {
      return this.idDemande;
   }

   @Generated
   public Gestionnaire getGestionnaire() {
      return this.gestionnaire;
   }

   @Generated
   public String getAdresseIp() {
      return this.adresseIp;
   }

   @Generated
   public String getNomReseau() {
      return this.nomReseau;
   }

   @Generated
   public String getStatut() {
      return this.statut;
   }

   @Generated
   public LocalDateTime getDateCreation() {
      return this.dateCreation;
   }

   @Generated
   public LocalDateTime getDateValidite() {
      return this.dateValidite;
   }

   @Generated
   public Gestionnaire getApprouvePar() {
      return this.approuvePar;
   }

   @Generated
   public void setIdDemande(final Long idDemande) {
      this.idDemande = idDemande;
   }

   @Generated
   public void setGestionnaire(final Gestionnaire gestionnaire) {
      this.gestionnaire = gestionnaire;
   }

   @Generated
   public void setAdresseIp(final String adresseIp) {
      this.adresseIp = adresseIp;
   }

   @Generated
   public void setNomReseau(final String nomReseau) {
      this.nomReseau = nomReseau;
   }

   @Generated
   public void setStatut(final String statut) {
      this.statut = statut;
   }

   @Generated
   public void setDateCreation(final LocalDateTime dateCreation) {
      this.dateCreation = dateCreation;
   }

   @Generated
   public void setDateValidite(final LocalDateTime dateValidite) {
      this.dateValidite = dateValidite;
   }

   @Generated
   public void setApprouvePar(final Gestionnaire approuvePar) {
      this.approuvePar = approuvePar;
   }

   @Generated
   public DemandeAccesExterieur() {
   }

   @Generated
   public DemandeAccesExterieur(final Long idDemande, final Gestionnaire gestionnaire, final String adresseIp, final String nomReseau, final String statut, final LocalDateTime dateCreation, final LocalDateTime dateValidite, final Gestionnaire approuvePar) {
      this.idDemande = idDemande;
      this.gestionnaire = gestionnaire;
      this.adresseIp = adresseIp;
      this.nomReseau = nomReseau;
      this.statut = statut;
      this.dateCreation = dateCreation;
      this.dateValidite = dateValidite;
      this.approuvePar = approuvePar;
   }

   @Generated
   public static class DemandeAccesExterieurBuilder {
      @Generated
      private Long idDemande;
      @Generated
      private Gestionnaire gestionnaire;
      @Generated
      private String adresseIp;
      @Generated
      private String nomReseau;
      @Generated
      private String statut;
      @Generated
      private LocalDateTime dateCreation;
      @Generated
      private LocalDateTime dateValidite;
      @Generated
      private Gestionnaire approuvePar;

      @Generated
      DemandeAccesExterieurBuilder() {
      }

      @Generated
      public DemandeAccesExterieurBuilder idDemande(final Long idDemande) {
         this.idDemande = idDemande;
         return this;
      }

      @Generated
      public DemandeAccesExterieurBuilder gestionnaire(final Gestionnaire gestionnaire) {
         this.gestionnaire = gestionnaire;
         return this;
      }

      @Generated
      public DemandeAccesExterieurBuilder adresseIp(final String adresseIp) {
         this.adresseIp = adresseIp;
         return this;
      }

      @Generated
      public DemandeAccesExterieurBuilder nomReseau(final String nomReseau) {
         this.nomReseau = nomReseau;
         return this;
      }

      @Generated
      public DemandeAccesExterieurBuilder statut(final String statut) {
         this.statut = statut;
         return this;
      }

      @Generated
      public DemandeAccesExterieurBuilder dateCreation(final LocalDateTime dateCreation) {
         this.dateCreation = dateCreation;
         return this;
      }

      @Generated
      public DemandeAccesExterieurBuilder dateValidite(final LocalDateTime dateValidite) {
         this.dateValidite = dateValidite;
         return this;
      }

      @Generated
      public DemandeAccesExterieurBuilder approuvePar(final Gestionnaire approuvePar) {
         this.approuvePar = approuvePar;
         return this;
      }

      @Generated
      public DemandeAccesExterieur build() {
         return new DemandeAccesExterieur(this.idDemande, this.gestionnaire, this.adresseIp, this.nomReseau, this.statut, this.dateCreation, this.dateValidite, this.approuvePar);
      }

      @Generated
      public String toString() {
         Long var10000 = this.idDemande;
         return "DemandeAccesExterieur.DemandeAccesExterieurBuilder(idDemande=" + var10000 + ", gestionnaire=" + String.valueOf(this.gestionnaire) + ", adresseIp=" + this.adresseIp + ", nomReseau=" + this.nomReseau + ", statut=" + this.statut + ", dateCreation=" + String.valueOf(this.dateCreation) + ", dateValidite=" + String.valueOf(this.dateValidite) + ", approuvePar=" + String.valueOf(this.approuvePar) + ")";
      }
   }
}
