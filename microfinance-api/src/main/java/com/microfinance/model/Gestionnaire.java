package com.microfinance.model;

import com.fasterxml.jackson.annotation.JsonIgnore;
import com.microfinance.model.enums.RoleUtilisateur;
import jakarta.persistence.Column;
import jakarta.persistence.Entity;
import jakarta.persistence.EnumType;
import jakarta.persistence.Enumerated;
import jakarta.persistence.GeneratedValue;
import jakarta.persistence.GenerationType;
import jakarta.persistence.Id;
import jakarta.persistence.SequenceGenerator;
import jakarta.persistence.Table;
import java.time.LocalDate;
import java.time.LocalDateTime;
import java.util.Collection;
import java.util.List;
import lombok.Generated;
import org.springframework.security.core.authority.SimpleGrantedAuthority;
import org.springframework.security.core.userdetails.UserDetails;

@Entity
@Table(
   name = "GESTIONNAIRES"
)
public class Gestionnaire implements UserDetails {
   @Id
   @GeneratedValue(
      strategy = GenerationType.SEQUENCE,
      generator = "gestionnaire_seq"
   )
   @SequenceGenerator(
      name = "gestionnaire_seq",
      sequenceName = "GESTIONNAIRE_SEQ",
      allocationSize = 1
   )
   @Column(
      name = "ID_GESTIONNAIRE"
   )
   private Long idGestionnaire;
   @Column(
      name = "NOM",
      nullable = false,
      length = 100
   )
   private String nom;
   @Column(
      name = "PRENOM",
      nullable = false,
      length = 100
   )
   private String prenom;
   @Column(
      name = "EMAIL",
      nullable = false,
      unique = true,
      length = 150
   )
   private String email;
   @JsonIgnore
   @Column(
      name = "MOT_DE_PASSE",
      nullable = false
   )
   private String motDePasse;
   @Enumerated(EnumType.STRING)
   @Column(
      name = "ROLE",
      nullable = false,
      columnDefinition = "VARCHAR2(30)"
   )
   private RoleUtilisateur role;
   @Column(
      name = "DATE_EMBAUCHE"
   )
   private LocalDate dateEmbauche;
   @Column(
      name = "ACTIF",
      nullable = false
   )
   private boolean actif = true;
   @Column(
      name = "OTP_CODE",
      length = 10
   )
   private String otpCode;
   @Column(
      name = "OTP_EXPIRATION"
   )
   private LocalDateTime otpExpiration;
   @Column(
      name = "TOKEN_RESET_PWD",
      length = 10
   )
   private String tokenResetPassword;
   @Column(
      name = "DATE_EXPIRATION_RESET"
   )
   private LocalDateTime dateExpirationReset;
   @JsonIgnore
   @Column(
      name = "CLE_SECRETE",
      length = 200
   )
   private String cleSecrete;

   public boolean validerOperation(Transaction t) {
      return t != null && t.getMontant() != null;
   }

   public Collection getAuthorities() {
      return List.of(new SimpleGrantedAuthority(this.role.name()));
   }

   public String getPassword() {
      return this.motDePasse;
   }

   public String getUsername() {
      return this.email;
   }

   public boolean isAccountNonExpired() {
      return true;
   }

   public boolean isAccountNonLocked() {
      return this.actif;
   }

   public boolean isCredentialsNonExpired() {
      return true;
   }

   public boolean isEnabled() {
      return this.actif;
   }

   @Generated
   public static GestionnaireBuilder builder() {
      return new GestionnaireBuilder();
   }

   @Generated
   public Long getIdGestionnaire() {
      return this.idGestionnaire;
   }

   @Generated
   public String getNom() {
      return this.nom;
   }

   @Generated
   public String getPrenom() {
      return this.prenom;
   }

   @Generated
   public String getEmail() {
      return this.email;
   }

   @Generated
   public String getMotDePasse() {
      return this.motDePasse;
   }

   @Generated
   public RoleUtilisateur getRole() {
      return this.role;
   }

   @Generated
   public LocalDate getDateEmbauche() {
      return this.dateEmbauche;
   }

   @Generated
   public boolean isActif() {
      return this.actif;
   }

   @Generated
   public String getOtpCode() {
      return this.otpCode;
   }

   @Generated
   public LocalDateTime getOtpExpiration() {
      return this.otpExpiration;
   }

   @Generated
   public String getTokenResetPassword() {
      return this.tokenResetPassword;
   }

   @Generated
   public LocalDateTime getDateExpirationReset() {
      return this.dateExpirationReset;
   }

   @Generated
   public String getCleSecrete() {
      return this.cleSecrete;
   }

   @Generated
   public void setIdGestionnaire(final Long idGestionnaire) {
      this.idGestionnaire = idGestionnaire;
   }

   @Generated
   public void setNom(final String nom) {
      this.nom = nom;
   }

   @Generated
   public void setPrenom(final String prenom) {
      this.prenom = prenom;
   }

   @Generated
   public void setEmail(final String email) {
      this.email = email;
   }

   @Generated
   public void setMotDePasse(final String motDePasse) {
      this.motDePasse = motDePasse;
   }

   @Generated
   public void setRole(final RoleUtilisateur role) {
      this.role = role;
   }

   @Generated
   public void setDateEmbauche(final LocalDate dateEmbauche) {
      this.dateEmbauche = dateEmbauche;
   }

   @Generated
   public void setActif(final boolean actif) {
      this.actif = actif;
   }

