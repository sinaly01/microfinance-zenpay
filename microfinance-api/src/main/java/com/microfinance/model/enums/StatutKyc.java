package com.microfinance.model.enums;

public enum StatutKyc {
   PENDING,
   DOCUMENTS_SOUMIS,
   VALIDE,
   REJETE;

   // $FF: synthetic method
   private static StatutKyc[] $values() {
      return new StatutKyc[]{PENDING, DOCUMENTS_SOUMIS, VALIDE, REJETE};
   }
}
