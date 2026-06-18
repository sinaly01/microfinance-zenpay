package com.microfinance.config;

import lombok.Generated;
import org.slf4j.Logger;
import org.slf4j.LoggerFactory;
import org.springframework.boot.CommandLineRunner;
import org.springframework.core.annotation.Order;
import org.springframework.jdbc.core.JdbcTemplate;
import org.springframework.stereotype.Component;

@Component
@Order(0)
public class SchemaFixRunner implements CommandLineRunner {
   @Generated
   private static final Logger log = LoggerFactory.getLogger(SchemaFixRunner.class);
   private final JdbcTemplate jdbc;

   public void run(String... args) {
      this.dropLegacyEnumCheckConstraint("GESTIONNAIRES");
      this.dropLegacyEnumCheckConstraint("CLIENTS");
   }

   private void dropLegacyEnumCheckConstraint(String tableName) {
      for(String constraintName : this.jdbc.queryForList("SELECT uc.constraint_name FROM user_constraints uc JOIN user_cons_columns ucc ON uc.constraint_name = ucc.constraint_name WHERE uc.table_name     = ?   AND uc.constraint_type = 'C'   AND ucc.column_name   = 'ROLE'   AND uc.constraint_name LIKE 'SYS_C%'", String.class, new Object[]{tableName})) {
         try {
            this.jdbc.execute("ALTER TABLE " + tableName + " DROP CONSTRAINT " + constraintName);
            log.info("  ✓ Contrainte obsolète {} supprimée de {} (valeurs enum étendues)", constraintName, tableName);
         } catch (Exception ex) {
            log.warn("  ⚠ Impossible de supprimer {} sur {} : {}", new Object[]{constraintName, tableName, ex.getMessage()});
         }
      }

   }

   @Generated
   public SchemaFixRunner(final JdbcTemplate jdbc) {
      this.jdbc = jdbc;
   }
}
