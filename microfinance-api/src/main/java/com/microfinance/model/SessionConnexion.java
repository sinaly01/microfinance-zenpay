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
   name = "SESSIONS_CONNEXION"
)
public class SessionConnexion {
   @Id
   @GeneratedValue(
      strategy = GenerationType.SEQUENCE,
      generator = "session_seq"
   )
   @SequenceGenerator(
      name = "session_seq",
      sequenceName = "SESSION_SEQ",
      allocationSize = 1
   )
   @Column(
      name = "ID_SESSION"
   )
   private Long idSession;
   @ManyToOne(
      fetch = FetchType.LAZY
   )
   @JoinColumn(
      name = "ID_GESTIONNAIRE"
   )
   private Gestionnaire gestionnaire;
   @ManyToOne(
      fetch = FetchType.LAZY
   )
   @JoinColumn(
      name = "ID_CLIENT"
   )
   private Client client;
   @Column(
      name = "ADRESSE_IP",
      nullable = false,
      length = 45
   )
   private String adresseIp;
   @Column(
      name = "JTI_TOKEN",
      length = 100
   )
   private String jtiToken;
   @Column(
      name = "DATE_CONNEXION",
      nullable = false
   )
   private LocalDateTime dateConnexion;
   @Column(
      name = "DATE_DECONNEXION"
   )
   private LocalDateTime dateDeconnexion;
   @Column(
      name = "STATUT_SESSION",
      nullable = false,
      length = 20
   )
   private String statutSession = "ACTIVE";

   @PrePersist
   public void prePersist() {
      this.dateConnexion = LocalDateTime.now();
   }

   public void clore() {
      this.statutSession = "EXPIREE";
      this.dateDeconnexion = LocalDateTime.now();
   }

   @Generated
   public static SessionConnexionBuilder builder() {
      return new SessionConnexionBuilder();
   }

   @Generated
   public Long getIdSession() {
      return this.idSession;
   }

   @Generated
   public Gestionnaire getGestionnaire() {
      return this.gestionnaire;
   }

   @Generated
   public Client getClient() {
      return this.client;
   }

   @Generated
   public String getAdresseIp() {
      return this.adresseIp;
   }

   @Generated
   public String getJtiToken() {
      return this.jtiToken;
   }

   @Generated
   public LocalDateTime getDateConnexion() {
      return this.dateConnexion;
   }

   @Generated
   public LocalDateTime getDateDeconnexion() {
      return this.dateDeconnexion;
   }

   @Generated
   public String getStatutSession() {
      return this.statutSession;
   }

   @Generated
   public void setIdSession(final Long idSession) {
      this.idSession = idSession;
   }

   @Generated
   public void setGestionnaire(final Gestionnaire gestionnaire) {
      this.gestionnaire = gestionnaire;
   }

   @Generated
   public void setClient(final Client client) {
      this.client = client;
   }

   @Generated
   public void setAdresseIp(final String adresseIp) {
      this.adresseIp = adresseIp;
   }

   @Generated
   public void setJtiToken(final String jtiToken) {
      this.jtiToken = jtiToken;
   }

   @Generated
   public void setDateConnexion(final LocalDateTime dateConnexion) {
      this.dateConnexion = dateConnexion;
   }

   @Generated
   public void setDateDeconnexion(final LocalDateTime dateDeconnexion) {
      this.dateDeconnexion = dateDeconnexion;
   }

   @Generated
   public void setStatutSession(final String statutSession) {
      this.statutSession = statutSession;
   }

   @Generated
   public SessionConnexion() {
   }

   @Generated
   public SessionConnexion(final Long idSession, final Gestionnaire gestionnaire, final Client client, final String adresseIp, final String jtiToken, final LocalDateTime dateConnexion, final LocalDateTime dateDeconnexion, final String statutSession) {
      this.idSession = idSession;
      this.gestionnaire = gestionnaire;
      this.client = client;
      this.adresseIp = adresseIp;
      this.jtiToken = jtiToken;
      this.dateConnexion = dateConnexion;
      this.dateDeconnexion = dateDeconnexion;
      this.statutSession = statutSession;
   }

   @Generated
   public static class SessionConnexionBuilder {
      @Generated
      private Long idSession;
      @Generated
      private Gestionnaire gestionnaire;
      @Generated
      private Client client;
      @Generated
      private String adresseIp;
      @Generated
      private String jtiToken;
      @Generated
      private LocalDateTime dateConnexion;
      @Generated
      private LocalDateTime dateDeconnexion;
      @Generated
      private String statutSession;

      @Generated
      SessionConnexionBuilder() {
      }

      @Generated
      public SessionConnexionBuilder idSession(final Long idSession) {
         this.idSession = idSession;
         return this;
      }

      @Generated
      public SessionConnexionBuilder gestionnaire(final Gestionnaire gestionnaire) {
         this.gestionnaire = gestionnaire;
         return this;
      }

      @Generated
      public SessionConnexionBuilder client(final Client client) {
         this.client = client;
         return this;
      }

      @Generated
      public SessionConnexionBuilder adresseIp(final String adresseIp) {
         this.adresseIp = adresseIp;
         return this;
      }

      @Generated
      public SessionConnexionBuilder jtiToken(final String jtiToken) {
         this.jtiToken = jtiToken;
         return this;
      }

      @Generated
      public SessionConnexionBuilder dateConnexion(final LocalDateTime dateConnexion) {
         this.dateConnexion = dateConnexion;
         return this;
      }

      @Generated
      public SessionConnexionBuilder dateDeconnexion(final LocalDateTime dateDeconnexion) {
         this.dateDeconnexion = dateDeconnexion;
         return this;
      }

      @Generated
      public SessionConnexionBuilder statutSession(final String statutSession) {
         this.statutSession = statutSession;
         return this;
      }

      @Generated
      public SessionConnexion build() {
         return new SessionConnexion(this.idSession, this.gestionnaire, this.client, this.adresseIp, this.jtiToken, this.dateConnexion, this.dateDeconnexion, this.statutSession);
      }

      @Generated
      public String toString() {
         Long var10000 = this.idSession;
         return "SessionConnexion.SessionConnexionBuilder(idSession=" + var10000 + ", gestionnaire=" + String.valueOf(this.gestionnaire) + ", client=" + String.valueOf(this.client) + ", adresseIp=" + this.adresseIp + ", jtiToken=" + this.jtiToken + ", dateConnexion=" + String.valueOf(this.dateConnexion) + ", dateDeconnexion=" + String.valueOf(this.dateDeconnexion) + ", statutSession=" + this.statutSession + ")";
      }
   }
}
