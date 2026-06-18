package com.microfinance.repository;

import java.util.List;
import java.util.Optional;
import org.springframework.data.jpa.repository.JpaRepository;

public interface DemandeAccesExterieurRepository extends JpaRepository {
   List findByStatutOrderByDateCreationDesc(String statut);

   Optional findTopByGestionnaire_IdGestionnaireAndAdresseIpOrderByDateCreationDesc(Long idGestionnaire, String adresseIp);
}
