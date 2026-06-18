package com.microfinance.repository;

import java.util.List;
import org.springframework.data.jpa.repository.JpaRepository;
import org.springframework.stereotype.Repository;

@Repository
public interface MessageSupportRepository extends JpaRepository {
   List findByClient_IdClientOrderByDateEnvoiAsc(Long idClient);

   boolean existsByClient_IdClient(Long idClient);

   long countByClient_IdClientAndLuFalseAndExpediteur(Long idClient, String expediteur);
}
