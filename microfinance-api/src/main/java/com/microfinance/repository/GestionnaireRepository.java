package com.microfinance.repository;

import com.microfinance.model.enums.RoleUtilisateur;
import java.util.List;
import java.util.Optional;
import org.springframework.data.jpa.repository.JpaRepository;
import org.springframework.stereotype.Repository;

@Repository
public interface GestionnaireRepository extends JpaRepository {
   Optional findByEmail(String email);

   List findByRole(RoleUtilisateur role);

   boolean existsByEmail(String email);
}
