package com.microfinance.controller;

import com.microfinance.dto.request.AuthRequest;
import com.microfinance.dto.request.InscriptionClientRequest;
import com.microfinance.dto.request.OtpRequest;
import com.microfinance.dto.response.AuthResponse;
import com.microfinance.dto.response.ClientResponse;
import com.microfinance.exception.BusinessException;
import com.microfinance.model.BlackListJeton;
import com.microfinance.model.Client;
import com.microfinance.model.Gestionnaire;
import com.microfinance.model.OffreAbonnement;
import com.microfinance.model.enums.RoleUtilisateur;
import com.microfinance.model.enums.StatutKyc;
import com.microfinance.repository.BlackListJetonRepository;
import com.microfinance.repository.ClientRepository;
import com.microfinance.repository.GestionnaireRepository;
import com.microfinance.repository.OffreAbonnementRepository;
import com.microfinance.security.JwtUtil;
import com.microfinance.service.AuditService;
import com.microfinance.service.CompteService;
import com.microfinance.service.EmailService;
import com.microfinance.service.IpWhitelistService;
import com.microfinance.service.SessionService;
import jakarta.servlet.http.HttpServletRequest;
import jakarta.validation.Valid;
import java.time.LocalDateTime;
import java.time.ZoneId;
import java.util.Date;
import java.util.HashMap;
import java.util.Map;
import java.util.Optional;
import java.util.Random;
import lombok.Generated;
import org.slf4j.Logger;
import org.slf4j.LoggerFactory;
import org.springframework.beans.factory.annotation.Value;
import org.springframework.http.HttpStatus;
import org.springframework.http.ResponseEntity;
import org.springframework.security.authentication.AuthenticationManager;
import org.springframework.security.authentication.BadCredentialsException;
import org.springframework.security.authentication.UsernamePasswordAuthenticationToken;
import org.springframework.security.core.Authentication;
import org.springframework.security.core.GrantedAuthority;
import org.springframework.security.core.context.SecurityContextHolder;
import org.springframework.security.core.userdetails.UserDetails;
import org.springframework.security.core.userdetails.UserDetailsService;
import org.springframework.security.core.userdetails.UsernameNotFoundException;
import org.springframework.security.crypto.password.PasswordEncoder;
import org.springframework.web.bind.annotation.GetMapping;
import org.springframework.web.bind.annotation.PostMapping;
import org.springframework.web.bind.annotation.RequestBody;
import org.springframework.web.bind.annotation.RequestHeader;
import org.springframework.web.bind.annotation.RequestMapping;
import org.springframework.web.bind.annotation.RestController;

@RestController
@RequestMapping({"/api/auth"})
public class AuthController {
   @Generated
   private static final Logger log = LoggerFactory.getLogger(AuthController.class);
   private final AuthenticationManager authenticationManager;
   private final UserDetailsService userDetailsService;
   private final ClientRepository clientRepository;
   private final GestionnaireRepository gestionnaireRepository;
   private final OffreAbonnementRepository offreRepository;
   private final BlackListJetonRepository blackListJetonRepository;
   private final JwtUtil jwtUtil;
   private final EmailService emailService;
   private final IpWhitelistService ipWhitelistService;
   private final SessionService sessionService;
   private final AuditService auditService;
   private final PasswordEncoder passwordEncoder;
   private final CompteService compteService;
   @Value("${jwt.expiration}")
   private long expiration;

