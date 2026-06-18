package com.microfinance.exception;

public class ResourceNotFoundException extends RuntimeException {
   public ResourceNotFoundException(String resource, Object id) {
      super(resource + " introuvable avec l'identifiant : " + String.valueOf(id));
   }
}
