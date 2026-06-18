package com.microfinance.repository;

import com.microfinance.model.enums.StatutCompte;
import java.util.List;
import java.util.Optional;
import org.springframework.data.jpa.repository.JpaRepository;
import org.springframework.data.jpa.repository.Query;
import org.springframework.data.repository.query.Param;
import org.springframework.stereotype.Repository;

@Repository
public interface CompteRepository extends JpaRepository {
   Optional findByNumeroCompte(String numeroCompte);

   List findByClientIdClient(Long idClient);

   List findByStatut(StatutCompte statut);

   @Query("SELECT c FROM Compte c WHERE c.client.idClient = :idClient AND c.statut = :statut")
   List findByClientAndStatut(@Param("idClient") Long idClient, @Param("statut") StatutCompte statut);

   boolean existsByNumeroCompte(String numeroCompte);
}