   @PostMapping({"/login"})
   public ResponseEntity login(@RequestBody @Valid AuthRequest req, HttpServletRequest httpRequest) {
      String ipClient = this.extractIp(httpRequest);

      try {
         this.authenticationManager.authenticate(new UsernamePasswordAuthenticationToken(req.email(), req.motDePasse()));
      } catch (BadCredentialsException var11) {
         return ResponseEntity.status(HttpStatus.UNAUTHORIZED).body(Map.of("error", "Identifiants incorrects."));
      }

      UserDetails user = this.userDetailsService.loadUserByUsername(req.email());
      String role = ((GrantedAuthority)user.getAuthorities().iterator().next()).getAuthority();
      if ("ROLE_CLIENT".equals(role)) {
         String token = this.jwtUtil.generateToken(user);
         String jti = this.jwtUtil.extractJti(token);
         this.sessionService.ouvrirSessionClient(req.email(), ipClient, jti);
         this.auditService.enregistrer(req.email(), "CONNEXION_CLIENT depuis " + ipClient, ipClient);
         return ResponseEntity.ok(AuthResponse.of(token, req.email(), role, this.expiration));
      } else {
         Gestionnaire gestionnaire = (Gestionnaire)this.gestionnaireRepository.findByEmail(req.email()).orElseThrow(() -> new UsernameNotFoundException("Gestionnaire introuvable"));
         if ("ROLE_SUPER_ADMIN".equals(role)) {
            String token = this.jwtUtil.generateToken(gestionnaire);
            String jti = this.jwtUtil.extractJti(token);
            this.sessionService.ouvrirSessionGestionnaire(gestionnaire.getEmail(), ipClient, jti);
            this.auditService.enregistrer(gestionnaire.getEmail(), "CONNEXION_SUPER_ADMIN depuis " + ipClient, ipClient);
            return ResponseEntity.ok(AuthResponse.of(token, gestionnaire.getEmail(), role, this.expiration));
         } else {
            boolean ipAutorisee = this.ipWhitelistService.estAutorisee(ipClient);
            if (!ipAutorisee) {
               boolean tempAutorise = this.ipWhitelistService.estAutoriseTemporairement(ipClient, gestionnaire.getIdGestionnaire());
               if (!tempAutorise) {
                  this.ipWhitelistService.creerDemandeAcces(req.email(), ipClient);
                  this.auditService.enregistrer(req.email(), "TENTATIVE_CONNEXION_RESEAU_INCONNU — IP: " + ipClient, ipClient);
                  return ResponseEntity.status(HttpStatus.FORBIDDEN).body(Map.of("error", "Votre réseau actuel n'est pas autorisé.", "message", "Une demande d'accès temporaire a été envoyée à la haute direction.", "step", "IP_BLOCKED"));
               }
            }

            String otp = String.format("%06d", (new Random()).nextInt(999999));
            gestionnaire.setOtpCode(otp);
            gestionnaire.setOtpExpiration(LocalDateTime.now().plusMinutes(10L));
            this.gestionnaireRepository.save(gestionnaire);
            log.info("===> OTP [{}] : {} (valable 10 min)", gestionnaire.getEmail(), otp);

            try {
               this.emailService.envoyerTokenConfirmation(gestionnaire.getEmail(), otp);
            } catch (Exception e) {
               log.error("Échec envoi email OTP à {} : {}", gestionnaire.getEmail(), e.getMessage());
            }

            this.auditService.enregistrer(req.email(), "OTP_ENVOYE depuis " + ipClient, ipClient);
            return ResponseEntity.status(HttpStatus.ACCEPTED).body(Map.of("step", "OTP_REQUIRED", "message", "Un code de confirmation a été envoyé à votre adresse email.", "email", gestionnaire.getEmail()));
         }
      }
   }

