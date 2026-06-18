package com.microfinance.dto.request;

import jakarta.validation.constraints.DecimalMin;
import jakarta.validation.constraints.NotNull;
import java.math.BigDecimal;

public record TransactionRequest(@NotNull Long idCompte, @NotNull @DecimalMin(
   value = "500",
   message = "Le montant minimum est 500 FCFA"
) BigDecimal montant, Long idCompteDestination, String canal, String description) {
}
