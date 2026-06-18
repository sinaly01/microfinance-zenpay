package com.microfinance.controller;

import com.microfinance.service.RibService;
import lombok.Generated;
import org.springframework.http.HttpHeaders;
import org.springframework.http.MediaType;
import org.springframework.http.ResponseEntity;
import org.springframework.security.access.prepost.PreAuthorize;
import org.springframework.web.bind.annotation.GetMapping;
import org.springframework.web.bind.annotation.PathVariable;
import org.springframework.web.bind.annotation.RequestMapping;
import org.springframework.web.bind.annotation.RestController;

@RestController
@RequestMapping({"/api/rib"})
public class RibController {
   private final RibService ribService;

   @GetMapping({"/{idClient}"})
   @PreAuthorize("hasAnyRole('CLIENT', 'GESTIONNAIRE', 'ADMIN_SYSTEME', 'SUPER_ADMIN')")
   public ResponseEntity telechargerRib(@PathVariable Long idClient) {
      byte[] pdf = this.ribService.genererRib(idClient);
      HttpHeaders headers = new HttpHeaders();
      headers.setContentType(MediaType.APPLICATION_PDF);
      headers.setContentDispositionFormData("attachment", "RIB_client_" + idClient + ".pdf");
      headers.setContentLength((long)pdf.length);
      return ((ResponseEntity.BodyBuilder)ResponseEntity.ok().headers(headers)).body(pdf);
   }

   @Generated
   public RibController(final RibService ribService) {
      this.ribService = ribService;
   }
}
