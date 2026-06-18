package com.microfinance.model;

import jakarta.persistence.Column;
import jakarta.persistence.Entity;
import jakarta.persistence.GeneratedValue;
import jakarta.persistence.GenerationType;
import jakarta.persistence.Id;
import jakarta.persistence.PrePersist;
import jakarta.persistence.SequenceGenerator;
import jakarta.persistence.Table;
import java.time.LocalDateTime;
import lombok.Generated;

@Entity
@Table(
   name = "ADRESSES_IP_AUTORISEES"
)
public class AdresseIpAutorisee {
   @Id
   @GeneratedValue(
      strategy = GenerationType.SEQUENCE,
      generator = "ip_seq"
   )
   @SequenceGenerator(
      name = "ip_seq",
      sequenceName = "IP_SEQ",
      allocationSize = 1
   )
   @Column(
      name = "ID_IP"
   )
   private Long idIp;
   @Column(
      name = "ADRESSE_IP",
      nullable = false,
      unique = true,
      length = 45
   )
   private String adresseIp;
   @Column(
      name = "NOM_MACHINE",
      nullable = false,
      length = 100
   )
   private String nomMachine;
   @Column(
      name = "EST_ACTIVE",
      nullable = false
   )
   private boolean estActive = true;
   @Column(
      name = "DATE_AJOUT",
      nullable = false
   )
   private LocalDateTime dateAjout;

   @PrePersist
   public void prePersist() {
      this.dateAjout = LocalDateTime.now();
   }

   @Generated
   public static AdresseIpAutoriseeBuilder builder() {
      return new AdresseIpAutoriseeBuilder();
   }

   @Generated
   public Long getIdIp() {
      return this.idIp;
   }

   @Generated
   public String getAdresseIp() {
      return this.adresseIp;
   }

   @Generated
   public String getNomMachine() {
      return this.nomMachine;
   }

   @Generated
   public boolean isEstActive() {
      return this.estActive;
   }

   @Generated
   public LocalDateTime getDateAjout() {
      return this.dateAjout;
   }

   @Generated
   public void setIdIp(final Long idIp) {
      this.idIp = idIp;
   }

   @Generated
   public void setAdresseIp(final String adresseIp) {
      this.adresseIp = adresseIp;
   }

   @Generated
   public void setNomMachine(final String nomMachine) {
      this.nomMachine = nomMachine;
   }

   @Generated
   public void setEstActive(final boolean estActive) {
      this.estActive = estActive;
   }

   @Generated
   public void setDateAjout(final LocalDateTime dateAjout) {
      this.dateAjout = dateAjout;
   }

   @Generated
   public AdresseIpAutorisee() {
   }

   @Generated
   public AdresseIpAutorisee(final Long idIp, final String adresseIp, final String nomMachine, final boolean estActive, final LocalDateTime dateAjout) {
      this.idIp = idIp;
      this.adresseIp = adresseIp;
      this.nomMachine = nomMachine;
      this.estActive = estActive;
      this.dateAjout = dateAjout;
   }

   @Generated
   public static class AdresseIpAutoriseeBuilder {
      @Generated
      private Long idIp;
      @Generated
      private String adresseIp;
      @Generated
      private String nomMachine;
      @Generated
      private boolean estActive;
      @Generated
      private LocalDateTime dateAjout;

      @Generated
      AdresseIpAutoriseeBuilder() {
      }

      @Generated
      public AdresseIpAutoriseeBuilder idIp(final Long idIp) {
         this.idIp = idIp;
         return this;
      }

      @Generated
      public AdresseIpAutoriseeBuilder adresseIp(final String adresseIp) {
         this.adresseIp = adresseIp;
         return this;
      }

      @Generated
      public AdresseIpAutoriseeBuilder nomMachine(final String nomMachine) {
         this.nomMachine = nomMachine;
         return this;
      }

      @Generated
      public AdresseIpAutoriseeBuilder estActive(final boolean estActive) {
         this.estActive = estActive;
         return this;
      }

      @Generated
      public AdresseIpAutoriseeBuilder dateAjout(final LocalDateTime dateAjout) {
         this.dateAjout = dateAjout;
         return this;
      }

      @Generated
      public AdresseIpAutorisee build() {
         return new AdresseIpAutorisee(this.idIp, this.adresseIp, this.nomMachine, this.estActive, this.dateAjout);
      }

      @Generated
      public String toString() {
         Long var10000 = this.idIp;
         return "AdresseIpAutorisee.AdresseIpAutoriseeBuilder(idIp=" + var10000 + ", adresseIp=" + this.adresseIp + ", nomMachine=" + this.nomMachine + ", estActive=" + this.estActive + ", dateAjout=" + String.valueOf(this.dateAjout) + ")";
      }
   }
}
