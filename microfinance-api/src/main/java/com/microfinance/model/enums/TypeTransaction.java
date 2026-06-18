package com.microfinance.model.enums;

public enum TypeTransaction {
   VERSEMENT,
   RETRAIT,
   VIREMENT;

   // $FF: synthetic method
   private static TypeTransaction[] $values() {
      return new TypeTransaction[]{VERSEMENT, RETRAIT, VIREMENT};
   }
}
