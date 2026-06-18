package com.microfinance.controller;

import com.microfinance.dto.request.ClientRequest;
import com.microfinance.exception.ResourceNotFoundException;
import com.microfinance.model.Client;
import com.microfinance.model.SuggestionModifProfil;
import com.microfinance.repository.ClientRepository;
import com.microfinance.repository.GestionnaireRepository;
import com.microfinance.repository.SuggestionModifProfilRepository;
import com.microfinance.service.AuditService;
import com.microfinance.service.ClientService;
import jakarta.validation.Valid;
import java.time.LocalDateTime;
import java.util.HashMap;
import java.util.List;
import java.util.Map;
import java.util.Objects;
import java.util.Optional;
import lombok.Generated;
import org.springframework.http.HttpStatus;
import org.springframework.http.ResponseEntity;
import org.springframework.security.access.prepost.PreAuthorize;
import org.springframework.security.core.Authentication;
import org.springframework.security.core.context.SecurityContextHolder;
import org.springframework.web.bind.annotation.DeleteMapping;
import org.springframework.web.bind.annotation.GetMapping;
import org.springframework.web.bind.annotation.PathVariable;
import org.springframework.web.bind.annotation.PostMapping;
import org.springframework.web.bind.annotation.PutMapping;
import org.springframework.web.bind.annotation.RequestBody;
import org.springframework.web.bind.annotation.RequestMapping;
import org.springframework.web.bind.annotation.RequestParam;
import org.springframework.web.bind.annotation.RestController;

@RestController
@RequestMapping({"/api/clients"})
public class ClientController {
   private final ClientService clientService;
   private final ClientRepository clientRepository;
   private final GestionnaireRepository gestionnaireRepository;
   private final SuggestionModifProfilRepository suggestionRepository;
   private final AuditService auditService;

   @PostMapping
   @PreAuthorize("hasAnyRole('GESTIONNAIRE', 'ADMIN_SYSTEME', 'SUPER_ADMIN')")
   public ResponseEntity creerClient(@RequestBody @Valid ClientRequest req) {
      return ResponseEntity.status(HttpStatus.CREATED).body(this.clientService.creerClient(req));
   }

   @GetMapping({"/{id}"})
   @PreAuthorize("hasAnyRole('CLIENT', 'GESTIONNAIRE', 'ADMIN_BD', 'ADMIN_SYSTEME', 'SUPER_ADMIN')")
   public ResponseEntity getClient(@PathVariable Long id) {
      return ResponseEntity.ok(this.clientService.getClient(id));
   }

   @GetMapping({"/by-telephone"})
   @PreAuthorize("hasAnyRole('CLIENT', 'GESTIONNAIRE', 'ADMIN_BD', 'ADMIN_SYSTEME', 'SUPER_ADMIN')")
   public ResponseEntity rechercherParTelephone(@RequestParam String telephone) {
      return (ResponseEntity)this.clientRepository.findByTelephone(telephone).map((c) -> ResponseEntity.ok(Map.of("idClient", c.getIdClient(), "nom", c.getNom(), "prenom", c.getPrenom(), "telephone", c.getTelephone()))).orElseGet(() -> ResponseEntity.notFound().build());
   }

   @GetMapping
   @PreAuthorize("hasAnyRole('GESTIONNAIRE', 'ADMIN_BD', 'ADMIN_SYSTEME', 'SUPER_ADMIN')")
   public ResponseEntity listerClients() {
      return ResponseEntity.ok(this.clientService.listerClients());
   }

   @PutMapping({"/{id}"})
   @PreAuthorize("hasAnyRole('CLIENT', 'GESTIONNAIRE', 'SUPER_ADMIN')")
   public ResponseEntity mettreAJour(@PathVariable Long id, @RequestBody @Valid ClientRequest req) {
      return ResponseEntity.ok(this.clientService.mettreAJour(id, req));
   }

   @PutMapping({"/{id}/suspendre"})
   @PreAuthorize("hasAnyRole('GESTIONNAIRE', 'ADMIN_SYSTEME', 'SUPER_ADMIN')")
   public ResponseEntity suspendreClient(@PathVariable Long id) {
      Client c = (Client)this.clientRepository.findById(id).orElseThrow(() -> new ResourceNotFoundException("Client", id));
      c.setActif(false);
      this.clientRepository.save(c);
      this.auditService.enregistrerSysteme("CLIENT_SUSPENDU — id=" + id + " email=" + c.getEmail());
      return ResponseEntity.ok(Map.of("message", "Client suspendu.", "actif", false, "idClient", id));
   }

   @PutMapping({"/{id}/activer"})
   @PreAuthorize("hasAnyRole('GESTIONNAIRE', 'ADMIN_SYSTEME', 'SUPER_ADMIN')")
   public ResponseEntity activerClient(@PathVariable Long id) {
      Client c = (Client)this.clientRepository.findById(id).orElseThrow(() -> new ResourceNotFoundException("Client", id));
      c.setActif(true);
      this.clientRepository.save(c);
      this.auditService.enregistrerSysteme("CLIENT_ACTIVE — id=" + id + " email=" + c.getEmail());
      return ResponseEntity.ok(Map.of("message", "Client réactivé.", "actif", true, "idClient", id));
   }

   @DeleteMapping({"/{id}"})
   @PreAuthorize("hasAnyRole('ADMIN_SYSTEME', 'SUPER_ADMIN')")
   public ResponseEntity desactiverClient(@PathVariable Long id) {
      this.clientService.desactiverClient(id);
      return ResponseEntity.noContent().build();
   }

