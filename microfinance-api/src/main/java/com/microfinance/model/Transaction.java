package com.microfinance.model;

import com.fasterxml.jackson.annotation.JsonIgnore;
import com.microfinance.model.enums.StatutTransaction;
import jakarta.persistence.Column;
import jakarta.persistence.DiscriminatorColumn;
import jakarta.persistence.DiscriminatorType;
import jakarta.persistence.Entity;
import jakarta.persistence.EnumType;
import jakarta.persistence.Enumerated;
import jakarta.persistence.FetchType;
import jakarta.persistence.GeneratedValue;
import jakarta.persistence.GenerationType;
import jakarta.persistence.Id;
import jakarta.persistence.Inheritance;
import jakarta.persistence.InheritanceType;
import jakarta.persistence.JoinColumn;
import jakarta.persistence.ManyToOne;
import jakarta.persistence.PrePersist;
import jakarta.persistence.SequenceGenerator;
import jakarta.persistence.Table;
import java.math.BigDecimal;
import java.time.LocalDateTime;
import java.util.UUID;
import lombok.Generated;

@Entity
@Table(
   name = "TRANSACTIONS"
)
@Inheritance(
   strategy = InheritanceType.SINGLE_TABLE
)
@DiscriminatorColumn(
   name = "TYPE_TRANSACTION",
   discriminatorType = DiscriminatorType.STRING
)
public abstract class Transaction {
   @Id
   @GeneratedValue(
      strategy = GenerationType.SEQUENCE,
      generator = "transaction_seq"
   )
   @SequenceGenerator(
      name = "transaction_seq",
      sequenceName = "TRANSACTION_SEQ",
      allocationSize = 1
   )
   @Column(
      name = "ID_TRANSACTION"
   )
   private Long idTransaction;
   @Column(
      name = "MONTANT",
      nullable = false,
      precision = 15,
      scale = 2
   )
   private BigDecimal montant;
   @Column(
      name = "DATE_HEURE",
      nullable = false
   )
   private LocalDateTime dateHeure;
   @Enumerated(EnumType.STRING)
   @Column(
      name = "STATUT",
      nullable = false,
      length = 20
   )
   private StatutTransaction statut;
   @Column(
      name = "REFERENCE",
      unique = true,
      length = 30
   )
   private String reference;
   @Column(
      name = "DESCRIPTION",
      length = 255
   )
   private String description;
   @JsonIgnore
   @ManyToOne(
      fetch = FetchType.LAZY
   )
   @JoinColumn(
      name = "ID_COMPTE",
      nullable = false
   )
   private Compte compte;

   @PrePersist
   public void prePersist() {
      if (this.dateHeure == null) {
         this.dateHeure = LocalDateTime.now();
      }

      if (this.reference == null) {
         String var10001 = UUID.randomUUID().toString().replace("-", "");
         this.reference = "TXN" + var10001.substring(0, 12).toUpperCase();
      }

   }

   public abstract void executer();

   @Generated
   public Long getIdTransaction() {
      return this.idTransaction;
   }

   @Generated
   public BigDecimal getMontant() {
      return this.montant;
   }

   @Generated
   public LocalDateTime getDateHeure() {
      return this.dateHeure;
   }

   @Generated
   public StatutTransaction getStatut() {
      return this.statut;
   }

   @Generated
   public String getReference() {
      return this.reference;
   }

   @Generated
   public String getDescription() {
      return this.description;
   }

   @Generated
   public Compte getCompte() {
      return this.compte;
   }

   @Generated
   public void setIdTransaction(final Long idTransaction) {
      this.idTransaction = idTransaction;
   }

   @Generated
   public void setMontant(final BigDecimal montant) {
      this.montant = montant;
   }

   @Generated
   public void setDateHeure(final LocalDateTime dateHeure) {
      this.dateHeure = dateHeure;
   }

   @Generated
   public void setStatut(final StatutTransaction statut) {
      this.statut = statut;
   }

   @Generated
   public void setReference(final String reference) {
      this.reference = reference;
   }

   @Generated
   public void setDescription(final String description) {
      this.description = description;
   }

   @Generated
   public void setCompte(final Compte compte) {
      this.compte = compte;
   }

   @Generated
   public Transaction() {
      this.statut = StatutTransaction.EN_COURS;
   }

   @Generated
   public Transaction(final Long idTransaction, final BigDecimal montant, final LocalDateTime dateHeure, final StatutTransaction statut, final String reference, final String description, final Compte compte) {
      this.statut = StatutTransaction.EN_COURS;
      this.idTransaction = idTransaction;
      this.montant = montant;
      this.dateHeure = dateHeure;
      this.statut = statut;
      this.reference = reference;
      this.description = description;
      this.compte = compte;
   }
}
