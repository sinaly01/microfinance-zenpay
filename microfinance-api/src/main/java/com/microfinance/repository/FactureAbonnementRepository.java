package com.microfinance.repository;

import java.util.List;
import org.springframework.data.jpa.repository.JpaRepository;

public interface FactureAbonnementRepository extends JpaRepository {
   List findByClientIdClientOrderByDatePrelevementDesc(Long idClient);
}
