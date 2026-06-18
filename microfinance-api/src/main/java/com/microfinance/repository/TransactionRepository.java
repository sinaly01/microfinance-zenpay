package com.microfinance.repository;

import com.microfinance.model.enums.StatutTransaction;
import java.time.LocalDateTime;
import java.util.List;
import java.util.Optional;
import org.springframework.data.jpa.repository.JpaRepository;
import org.springframework.data.jpa.repository.Query;
import org.springframework.data.repository.query.Param;
import org.springframework.stereotype.Repository;

@Repository
public interface TransactionRepository extends JpaRepository {
   List findByCompteIdCompteOrderByDateHeureDesc(Long idCompte);

   Optional findByReference(String reference);

   List findByStatut(StatutTransaction statut);

   @Query("SELECT t FROM Transaction t WHERE t.compte.idCompte = :idCompte AND t.dateHeure BETWEEN :debut AND :fin ORDER BY t.dateHeure DESC")
   List findByCompteAndPeriode(@Param("idCompte") Long idCompte, @Param("debut") LocalDateTime debut, @Param("fin") LocalDateTime fin);

   @Query("SELECT t FROM Transaction t WHERE t.dateHeure BETWEEN :debut AND :fin")
   List findByPeriode(@Param("debut") LocalDateTime debut, @Param("fin") LocalDateTime fin);
}
