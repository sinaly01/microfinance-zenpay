package com.microfinance.repository;

import java.util.Optional;
import org.springframework.data.jpa.repository.JpaRepository;

public interface AdresseIpAutoriseeRepository extends JpaRepository {
   Optional findByAdresseIpAndEstActiveTrue(String adresseIp);

   boolean existsByAdresseIpAndEstActiveTrue(String adresseIp);
}
