package com.microfinance.model;

import com.fasterxml.jackson.annotation.JsonIgnore;
import com.microfinance.model.enums.StatutCompte;
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
import java.math.BigDecimal;
import java.time.LocalDateTime;
import java.util.ArrayList;
import java.util.List;
import java.util.UUID;
import lombok.Generated;

@Entity
@Table(
   name = "COMPTES"
)
public class Compte {
   @Id
   @GeneratedValue(
      strategy = GenerationType.SEQUENCE,
      generator = "compte_seq"
   )
   @SequenceGenerator(
      name = "compte_seq",
      sequenceName = "COMPTE_SEQ",
      allocationSize = 1
   )
   @Column(
      name = "ID_COMPTE"
   )
   private Long idCompte;
   @Column(
      name = "NUMERO_COMPTE",
      nullable = false,
      unique = true,
      length = 20
   )
   private String numeroCompte;
   @Column(
      name = "SOLDE",
      nullable = false,
      precision = 15,
      scale = 2
   )
   private BigDecimal solde;
   @Enumerated(EnumType.STRING)
   @Column(
      name = "STATUT",
      nullable = false,
      length = 20
   )
   private StatutCompte statut;
   @Column(
      name = "DATE_OUVERTURE",
      nullable = false
   )
   private LocalDateTime dateOuverture;
   @Column(
      name = "DATE_FERMETURE"
   )
   private LocalDateTime dateFermeture;
   @Column(
      name = "PLAFOND_RETRAIT",
      precision = 15,
      scale = 2
   )
   private BigDecimal plafondRetrait;
   @Column(
      name = "MONTANT_MIN_SOLDE",
      precision = 15,
      scale = 2
   )
   private BigDecimal montantMinSolde;
   @Column(
      name = "TAUX_AGIOS",
      precision = 5,
      scale = 4
   )
   private BigDecimal tauxAgios;
   @JsonIgnore
   @ManyToOne(
      fetch = FetchType.LAZY
   )
   @JoinColumn(
      name = "ID_CLIENT",
      nullable = false
   )
   private Client client;
   @JsonIgnore
   @OneToMany(
      mappedBy = "compte",
      cascade = {CascadeType.ALL},
      fetch = FetchType.LAZY
   )
   private List transactions;

   @PrePersist
   public void prePersist() {
      this.dateOuverture = LocalDateTime.now();
      this.numeroCompte = this.genererNumeroCompte();
   }

   private String genererNumeroCompte() {
      String var10000 = UUID.randomUUID().toString().replace("-", "");
      return "MF" + var10000.substring(0, 14).toUpperCase();
   }

   public void bloquer() {
      this.statut = StatutCompte.BLOQUE;
   }

   public void debloquer() {
      this.statut = StatutCompte.ACTIF;
   }

   public void suspendre() {
      this.statut = StatutCompte.SUSPENDU;
   }

   public void fermer() {
      this.statut = StatutCompte.FERME;
      this.dateFermeture = LocalDateTime.now();
   }

   public void activer() {
      this.statut = StatutCompte.ACTIF;
   }

   public boolean isActif() {
      return StatutCompte.ACTIF.equals(this.statut);
   }

   @Generated
   public static CompteBuilder builder() {
      return new CompteBuilder();
   }

   @Generated
   public Long getIdCompte() {
      return this.idCompte;
   }

   @Generated
   public String getNumeroCompte() {
      return this.numeroCompte;
   }

   @Generated
   public BigDecimal getSolde() {
      return this.solde;
   }

   @Generated
   public StatutCompte getStatut() {
      return this.statut;
   }

   @Generated
   public LocalDateTime getDateOuverture() {
      return this.dateOuverture;
   }

   @Generated
   public LocalDateTime getDateFermeture() {
      return this.dateFermeture;
   }

   @Generated
   public BigDecimal getPlafondRetrait() {
      return this.plafondRetrait;
   }

   @Generated
   public BigDecimal getMontantMinSolde() {
      return this.montantMinSolde;
   }

   @Generated
   public BigDecimal getTauxAgios() {
      return this.tauxAgios;
   }

   @Generated
   public Client getClient() {
      return this.client;
   }

   @Generated
   public List getTransactions() {
      return this.transactions;
   }

   @Generated
   public void setIdCompte(final Long idCompte) {
      this.idCompte = idCompte;
   }

   @Generated
   public void setNumeroCompte(final String numeroCompte) {
      this.numeroCompte = numeroCompte;
   }

   @Generated
   public void setSolde(final BigDecimal solde) {
      this.solde = solde;
   }

   @Generated
   public void setStatut(final StatutCompte statut) {
      this.statut = statut;
   }

   @Generated
   public void setDateOuverture(final LocalDateTime dateOuverture) {
      this.dateOuverture = dateOuverture;
   }

   @Generated
   public void setDateFermeture(final LocalDateTime dateFermeture) {
      this.dateFermeture = dateFermeture;
   }

   @Generated
   public void setPlafondRetrait(final BigDecimal plafondRetrait) {
      this.plafondRetrait = plafondRetrait;
   }

