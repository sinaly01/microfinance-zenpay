package com.microfinance.controller;

import com.microfinance.exception.ResourceNotFoundException;
import com.microfinance.model.Client;
import com.microfinance.model.Gestionnaire;
import com.microfinance.model.MessageSupport;
import com.microfinance.model.enums.RoleUtilisateur;
import com.microfinance.repository.ClientRepository;
import com.microfinance.repository.GestionnaireRepository;
import com.microfinance.repository.MessageSupportRepository;
import java.util.HashMap;
import java.util.LinkedHashMap;
import java.util.List;
import java.util.Map;
import java.util.Random;
import lombok.Generated;
import org.springframework.http.HttpStatus;
import org.springframework.http.ResponseEntity;
import org.springframework.security.access.prepost.PreAuthorize;
import org.springframework.web.bind.annotation.GetMapping;
import org.springframework.web.bind.annotation.PathVariable;
import org.springframework.web.bind.annotation.PostMapping;
import org.springframework.web.bind.annotation.PutMapping;
import org.springframework.web.bind.annotation.RequestMapping;
import org.springframework.web.bind.annotation.RequestParam;
import org.springframework.web.bind.annotation.RestController;

@RestController
@RequestMapping({"/api/support"})
public class SupportController {
   private final MessageSupportRepository messageRepo;
   private final ClientRepository clientRepo;
   private final GestionnaireRepository gestionnaireRepo;

   @PostMapping({"/message"})
   @PreAuthorize("hasAnyRole('CLIENT', 'GESTIONNAIRE', 'SUPER_ADMIN', 'ADMIN_SYSTEME')")
   public ResponseEntity envoyerMessage(@RequestParam Long idClient, @RequestParam String contenu, @RequestParam(defaultValue = "CLIENT") String expediteur) {
      Client client = (Client)this.clientRepo.findById(idClient).orElseThrow(() -> new ResourceNotFoundException("Client", idClient));
      Gestionnaire gestionnaire = this.trouverOuAssignerGestionnaire(idClient);
      MessageSupport msg = MessageSupport.builder().client(client).gestionnaire(gestionnaire).contenu(contenu).expediteur(expediteur).lu(false).build();
      MessageSupport saved = (MessageSupport)this.messageRepo.save(msg);
      if (!this.messageRepo.existsByClient_IdClient(idClient) || this.messageRepo.findByClient_IdClientOrderByDateEnvoiAsc(idClient).size() == 1) {
         MessageSupport auto = MessageSupport.builder().client(client).gestionnaire(gestionnaire).contenu("Bonjour ! Votre message a bien été reçu. Notre équipe ZEN-PAY vous répondra dès que possible. Merci de votre patience. \ud83d\ude0a").expediteur("GESTIONNAIRE").lu(false).build();
         this.messageRepo.save(auto);
      }

      return ResponseEntity.status(HttpStatus.CREATED).body(saved);
   }

   @PostMapping({"/repondre"})
   @PreAuthorize("hasAnyRole('GESTIONNAIRE', 'SUPERVISOR', 'ADMIN_SYSTEME', 'SUPER_ADMIN')")
   public ResponseEntity repondre(@RequestParam Long idClient, @RequestParam Long idGestionnaire, @RequestParam String contenu) {
      Client client = (Client)this.clientRepo.findById(idClient).orElseThrow(() -> new ResourceNotFoundException("Client", idClient));
      Gestionnaire gestionnaire = (Gestionnaire)this.gestionnaireRepo.findById(idGestionnaire).orElseThrow(() -> new ResourceNotFoundException("Gestionnaire", idGestionnaire));
      MessageSupport msg = MessageSupport.builder().client(client).gestionnaire(gestionnaire).contenu(contenu).expediteur("GESTIONNAIRE").lu(false).build();
      return ResponseEntity.status(HttpStatus.CREATED).body((MessageSupport)this.messageRepo.save(msg));
   }

   @GetMapping({"/{idClient}"})
   @PreAuthorize("hasAnyRole('CLIENT', 'GESTIONNAIRE', 'SUPERVISOR', 'ADMIN_SYSTEME', 'SUPER_ADMIN')")
   public ResponseEntity getConversation(@PathVariable Long idClient) {
      return ResponseEntity.ok(this.messageRepo.findByClient_IdClientOrderByDateEnvoiAsc(idClient));
   }

   @GetMapping({"/conversations"})
   @PreAuthorize("hasAnyRole('GESTIONNAIRE', 'SUPERVISOR', 'ADMIN_SYSTEME', 'SUPER_ADMIN')")
   public ResponseEntity toutesConversations() {
      List<MessageSupport> tous = this.messageRepo.findAll();
      Map<Long, MessageSupport> dernierMessage = new LinkedHashMap();
      tous.forEach((m) -> {
         Long cid = m.getClient().getIdClient();
         if (!dernierMessage.containsKey(cid) || m.getDateEnvoi().isAfter(((MessageSupport)dernierMessage.get(cid)).getDateEnvoi())) {
            dernierMessage.put(cid, m);
         }

      });
      List<Map<String, Object>> result = dernierMessage.values().stream().sorted((a, b) -> b.getDateEnvoi().compareTo(a.getDateEnvoi())).map((m) -> {
         Map<String, Object> r = new HashMap();
         r.put("idClient", m.getClient().getIdClient());
         String var10002 = m.getClient().getPrenom();
         r.put("nomClient", var10002 + " " + m.getClient().getNom());
         r.put("dernierMessage", m.getContenu());
         r.put("dateEnvoi", m.getDateEnvoi());
         r.put("expediteur", m.getExpediteur());
         long nonLus = this.messageRepo.countByClient_IdClientAndLuFalseAndExpediteur(m.getClient().getIdClient(), "CLIENT");
         r.put("nonLus", nonLus);
         return r;
      }).toList();
      return ResponseEntity.ok(result);
   }

   @PutMapping({"/{idClient}/lire"})
   @PreAuthorize("hasAnyRole('CLIENT', 'GESTIONNAIRE', 'SUPERVISOR', 'ADMIN_SYSTEME', 'SUPER_ADMIN')")
   public ResponseEntity marquerLus(@PathVariable Long idClient) {
      List<MessageSupport> msgs = this.messageRepo.findByClient_IdClientOrderByDateEnvoiAsc(idClient);
      msgs.forEach((m) -> m.setLu(true));
      this.messageRepo.saveAll(msgs);
      return ResponseEntity.ok(Map.of("message", "Messages marqués comme lus."));
   }

   private Gestionnaire trouverOuAssignerGestionnaire(Long idClient) {
      List<MessageSupport> existing = this.messageRepo.findByClient_IdClientOrderByDateEnvoiAsc(idClient);
      if (!existing.isEmpty() && ((MessageSupport)existing.get(0)).getGestionnaire() != null) {
         return ((MessageSupport)existing.get(0)).getGestionnaire();
      } else {
         List<Gestionnaire> gestionnaires = this.gestionnaireRepo.findByRole(RoleUtilisateur.ROLE_GESTIONNAIRE).stream().filter(Gestionnaire::isActif).toList();
         return !gestionnaires.isEmpty() ? (Gestionnaire)gestionnaires.get((new Random()).nextInt(gestionnaires.size())) : null;
      }
   }

   @Generated
   public SupportController(final MessageSupportRepository messageRepo, final ClientRepository clientRepo, final GestionnaireRepository gestionnaireRepo) {
      this.messageRepo = messageRepo;
      this.clientRepo = clientRepo;
      this.gestionnaireRepo = gestionnaireRepo;
   }
}
