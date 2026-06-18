-- =====================================================================
-- DUMP de la base Microfinance / ZEN-PAY
-- À exécuter via SQL*Plus ou DBeaver après création de l'utilisateur.
-- Génère ce fichier avec le script export-data.sql dans le même dossier.
-- =====================================================================

-- ⚠️ NE PAS exécuter en production. Réservé aux environnements de test.

-- 1. Vider les tables existantes (ordre des FK)
DELETE FROM RAPPORTS;
DELETE FROM TRANSACTIONS;
DELETE FROM COMPTES;
DELETE FROM CLIENTS;
DELETE FROM GESTIONNAIRES;
COMMIT;

-- 2. Réinitialiser les séquences (optionnel)
-- ALTER SEQUENCE CLIENT_SEQ RESTART START WITH 1;
-- ALTER SEQUENCE GESTIONNAIRE_SEQ RESTART START WITH 1;
-- ALTER SEQUENCE COMPTE_SEQ RESTART START WITH 1;
-- ALTER SEQUENCE TRANSACTION_SEQ RESTART START WITH 1;
-- ALTER SEQUENCE RAPPORT_SEQ RESTART START WITH 1;

-- 3. Les données seront générées automatiquement par DataInitializer.java
--    + DataGenerator.java au démarrage de Spring Boot.
--    => Inutile d'inclure les INSERT ici, ils sont générés à la volée.

-- 4. Si tu veux quand même un dump complet (production-like), exécute :
--    SET SQLFORMAT INSERT
--    SPOOL dump-genere.sql
--    SELECT * FROM CLIENTS;
--    SELECT * FROM GESTIONNAIRES;
--    SELECT * FROM COMPTES;
--    SELECT * FROM TRANSACTIONS;
--    SELECT * FROM RAPPORTS;
--    SPOOL OFF;

COMMIT;
