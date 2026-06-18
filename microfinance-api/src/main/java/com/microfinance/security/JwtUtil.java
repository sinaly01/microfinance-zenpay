package com.microfinance.security;

import io.jsonwebtoken.Claims;
import io.jsonwebtoken.Jwts;
import io.jsonwebtoken.SignatureAlgorithm;
import io.jsonwebtoken.security.Keys;
import java.security.Key;
import java.util.Date;
import java.util.UUID;
import org.springframework.beans.factory.annotation.Value;
import org.springframework.security.core.userdetails.UserDetails;
import org.springframework.stereotype.Component;

@Component
public class JwtUtil {
   @Value("${jwt.secret}")
   private String secret;
   @Value("${jwt.expiration}")
   private long expiration;

   public String generateToken(UserDetails userDetails) {
      return Jwts.builder().setId(UUID.randomUUID().toString()).setSubject(userDetails.getUsername()).claim("roles", userDetails.getAuthorities().stream().map((a) -> a.getAuthority()).toList()).setIssuedAt(new Date()).setExpiration(new Date(System.currentTimeMillis() + this.expiration)).signWith(this.getKey(), SignatureAlgorithm.HS256).compact();
   }

   public String extractJti(String token) {
      return this.getClaims(token).getId();
   }

   public String extractUsername(String token) {
      return this.getClaims(token).getSubject();
   }

   public boolean validateToken(String token, UserDetails userDetails) {
      String username = this.extractUsername(token);
      return username.equals(userDetails.getUsername()) && !this.isTokenExpired(token);
   }

   public Date extractExpiration(String token) {
      return this.getClaims(token).getExpiration();
   }

   private boolean isTokenExpired(String token) {
      return this.getClaims(token).getExpiration().before(new Date());
   }

   private Claims getClaims(String token) {
      return (Claims)Jwts.parserBuilder().setSigningKey(this.getKey()).build().parseClaimsJws(token).getBody();
   }

   private Key getKey() {
      return Keys.hmacShaKeyFor(this.secret.getBytes());
   }
}
