package com.microfinance.repository;

import java.util.List;
import org.springframework.data.jpa.repository.JpaRepository;
import org.springframework.stereotype.Repository;

@Repository
public interface SuggestionModifProfilRepository extends JpaRepository {
   List findByStatutOrderByDateDemandeDesc(String statut);

   List findByClientIdClientOrderByDateDemandeDesc(Long idClient);
}
