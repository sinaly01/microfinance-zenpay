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
import java.math.BigDecimal;
import java.time.LocalDateTime;
import lombok.Generated;

@Entity
@Table(
   name = "FACTURES_ABONNEMENT"
)
public class FactureAbonnement {
   @Id
   @GeneratedValue(
      strategy = GenerationType.SEQUENCE,
      generator = "facture_seq"
   )
   @SequenceGenerator(
      name = "facture_seq",
      sequenceName = "FACTURE_SEQ",
      allocationSize = 1
   )
   @Column(
      name = "ID_FACTURE"
   )
   private Long idFacture;
   @ManyToOne(
      fetch = FetchType.LAZY
   )
   @JoinColumn(
      name = "ID_CLIENT",
      nullable = false
   )
   private Client client;
   @ManyToOne(
      fetch = FetchType.LAZY
   )
   @JoinColumn(
      name = "ID_OFFRE",
      nullable = false
   )
   private OffreAbonnement offre;
   @Column(
      name = "MONTANT_PRELEVE",
      nullable = false,
      precision = 15,
      scale = 2
   )
   private BigDecimal montantPreleve;
   @Column(
      name = "STATUT_PAIEMENT",
      nullable = false,
      length = 30
   )
   private String statutPaiement;
   @Column(
      name = "DATE_PRELEVEMENT",
      nullable = false
   )
   private LocalDateTime datePrelevement;

   @PrePersist
   public void prePersist() {
      this.datePrelevement = LocalDateTime.now();
   }

   @Generated
   public static FactureAbonnementBuilder builder() {
      return new FactureAbonnementBuilder();
   }

   @Generated
   public Long getIdFacture() {
      return this.idFacture;
   }

   @Generated
   public Client getClient() {
      return this.client;
   }

   @Generated
   public OffreAbonnement getOffre() {
      return this.offre;
   }

   @Generated
   public BigDecimal getMontantPreleve() {
      return this.montantPreleve;
   }

   @Generated
   public String getStatutPaiement() {
      return this.statutPaiement;
   }

   @Generated
   public LocalDateTime getDatePrelevement() {
      return this.datePrelevement;
   }

   @Generated
   public void setIdFacture(final Long idFacture) {
      this.idFacture = idFacture;
   }

   @Generated
   public void setClient(final Client client) {
      this.client = client;
   }

   @Generated
   public void setOffre(final OffreAbonnement offre) {
      this.offre = offre;
   }

   @Generated
   public void setMontantPreleve(final BigDecimal montantPreleve) {
      this.montantPreleve = montantPreleve;
   }

   @Generated
   public void setStatutPaiement(final String statutPaiement) {
      this.statutPaiement = statutPaiement;
   }

   @Generated
   public void setDatePrelevement(final LocalDateTime datePrelevement) {
      this.datePrelevement = datePrelevement;
   }

   @Generated
   public FactureAbonnement() {
   }

   @Generated
   public FactureAbonnement(final Long idFacture, final Client client, final OffreAbonnement offre, final BigDecimal montantPreleve, final String statutPaiement, final LocalDateTime datePrelevement) {
      this.idFacture = idFacture;
      this.client = client;
      this.offre = offre;
      this.montantPreleve = montantPreleve;
      this.statutPaiement = statutPaiement;
      this.datePrelevement = datePrelevement;
   }

   @Generated
   public static class FactureAbonnementBuilder {
      @Generated
      private Long idFacture;
      @Generated
      private Client client;
      @Generated
      private OffreAbonnement offre;
      @Generated
      private BigDecimal montantPreleve;
      @Generated
      private String statutPaiement;
      @Generated
      private LocalDateTime datePrelevement;

      @Generated
      FactureAbonnementBuilder() {
      }

      @Generated
      public FactureAbonnementBuilder idFacture(final Long idFacture) {
         this.idFacture = idFacture;
         return this;
      }

      @Generated
      public FactureAbonnementBuilder client(final Client client) {
         this.client = client;
         return this;
      }

      @Generated
      public FactureAbonnementBuilder offre(final OffreAbonnement offre) {
         this.offre = offre;
         return this;
      }

      @Generated
      public FactureAbonnementBuilder montantPreleve(final BigDecimal montantPreleve) {
         this.montantPreleve = montantPreleve;
         return this;
      }

      @Generated
      public FactureAbonnementBuilder statutPaiement(final String statutPaiement) {
         this.statutPaiement = statutPaiement;
         return this;
      }

      @Generated
      public FactureAbonnementBuilder datePrelevement(final LocalDateTime datePrelevement) {
         this.datePrelevement = datePrelevement;
         return this;
      }

      @Generated
      public FactureAbonnement build() {
         return new FactureAbonnement(this.idFacture, this.client, this.offre, this.montantPreleve, this.statutPaiement, this.datePrelevement);
      }

      @Generated
      public String toString() {
         Long var10000 = this.idFacture;
         return "FactureAbonnement.FactureAbonnementBuilder(idFacture=" + var10000 + ", client=" + String.valueOf(this.client) + ", offre=" + String.valueOf(this.offre) + ", montantPreleve=" + String.valueOf(this.montantPreleve) + ", statutPaiement=" + this.statutPaiement + ", datePrelevement=" + String.valueOf(this.datePrelevement) + ")";
      }
   }
}
