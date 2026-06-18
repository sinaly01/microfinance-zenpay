package com.microfinance.model;

import com.microfinance.model.enums.StatutTransaction;
import jakarta.persistence.Column;
import jakarta.persistence.DiscriminatorValue;
import jakarta.persistence.Entity;
import lombok.Generated;

@Entity
@DiscriminatorValue("VERSEMENT")
public class Versement extends Transaction {
   @Column(
      name = "SOURCE_VERSEMENT",
      length = 100
   )
   private String source;

   public void executer() {
      this.getCompte().setSolde(this.getCompte().getSolde().add(this.getMontant()));
      this.setStatut(StatutTransaction.VALIDEE);
   }

   @Generated
   public String getSource() {
      return this.source;
   }

   @Generated
   public void setSource(final String source) {
      this.source = source;
   }
}
