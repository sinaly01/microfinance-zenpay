Ce dossier est monté dans /container-entrypoint-initdb.d/ du conteneur Oracle.

Tout fichier .sql présent ici sera exécuté au PREMIER démarrage du conteneur,
après la création de l'utilisateur APP_USER (microfinance par défaut).

Pour ce projet, on laisse Spring Boot créer les tables via ddl-auto=update.
DataInitializer + DataGenerator se chargent du peuplement de données fictives.

Si tu veux forcer un schéma SQL custom, dépose ton fichier .sql ici (il sera
exécuté en tant que microfinance/microfinance123 sur la PDB XEPDB1).
