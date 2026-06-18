package com.microfinance.repository;

import java.util.Optional;
import org.springframework.data.jpa.repository.JpaRepository;
import org.springframework.stereotype.Repository;

@Repository
public interface ClientRepository extends JpaRepository {
   Optional findByEmail(String email);

   Optional findByTelephone(String telephone);

   boolean existsByEmail(String email);

   boolean existsByTelephone(String telephone);

   boolean existsByNumeroCni(String numeroCni);
}