   @Generated
   public void setMontantMinSolde(final BigDecimal montantMinSolde) {
      this.montantMinSolde = montantMinSolde;
   }

   @Generated
   public void setTauxAgios(final BigDecimal tauxAgios) {
      this.tauxAgios = tauxAgios;
   }

   @Generated
   public void setClient(final Client client) {
      this.client = client;
   }

   @Generated
   public void setTransactions(final List transactions) {
      this.transactions = transactions;
   }

   @Generated
   public Compte() {
      this.solde = BigDecimal.ZERO;
      this.statut = StatutCompte.EN_ATTENTE;
      this.plafondRetrait = new BigDecimal("500000");
      this.montantMinSolde = new BigDecimal("5000");
      this.tauxAgios = new BigDecimal("0.0050");
      this.transactions = new ArrayList();
   }

   @Generated
   public Compte(final Long idCompte, final String numeroCompte, final BigDecimal solde, final StatutCompte statut, final LocalDateTime dateOuverture, final LocalDateTime dateFermeture, final BigDecimal plafondRetrait, final BigDecimal montantMinSolde, final BigDecimal tauxAgios, final Client client, final List transactions) {
      this.solde = BigDecimal.ZERO;
      this.statut = StatutCompte.EN_ATTENTE;
      this.plafondRetrait = new BigDecimal("500000");
      this.montantMinSolde = new BigDecimal("5000");
      this.tauxAgios = new BigDecimal("0.0050");
      this.transactions = new ArrayList();
      this.idCompte = idCompte;
      this.numeroCompte = numeroCompte;
      this.solde = solde;
      this.statut = statut;
      this.dateOuverture = dateOuverture;
      this.dateFermeture = dateFermeture;
      this.plafondRetrait = plafondRetrait;
      this.montantMinSolde = montantMinSolde;
      this.tauxAgios = tauxAgios;
      this.client = client;
      this.transactions = transactions;
   }

   @Generated
   public static class CompteBuilder {
      @Generated
      private Long idCompte;
      @Generated
      private String numeroCompte;
      @Generated
      private BigDecimal solde;
      @Generated
      private StatutCompte statut;
      @Generated
      private LocalDateTime dateOuverture;
      @Generated
      private LocalDateTime dateFermeture;
      @Generated
      private BigDecimal plafondRetrait;
      @Generated
      private BigDecimal montantMinSolde;
      @Generated
      private BigDecimal tauxAgios;
      @Generated
      private Client client;
      @Generated
      private List transactions;

      @Generated
      CompteBuilder() {
      }

      @Generated
      public CompteBuilder idCompte(final Long idCompte) {
         this.idCompte = idCompte;
         return this;
      }

      @Generated
      public CompteBuilder numeroCompte(final String numeroCompte) {
         this.numeroCompte = numeroCompte;
         return this;
      }

      @Generated
      public CompteBuilder solde(final BigDecimal solde) {
         this.solde = solde;
         return this;
      }

      @Generated
      public CompteBuilder statut(final StatutCompte statut) {
         this.statut = statut;
         return this;
      }

      @Generated
      public CompteBuilder dateOuverture(final LocalDateTime dateOuverture) {
         this.dateOuverture = dateOuverture;
         return this;
      }

      @Generated
      public CompteBuilder dateFermeture(final LocalDateTime dateFermeture) {
         this.dateFermeture = dateFermeture;
         return this;
      }

      @Generated
      public CompteBuilder plafondRetrait(final BigDecimal plafondRetrait) {
         this.plafondRetrait = plafondRetrait;
         return this;
      }

      @Generated
      public CompteBuilder montantMinSolde(final BigDecimal montantMinSolde) {
         this.montantMinSolde = montantMinSolde;
         return this;
      }

      @Generated
      public CompteBuilder tauxAgios(final BigDecimal tauxAgios) {
         this.tauxAgios = tauxAgios;
         return this;
      }

      @JsonIgnore
      @Generated
      public CompteBuilder client(final Client client) {
         this.client = client;
         return this;
      }

      @JsonIgnore
      @Generated
      public CompteBuilder transactions(final List transactions) {
         this.transactions = transactions;
         return this;
      }

      @Generated
      public Compte build() {
         return new Compte(this.idCompte, this.numeroCompte, this.solde, this.statut, this.dateOuverture, this.dateFermeture, this.plafondRetrait, this.montantMinSolde, this.tauxAgios, this.client, this.transactions);
      }

      @Generated
      public String toString() {
         Long var10000 = this.idCompte;
         return "Compte.CompteBuilder(idCompte=" + var10000 + ", numeroCompte=" + this.numeroCompte + ", solde=" + String.valueOf(this.solde) + ", statut=" + String.valueOf(this.statut) + ", dateOuverture=" + String.valueOf(this.dateOuverture) + ", dateFermeture=" + String.valueOf(this.dateFermeture) + ", plafondRetrait=" + String.valueOf(this.plafondRetrait) + ", montantMinSolde=" + String.valueOf(this.montantMinSolde) + ", tauxAgios=" + String.valueOf(this.tauxAgios) + ", client=" + String.valueOf(this.client) + ", transactions=" + String.valueOf(this.transactions) + ")";
      }
   }
}
