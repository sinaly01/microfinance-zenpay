package com.microfinance.dto.response;

import com.microfinance.model.Transaction;
import com.microfinance.model.Virement;
import com.microfinance.model.enums.StatutTransaction;
import java.math.BigDecimal;
import java.time.LocalDateTime;

public record TransactionResponse(Long idTransaction, String reference, String typeTransaction, BigDecimal montant, StatutTransaction statut, LocalDateTime dateHeure, String description, String numeroCompteSource, String numeroCompteDestination) {
   public static TransactionResponse from(Transaction t) {
      String type = t.getClass().getSimpleName().toUpperCase();
      String compteDest = null;
      if (t instanceof Virement v) {
         if (v.getCompteDestination() != null) {
            compteDest = v.getCompteDestination().getNumeroCompte();
         }
      }

      return new TransactionResponse(t.getIdTransaction(), t.getReference(), type, t.getMontant(), t.getStatut(), t.getDateHeure(), t.getDescription(), t.getCompte() != null ? t.getCompte().getNumeroCompte() : null, compteDest);
   }
}
