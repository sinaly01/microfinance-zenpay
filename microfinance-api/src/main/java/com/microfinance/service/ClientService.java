package com.microfinance.service;

import com.microfinance.dto.request.ClientRequest;
import com.microfinance.dto.response.ClientResponse;
import com.microfinance.exception.BusinessException;
import com.microfinance.exception.ResourceNotFoundException;
import com.microfinance.model.Client;
import com.microfinance.model.enums.RoleUtilisateur;
import com.microfinance.repository.ClientRepository;
import java.util.List;
import lombok.Generated;
import org.springframework.security.crypto.password.PasswordEncoder;
import org.springframework.stereotype.Service;
import org.springframework.transaction.annotation.Transactional;

@Service
@Transactional
public class ClientService {
   private final ClientRepository clientRepository;
   private final PasswordEncoder passwordEncoder;

   public ClientResponse creerClient(ClientRequest req) {
      if (this.clientRepository.existsByEmail(req.email())) {
         throw new BusinessException("Un client avec cet email existe déjà");
      } else if (this.clientRepository.existsByTelephone(req.telephone())) {
         throw new BusinessException("Un client avec ce numéro de téléphone existe déjà");
      } else {
         Client client = Client.builder().nom(req.nom()).prenom(req.prenom()).telephone(req.telephone()).email(req.email()).adresse(req.adresse()).numeroCni(req.numeroCni()).motDePasse(this.passwordEncoder.encode(req.motDePasse())).role(RoleUtilisateur.ROLE_CLIENT).actif(true).build();
         return ClientResponse.from((Client)this.clientRepository.save(client));
      }
   }

   @Transactional(
      readOnly = true
   )
   public ClientResponse getClient(Long id) {
      return ClientResponse.from(this.findOrThrow(id));
   }

   @Transactional(
      readOnly = true
   )
   public List listerClients() {
      return this.clientRepository.findAll().stream().map(ClientResponse::from).toList();
   }

   public ClientResponse mettreAJour(Long id, ClientRequest req) {
      Client client = this.findOrThrow(id);
      client.setNom(req.nom());
      client.setPrenom(req.prenom());
      client.setAdresse(req.adresse());
      client.setTelephone(req.telephone());
      if (req.email() != null) {
         client.setEmail(req.email());
      }

      return ClientResponse.from((Client)this.clientRepository.save(client));
   }

   public void desactiverClient(Long id) {
      Client client = this.findOrThrow(id);
      client.setActif(false);
      this.clientRepository.save(client);
   }

   private Client findOrThrow(Long id) {
      return (Client)this.clientRepository.findById(id).orElseThrow(() -> new ResourceNotFoundException("Client", id));
   }

   @Generated
   public ClientService(final ClientRepository clientRepository, final PasswordEncoder passwordEncoder) {
      this.clientRepository = clientRepository;
      this.passwordEncoder = passwordEncoder;
   }
}