   @Generated
   public void setOtpCode(final String otpCode) {
      this.otpCode = otpCode;
   }

   @Generated
   public void setOtpExpiration(final LocalDateTime otpExpiration) {
      this.otpExpiration = otpExpiration;
   }

   @Generated
   public void setTokenResetPassword(final String tokenResetPassword) {
      this.tokenResetPassword = tokenResetPassword;
   }

   @Generated
   public void setDateExpirationReset(final LocalDateTime dateExpirationReset) {
      this.dateExpirationReset = dateExpirationReset;
   }

   @Generated
   public void setCleSecrete(final String cleSecrete) {
      this.cleSecrete = cleSecrete;
   }

   @Generated
   public Gestionnaire() {
   }

   @Generated
   public Gestionnaire(final Long idGestionnaire, final String nom, final String prenom, final String email, final String motDePasse, final RoleUtilisateur role, final LocalDate dateEmbauche, final boolean actif, final String otpCode, final LocalDateTime otpExpiration, final String tokenResetPassword, final LocalDateTime dateExpirationReset, final String cleSecrete) {
      this.idGestionnaire = idGestionnaire;
      this.nom = nom;
      this.prenom = prenom;
      this.email = email;
      this.motDePasse = motDePasse;
      this.role = role;
      this.dateEmbauche = dateEmbauche;
      this.actif = actif;
      this.otpCode = otpCode;
      this.otpExpiration = otpExpiration;
      this.tokenResetPassword = tokenResetPassword;
      this.dateExpirationReset = dateExpirationReset;
      this.cleSecrete = cleSecrete;
   }

   @Generated
   public static class GestionnaireBuilder {
      @Generated
      private Long idGestionnaire;
      @Generated
      private String nom;
      @Generated
      private String prenom;
      @Generated
      private String email;
      @Generated
      private String motDePasse;
      @Generated
      private RoleUtilisateur role;
      @Generated
      private LocalDate dateEmbauche;
      @Generated
      private boolean actif;
      @Generated
      private String otpCode;
      @Generated
      private LocalDateTime otpExpiration;
      @Generated
      private String tokenResetPassword;
      @Generated
      private LocalDateTime dateExpirationReset;
      @Generated
      private String cleSecrete;

      @Generated
      GestionnaireBuilder() {
      }

      @Generated
      public GestionnaireBuilder idGestionnaire(final Long idGestionnaire) {
         this.idGestionnaire = idGestionnaire;
         return this;
      }

      @Generated
      public GestionnaireBuilder nom(final String nom) {
         this.nom = nom;
         return this;
      }

      @Generated
      public GestionnaireBuilder prenom(final String prenom) {
         this.prenom = prenom;
         return this;
      }

      @Generated
      public GestionnaireBuilder email(final String email) {
         this.email = email;
         return this;
      }

      @JsonIgnore
      @Generated
      public GestionnaireBuilder motDePasse(final String motDePasse) {
         this.motDePasse = motDePasse;
         return this;
      }

      @Generated
      public GestionnaireBuilder role(final RoleUtilisateur role) {
         this.role = role;
         return this;
      }

      @Generated
      public GestionnaireBuilder dateEmbauche(final LocalDate dateEmbauche) {
         this.dateEmbauche = dateEmbauche;
         return this;
      }

      @Generated
      public GestionnaireBuilder actif(final boolean actif) {
         this.actif = actif;
         return this;
      }

      @Generated
      public GestionnaireBuilder otpCode(final String otpCode) {
         this.otpCode = otpCode;
         return this;
      }

      @Generated
      public GestionnaireBuilder otpExpiration(final LocalDateTime otpExpiration) {
         this.otpExpiration = otpExpiration;
         return this;
      }

      @Generated
      public GestionnaireBuilder tokenResetPassword(final String tokenResetPassword) {
         this.tokenResetPassword = tokenResetPassword;
         return this;
      }

      @Generated
      public GestionnaireBuilder dateExpirationReset(final LocalDateTime dateExpirationReset) {
         this.dateExpirationReset = dateExpirationReset;
         return this;
      }

      @JsonIgnore
      @Generated
      public GestionnaireBuilder cleSecrete(final String cleSecrete) {
         this.cleSecrete = cleSecrete;
         return this;
      }

      @Generated
      public Gestionnaire build() {
         return new Gestionnaire(this.idGestionnaire, this.nom, this.prenom, this.email, this.motDePasse, this.role, this.dateEmbauche, this.actif, this.otpCode, this.otpExpiration, this.tokenResetPassword, this.dateExpirationReset, this.cleSecrete);
      }

      @Generated
      public String toString() {
         Long var10000 = this.idGestionnaire;
         return "Gestionnaire.GestionnaireBuilder(idGestionnaire=" + var10000 + ", nom=" + this.nom + ", prenom=" + this.prenom + ", email=" + this.email + ", motDePasse=" + this.motDePasse + ", role=" + String.valueOf(this.role) + ", dateEmbauche=" + String.valueOf(this.dateEmbauche) + ", actif=" + this.actif + ", otpCode=" + this.otpCode + ", otpExpiration=" + String.valueOf(this.otpExpiration) + ", tokenResetPassword=" + this.tokenResetPassword + ", dateExpirationReset=" + String.valueOf(this.dateExpirationReset) + ", cleSecrete=" + this.cleSecrete + ")";
      }
   }
}
