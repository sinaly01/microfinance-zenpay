package com.microfinance.controller;

import com.microfinance.service.KycService;
import java.net.MalformedURLException;
import java.nio.file.Path;
import java.util.Map;
import lombok.Generated;
import org.slf4j.Logger;
import org.slf4j.LoggerFactory;
import org.springframework.core.io.Resource;
import org.springframework.core.io.UrlResource;
import org.springframework.http.HttpStatus;
import org.springframework.http.MediaType;
import org.springframework.http.ResponseEntity;
import org.springframework.security.access.prepost.PreAuthorize;
import org.springframework.web.bind.annotation.GetMapping;
import org.springframework.web.bind.annotation.PathVariable;
import org.springframework.web.bind.annotation.PostMapping;
import org.springframework.web.bind.annotation.PutMapping;
import org.springframework.web.bind.annotation.RequestMapping;
import org.springframework.web.bind.annotation.RequestParam;
import org.springframework.web.bind.annotation.RestController;
import org.springframework.web.multipart.MultipartFile;

@RestController
@RequestMapping({"/api/kyc"})
public class KycController {
   @Generated
   private static final Logger log = LoggerFactory.getLogger(KycController.class);
   private final KycService kycService;

   @PostMapping({"/soumettre/{idClient}"})
   @PreAuthorize("hasAnyRole('CLIENT', 'GESTIONNAIRE')")
   public ResponseEntity soumettreDocument(@PathVariable Long idClient, @RequestParam String typeDocument, @RequestParam String urlDocument) {
      return ResponseEntity.ok(this.kycService.soumettreDocument(idClient, typeDocument, urlDocument));
   }

   @GetMapping({"/documents/{idClient}"})
   @PreAuthorize("hasAnyRole('CLIENT', 'GESTIONNAIRE', 'ADMIN_BD')")
   public ResponseEntity getDocuments(@PathVariable Long idClient) {
      return ResponseEntity.ok(this.kycService.getDocumentsClient(idClient));
   }

   @GetMapping({"/en-attente"})
   @PreAuthorize("hasAnyRole('GESTIONNAIRE', 'SUPERVISOR', 'ADMIN_SYSTEME', 'SUPER_ADMIN')")
   public ResponseEntity getClientsEnAttente() {
      return ResponseEntity.ok(this.kycService.getClientsEnAttenteKyc());
   }

   @PutMapping({"/valider/{idClient}"})
   @PreAuthorize("hasAnyRole('GESTIONNAIRE', 'SUPERVISOR', 'ADMIN_SYSTEME', 'SUPER_ADMIN')")
   public ResponseEntity validerKyc(@PathVariable Long idClient) {
      return ResponseEntity.ok(this.kycService.validerKyc(idClient));
   }

   @PutMapping({"/rejeter/{idClient}"})
   @PreAuthorize("hasAnyRole('GESTIONNAIRE', 'SUPERVISOR', 'ADMIN_SYSTEME', 'SUPER_ADMIN')")
   public ResponseEntity rejeterKyc(@PathVariable Long idClient) {
      return ResponseEntity.ok(this.kycService.rejeterKyc(idClient));
   }

   @PostMapping(
      value = {"/upload/{idClient}"},
      consumes = {"multipart/form-data"}
   )
   @PreAuthorize("hasAnyRole('CLIENT', 'GESTIONNAIRE', 'ADMIN_BD', 'SUPER_ADMIN')")
   public ResponseEntity uploadDocument(@PathVariable Long idClient, @RequestParam("typeDocument") String typeDocument, @RequestParam("file") MultipartFile file) {
      if (file.isEmpty()) {
         return ResponseEntity.badRequest().body(Map.of("error", "Fichier vide."));
      } else {
         try {
            return ResponseEntity.ok(this.kycService.uploadDocument(idClient, typeDocument, file));
         } catch (Exception e) {
            log.error("Erreur upload KYC : {}", e.getMessage());
            return ResponseEntity.status(HttpStatus.INTERNAL_SERVER_ERROR).body(Map.of("error", "Erreur lors du téléversement : " + e.getMessage()));
         }
      }
   }

   @GetMapping({"/file/{filename:.+}"})
   public ResponseEntity servirFichier(@PathVariable String filename) {
      try {
         Path filePath = this.kycService.getFilePath(filename);
         Resource resource = new UrlResource(filePath.toUri());
         if (resource.exists() && resource.isReadable()) {
            String contentType = this.determineContentType(filename);
            return ((ResponseEntity.BodyBuilder)ResponseEntity.ok().contentType(MediaType.parseMediaType(contentType)).header("Content-Disposition", new String[]{"inline; filename=\"" + filename + "\""})).body(resource);
         } else {
            return ResponseEntity.notFound().build();
         }
      } catch (MalformedURLException var5) {
         return ResponseEntity.badRequest().build();
      }
   }

   private String determineContentType(String filename) {
      String lower = filename.toLowerCase();
      if (lower.endsWith(".pdf")) {
         return "application/pdf";
      } else if (lower.endsWith(".png")) {
         return "image/png";
      } else {
         return lower.endsWith(".gif") ? "image/gif" : "image/jpeg";
      }
   }

   @Generated
   public KycController(final KycService kycService) {
      this.kycService = kycService;
   }
}
