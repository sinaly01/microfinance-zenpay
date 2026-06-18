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
   name = "LOGS_AUDIT"
)
public class LogAudit {
   @Id
   @GeneratedValue(
      strategy = GenerationType.SEQUENCE,
      generator = "log_seq"
   )
   @SequenceGenerator(
      name = "log_seq",
      sequenceName = "LOG_SEQ",
      allocationSize = 1
   )
   @Column(
      name = "ID_LOG"
   )
   private Long idLog;
   @ManyToOne(
      fetch = FetchType.LAZY
   )
   @JoinColumn(
      name = "ID_UTILISATEUR"
   )
   private Gestionnaire utilisateur;
   @Column(
      name = "ACTION_EFFECTUEE",
      nullable = false,
      length = 255
   )
   private String actionEffectuee;
   @Column(
      name = "ADRESSE_IP",
      length = 45
   )
   private String adresseIp;
   @Column(
      name = "DATE_HEURE",
      nullable = false
   )
   private LocalDateTime dateHeure;

   @PrePersist
   public void prePersist() {
      this.dateHeure = LocalDateTime.now();
   }

   @Generated
   public static LogAuditBuilder builder() {
      return new LogAuditBuilder();
   }

   @Generated
   public Long getIdLog() {
      return this.idLog;
   }

   @Generated
   public Gestionnaire getUtilisateur() {
      return this.utilisateur;
   }

   @Generated
   public String getActionEffectuee() {
      return this.actionEffectuee;
   }

   @Generated
   public String getAdresseIp() {
      return this.adresseIp;
   }

   @Generated
   public LocalDateTime getDateHeure() {
      return this.dateHeure;
   }

   @Generated
   public void setIdLog(final Long idLog) {
      this.idLog = idLog;
   }

   @Generated
   public void setUtilisateur(final Gestionnaire utilisateur) {
      this.utilisateur = utilisateur;
   }

   @Generated
   public void setActionEffectuee(final String actionEffectuee) {
      this.actionEffectuee = actionEffectuee;
   }

   @Generated
   public void setAdresseIp(final String adresseIp) {
      this.adresseIp = adresseIp;
   }

   @Generated
   public void setDateHeure(final LocalDateTime dateHeure) {
      this.dateHeure = dateHeure;
   }

   @Generated
   public LogAudit() {
   }

   @Generated
   public LogAudit(final Long idLog, final Gestionnaire utilisateur, final String actionEffectuee, final String adresseIp, final LocalDateTime dateHeure) {
      this.idLog = idLog;
      this.utilisateur = utilisateur;
      this.actionEffectuee = actionEffectuee;
      this.adresseIp = adresseIp;
      this.dateHeure = dateHeure;
   }

   @Generated
   public static class LogAuditBuilder {
      @Generated
      private Long idLog;
      @Generated
      private Gestionnaire utilisateur;
      @Generated
      private String actionEffectuee;
      @Generated
      private String adresseIp;
      @Generated
      private LocalDateTime dateHeure;

      @Generated
      LogAuditBuilder() {
      }

      @Generated
      public LogAuditBuilder idLog(final Long idLog) {
         this.idLog = idLog;
         return this;
      }

      @Generated
      public LogAuditBuilder utilisateur(final Gestionnaire utilisateur) {
         this.utilisateur = utilisateur;
         return this;
      }

      @Generated
      public LogAuditBuilder actionEffectuee(final String actionEffectuee) {
         this.actionEffectuee = actionEffectuee;
         return this;
      }

      @Generated
      public LogAuditBuilder adresseIp(final String adresseIp) {
         this.adresseIp = adresseIp;
         return this;
      }

      @Generated
      public LogAuditBuilder dateHeure(final LocalDateTime dateHeure) {
         this.dateHeure = dateHeure;
         return this;
      }

      @Generated
      public LogAudit build() {
         return new LogAudit(this.idLog, this.utilisateur, this.actionEffectuee, this.adresseIp, this.dateHeure);
      }

      @Generated
      public String toString() {
         Long var10000 = this.idLog;
         return "LogAudit.LogAuditBuilder(idLog=" + var10000 + ", utilisateur=" + String.valueOf(this.utilisateur) + ", actionEffectuee=" + this.actionEffectuee + ", adresseIp=" + this.adresseIp + ", dateHeure=" + String.valueOf(this.dateHeure) + ")";
      }
   }
}
