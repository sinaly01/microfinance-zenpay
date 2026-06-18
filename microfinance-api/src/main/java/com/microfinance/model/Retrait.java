package com.microfinance.model;

import com.microfinance.exception.BusinessException;
import com.microfinance.model.enums.StatutTransaction;
import jakarta.persistence.Column;
import jakarta.persistence.DiscriminatorValue;
import jakarta.persistence.Entity;
import java.math.BigDecimal;
import lombok.Generated;

@Entity
@DiscriminatorValue("RETRAIT")
public class Retrait extends Transaction {
   @Column(
      name = "CANAL_RETRAIT",
      length = 50
   )
   private String canal;

   public void executer() {
      BigDecimal minSolde = this.getCompte().getMontantMinSolde() != null ? this.getCompte().getMontantMinSolde() : BigDecimal.ZERO;
      BigDecimal plafond = this.getCompte().getPlafondRetrait() != null ? this.getCompte().getPlafondRetrait() : new BigDecimal("500000");
      BigDecimal soldeApres = this.getCompte().getSolde().subtract(this.getMontant());
      if (soldeApres.compareTo(minSolde) < 0) {
         this.setStatut(StatutTransaction.REJETEE);
         throw new BusinessException("Solde insuffisant après retrait. Solde minimum requis : " + String.valueOf(minSolde) + " FCFA");
      } else if (this.getMontant().compareTo(plafond) > 0) {
         this.setStatut(StatutTransaction.REJETEE);
         throw new BusinessException("Montant dépasse le plafond autorisé : " + String.valueOf(plafond) + " FCFA");
      } else {
         this.getCompte().setSolde(soldeApres);
         this.setStatut(StatutTransaction.VALIDEE);
      }
   }

   @Generated
   public String getCanal() {
      return this.canal;
   }

   @Generated
   public void setCanal(final String canal) {
      this.canal = canal;
   }
}
