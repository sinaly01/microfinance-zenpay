package com.microfinance.repository;

import java.util.List;
import org.springframework.data.jpa.repository.JpaRepository;

public interface DocumentKycRepository extends JpaRepository {
   List findByClientIdClient(Long idClient);
}
