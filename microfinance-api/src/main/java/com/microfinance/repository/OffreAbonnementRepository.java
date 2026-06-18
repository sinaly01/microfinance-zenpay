package com.microfinance.repository;

import java.util.Optional;
import org.springframework.data.jpa.repository.JpaRepository;

public interface OffreAbonnementRepository extends JpaRepository {
   Optional findByNomOffre(String nomOffre);
}
