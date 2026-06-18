package com.microfinance.repository;

import java.util.List;
import org.springframework.data.jpa.repository.JpaRepository;

public interface LogAuditRepository extends JpaRepository {
   List findTop100ByOrderByDateHeureDesc();
}
