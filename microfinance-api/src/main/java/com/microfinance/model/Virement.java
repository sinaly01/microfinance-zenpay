package com.microfinance.model;

import com.microfinance.exception.BusinessException;
import com.microfinance.model.enums.StatutTransaction;
import jakarta.persistence.DiscriminatorValue;
import jakarta.persistence.Entity;
import jakarta.persistence.FetchType;
import jakarta.persistence.JoinColumn;
import jakarta.persistence.ManyToOne;
import java.math.BigDecimal;
import lombok.Generated;

@Entity
@DiscriminatorValue("VIREMENT")
public class Virement extends Transaction {
   @ManyToOne(
      fetch = FetchType.LAZY
   )
   @JoinColumn(
      name = "ID_COMPTE_DEST"
   )
   private Compte compteDestination;

   public void executer() {
      if (this.compteDestination == null) {
         throw new BusinessException("Compte destinataire introuvable");
      } else if (!this.compteDestination.isActif()) {
         throw new BusinessException("Le compte destinataire n'est pas actif");
      } else {
         BigDecimal minSolde = this.getCompte().getMontantMinSolde() != null ? this.getCompte().getMontantMinSolde() : BigDecimal.ZERO;
         if (this.getCompte().getSolde().subtract(this.getMontant()).compareTo(minSolde) < 0) {
            this.setStatut(StatutTransaction.REJETEE);
            throw new BusinessException("Solde insuffisant pour effectuer le virement");
         } else {
            this.getCompte().setSolde(this.getCompte().getSolde().subtract(this.getMontant()));
            this.compteDestination.setSolde(this.compteDestination.getSolde().add(this.getMontant()));
            this.setStatut(StatutTransaction.VALIDEE);
         }
      }
   }

   @Generated
   public Compte getCompteDestination() {
      return this.compteDestination;
   }

   @Generated
   public void setCompteDestination(final Compte compteDestination) {
      this.compteDestination = compteDestination;
   }
}
