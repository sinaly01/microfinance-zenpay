package com.microfinance.service;

import com.microfinance.exception.BusinessException;
import com.microfinance.exception.ResourceNotFoundException;
import com.microfinance.model.Client;
import com.microfinance.model.DocumentKyc;
import com.microfinance.model.enums.StatutKyc;
import com.microfinance.repository.ClientRepository;
import com.microfinance.repository.DocumentKycRepository;
import java.io.IOException;
import java.nio.file.CopyOption;
import java.nio.file.Files;
import java.nio.file.Path;
import java.nio.file.Paths;
import java.nio.file.StandardCopyOption;
import java.util.List;
import java.util.Map;
import java.util.Objects;
import java.util.UUID;
import java.util.stream.Stream;
import lombok.Generated;
import org.slf4j.Logger;
import org.slf4j.LoggerFactory;
import org.springframework.beans.factory.annotation.Value;
import org.springframework.stereotype.Service;
import org.springframework.transaction.annotation.Transactional;
import org.springframework.web.multipart.MultipartFile;

@Service
@Transactional
public class KycService {
   @Generated
   private static final Logger log = LoggerFactory.getLogger(KycService.class);
   private final ClientRepository clientRepository;
   private final DocumentKycRepository documentKycRepository;
   private final AuditService auditService;
   @Value("${upload.kyc.dir:/tmp/zenpay-kyc}")
   private String uploadDir;

   public DocumentKyc soumettreDocument(Long idClient, String typeDocument, String urlDocument) {
      Client client = (Client)this.clientRepository.findById(idClient).orElseThrow(() -> new ResourceNotFoundException("Client", idClient));
      Stream var10000 = this.documentKycRepository.findByClientIdClient(idClient).stream().filter((d) -> d.getTypeDocument().equals(typeDocument));
      DocumentKycRepository var10001 = this.documentKycRepository;
      Objects.requireNonNull(var10001);
      var10000.forEach(var10001::delete);
      DocumentKyc doc = DocumentKyc.builder().client(client).typeDocument(typeDocument).urlDocument(urlDocument).build();
      DocumentKyc saved = (DocumentKyc)this.documentKycRepository.save(doc);
      this.updateStatutApresDocument(client);
      return saved;
   }

   public Map uploadDocument(Long idClient, String typeDocument, MultipartFile file) throws IOException {
      Client client = (Client)this.clientRepository.findById(idClient).orElseThrow(() -> new ResourceNotFoundException("Client", idClient));
      Path dir = Paths.get(this.uploadDir);
      Files.createDirectories(dir);
      String ext = "";
      String orig = file.getOriginalFilename();
      if (orig != null && orig.contains(".")) {
         ext = orig.substring(orig.lastIndexOf("."));
      }

      String filename = "client_" + idClient + "_" + typeDocument + "_" + UUID.randomUUID().toString().substring(0, 8) + ext;
      Path dest = dir.resolve(filename);
      Files.copy(file.getInputStream(), dest, new CopyOption[]{StandardCopyOption.REPLACE_EXISTING});
      Stream var10000 = this.documentKycRepository.findByClientIdClient(idClient).stream().filter((d) -> d.getTypeDocument().equals(typeDocument));
      DocumentKycRepository var10001 = this.documentKycRepository;
      Objects.requireNonNull(var10001);
      var10000.forEach(var10001::delete);
      String urlDocument = "/api/kyc/file/" + filename;
      DocumentKyc doc = DocumentKyc.builder().client(client).typeDocument(typeDocument).urlDocument(urlDocument).build();
      this.documentKycRepository.save(doc);
      this.updateStatutApresDocument(client);
      this.auditService.enregistrerSysteme("KYC_UPLOAD — Client " + idClient + " type=" + typeDocument);
      return Map.of("filename", filename, "urlDocument", urlDocument, "typeDocument", typeDocument);
   }

   public Path getFilePath(String filename) {
      return Paths.get(this.uploadDir).resolve(filename).normalize();
   }

   private void updateStatutApresDocument(Client client) {
      List<DocumentKyc> docs = this.documentKycRepository.findByClientIdClient(client.getIdClient());
      boolean hasRecto = docs.stream().anyMatch((d) -> "CNI_RECTO".equals(d.getTypeDocument()));
      boolean hasVerso = docs.stream().anyMatch((d) -> "CNI_VERSO".equals(d.getTypeDocument()));
      if (hasRecto && hasVerso && client.getStatutKyc() == StatutKyc.PENDING) {
         client.setStatutKyc(StatutKyc.DOCUMENTS_SOUMIS);
         this.clientRepository.save(client);
      }

   }

   @Transactional(
      readOnly = true
   )
   public List getClientsEnAttenteKyc() {
      return this.clientRepository.findAll().stream().filter((c) -> c.getStatutKyc() == StatutKyc.PENDING).toList();
   }

   @Transactional(
      readOnly = true
   )
   public List getDocumentsClient(Long idClient) {
      return this.documentKycRepository.findByClientIdClient(idClient);
   }

   public Map validerKyc(Long idClient) {
      Client client = (Client)this.clientRepository.findById(idClient).orElseThrow(() -> new ResourceNotFoundException("Client", idClient));
      if (client.getStatutKyc() == StatutKyc.VALIDE) {
         throw new BusinessException("Le KYC de ce client est déjà validé.");
      } else {
         client.setStatutKyc(StatutKyc.VALIDE);
         this.clientRepository.save(client);
         this.auditService.enregistrerSysteme("KYC_VALIDE — Client " + idClient + " (" + client.getNom() + " " + client.getPrenom() + ")");
         return Map.of("message", "KYC validé avec succès", "idClient", idClient);
      }
   }

   public Map rejeterKyc(Long idClient) {
      Client client = (Client)this.clientRepository.findById(idClient).orElseThrow(() -> new ResourceNotFoundException("Client", idClient));
      client.setStatutKyc(StatutKyc.REJETE);
      this.clientRepository.save(client);
      this.auditService.enregistrerSysteme("KYC_REJETE — Client " + idClient + " (" + client.getNom() + " " + client.getPrenom() + ")");
      return Map.of("message", "KYC rejeté", "idClient", idClient);
   }

   @Generated
   public KycService(final ClientRepository clientRepository, final DocumentKycRepository documentKycRepository, final AuditService auditService) {
      this.clientRepository = clientRepository;
      this.documentKycRepository = documentKycRepository;
      this.auditService = auditService;
   }
}
