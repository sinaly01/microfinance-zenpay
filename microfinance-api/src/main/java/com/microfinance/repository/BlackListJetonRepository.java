package com.microfinance.repository;

import java.time.LocalDateTime;
import org.springframework.data.jpa.repository.JpaRepository;
import org.springframework.data.jpa.repository.Modifying;
import org.springframework.data.jpa.repository.Query;
import org.springframework.data.repository.query.Param;

public interface BlackListJetonRepository extends JpaRepository {
   boolean existsByValeurJeton(String valeurJeton);

   @Modifying
   @Query("DELETE FROM BlackListJeton j WHERE j.dateExpiration < :now")
   int deleteByDateExpirationBefore(@Param("now") LocalDateTime now);
}
