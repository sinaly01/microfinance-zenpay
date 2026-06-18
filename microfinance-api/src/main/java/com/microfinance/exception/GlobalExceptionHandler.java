package com.microfinance.exception;

import java.time.LocalDateTime;
import java.util.LinkedHashMap;
import java.util.Map;
import org.springframework.http.HttpStatus;
import org.springframework.http.ResponseEntity;
import org.springframework.security.access.AccessDeniedException;
import org.springframework.security.authentication.BadCredentialsException;
import org.springframework.validation.FieldError;
import org.springframework.web.bind.MethodArgumentNotValidException;
import org.springframework.web.bind.annotation.ExceptionHandler;
import org.springframework.web.bind.annotation.RestControllerAdvice;

@RestControllerAdvice
public class GlobalExceptionHandler {
   @ExceptionHandler({ResourceNotFoundException.class})
   public ResponseEntity handleNotFound(ResourceNotFoundException ex) {
      return this.build(HttpStatus.NOT_FOUND, "Ressource introuvable", ex.getMessage(), (Map)null);
   }

   @ExceptionHandler({BusinessException.class})
   public ResponseEntity handleBusiness(BusinessException ex) {
      return this.build(HttpStatus.UNPROCESSABLE_ENTITY, "Erreur métier", ex.getMessage(), (Map)null);
   }

   @ExceptionHandler({BadCredentialsException.class})
   public ResponseEntity handleBadCredentials(BadCredentialsException ex) {
      return this.build(HttpStatus.UNAUTHORIZED, "Authentification échouée", "Email ou mot de passe incorrect", (Map)null);
   }

   @ExceptionHandler({AccessDeniedException.class})
   public ResponseEntity handleAccessDenied(AccessDeniedException ex) {
      return this.build(HttpStatus.FORBIDDEN, "Accès refusé", "Vous n'avez pas les droits nécessaires", (Map)null);
   }

   @ExceptionHandler({MethodArgumentNotValidException.class})
   public ResponseEntity handleValidation(MethodArgumentNotValidException ex) {
      Map<String, String> details = new LinkedHashMap();

      for(FieldError fe : ex.getBindingResult().getFieldErrors()) {
         details.put(fe.getField(), fe.getDefaultMessage());
      }

      return this.build(HttpStatus.BAD_REQUEST, "Données invalides", "Veuillez corriger les champs", details);
   }

   @ExceptionHandler({Exception.class})
   public ResponseEntity handleGeneric(Exception ex) {
      return this.build(HttpStatus.INTERNAL_SERVER_ERROR, "Erreur interne", ex.getMessage(), (Map)null);
   }

   private ResponseEntity build(HttpStatus status, String error, String message, Map details) {
      return ResponseEntity.status(status).body(new ApiError(LocalDateTime.now(), status.value(), error, message, details));
   }

   static record ApiError(LocalDateTime timestamp, int status, String error, String message, Map details) {
   }
}
