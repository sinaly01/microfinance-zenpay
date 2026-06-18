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
   name = "SUGGESTIONS_MODIF_PROFIL"
)
public class SuggestionModifProfil {
   @Id
   @GeneratedValue(
      strategy = GenerationType.SEQUENCE,
      generator = "suggestion_seq"
   )
   @SequenceGenerator(
      name = "suggestion_seq",
      sequenceName = "SUGGESTION_SEQ",
      allocationSize = 1
   )
   @Column(
      name = "ID_SUGGESTION"
   )
   private Long idSuggestion;
   @ManyToOne(
      fetch = FetchType.LAZY
   )
   @JoinColumn(
      name = "ID_CLIENT",
      nullable = false
   )
   private Client client;
   @Column(
      name = "NOUVEAU_NOM",
      length = 100
   )
   private String nouveauNom;
   @Column(
      name = "NOUVEAU_PRENOM",
      length = 100
   )
   private String nouveauPrenom;
   @Column(
      name = "NOUVEL_EMAIL",
      length = 150
   )
   private String nouvelEmail;
   @Column(
      name = "NOUVEAU_TELEPHONE",
      length = 20
   )
   private String nouveauTelephone;
   @Column(
      name = "NOUVELLE_ADRESSE",
      length = 255
   )
   private String nouvelleAdresse;
   @Column(
      name = "STATUT",
      nullable = false,
      length = 20
   )
   private String statut = "EN_ATTENTE";
   @ManyToOne(
      fetch = FetchType.LAZY
   )
   @JoinColumn(
      name = "ID_GESTIONNAIRE_VALIDATEUR"
   )
   private Gestionnaire gestionnaireValidateur;
   @Column(
      name = "DATE_DEMANDE",
      nullable = false
   )
   private LocalDateTime dateDemande;
   @Column(
      name = "DATE_TRAITEMENT"
   )
   private LocalDateTime dateTraitement;

   @PrePersist
   public void prePersist() {
      this.dateDemande = LocalDateTime.now();
      if (this.statut == null) {
         this.statut = "EN_ATTENTE";
      }

   }

   @Generated
   public static SuggestionModifProfilBuilder builder() {
      return new SuggestionModifProfilBuilder();
   }

   @Generated
   public Long getIdSuggestion() {
      return this.idSuggestion;
   }

   @Generated
   public Client getClient() {
      return this.client;
   }

   @Generated
   public String getNouveauNom() {
      return this.nouveauNom;
   }

   @Generated
   public String getNouveauPrenom() {
      return this.nouveauPrenom;
   }

   @Generated
   public String getNouvelEmail() {
      return this.nouvelEmail;
   }

   @Generated
   public String getNouveauTelephone() {
      return this.nouveauTelephone;
   }

   @Generated
   public String getNouvelleAdresse() {
      return this.nouvelleAdresse;
   }

   @Generated
   public String getStatut() {
      return this.statut;
   }

   @Generated
   public Gestionnaire getGestionnaireValidateur() {
      return this.gestionnaireValidateur;
   }

   @Generated
   public LocalDateTime getDateDemande() {
      return this.dateDemande;
   }

   @Generated
   public LocalDateTime getDateTraitement() {
      return this.dateTraitement;
   }

   @Generated
   public void setIdSuggestion(final Long idSuggestion) {
      this.idSuggestion = idSuggestion;
   }

   @Generated
   public void setClient(final Client client) {
      this.client = client;
   }

   @Generated
   public void setNouveauNom(final String nouveauNom) {
      this.nouveauNom = nouveauNom;
   }

   @Generated
   public void setNouveauPrenom(final String nouveauPrenom) {
      this.nouveauPrenom = nouveauPrenom;
   }

   @Generated
   public void setNouvelEmail(final String nouvelEmail) {
      this.nouvelEmail = nouvelEmail;
   }

   @Generated
   public void setNouveauTelephone(final String nouveauTelephone) {
      this.nouveauTelephone = nouveauTelephone;
   }

   @Generated
   public void setNouvelleAdresse(final String nouvelleAdresse) {
      this.nouvelleAdresse = nouvelleAdresse;
   }

   @Generated
   public void setStatut(final String statut) {
      this.statut = statut;
   }

   @Generated
   public void setGestionnaireValidateur(final Gestionnaire gestionnaireValidateur) {
      this.gestionnaireValidateur = gestionnaireValidateur;
   }

   @Generated
   public void setDateDemande(final LocalDateTime dateDemande) {
      this.dateDemande = dateDemande;
   }

   @Generated
   public void setDateTraitement(final LocalDateTime dateTraitement) {
      this.dateTraitement = dateTraitement;
   }

