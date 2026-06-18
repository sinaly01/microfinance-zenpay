package com.microfinance.model.enums;

public enum StatutCompte {
   EN_ATTENTE,
   ACTIF,
   SUSPENDU,
   BLOQUE,
   FERME;

   // $FF: synthetic method
   private static StatutCompte[] $values() {
      return new StatutCompte[]{EN_ATTENTE, ACTIF, SUSPENDU, BLOQUE, FERME};
   }
}
