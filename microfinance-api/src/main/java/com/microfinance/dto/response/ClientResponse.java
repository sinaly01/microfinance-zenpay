package com.microfinance.dto.response;

import com.microfinance.model.Client;
import com.microfinance.model.OffreAbonnement;
import com.microfinance.model.enums.StatutKyc;
import java.time.LocalDate;

public record ClientResponse(Long idClient, String nom, String prenom, String email, String telephone, String adresse, String numeroCni, String operateurMomo, boolean actif, StatutKyc statutKyc, LocalDate dateInscription, OffreAbonnement offreAbonnement) {
   public static ClientResponse from(Client c) {
      return new ClientResponse(c.getIdClient(), c.getNom(), c.getPrenom(), c.getEmail(), c.getTelephone(), c.getAdresse(), c.getNumeroCni(), c.getOperateurMomo(), c.isActif(), c.getStatutKyc(), c.getDateInscription(), c.getOffreAbonnement());
   }
}