   @PostMapping({"/suggestion-profil"})
   @PreAuthorize("hasRole('CLIENT')")
   public ResponseEntity soumettreModification(@RequestBody Map body) {
      Authentication auth = SecurityContextHolder.getContext().getAuthentication();
      String email = auth.getName();
      Client client = (Client)this.clientRepository.findByEmail(email).orElseThrow(() -> new ResourceNotFoundException("Client", 0L));
      SuggestionModifProfil sug = SuggestionModifProfil.builder().client(client).nouveauNom((String)body.get("nom")).nouveauPrenom((String)body.get("prenom")).nouvelEmail((String)body.get("email")).nouveauTelephone((String)body.get("telephone")).nouvelleAdresse((String)body.get("adresse")).statut("EN_ATTENTE").build();
      this.suggestionRepository.save(sug);
      this.auditService.enregistrer(email, "SUGGESTION_MODIF_PROFIL_SOUMISE", "N/A");
      return ResponseEntity.ok(Map.of("message", "Demande transmise. Vos informations s'actualiseront après validation par un gestionnaire."));
   }

   @GetMapping({"/suggestions"})
   @PreAuthorize("hasAnyRole('GESTIONNAIRE', 'ADMIN_BD', 'ADMIN_SYSTEME', 'SUPER_ADMIN')")
   public ResponseEntity listerSuggestions(@RequestParam(defaultValue = "EN_ATTENTE") String statut) {
      List<SuggestionModifProfil> sugs = this.suggestionRepository.findByStatutOrderByDateDemandeDesc(statut);
      List<Map<String, Object>> result = sugs.stream().map((s) -> {
         Map<String, Object> m = new HashMap();
         m.put("idSuggestion", s.getIdSuggestion());
         m.put("idClient", s.getClient().getIdClient());
         String var10002 = s.getClient().getNom();
         m.put("clientNom", var10002 + " " + s.getClient().getPrenom());
         m.put("clientEmail", s.getClient().getEmail());
         m.put("nouveauNom", s.getNouveauNom());
         m.put("nouveauPrenom", s.getNouveauPrenom());
         m.put("nouvelEmail", s.getNouvelEmail());
         m.put("nouveauTelephone", s.getNouveauTelephone());
         m.put("nouvelleAdresse", s.getNouvelleAdresse());
         m.put("statut", s.getStatut());
         m.put("dateDemande", s.getDateDemande());
         m.put("dateTraitement", s.getDateTraitement());
         return m;
      }).toList();
      return ResponseEntity.ok(result);
   }

   @PutMapping({"/suggestions/{idSuggestion}"})
   @PreAuthorize("hasAnyRole('GESTIONNAIRE', 'ADMIN_BD', 'ADMIN_SYSTEME', 'SUPER_ADMIN')")
   public ResponseEntity traiterSuggestion(@PathVariable Long idSuggestion, @RequestBody Map body) {
      String decision = (String)body.get("decision");
      if (!"APPROUVE".equals(decision) && !"REJETE".equals(decision)) {
         return ResponseEntity.badRequest().body(Map.of("error", "Décision invalide. Valeurs acceptées : APPROUVE, REJETE"));
      } else {
         SuggestionModifProfil sug = (SuggestionModifProfil)this.suggestionRepository.findById(idSuggestion).orElseThrow(() -> new ResourceNotFoundException("Suggestion", idSuggestion));
         Authentication auth = SecurityContextHolder.getContext().getAuthentication();
         String emailGest = auth.getName();
         Optional var10000 = this.gestionnaireRepository.findByEmail(emailGest);
         Objects.requireNonNull(sug);
         var10000.ifPresent(sug::setGestionnaireValidateur);
         sug.setStatut(decision);
         sug.setDateTraitement(LocalDateTime.now());
         if ("APPROUVE".equals(decision)) {
            Client client = sug.getClient();
            if (sug.getNouveauNom() != null && !sug.getNouveauNom().isBlank()) {
               client.setNom(sug.getNouveauNom());
            }

            if (sug.getNouveauPrenom() != null && !sug.getNouveauPrenom().isBlank()) {
               client.setPrenom(sug.getNouveauPrenom());
            }

            if (sug.getNouvelEmail() != null && !sug.getNouvelEmail().isBlank()) {
               client.setEmail(sug.getNouvelEmail());
            }

            if (sug.getNouveauTelephone() != null && !sug.getNouveauTelephone().isBlank()) {
               client.setTelephone(sug.getNouveauTelephone());
            }

            if (sug.getNouvelleAdresse() != null && !sug.getNouvelleAdresse().isBlank()) {
               client.setAdresse(sug.getNouvelleAdresse());
            }

            this.clientRepository.save(client);
            this.auditService.enregistrer(emailGest, "SUGGESTION_APPROUVEE — id=" + idSuggestion + " client=" + client.getEmail(), "N/A");
         } else {
            this.auditService.enregistrer(emailGest, "SUGGESTION_REJETEE — id=" + idSuggestion, "N/A");
         }

         this.suggestionRepository.save(sug);
         return ResponseEntity.ok(Map.of("message", "Suggestion " + decision.toLowerCase() + "e.", "idSuggestion", idSuggestion));
      }
   }

   @Generated
   public ClientController(final ClientService clientService, final ClientRepository clientRepository, final GestionnaireRepository gestionnaireRepository, final SuggestionModifProfilRepository suggestionRepository, final AuditService auditService) {
      this.clientService = clientService;
      this.clientRepository = clientRepository;
      this.gestionnaireRepository = gestionnaireRepository;
      this.suggestionRepository = suggestionRepository;
      this.auditService = auditService;
   }
}