   @PostMapping({"/verify-otp"})
   public ResponseEntity verifierOtp(@RequestBody @Valid OtpRequest req, HttpServletRequest httpRequest) {
      String ipClient = this.extractIp(httpRequest);
      Gestionnaire gestionnaire = (Gestionnaire)this.gestionnaireRepository.findByEmail(req.email()).orElse((Object)null);
      if (gestionnaire == null) {
         return ResponseEntity.status(HttpStatus.UNAUTHORIZED).body(Map.of("error", "Email inconnu."));
      } else if (gestionnaire.getOtpCode() != null && gestionnaire.getOtpExpiration() != null && !LocalDateTime.now().isAfter(gestionnaire.getOtpExpiration())) {
         if (!gestionnaire.getOtpCode().equals(req.code())) {
            return ResponseEntity.status(HttpStatus.UNAUTHORIZED).body(Map.of("error", "Code OTP incorrect."));
         } else {
            gestionnaire.setOtpCode((String)null);
            gestionnaire.setOtpExpiration((LocalDateTime)null);
            this.gestionnaireRepository.save(gestionnaire);
            String token = this.jwtUtil.generateToken(gestionnaire);
            String jti = this.jwtUtil.extractJti(token);
            String role = ((GrantedAuthority)gestionnaire.getAuthorities().iterator().next()).getAuthority();
            this.sessionService.ouvrirSessionGestionnaire(gestionnaire.getEmail(), ipClient, jti);
            this.auditService.enregistrer(gestionnaire.getEmail(), "CONNEXION_VALIDEE_OTP depuis " + ipClient, ipClient);
            return ResponseEntity.ok(AuthResponse.of(token, gestionnaire.getEmail(), role, this.expiration));
         }
      } else {
         return ResponseEntity.status(HttpStatus.UNAUTHORIZED).body(Map.of("error", "Code OTP expiré. Veuillez vous reconnecter."));
      }
   }

   @PostMapping({"/resend-otp"})
   public ResponseEntity renvoyerOtp(@RequestBody Map body) {
      String email = (String)body.get("email");
      if (email != null && !email.isBlank()) {
         Gestionnaire gestionnaire = (Gestionnaire)this.gestionnaireRepository.findByEmail(email).orElse((Object)null);
         if (gestionnaire == null) {
            return ResponseEntity.status(HttpStatus.NOT_FOUND).body(Map.of("error", "Email inconnu."));
         } else {
            String otp = String.format("%06d", (new Random()).nextInt(999999));
            gestionnaire.setOtpCode(otp);
            gestionnaire.setOtpExpiration(LocalDateTime.now().plusMinutes(10L));
            this.gestionnaireRepository.save(gestionnaire);
            log.info("===> OTP [RENVOI] [{}] : {} (valable 10 min)", email, otp);

            try {
               this.emailService.envoyerTokenConfirmation(email, otp);
            } catch (Exception e) {
               log.error("Échec envoi email OTP (renvoi) à {} : {}", email, e.getMessage());
            }

            return ResponseEntity.ok(Map.of("message", "Code renvoyé à " + email));
         }
      } else {
         return ResponseEntity.badRequest().body(Map.of("error", "Email requis."));
      }
   }

   @PostMapping({"/logout"})
   public ResponseEntity logout(@RequestHeader("Authorization") String authHeader) {
      if (authHeader != null && authHeader.startsWith("Bearer ")) {
         String token = authHeader.substring(7);

         try {
            Date expDate = this.jwtUtil.extractExpiration(token);
            String jti = this.jwtUtil.extractJti(token);
            this.blackListJetonRepository.save(BlackListJeton.builder().valeurJeton(token).dateExpiration(LocalDateTime.ofInstant(expDate.toInstant(), ZoneId.systemDefault())).build());
            this.sessionService.fermerSessionParJti(jti);
            String username = this.jwtUtil.extractUsername(token);
            this.auditService.enregistrer(username, "DECONNEXION", "N/A");
         } catch (Exception var6) {
         }

         return ResponseEntity.ok(Map.of("message", "Déconnexion réussie."));
      } else {
         return ResponseEntity.badRequest().body(Map.of("error", "Token manquant."));
      }
   }

   @PostMapping({"/register"})
   public ResponseEntity inscrireClient(@RequestBody @Valid InscriptionClientRequest req) {
      if (this.clientRepository.existsByEmail(req.email())) {
         throw new BusinessException("Un compte avec cet email existe déjà.");
      } else if (this.clientRepository.existsByTelephone(req.telephone())) {
         throw new BusinessException("Un compte avec ce numéro de téléphone existe déjà.");
      } else {
         OffreAbonnement offreStandard = (OffreAbonnement)this.offreRepository.findByNomOffre("STANDARD").orElseThrow(() -> new BusinessException("Offre standard introuvable."));
         Client client = Client.builder().nom(req.nom()).prenom(req.prenom()).telephone(req.telephone()).email(req.email()).adresse(req.adresse()).numeroCni(req.numeroCni()).motDePasse(this.passwordEncoder.encode(req.motDePasse())).role(RoleUtilisateur.ROLE_CLIENT).statutKyc(StatutKyc.PENDING).operateurMomo(req.operateurMomo()).offreAbonnement(offreStandard).actif(true).build();
         Client saved = (Client)this.clientRepository.save(client);

         try {
            this.compteService.creerCompteAutoClient(saved.getEmail());
            log.info("Compte auto-créé pour le nouveau client {}", saved.getEmail());
         } catch (Exception e) {
            log.warn("Compte non créé à l'inscription pour {} : {}", saved.getEmail(), e.getMessage());
         }

         return ResponseEntity.status(HttpStatus.CREATED).body(ClientResponse.from(saved));
      }
   }

