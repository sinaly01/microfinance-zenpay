# CONTEXTE — Projet Microfinance API

> Document de transfert : à fournir à un autre Claude / collègue pour reprendre le projet sans perdre l'historique.

---

## 1. Objectif du projet

Logiciel de gestion d'opérations pour une microfinance (style BCEAO Afrique de l'Ouest), réalisé en groupe (5 étudiants).

Composants :
- **Backend** : API REST Java Spring Boot + Oracle XE (CRUD clients, comptes, transactions, rapports)
- **Frontend** : pages HTML/CSS/JS pures consommant l'API via `fetch()`
- **Sécurité** : JWT (Bearer token) + Spring Security
- **Données fictives** : 150 clients, ~200 comptes, 600 transactions générées au démarrage

---

## 2. Stack technique

| Couche | Choix | Version |
|---|---|---|
| Langage | Java | 21 (JDK 25 installé sur la machine, mais `<java.version>21</java.version>` dans pom.xml) |
| Framework | Spring Boot | 3.4.5 |
| ORM | Spring Data JPA + Hibernate | 6.6.13 |
| BDD | Oracle XE | 21c (PDB : `XEPDB1`) |
| Sécurité | Spring Security + JWT (jjwt) | 0.11.5 |
| Doc API | springdoc-openapi (Swagger UI) | 2.8.8 |
| Lombok | annotation processor | 1.18.36 |
| Build | Maven (bundled IntelliJ) | — |
| IDE | IntelliJ IDEA | 2025.3.2 |
| Frontend | HTML / CSS / JS vanilla | — |

---

## 3. Localisation du projet

```
C:\Users\anojo\microfinance-api\          ← backend Spring Boot
C:\Users\anojo\microfinance-frontend\     ← frontend HTML/JS
```

---

## 4. Connexion Oracle

```properties
spring.datasource.url=jdbc:oracle:thin:@localhost:1521/XEPDB1
spring.datasource.username=microfinance
spring.datasource.password=microfinance123
```

> Pour le déploiement réseau (machine serveur sur 192.168.1.20), décommenter la 2e ligne `spring.datasource.url` dans `application.properties`.

Tables créées par `schema-oracle.sql` :
- `CLIENTS`, `GESTIONNAIRES`, `COMPTES`, `TRANSACTIONS` (single-table inheritance), `RAPPORTS`
- 5 séquences : `CLIENT_SEQ`, `GESTIONNAIRE_SEQ`, `COMPTE_SEQ`, `TRANSACTION_SEQ`, `RAPPORT_SEQ`

---

## 5. Comptes par défaut (créés par `DataInitializer.java`)

### 5.1 Gestionnaires

| Rôle | Email | Mot de passe | Redirection après login |
|---|---|---|---|
| ADMIN_SYSTEME | admin@microfinance.local | Admin@2024 | `dashboard.html` |
| GESTIONNAIRE | gestionnaire@microfinance.local | Admin@2024 | `dashboard.html` |
| ADMIN_BD | adminbd@microfinance.local | Admin@2024 | `dashboard.html` |

### 5.2 Clients

| Rôle | Email | Mot de passe | Redirection après login |
|---|---|---|---|
| CLIENT (test) | client@microfinance.local | Client@2024 | `client-dashboard.html` |
| CLIENT (fictif) | prenom.nomNN@email.com | Client@2024 | `client-dashboard.html` |

> Les 150 clients fictifs sont générés par `DataGenerator` au premier démarrage. Pour trouver un email valide, regarder dans la table `CLIENTS` ou utiliser `client@microfinance.local` qui est créé par `DataInitializer`.

---

## 6. Comment lancer

### Backend
1. Vérifier qu'Oracle XE tourne (service Windows `OracleServiceXE`)
2. Dans IntelliJ → panneau **Maven** (icône `m` à droite) → `microfinance-api` → `Plugins` → `spring-boot` → double-clic sur **`spring-boot:run`**
3. Attendre `Started MicrofinanceApplication in X seconds`
4. Swagger : http://localhost:8080/swagger-ui/index.html

### Frontend
- Ouvrir `microfinance-frontend/index.html` dans un navigateur, OU
- Servir via Apache/IIS, OU
- `python -m http.server 5500` dans le dossier frontend

### Si le port 8080 est occupé
```powershell
netstat -ano | findstr :8080
taskkill /PID <PID> /F
```

---

## 7. Endpoints API (résumé)

Base : `http://localhost:8080`

| Méthode | URL | Rôle |
|---|---|---|
| POST | `/api/auth/login` | { email, motDePasse } → { token, type, email, role, expiresIn } |
| GET/POST/PUT/DELETE | `/api/clients[/id]` | CRUD clients |
| POST | `/api/comptes/ouvrir?idClient=&depotInitial=` | Ouvrir compte |
| PUT | `/api/comptes/{id}/{valider\|bloquer\|debloquer\|suspendre\|fermer}` | Cycle de vie compte |
| GET | `/api/comptes`, `/api/comptes/{id}`, `/api/comptes/client/{idClient}` | Lecture |
| POST | `/api/transactions/{versement\|retrait\|virement}` | Opérations |
| GET | `/api/transactions/releve/{idCompte}[/periode?debut=&fin=]` | Relevés |
| GET | `/api/transactions/surveillance` | Détection mouvements suspects |
| POST | `/api/rapports/generer?type=&debut=&fin=` | Génération rapport |
| GET | `/api/rapports` | Liste rapports |

