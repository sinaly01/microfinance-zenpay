package com.microfinance.repository;

import com.microfinance.model.enums.StatutTicket;
import java.util.List;
import org.springframework.data.jpa.repository.JpaRepository;

public interface TicketReclamationRepository extends JpaRepository {
   List findByClientIdClientOrderByDateCreationDesc(Long idClient);

   List findByStatutOrderByDateCreationDesc(StatutTicket statut);

   List findAllByOrderByDateCreationDesc();
}
