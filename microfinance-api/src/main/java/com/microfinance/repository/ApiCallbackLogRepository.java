package com.microfinance.repository;

import java.util.List;
import org.springframework.data.jpa.repository.JpaRepository;

public interface ApiCallbackLogRepository extends JpaRepository {
   List findByTransaction_IdTransactionOrderByDateReceptionDesc(Long idTransaction);
}
