package com.microfinance.repository;

import java.time.LocalDate;
import java.util.List;
import org.springframework.data.jpa.repository.JpaRepository;
import org.springframework.stereotype.Repository;

@Repository
public interface RapportRepository extends JpaRepository {
   List findByType(String type);

   List findByPeriodeDebutGreaterThanEqualAndPeriodeFinLessThanEqual(LocalDate debut, LocalDate fin);
}
