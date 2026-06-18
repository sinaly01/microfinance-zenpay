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
   name = "MESSAGES_SUPPORT"
)
public class MessageSupport {
   @Id
   @GeneratedValue(
      strategy = GenerationType.SEQUENCE,
      generator = "msg_support_seq"
   )
   @SequenceGenerator(
      name = "msg_support_seq",
      sequenceName = "MSG_SUPPORT_SEQ",
      allocationSize = 1
   )
   @Column(
      name = "ID_MESSAGE"
   )
   private Long idMessage;
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
      name = "ID_GESTIONNAIRE"
   )
   @JsonIgnoreProperties({"hibernateLazyInitializer", "handler", "motDePasse", "otpCode", "otpExpiration"})
   private Gestionnaire gestionnaire;
   @Column(
      name = "CONTENU",
      nullable = false,
      columnDefinition = "CLOB"
   )
   private String contenu;
   @Column(
      name = "DATE_ENVOI",
      nullable = false
   )
   private LocalDateTime dateEnvoi;
   @Column(
      name = "EXPEDITEUR",
      nullable = false,
      length = 20
   )
   private String expediteur;
   @Column(
      name = "LU",
      nullable = false
   )
   private boolean lu = false;

   @PrePersist
   public void prePersist() {
      this.dateEnvoi = LocalDateTime.now();
   }

   @Generated
   public static MessageSupportBuilder builder() {
      return new MessageSupportBuilder();
   }

   @Generated
   public Long getIdMessage() {
      return this.idMessage;
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
   public String getContenu() {
      return this.contenu;
   }

   @Generated
   public LocalDateTime getDateEnvoi() {
      return this.dateEnvoi;
   }

   @Generated
   public String getExpediteur() {
      return this.expediteur;
   }

   @Generated
   public boolean isLu() {
      return this.lu;
   }

   @Generated
   public void setIdMessage(final Long idMessage) {
      this.idMessage = idMessage;
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
   public void setContenu(final String contenu) {
      this.contenu = contenu;
   }

   @Generated
   public void setDateEnvoi(final LocalDateTime dateEnvoi) {
      this.dateEnvoi = dateEnvoi;
   }

   @Generated
   public void setExpediteur(final String expediteur) {
      this.expediteur = expediteur;
   }

   @Generated
   public void setLu(final boolean lu) {
      this.lu = lu;
   }

   @Generated
   public MessageSupport() {
   }

   @Generated
   public MessageSupport(final Long idMessage, final Client client, final Gestionnaire gestionnaire, final String contenu, final LocalDateTime dateEnvoi, final String expediteur, final boolean lu) {
      this.idMessage = idMessage;
      this.client = client;
      this.gestionnaire = gestionnaire;
      this.contenu = contenu;
      this.dateEnvoi = dateEnvoi;
      this.expediteur = expediteur;
      this.lu = lu;
   }

   @Generated
   public static class MessageSupportBuilder {
      @Generated
      private Long idMessage;
      @Generated
      private Client client;
      @Generated
      private Gestionnaire gestionnaire;
      @Generated
      private String contenu;
      @Generated
      private LocalDateTime dateEnvoi;
      @Generated
      private String expediteur;
      @Generated
      private boolean lu;

      @Generated
      MessageSupportBuilder() {
      }

      @Generated
      public MessageSupportBuilder idMessage(final Long idMessage) {
         this.idMessage = idMessage;
         return this;
      }

      @JsonIgnoreProperties({"hibernateLazyInitializer", "handler", "motDePasse", "comptes"})
      @Generated
      public MessageSupportBuilder client(final Client client) {
         this.client = client;
         return this;
      }

      @JsonIgnoreProperties({"hibernateLazyInitializer", "handler", "motDePasse", "otpCode", "otpExpiration"})
      @Generated
      public MessageSupportBuilder gestionnaire(final Gestionnaire gestionnaire) {
         this.gestionnaire = gestionnaire;
         return this;
      }

      @Generated
      public MessageSupportBuilder contenu(final String contenu) {
         this.contenu = contenu;
         return this;
      }

      @Generated
      public MessageSupportBuilder dateEnvoi(final LocalDateTime dateEnvoi) {
         this.dateEnvoi = dateEnvoi;
         return this;
      }

      @Generated
      public MessageSupportBuilder expediteur(final String expediteur) {
         this.expediteur = expediteur;
         return this;
      }

      @Generated
      public MessageSupportBuilder lu(final boolean lu) {
         this.lu = lu;
         return this;
      }

      @Generated
      public MessageSupport build() {
         return new MessageSupport(this.idMessage, this.client, this.gestionnaire, this.contenu, this.dateEnvoi, this.expediteur, this.lu);
      }

      @Generated
      public String toString() {
         Long var10000 = this.idMessage;
         return "MessageSupport.MessageSupportBuilder(idMessage=" + var10000 + ", client=" + String.valueOf(this.client) + ", gestionnaire=" + String.valueOf(this.gestionnaire) + ", contenu=" + this.contenu + ", dateEnvoi=" + String.valueOf(this.dateEnvoi) + ", expediteur=" + this.expediteur + ", lu=" + this.lu + ")";
      }
   }
}
