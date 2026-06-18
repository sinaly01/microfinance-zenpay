package com.microfinance.dto.response;

import com.microfinance.model.Compte;
import com.microfinance.model.enums.StatutCompte;
import java.math.BigDecimal;
import java.time.LocalDateTime;

public record CompteResponse(Long idCompte, String numeroCompte, BigDecimal solde, StatutCompte statut, LocalDateTime dateOuverture, BigDecimal plafondRetrait, BigDecimal montantMinSolde, BigDecimal tauxAgios, String nomClient) {
   public static CompteResponse from(Compte c) {
      String nomClient = c.getClient() != null ? c.getClient().getNom() + " " + c.getClient().getPrenom() : "—";
      return new CompteResponse(c.getIdCompte(), c.getNumeroCompte(), c.getSolde(), c.getStatut(), c.getDateOuverture(), c.getPlafondRetrait(), c.getMontantMinSolde(), c.getTauxAgios(), nomClient);
   }
}
