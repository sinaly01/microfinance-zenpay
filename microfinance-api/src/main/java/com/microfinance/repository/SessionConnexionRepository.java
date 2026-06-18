package com.microfinance.repository;

import java.util.List;
import java.util.Optional;
import org.springframework.data.jpa.repository.JpaRepository;

public interface SessionConnexionRepository extends JpaRepository {
   List findByGestionnaire_IdGestionnaireAndStatutSession(Long id, String statut);

   List findByClient_IdClientAndStatutSession(Long id, String statut);

   Optional findByJtiTokenAndStatutSession(String jti, String statut);
}