   @Generated
   public SuggestionModifProfil() {
   }

   @Generated
   public SuggestionModifProfil(final Long idSuggestion, final Client client, final String nouveauNom, final String nouveauPrenom, final String nouvelEmail, final String nouveauTelephone, final String nouvelleAdresse, final String statut, final Gestionnaire gestionnaireValidateur, final LocalDateTime dateDemande, final LocalDateTime dateTraitement) {
      this.idSuggestion = idSuggestion;
      this.client = client;
      this.nouveauNom = nouveauNom;
      this.nouveauPrenom = nouveauPrenom;
      this.nouvelEmail = nouvelEmail;
      this.nouveauTelephone = nouveauTelephone;
      this.nouvelleAdresse = nouvelleAdresse;
      this.statut = statut;
      this.gestionnaireValidateur = gestionnaireValidateur;
      this.dateDemande = dateDemande;
      this.dateTraitement = dateTraitement;
   }

   @Generated
   public static class SuggestionModifProfilBuilder {
      @Generated
      private Long idSuggestion;
      @Generated
      private Client client;
      @Generated
      private String nouveauNom;
      @Generated
      private String nouveauPrenom;
      @Generated
      private String nouvelEmail;
      @Generated
      private String nouveauTelephone;
      @Generated
      private String nouvelleAdresse;
      @Generated
      private String statut;
      @Generated
      private Gestionnaire gestionnaireValidateur;
      @Generated
      private LocalDateTime dateDemande;
      @Generated
      private LocalDateTime dateTraitement;

      @Generated
      SuggestionModifProfilBuilder() {
      }

      @Generated
      public SuggestionModifProfilBuilder idSuggestion(final Long idSuggestion) {
         this.idSuggestion = idSuggestion;
         return this;
      }

      @Generated
      public SuggestionModifProfilBuilder client(final Client client) {
         this.client = client;
         return this;
      }

      @Generated
      public SuggestionModifProfilBuilder nouveauNom(final String nouveauNom) {
         this.nouveauNom = nouveauNom;
         return this;
      }

      @Generated
      public SuggestionModifProfilBuilder nouveauPrenom(final String nouveauPrenom) {
         this.nouveauPrenom = nouveauPrenom;
         return this;
      }

      @Generated
      public SuggestionModifProfilBuilder nouvelEmail(final String nouvelEmail) {
         this.nouvelEmail = nouvelEmail;
         return this;
      }

      @Generated
      public SuggestionModifProfilBuilder nouveauTelephone(final String nouveauTelephone) {
         this.nouveauTelephone = nouveauTelephone;
         return this;
      }

      @Generated
      public SuggestionModifProfilBuilder nouvelleAdresse(final String nouvelleAdresse) {
         this.nouvelleAdresse = nouvelleAdresse;
         return this;
      }

      @Generated
      public SuggestionModifProfilBuilder statut(final String statut) {
         this.statut = statut;
         return this;
      }

      @Generated
      public SuggestionModifProfilBuilder gestionnaireValidateur(final Gestionnaire gestionnaireValidateur) {
         this.gestionnaireValidateur = gestionnaireValidateur;
         return this;
      }

      @Generated
      public SuggestionModifProfilBuilder dateDemande(final LocalDateTime dateDemande) {
         this.dateDemande = dateDemande;
         return this;
      }

      @Generated
      public SuggestionModifProfilBuilder dateTraitement(final LocalDateTime dateTraitement) {
         this.dateTraitement = dateTraitement;
         return this;
      }

      @Generated
      public SuggestionModifProfil build() {
         return new SuggestionModifProfil(this.idSuggestion, this.client, this.nouveauNom, this.nouveauPrenom, this.nouvelEmail, this.nouveauTelephone, this.nouvelleAdresse, this.statut, this.gestionnaireValidateur, this.dateDemande, this.dateTraitement);
      }

      @Generated
      public String toString() {
         Long var10000 = this.idSuggestion;
         return "SuggestionModifProfil.SuggestionModifProfilBuilder(idSuggestion=" + var10000 + ", client=" + String.valueOf(this.client) + ", nouveauNom=" + this.nouveauNom + ", nouveauPrenom=" + this.nouveauPrenom + ", nouvelEmail=" + this.nouvelEmail + ", nouveauTelephone=" + this.nouveauTelephone + ", nouvelleAdresse=" + this.nouvelleAdresse + ", statut=" + this.statut + ", gestionnaireValidateur=" + String.valueOf(this.gestionnaireValidateur) + ", dateDemande=" + String.valueOf(this.dateDemande) + ", dateTraitement=" + String.valueOf(this.dateTraitement) + ")";
      }
   }
}