   @GetMapping({"/me"})
   public ResponseEntity me() {
      Authentication auth = SecurityContextHolder.getContext().getAuthentication();
      String email = auth.getName();
      String role = ((GrantedAuthority)auth.getAuthorities().iterator().next()).getAuthority();
      Map<String, Object> body = new HashMap();
      body.put("email", email);
      body.put("role", role);
      this.gestionnaireRepository.findByEmail(email).ifPresent((g) -> {
         body.put("idGestionnaire", g.getIdGestionnaire());
         body.put("nom", g.getNom());
         body.put("prenom", g.getPrenom());
      });
      this.clientRepository.findByEmail(email).or(() -> this.clientRepository.findByTelephone(email)).ifPresent((c) -> {
         body.put("idClient", c.getIdClient());
         body.put("nom", c.getNom());
         body.put("prenom", c.getPrenom());
         body.put("telephone", c.getTelephone());
         body.put("adresse", c.getAdresse());
         body.put("statutKyc", c.getStatutKyc());
         body.put("operateurMomo", c.getOperateurMomo());
         if (c.getOffreAbonnement() != null) {
            Map<String, Object> offre = new HashMap();
            offre.put("idOffre", c.getOffreAbonnement().getIdOffre());
            offre.put("nomOffre", c.getOffreAbonnement().getNomOffre());
            offre.put("optionRibDispo", c.getOffreAbonnement().isOptionRibDispo());
            body.put("offreAbonnement", offre);
         }

      });
      return ResponseEntity.ok(body);
   }

   @PostMapping({"/forgot-password"})
   public ResponseEntity forgotPassword(@RequestBody Map body) {
      String email = (String)body.get("email");
      if (email != null && !email.isBlank()) {
         String code = String.format("%06d", (new Random()).nextInt(999999));
         LocalDateTime expiration = LocalDateTime.now().plusMinutes(15L);
         boolean found = false;
         Optional<Client> optClient = this.clientRepository.findByEmail(email);
         if (optClient.isPresent()) {
            Client c = (Client)optClient.get();
            c.setTokenResetPassword(code);
            c.setDateExpirationReset(expiration);
            this.clientRepository.save(c);
            found = true;
         } else {
            Optional<Gestionnaire> optGest = this.gestionnaireRepository.findByEmail(email);
            if (optGest.isPresent()) {
               Gestionnaire g = (Gestionnaire)optGest.get();
               g.setTokenResetPassword(code);
               g.setDateExpirationReset(expiration);
               this.gestionnaireRepository.save(g);
               found = true;
            }
         }

         if (!found) {
            return ResponseEntity.ok(Map.of("message", "Si cet email est enregistré, vous recevrez un code."));
         } else {
            log.info("===> Code reset [{}] : {} (valable 15 min)", email, code);

            try {
               this.emailService.envoyerCodeReset(email, code);
            } catch (Exception e) {
               log.error("Échec envoi code reset à {} : {}", email, e.getMessage());
            }

            this.auditService.enregistrer(email, "DEMANDE_RESET_PASSWORD", "N/A");
            return ResponseEntity.ok(Map.of("message", "Si cet email est enregistré, vous recevrez un code."));
         }
      } else {
         return ResponseEntity.badRequest().body(Map.of("error", "Email requis."));
      }
   }

