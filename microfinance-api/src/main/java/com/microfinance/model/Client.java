package com.microfinance.model;

import com.fasterxml.jackson.annotation.JsonIgnore;
import com.microfinance.model.enums.RoleUtilisateur;
import com.microfinance.model.enums.StatutKyc;
import jakarta.persistence.CascadeType;
import jakarta.persistence.Column;
import jakarta.persistence.Entity;
import jakarta.persistence.EnumType;
import jakarta.persistence.Enumerated;
import jakarta.persistence.FetchType;
import jakarta.persistence.GeneratedValue;
import jakarta.persistence.GenerationType;
import jakarta.persistence.Id;
import jakarta.persistence.JoinColumn;
import jakarta.persistence.ManyToOne;
import jakarta.persistence.OneToMany;
import jakarta.persistence.PrePersist;
import jakarta.persistence.SequenceGenerator;
import jakarta.persistence.Table;
import jakarta.validation.constraints.Email;
import jakarta.validation.constraints.NotBlank;
import java.time.LocalDate;
import java.time.LocalDateTime;
import java.util.ArrayList;
import java.util.Collection;
import java.util.List;
import lombok.Generated;
import org.springframework.security.core.authority.SimpleGrantedAuthority;
import org.springframework.security.core.userdetails.UserDetails;

