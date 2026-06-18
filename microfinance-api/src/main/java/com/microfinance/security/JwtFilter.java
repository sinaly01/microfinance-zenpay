package com.microfinance.security;

import com.microfinance.repository.BlackListJetonRepository;
import com.microfinance.service.SystemService;
import jakarta.servlet.FilterChain;
import jakarta.servlet.ServletException;
import jakarta.servlet.http.HttpServletRequest;
import jakarta.servlet.http.HttpServletResponse;
import java.io.IOException;
import lombok.Generated;
import org.springframework.security.authentication.UsernamePasswordAuthenticationToken;
import org.springframework.security.core.context.SecurityContextHolder;
import org.springframework.security.core.userdetails.UserDetails;
import org.springframework.security.core.userdetails.UserDetailsService;
import org.springframework.security.web.authentication.WebAuthenticationDetailsSource;
import org.springframework.stereotype.Component;
import org.springframework.web.filter.OncePerRequestFilter;

@Component
public class JwtFilter extends OncePerRequestFilter {
   private final JwtUtil jwtUtil;
   private final UserDetailsService userDetailsService;
   private final BlackListJetonRepository blackListJetonRepository;
   private final SystemService systemService;

   protected void doFilterInternal(HttpServletRequest request, HttpServletResponse response, FilterChain chain) throws ServletException, IOException {
      String authHeader = request.getHeader("Authorization");
      if (authHeader != null && authHeader.startsWith("Bearer ")) {
         String token = authHeader.substring(7);

         try {
            if (this.blackListJetonRepository.existsByValeurJeton(token)) {
               response.setStatus(401);
               response.setContentType("application/json;charset=UTF-8");
               response.getWriter().write("{\"error\":\"Session révoquée. Veuillez vous reconnecter.\"}");
               return;
            }

            String username = this.jwtUtil.extractUsername(token);
            if (username != null && SecurityContextHolder.getContext().getAuthentication() == null) {
               UserDetails userDetails = this.userDetailsService.loadUserByUsername(username);
               if (this.jwtUtil.validateToken(token, userDetails)) {
                  UsernamePasswordAuthenticationToken auth = new UsernamePasswordAuthenticationToken(userDetails, (Object)null, userDetails.getAuthorities());
                  auth.setDetails((new WebAuthenticationDetailsSource()).buildDetails(request));
                  SecurityContextHolder.getContext().setAuthentication(auth);
                  boolean isSuperAdmin = userDetails.getAuthorities().stream().anyMatch((a) -> "ROLE_SUPER_ADMIN".equals(a.getAuthority()));
                  if (!isSuperAdmin && "MAINTENANCE_CRITIQUE".equals(this.systemService.getStatus())) {
                     response.setStatus(503);
                     response.setContentType("application/json;charset=UTF-8");
                     response.getWriter().write("{\"error\":\"Service momentanément indisponible pour maintenance d'urgence.\"}");
                     return;
                  }
               }
            }
         } catch (Exception var10) {
         }
      }

      chain.doFilter(request, response);
   }

   @Generated
   public JwtFilter(final JwtUtil jwtUtil, final UserDetailsService userDetailsService, final BlackListJetonRepository blackListJetonRepository, final SystemService systemService) {
      this.jwtUtil = jwtUtil;
      this.userDetailsService = userDetailsService;
      this.blackListJetonRepository = blackListJetonRepository;
      this.systemService = systemService;
   }
}