   @PostMapping({"/reset-password"})
   public ResponseEntity resetPassword(@RequestBody Map body) {
      String email = (String)body.get("email");
      String code = (String)body.get("code");
      String nouveauMdp = (String)body.get("nouveauMotDePasse");
      if (email != null && code != null && nouveauMdp != null && nouveauMdp.length() >= 8) {
         Optional<Client> optClient = this.clientRepository.findByEmail(email);
         if (optClient.isPresent()) {
            Client c = (Client)optClient.get();
            if (c.getTokenResetPassword() != null && c.getTokenResetPassword().equals(code) && c.getDateExpirationReset() != null && !LocalDateTime.now().isAfter(c.getDateExpirationReset())) {
               c.setMotDePasse(this.passwordEncoder.encode(nouveauMdp));
               c.setTokenResetPassword((String)null);
               c.setDateExpirationReset((LocalDateTime)null);
               this.clientRepository.save(c);
               this.auditService.enregistrer(email, "RESET_PASSWORD_CLIENT", "N/A");
               return ResponseEntity.ok(Map.of("message", "Mot de passe réinitialisé avec succès."));
            } else {
               return ResponseEntity.status(HttpStatus.UNAUTHORIZED).body(Map.of("error", "Code invalide ou expiré."));
            }
         } else {
            Optional<Gestionnaire> optGest = this.gestionnaireRepository.findByEmail(email);
            if (optGest.isPresent()) {
               Gestionnaire g = (Gestionnaire)optGest.get();
               if (g.getTokenResetPassword() != null && g.getTokenResetPassword().equals(code) && g.getDateExpirationReset() != null && !LocalDateTime.now().isAfter(g.getDateExpirationReset())) {
                  g.setMotDePasse(this.passwordEncoder.encode(nouveauMdp));
                  g.setTokenResetPassword((String)null);
                  g.setDateExpirationReset((LocalDateTime)null);
                  this.gestionnaireRepository.save(g);
                  this.auditService.enregistrer(email, "RESET_PASSWORD_PERSONNEL", "N/A");
                  return ResponseEntity.ok(Map.of("message", "Mot de passe réinitialisé avec succès."));
               } else {
                  return ResponseEntity.status(HttpStatus.UNAUTHORIZED).body(Map.of("error", "Code invalide ou expiré."));
               }
            } else {
               return ResponseEntity.status(HttpStatus.UNAUTHORIZED).body(Map.of("error", "Code invalide ou expiré."));
            }
         }
      } else {
         return ResponseEntity.badRequest().body(Map.of("error", "Données incomplètes ou mot de passe trop court (8 caractères min)."));
      }
   }

   @PostMapping({"/change-password"})
   public ResponseEntity changePassword(@RequestBody Map body) {
      Authentication auth = SecurityContextHolder.getContext().getAuthentication();
      String email = auth.getName();
      String ancienMdp = (String)body.get("ancienMotDePasse");
      String nouveauMdp = (String)body.get("nouveauMotDePasse");
      if (ancienMdp != null && nouveauMdp != null && nouveauMdp.length() >= 8) {
         Optional<Client> optClient = this.clientRepository.findByEmail(email);
         if (optClient.isPresent()) {
            Client c = (Client)optClient.get();
            if (!this.passwordEncoder.matches(ancienMdp, c.getMotDePasse())) {
               return ResponseEntity.status(HttpStatus.UNAUTHORIZED).body(Map.of("error", "Ancien mot de passe incorrect."));
            } else {
               c.setMotDePasse(this.passwordEncoder.encode(nouveauMdp));
               this.clientRepository.save(c);
               this.auditService.enregistrer(email, "CHANGEMENT_PASSWORD_CLIENT", "N/A");
               return ResponseEntity.ok(Map.of("message", "Mot de passe modifié avec succès."));
            }
         } else {
            Optional<Gestionnaire> optGest = this.gestionnaireRepository.findByEmail(email);
            if (optGest.isPresent()) {
               Gestionnaire g = (Gestionnaire)optGest.get();
               if (!this.passwordEncoder.matches(ancienMdp, g.getMotDePasse())) {
                  return ResponseEntity.status(HttpStatus.UNAUTHORIZED).body(Map.of("error", "Ancien mot de passe incorrect."));
               } else {
                  g.setMotDePasse(this.passwordEncoder.encode(nouveauMdp));
                  this.gestionnaireRepository.save(g);
                  this.auditService.enregistrer(email, "CHANGEMENT_PASSWORD_PERSONNEL", "N/A");
                  return ResponseEntity.ok(Map.of("message", "Mot de passe modifié avec succès."));
               }
            } else {
               return ResponseEntity.status(HttpStatus.NOT_FOUND).body(Map.of("error", "Utilisateur introuvable."));
            }
         }
      } else {
         return ResponseEntity.badRequest().body(Map.of("error", "Données incomplètes ou nouveau mot de passe trop court."));
      }
   }

