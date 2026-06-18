package com.microfinance.repository;

import java.util.List;
import org.springframework.data.jpa.repository.JpaRepository;
import org.springframework.stereotype.Repository;

@Repository
public interface DemandeChangementOffreRepository extends JpaRepository {
   List findByStatutOrderByDateCreationDesc(String statut);

   List findAllByOrderByDateCreationDesc();

   List findByClient_IdClientOrderByDateCreationDesc(Long idClient);
}
