package com.microfinance.dto.response;

public record AuthResponse(String token, String type, String email, String role, long expiresIn) {
   public static AuthResponse of(String token, String email, String role, long expiresIn) {
      return new AuthResponse(token, "Bearer", email, role, expiresIn);
   }
}
