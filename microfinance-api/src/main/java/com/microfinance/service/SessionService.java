package com.microfinance.service;

import com.microfinance.model.Client;
import com.microfinance.model.Gestionnaire;
import com.microfinance.model.SessionConnexion;
import com.microfinance.repository.ClientRepository;
import com.microfinance.repository.GestionnaireRepository;
import com.microfinance.repository.SessionConnexionRepository;
import java.util.List;
import lombok.Generated;
import org.springframework.stereotype.Service;
import org.springframework.transaction.annotation.Transactional;

@Service
public class SessionService {
   private final SessionConnexionRepository sessionRepo;
   private final GestionnaireRepository gestionnaireRepo;
   private final ClientRepository clientRepo;

   @Transactional
   public SessionConnexion ouvrirSessionGestionnaire(String email, String adresseIp, String jti) {
      Gestionnaire g = (Gestionnaire)this.gestionnaireRepo.findByEmail(email).orElse((Object)null);
      SessionConnexion session = SessionConnexion.builder().gestionnaire(g).adresseIp(adresseIp).jtiToken(jti).statutSession("ACTIVE").build();
      return (SessionConnexion)this.sessionRepo.save(session);
   }

   @Transactional
   public SessionConnexion ouvrirSessionClient(String email, String adresseIp, String jti) {
      Client c = (Client)this.clientRepo.findByEmail(email).or(() -> this.clientRepo.findByTelephone(email)).orElse((Object)null);
      SessionConnexion session = SessionConnexion.builder().client(c).adresseIp(adresseIp).jtiToken(jti).statutSession("ACTIVE").build();
      return (SessionConnexion)this.sessionRepo.save(session);
   }

   @Transactional
   public void fermerSessionParJti(String jti) {
      this.sessionRepo.findByJtiTokenAndStatutSession(jti, "ACTIVE").ifPresent((s) -> {
         s.clore();
         this.sessionRepo.save(s);
      });
   }

   @Transactional(
      readOnly = true
   )
   public List getSessionsActives() {
      return this.sessionRepo.findAll().stream().filter((s) -> "ACTIVE".equals(s.getStatutSession())).toList();
   }

   @Generated
   public SessionService(final SessionConnexionRepository sessionRepo, final GestionnaireRepository gestionnaireRepo, final ClientRepository clientRepo) {
      this.sessionRepo = sessionRepo;
      this.gestionnaireRepo = gestionnaireRepo;
      this.clientRepo = clientRepo;
   }
}
