package com.microfinance.model;

import jakarta.persistence.Column;
import jakarta.persistence.Entity;
import jakarta.persistence.GeneratedValue;
import jakarta.persistence.GenerationType;
import jakarta.persistence.Id;
import jakarta.persistence.SequenceGenerator;
import jakarta.persistence.Table;
import java.math.BigDecimal;
import lombok.Generated;

@Entity
@Table(
   name = "OFFRES_ABONNEMENT"
)
public class OffreAbonnement {
   @Id
   @GeneratedValue(
      strategy = GenerationType.SEQUENCE,
      generator = "offre_seq"
   )
   @SequenceGenerator(
      name = "offre_seq",
      sequenceName = "OFFRE_SEQ",
      allocationSize = 1
   )
   @Column(
      name = "ID_OFFRE"
   )
   private Long idOffre;
   @Column(
      name = "NOM_OFFRE",
      nullable = false,
      unique = true,
      length = 50
   )
   private String nomOffre;
   @Column(
      name = "PRIX_MENSUEL",
      nullable = false,
      precision = 15,
      scale = 2
   )
   private BigDecimal prixMensuel;
   @Column(
      name = "POURCENTAGE_FRAIS_MOMO",
      nullable = false,
      precision = 5,
      scale = 2
   )
   private BigDecimal pourcentageFraisMomo;
   @Column(
      name = "FRAIS_VIREMENT_INTERNE",
      nullable = false,
      precision = 5,
      scale = 2
   )
   private BigDecimal fraisVirementInterne;
   @Column(
      name = "OPTION_RIB_DISPO",
      nullable = false
   )
   private boolean optionRibDispo;

   @Generated
   public static OffreAbonnementBuilder builder() {
      return new OffreAbonnementBuilder();
   }

   @Generated
   public Long getIdOffre() {
      return this.idOffre;
   }

   @Generated
   public String getNomOffre() {
      return this.nomOffre;
   }

   @Generated
   public BigDecimal getPrixMensuel() {
      return this.prixMensuel;
   }

   @Generated
   public BigDecimal getPourcentageFraisMomo() {
      return this.pourcentageFraisMomo;
   }

   @Generated
   public BigDecimal getFraisVirementInterne() {
      return this.fraisVirementInterne;
   }

   @Generated
   public boolean isOptionRibDispo() {
      return this.optionRibDispo;
   }

   @Generated
   public void setIdOffre(final Long idOffre) {
      this.idOffre = idOffre;
   }

   @Generated
   public void setNomOffre(final String nomOffre) {
      this.nomOffre = nomOffre;
   }

   @Generated
   public void setPrixMensuel(final BigDecimal prixMensuel) {
      this.prixMensuel = prixMensuel;
   }

   @Generated
   public void setPourcentageFraisMomo(final BigDecimal pourcentageFraisMomo) {
      this.pourcentageFraisMomo = pourcentageFraisMomo;
   }

   @Generated
   public void setFraisVirementInterne(final BigDecimal fraisVirementInterne) {
      this.fraisVirementInterne = fraisVirementInterne;
   }

   @Generated
   public void setOptionRibDispo(final boolean optionRibDispo) {
      this.optionRibDispo = optionRibDispo;
   }

   @Generated
   public OffreAbonnement() {
   }

   @Generated
   public OffreAbonnement(final Long idOffre, final String nomOffre, final BigDecimal prixMensuel, final BigDecimal pourcentageFraisMomo, final BigDecimal fraisVirementInterne, final boolean optionRibDispo) {
      this.idOffre = idOffre;
      this.nomOffre = nomOffre;
      this.prixMensuel = prixMensuel;
      this.pourcentageFraisMomo = pourcentageFraisMomo;
      this.fraisVirementInterne = fraisVirementInterne;
      this.optionRibDispo = optionRibDispo;
   }

   @Generated
   public static class OffreAbonnementBuilder {
      @Generated
      private Long idOffre;
      @Generated
      private String nomOffre;
      @Generated
      private BigDecimal prixMensuel;
      @Generated
      private BigDecimal pourcentageFraisMomo;
      @Generated
      private BigDecimal fraisVirementInterne;
      @Generated
      private boolean optionRibDispo;

      @Generated
      OffreAbonnementBuilder() {
      }

      @Generated
      public OffreAbonnementBuilder idOffre(final Long idOffre) {
         this.idOffre = idOffre;
         return this;
      }

      @Generated
      public OffreAbonnementBuilder nomOffre(final String nomOffre) {
         this.nomOffre = nomOffre;
         return this;
      }

      @Generated
      public OffreAbonnementBuilder prixMensuel(final BigDecimal prixMensuel) {
         this.prixMensuel = prixMensuel;
         return this;
      }

      @Generated
      public OffreAbonnementBuilder pourcentageFraisMomo(final BigDecimal pourcentageFraisMomo) {
         this.pourcentageFraisMomo = pourcentageFraisMomo;
         return this;
      }

      @Generated
      public OffreAbonnementBuilder fraisVirementInterne(final BigDecimal fraisVirementInterne) {
         this.fraisVirementInterne = fraisVirementInterne;
         return this;
      }

      @Generated
      public OffreAbonnementBuilder optionRibDispo(final boolean optionRibDispo) {
         this.optionRibDispo = optionRibDispo;
         return this;
      }

      @Generated
      public OffreAbonnement build() {
         return new OffreAbonnement(this.idOffre, this.nomOffre, this.prixMensuel, this.pourcentageFraisMomo, this.fraisVirementInterne, this.optionRibDispo);
      }

      @Generated
      public String toString() {
         Long var10000 = this.idOffre;
         return "OffreAbonnement.OffreAbonnementBuilder(idOffre=" + var10000 + ", nomOffre=" + this.nomOffre + ", prixMensuel=" + String.valueOf(this.prixMensuel) + ", pourcentageFraisMomo=" + String.valueOf(this.pourcentageFraisMomo) + ", fraisVirementInterne=" + String.valueOf(this.fraisVirementInterne) + ", optionRibDispo=" + this.optionRibDispo + ")";
      }
   }
}