> Tous les endpoints (sauf `/api/auth/**` et Swagger) exigent l'en-tête `Authorization: Bearer <token>`.

---

## 8. Pièges déjà rencontrés (et résolus)

| Symptôme | Cause | Fix |
|---|---|---|
| `ExceptionInInitializerError: TypeTag :: UNKNOWN` | Lombok < 1.18.36 incompatible JDK 25 | Lombok 1.18.36 + Spring Boot 3.4.5 + Java 21 dans pom.xml |
| `Port 8080 already in use` | App pas correctement arrêtée | `taskkill /PID xxx /F` + `server.shutdown=graceful` |
| Login renvoie 401 alors que le mot de passe semble bon | Hash BCrypt en dur dans `schema-oracle.sql` (placeholder, pas un vrai hash) | `DataInitializer` crée les comptes avec `passwordEncoder.encode()` |
| `/v3/api-docs` → 500 | (1) springdoc 2.5.0 incompatible Spring Boot 3.4.5, (2) `UserDetails.getPassword()` exposé en JSON et références circulaires JPA | (1) springdoc 2.8.8, (2) `@JsonIgnore` sur mots de passe et relations bidirectionnelles |
| Swagger UI page blanche | `/webjars/**` non autorisé dans SecurityConfig | Ajouter `/webjars/**` à `permitAll()` |
| `ORA-00001` sur `NUMERO_COMPTE` (table COMPTES) | `Compte.genererNumeroCompte()` utilisait `System.currentTimeMillis()` → doublons en batch | Remplacé par `UUID.randomUUID()` |
| `ORA-00001` sur `REFERENCE` (table TRANSACTIONS) | `Transaction.@PrePersist` écrasait la `reference` définie par DataGenerator avec `System.currentTimeMillis()` | `prePersist` rend la génération conditionnelle (`if null`) + UUID |

---

## 9. Architecture du code (backend)

```
src/main/java/com/microfinance/
├── MicrofinanceApplication.java        ← @SpringBootApplication
├── config/
│   ├── SecurityConfig.java             ← filterChain, CORS, BCrypt
│   ├── SwaggerConfig.java              ← OpenAPI bean (Bearer JWT)
│   ├── DataInitializer.java            ← @Order(1) — gestionnaires
│   └── DataGenerator.java              ← @Order(2) — données fictives
├── controller/                         ← REST controllers
├── dto/                                ← Request/Response DTOs (validation Jakarta)
├── exception/
│   └── GlobalExceptionHandler.java     ← @RestControllerAdvice
├── model/                              ← entités JPA (@Entity)
│   ├── Client.java                     ← UserDetails
│   ├── Gestionnaire.java               ← UserDetails
│   ├── Compte.java
│   ├── Transaction.java                ← @Inheritance(SINGLE_TABLE)
│   ├── Versement.java / Retrait.java / Virement.java
│   ├── Rapport.java
│   └── enums/                          ← TypeTransaction, StatutCompte, etc.
├── repository/                         ← JpaRepository
├── security/
│   ├── JwtFilter.java                  ← OncePerRequestFilter
│   ├── JwtUtil.java                    ← génération/validation HS256
│   └── UserDetailsServiceImpl.java     ← Gestionnaire OR Client par email
└── service/                            ← logique métier (@Service)
```

---

## 10. Génération de données fictives (`DataGenerator.java`)

- Garde-fou : ne génère que si `clientRepository.count() <= 5` (pour ne pas dupliquer entre redémarrages).
- Utilise `Random(42)` pour des données reproductibles.
- Noms / prénoms / villes ouest-africains réalistes.
- Comptes : 80 % ACTIF, 10 % SUSPENDU, 10 % BLOQUE — 33 % des clients ont 2 comptes.
- Transactions : ~50 % versements, 30 % retraits, 20 % virements.

**Pour reset les données fictives** (SQL*Plus, user microfinance) :
```sql
DELETE FROM TRANSACTIONS;
DELETE FROM COMPTES;
DELETE FROM CLIENTS WHERE ROLE = 'ROLE_CLIENT';
COMMIT;
```

---

## 11. État d'avancement (à la date de transfert : 2026-05-06)

- ✅ Backend complet, démarre, données fictives générées
- ✅ Swagger UI accessible
- ✅ Login JWT testé OK
- 🔄 Frontend HTML/JS en cours
- ⬜ Tests unitaires
- ⬜ Déploiement réseau (192.168.1.20)
- ⬜ Livrable final (zip + rapport PDF)

---

## 12. Mémoires Claude pertinentes

Le user Claude possède déjà ces mémoires (dans `C:\Users\anojo\.claude\projects\C--Users-anojo\memory\`) :
- `project_microfinance.md` : pointeur vers ce projet

---

## 13. Pour reprendre avec un nouveau Claude

Coller ce prompt initial :

> Je reprends un projet Spring Boot + Oracle de microfinance. Tout le contexte est dans `C:\Users\anojo\microfinance-api\CONTEXTE.md`. Lis ce fichier en entier, puis aide-moi à continuer. État actuel : [décrire ce qu'il faut faire].
