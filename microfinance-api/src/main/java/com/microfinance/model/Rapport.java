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
import java.time.LocalDate;
import java.time.LocalDateTime;
import lombok.Generated;

@Entity
@Table(
   name = "RAPPORTS"
)
public class Rapport {
   @Id
   @GeneratedValue(
      strategy = GenerationType.SEQUENCE,
      generator = "rapport_seq"
   )
   @SequenceGenerator(
      name = "rapport_seq",
      sequenceName = "RAPPORT_SEQ",
      allocationSize = 1
   )
   @Column(
      name = "ID_RAPPORT"
   )
   private Long idRapport;
   @Column(
      name = "TYPE_RAPPORT",
      nullable = false,
      length = 50
   )
   private String type;
   @Column(
      name = "PERIODE_DEBUT",
      nullable = false
   )
   private LocalDate periodeDebut;
   @Column(
      name = "PERIODE_FIN",
      nullable = false
   )
   private LocalDate periodeFin;
   @Column(
      name = "DATE_GENERATION",
      nullable = false
   )
   private LocalDateTime dateGeneration;
   @Column(
      name = "CONTENU",
      columnDefinition = "CLOB"
   )
   private String contenu;
   @JsonIgnoreProperties({"motDePasse", "authorities", "accountNonExpired", "accountNonLocked", "credentialsNonExpired", "enabled"})
   @ManyToOne(
      fetch = FetchType.LAZY
   )
   @JoinColumn(
      name = "ID_GESTIONNAIRE"
   )
   private Gestionnaire generePar;

   @PrePersist
   public void prePersist() {
      this.dateGeneration = LocalDateTime.now();
   }

   public String generer() {
      String var10000 = this.type;
      return "Rapport [" + var10000 + "] du " + String.valueOf(this.periodeDebut) + " au " + String.valueOf(this.periodeFin);
   }

   @Generated
   public static RapportBuilder builder() {
      return new RapportBuilder();
   }

   @Generated
   public Long getIdRapport() {
      return this.idRapport;
   }

   @Generated
   public String getType() {
      return this.type;
   }

   @Generated
   public LocalDate getPeriodeDebut() {
      return this.periodeDebut;
   }

   @Generated
   public LocalDate getPeriodeFin() {
      return this.periodeFin;
   }

   @Generated
   public LocalDateTime getDateGeneration() {
      return this.dateGeneration;
   }

   @Generated
   public String getContenu() {
      return this.contenu;
   }

   @Generated
   public Gestionnaire getGenerePar() {
      return this.generePar;
   }

   @Generated
   public void setIdRapport(final Long idRapport) {
      this.idRapport = idRapport;
   }

   @Generated
   public void setType(final String type) {
      this.type = type;
   }

   @Generated
   public void setPeriodeDebut(final LocalDate periodeDebut) {
      this.periodeDebut = periodeDebut;
   }

   @Generated
   public void setPeriodeFin(final LocalDate periodeFin) {
      this.periodeFin = periodeFin;
   }

   @Generated
   public void setDateGeneration(final LocalDateTime dateGeneration) {
      this.dateGeneration = dateGeneration;
   }

   @Generated
   public void setContenu(final String contenu) {
      this.contenu = contenu;
   }

   @Generated
   public void setGenerePar(final Gestionnaire generePar) {
      this.generePar = generePar;
   }

   @Generated
   public Rapport() {
   }

   @Generated
   public Rapport(final Long idRapport, final String type, final LocalDate periodeDebut, final LocalDate periodeFin, final LocalDateTime dateGeneration, final String contenu, final Gestionnaire generePar) {
      this.idRapport = idRapport;
      this.type = type;
      this.periodeDebut = periodeDebut;
      this.periodeFin = periodeFin;
      this.dateGeneration = dateGeneration;
      this.contenu = contenu;
      this.generePar = generePar;
   }

   @Generated
   public static class RapportBuilder {
      @Generated
      private Long idRapport;
      @Generated
      private String type;
      @Generated
      private LocalDate periodeDebut;
      @Generated
      private LocalDate periodeFin;
      @Generated
      private LocalDateTime dateGeneration;
      @Generated
      private String contenu;
      @Generated
      private Gestionnaire generePar;

      @Generated
      RapportBuilder() {
      }

      @Generated
      public RapportBuilder idRapport(final Long idRapport) {
         this.idRapport = idRapport;
         return this;
      }

      @Generated
      public RapportBuilder type(final String type) {
         this.type = type;
         return this;
      }

      @Generated
      public RapportBuilder periodeDebut(final LocalDate periodeDebut) {
         this.periodeDebut = periodeDebut;
         return this;
      }

      @Generated
      public RapportBuilder periodeFin(final LocalDate periodeFin) {
         this.periodeFin = periodeFin;
         return this;
      }

      @Generated
      public RapportBuilder dateGeneration(final LocalDateTime dateGeneration) {
         this.dateGeneration = dateGeneration;
         return this;
      }

      @Generated
      public RapportBuilder contenu(final String contenu) {
         this.contenu = contenu;
         return this;
      }

      @JsonIgnoreProperties({"motDePasse", "authorities", "accountNonExpired", "accountNonLocked", "credentialsNonExpired", "enabled"})
      @Generated
      public RapportBuilder generePar(final Gestionnaire generePar) {
         this.generePar = generePar;
         return this;
      }

      @Generated
      public Rapport build() {
         return new Rapport(this.idRapport, this.type, this.periodeDebut, this.periodeFin, this.dateGeneration, this.contenu, this.generePar);
      }

      @Generated
      public String toString() {
         Long var10000 = this.idRapport;
         return "Rapport.RapportBuilder(idRapport=" + var10000 + ", type=" + this.type + ", periodeDebut=" + String.valueOf(this.periodeDebut) + ", periodeFin=" + String.valueOf(this.periodeFin) + ", dateGeneration=" + String.valueOf(this.dateGeneration) + ", contenu=" + this.contenu + ", generePar=" + String.valueOf(this.generePar) + ")";
      }
   }
}
