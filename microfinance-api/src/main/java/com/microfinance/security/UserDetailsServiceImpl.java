package com.microfinance.security;

import com.microfinance.repository.ClientRepository;
import com.microfinance.repository.GestionnaireRepository;
import lombok.Generated;
import org.springframework.security.core.userdetails.UserDetails;
import org.springframework.security.core.userdetails.UserDetailsService;
import org.springframework.security.core.userdetails.UsernameNotFoundException;
import org.springframework.stereotype.Service;

@Service
public class UserDetailsServiceImpl implements UserDetailsService {
   private final ClientRepository clientRepository;
   private final GestionnaireRepository gestionnaireRepository;

   public UserDetails loadUserByUsername(String identifier) throws UsernameNotFoundException {
      return (UserDetails)this.gestionnaireRepository.findByEmail(identifier).map((g) -> g).or(() -> this.clientRepository.findByEmail(identifier).map((c) -> c)).or(() -> this.clientRepository.findByTelephone(identifier).map((c) -> c)).orElseThrow(() -> new UsernameNotFoundException("Utilisateur introuvable : " + identifier));
   }

   @Generated
   public UserDetailsServiceImpl(final ClientRepository clientRepository, final GestionnaireRepository gestionnaireRepository) {
      this.clientRepository = clientRepository;
      this.gestionnaireRepository = gestionnaireRepository;
   }
}
