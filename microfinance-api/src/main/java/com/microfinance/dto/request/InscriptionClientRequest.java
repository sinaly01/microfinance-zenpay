package com.microfinance.dto.request;

import jakarta.validation.constraints.Email;
import jakarta.validation.constraints.NotBlank;
import jakarta.validation.constraints.Pattern;
import jakarta.validation.constraints.Size;

public record InscriptionClientRequest(@NotBlank String nom, @NotBlank String prenom, @NotBlank @Pattern(
   regexp = "^\\+?[0-9]{8,15}$",
   message = "Numéro de téléphone invalide"
) String telephone, @Email String email, @NotBlank String adresse, String numeroCni, @NotBlank @Size(
   min = 8,
   message = "Le mot de passe doit contenir au moins 8 caractères"
) String motDePasse, String operateurMomo) {
}