@Entity
@Table(
   name = "CLIENTS"
)
public class Client implements UserDetails {
   @Id
   @GeneratedValue(
      strategy = GenerationType.SEQUENCE,
      generator = "client_seq"
   )
   @SequenceGenerator(
      name = "client_seq",
      sequenceName = "CLIENT_SEQ",
      allocationSize = 1
   )
   @Column(
      name = "ID_CLIENT"
   )
   private Long idClient;
   @Column(
      name = "NOM",
      nullable = false,
      length = 100
   )
   private @NotBlank String nom;
   @Column(
      name = "PRENOM",
      nullable = false,
      length = 100
   )
   private @NotBlank String prenom;
   @Column(
      name = "TELEPHONE",
      nullable = false,
      unique = true,
      length = 20
   )
   private @NotBlank String telephone;
   @Column(
      name = "EMAIL",
      unique = true,
      length = 150
   )
   private @Email String email;
   @Column(
      name = "ADRESSE",
      nullable = false,
      length = 255
   )
   private @NotBlank String adresse;
   @Column(
      name = "NUMERO_CNI",
      unique = true,
      length = 50
   )
   private String numeroCni;
   @Column(
      name = "DATE_NAISSANCE"
   )
   private LocalDate dateNaissance;
   @Column(
      name = "DATE_INSCRIPTION",
      nullable = false
   )
   private LocalDate dateInscription;
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
      name = "ACTIF",
      nullable = false
   )
   private boolean actif;
   @Enumerated(EnumType.STRING)
   @Column(
      name = "STATUT_KYC",
      length = 20
   )
   private StatutKyc statutKyc;
   @Column(
      name = "OPERATEUR_MOMO",
      length = 20
   )
   private String operateurMomo;
   @ManyToOne(
      fetch = FetchType.EAGER
   )
   @JoinColumn(
      name = "ID_OFFRE"
   )
   private OffreAbonnement offreAbonnement;
   @Column(
      name = "DATE_PROCHAIN_PRELEVEMENT"
   )
   private LocalDateTime dateProchainPrelevement;
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
   @OneToMany(
      mappedBy = "client",
      cascade = {CascadeType.ALL},
      fetch = FetchType.LAZY
   )
   private List comptes;

   @PrePersist
   public void prePersist() {
      this.dateInscription = LocalDate.now();
      if (this.statutKyc == null) {
         this.statutKyc = StatutKyc.PENDING;
      }

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
   public static ClientBuilder builder() {
      return new ClientBuilder();
   }

   @Generated
   public Long getIdClient() {
      return this.idClient;
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
   public String getTelephone() {
      return this.telephone;
   }

   @Generated
   public String getEmail() {
      return this.email;
   }

   @Generated
   public String getAdresse() {
      return this.adresse;
   }

   @Generated
   public String getNumeroCni() {
      return this.numeroCni;
   }

   @Generated
   public LocalDate getDateNaissance() {
      return this.dateNaissance;
   }

   @Generated
   public LocalDate getDateInscription() {
      return this.dateInscription;
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
   public boolean isActif() {
      return this.actif;
   }

   @Generated
   public StatutKyc getStatutKyc() {
      return this.statutKyc;
   }

   @Generated
   public String getOperateurMomo() {
      return this.operateurMomo;
   }

   @Generated
   public OffreAbonnement getOffreAbonnement() {
      return this.offreAbonnement;
   }

   @Generated
   public LocalDateTime getDateProchainPrelevement() {
      return this.dateProchainPrelevement;
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
   public List getComptes() {
      return this.comptes;
   }

   @Generated
   public void setIdClient(final Long idClient) {
      this.idClient = idClient;
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
   public void setTelephone(final String telephone) {
      this.telephone = telephone;
   }

   @Generated
   public void setEmail(final String email) {
      this.email = email;
   }

   @Generated
   public void setAdresse(final String adresse) {
      this.adresse = adresse;
   }

   @Generated
   public void setNumeroCni(final String numeroCni) {
      this.numeroCni = numeroCni;
   }

   @Generated
   public void setDateNaissance(final LocalDate dateNaissance) {
      this.dateNaissance = dateNaissance;
   }

   @Generated
   public void setDateInscription(final LocalDate dateInscription) {
      this.dateInscription = dateInscription;
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
   public void setActif(final boolean actif) {
      this.actif = actif;
   }

   @Generated
   public void setStatutKyc(final StatutKyc statutKyc) {
      this.statutKyc = statutKyc;
   }

   @Generated
   public void setOperateurMomo(final String operateurMomo) {
      this.operateurMomo = operateurMomo;
   }

   @Generated
   public void setOffreAbonnement(final OffreAbonnement offreAbonnement) {
      this.offreAbonnement = offreAbonnement;
   }

   @Generated
   public void setDateProchainPrelevement(final LocalDateTime dateProchainPrelevement) {
      this.dateProchainPrelevement = dateProchainPrelevement;
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
   public void setComptes(final List comptes) {
      this.comptes = comptes;
   }

   @Generated
   public Client() {
      this.role = RoleUtilisateur.ROLE_CLIENT;
      this.actif = true;
      this.statutKyc = StatutKyc.PENDING;
      this.comptes = new ArrayList();
   }

   @Generated
   public Client(final Long idClient, final String nom, final String prenom, final String telephone, final String email, final String adresse, final String numeroCni, final LocalDate dateNaissance, final LocalDate dateInscription, final String motDePasse, final RoleUtilisateur role, final boolean actif, final StatutKyc statutKyc, final String operateurMomo, final OffreAbonnement offreAbonnement, final LocalDateTime dateProchainPrelevement, final String tokenResetPassword, final LocalDateTime dateExpirationReset, final List comptes) {
      this.role = RoleUtilisateur.ROLE_CLIENT;
      this.actif = true;
      this.statutKyc = StatutKyc.PENDING;
      this.comptes = new ArrayList();
      this.idClient = idClient;
      this.nom = nom;
      this.prenom = prenom;
      this.telephone = telephone;
      this.email = email;
      this.adresse = adresse;
      this.numeroCni = numeroCni;
      this.dateNaissance = dateNaissance;
      this.dateInscription = dateInscription;
      this.motDePasse = motDePasse;
      this.role = role;
      this.actif = actif;
      this.statutKyc = statutKyc;
      this.operateurMomo = operateurMomo;
      this.offreAbonnement = offreAbonnement;
      this.dateProchainPrelevement = dateProchainPrelevement;
      this.tokenResetPassword = tokenResetPassword;
      this.dateExpirationReset = dateExpirationReset;
      this.comptes = comptes;
   }

   @Generated
   public static class ClientBuilder {
      @Generated
      private Long idClient;
      @Generated
      private String nom;
      @Generated
      private String prenom;
      @Generated
      private String telephone;
      @Generated
      private String email;
      @Generated
      private String adresse;
      @Generated
      private String numeroCni;
      @Generated
      private LocalDate dateNaissance;
      @Generated
      private LocalDate dateInscription;
      @Generated
      private String motDePasse;
      @Generated
      private RoleUtilisateur role;
      @Generated
      private boolean actif;
      @Generated
      private StatutKyc statutKyc;
      @Generated
      private String operateurMomo;
      @Generated
      private OffreAbonnement offreAbonnement;
      @Generated
      private LocalDateTime dateProchainPrelevement;
      @Generated
      private String tokenResetPassword;
      @Generated
      private LocalDateTime dateExpirationReset;
      @Generated
      private List comptes;

      @Generated
      ClientBuilder() {
      }

      @Generated
      public ClientBuilder idClient(final Long idClient) {
         this.idClient = idClient;
         return this;
      }

      @Generated
      public ClientBuilder nom(final String nom) {
         this.nom = nom;
         return this;
      }

      @Generated
      public ClientBuilder prenom(final String prenom) {
         this.prenom = prenom;
         return this;
      }

      @Generated
      public ClientBuilder telephone(final String telephone) {
         this.telephone = telephone;
         return this;
      }

      @Generated
      public ClientBuilder email(final String email) {
         this.email = email;
         return this;
      }

      @Generated
      public ClientBuilder adresse(final String adresse) {
         this.adresse = adresse;
         return this;
      }

      @Generated
      public ClientBuilder numeroCni(final String numeroCni) {
         this.numeroCni = numeroCni;
         return this;
      }

      @Generated
      public ClientBuilder dateNaissance(final LocalDate dateNaissance) {
         this.dateNaissance = dateNaissance;
         return this;
      }

      @Generated
      public ClientBuilder dateInscription(final LocalDate dateInscription) {
         this.dateInscription = dateInscription;
         return this;
      }

      @JsonIgnore
      @Generated
      public ClientBuilder motDePasse(final String motDePasse) {
         this.motDePasse = motDePasse;
         return this;
      }

      @Generated
      public ClientBuilder role(final RoleUtilisateur role) {
         this.role = role;
         return this;
      }

      @Generated
      public ClientBuilder actif(final boolean actif) {
         this.actif = actif;
         return this;
      }

      @Generated
      public ClientBuilder statutKyc(final StatutKyc statutKyc) {
         this.statutKyc = statutKyc;
         return this;
      }

      @Generated
      public ClientBuilder operateurMomo(final String operateurMomo) {
         this.operateurMomo = operateurMomo;
         return this;
      }

      @Generated
      public ClientBuilder offreAbonnement(final OffreAbonnement offreAbonnement) {
         this.offreAbonnement = offreAbonnement;
         return this;
      }

      @Generated
      public ClientBuilder dateProchainPrelevement(final LocalDateTime dateProchainPrelevement) {
         this.dateProchainPrelevement = dateProchainPrelevement;
         return this;
      }

      @Generated
      public ClientBuilder tokenResetPassword(final String tokenResetPassword) {
         this.tokenResetPassword = tokenResetPassword;
         return this;
      }

      @Generated
      public ClientBuilder dateExpirationReset(final LocalDateTime dateExpirationReset) {
         this.dateExpirationReset = dateExpirationReset;
         return this;
      }

      @JsonIgnore
      @Generated
      public ClientBuilder comptes(final List comptes) {
         this.comptes = comptes;
         return this;
      }

      @Generated
      public Client build() {
         return new Client(this.idClient, this.nom, this.prenom, this.telephone, this.email, this.adresse, this.numeroCni, this.dateNaissance, this.dateInscription, this.motDePasse, this.role, this.actif, this.statutKyc, this.operateurMomo, this.offreAbonnement, this.dateProchainPrelevement, this.tokenResetPassword, this.dateExpirationReset, this.comptes);
      }

      @Generated
      public String toString() {
         Long var10000 = this.idClient;
         return "Client.ClientBuilder(idClient=" + var10000 + ", nom=" + this.nom + ", prenom=" + this.prenom + ", telephone=" + this.telephone + ", email=" + this.email + ", adresse=" + this.adresse + ", numeroCni=" + this.numeroCni + ", dateNaissance=" + String.valueOf(this.dateNaissance) + ", dateInscription=" + String.valueOf(this.dateInscription) + ", motDePasse=" + this.motDePasse + ", role=" + String.valueOf(this.role) + ", actif=" + this.actif + ", statutKyc=" + String.valueOf(this.statutKyc) + ", operateurMomo=" + this.operateurMomo + ", offreAbonnement=" + String.valueOf(this.offreAbonnement) + ", dateProchainPrelevement=" + String.valueOf(this.dateProchainPrelevement) + ", tokenResetPassword=" + this.tokenResetPassword + ", dateExpirationReset=" + String.valueOf(this.dateExpirationReset) + ", comptes=" + String.valueOf(this.comptes) + ")";
      }
   }
}