   @PostMapping({"/superadmin/verify-key"})
   public ResponseEntity verifyCleSecrete(@RequestBody Map body) {
      String email = (String)body.get("email");
      String cleSecrete = (String)body.get("cleSecrete");
      if (email != null && !email.isBlank() && cleSecrete != null && !cleSecrete.isBlank()) {
         Optional<Gestionnaire> optGest = this.gestionnaireRepository.findByEmail(email);
         if (!optGest.isEmpty() && ((Gestionnaire)optGest.get()).getRole() == RoleUtilisateur.ROLE_SUPER_ADMIN && ((Gestionnaire)optGest.get()).isActif()) {
            Gestionnaire g = (Gestionnaire)optGest.get();
            if (g.getCleSecrete() != null && this.passwordEncoder.matches(cleSecrete, g.getCleSecrete())) {
               this.auditService.enregistrer(email, "SUPERADMIN_PORTAL_KEY_VERIFIED", "portal");
               return ResponseEntity.ok(Map.of("valid", true, "email", email));
            } else {
               return ResponseEntity.status(HttpStatus.UNAUTHORIZED).body(Map.of("error", "Mot clé incorrect."));
            }
         } else {
            return ResponseEntity.status(HttpStatus.UNAUTHORIZED).body(Map.of("error", "Accès refusé. Vérifiez vos identifiants."));
         }
      } else {
         return ResponseEntity.badRequest().body(Map.of("error", "Données incomplètes."));
      }
   }

   @GetMapping({"/my-ip"})
   public ResponseEntity monIp(HttpServletRequest request) {
      String raw = this.extractIp(request);
      String normalized = this.ipWhitelistService.normaliserIp(raw);
      return ResponseEntity.ok(Map.of("ip", normalized, "ipRaw", raw, "whitelisted", String.valueOf(this.ipWhitelistService.estAutorisee(raw))));
   }

   private String extractIp(HttpServletRequest request) {
      String xff = request.getHeader("X-Forwarded-For");
      return xff != null && !xff.isBlank() ? xff.split(",")[0].trim() : request.getRemoteAddr();
   }

   @Generated
   public AuthController(final AuthenticationManager authenticationManager, final UserDetailsService userDetailsService, final ClientRepository clientRepository, final GestionnaireRepository gestionnaireRepository, final OffreAbonnementRepository offreRepository, final BlackListJetonRepository blackListJetonRepository, final JwtUtil jwtUtil, final EmailService emailService, final IpWhitelistService ipWhitelistService, final SessionService sessionService, final AuditService auditService, final PasswordEncoder passwordEncoder, final CompteService compteService) {
      this.authenticationManager = authenticationManager;
      this.userDetailsService = userDetailsService;
      this.clientRepository = clientRepository;
      this.gestionnaireRepository = gestionnaireRepository;
      this.offreRepository = offreRepository;
      this.blackListJetonRepository = blackListJetonRepository;
      this.jwtUtil = jwtUtil;
      this.emailService = emailService;
      this.ipWhitelistService = ipWhitelistService;
      this.sessionService = sessionService;
      this.auditService = auditService;
      this.passwordEncoder = passwordEncoder;
      this.compteService = compteService;
   }
}
