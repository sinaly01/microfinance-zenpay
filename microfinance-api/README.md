# Microfinance API — Logiciel de gestion d'opérations

Projet étudiant — gestion complète d'une microfinance (clients, comptes, transactions, rapports) avec API REST sécurisée par JWT et frontend HTML/JS.

---

## 1. Architecture

```
┌─────────────────────────────┐         ┌─────────────────────────┐
│   Frontend HTML/CSS/JS      │ ──HTTP─►│  API REST Spring Boot   │
│   (microfinance-frontend/)  │   JWT   │  (microfinance-api/)    │
└─────────────────────────────┘         └────────────┬────────────┘
                                                     │ JDBC
                                                     ▼
                                        ┌─────────────────────────┐
                                        │   Oracle XE 21c         │
                                        │   (XEPDB1)              │
                                        └─────────────────────────┘
```

- **Backend** : Java 21, Spring Boot 3.4.5, Spring Security + JWT, JPA/Hibernate, Oracle JDBC
- **Frontend** : HTML5 + CSS3 + JavaScript vanilla (fetch API)
- **Doc** : Swagger UI sur `/swagger-ui/index.html`

---

## 2. Prérequis

| Outil | Version |
|---|---|
| JDK | 21+ (testé jusqu'à 25) |
| Oracle XE | 21c |
| IntelliJ IDEA | 2025.x (Maven bundled) |
| Navigateur | Chrome / Edge / Firefox récent |

---

## 3. Installation

### a) Base de données Oracle

1. Installer Oracle XE 21c (PDB par défaut : `XEPDB1`)
2. Se connecter en SYSDBA et créer l'utilisateur :
   ```sql
   ALTER SESSION SET CONTAINER = XEPDB1;
   CREATE USER microfinance IDENTIFIED BY microfinance123;
   GRANT CONNECT, RESOURCE, UNLIMITED TABLESPACE TO microfinance;
   ```
3. Se reconnecter en `microfinance/microfinance123@//localhost:1521/XEPDB1` puis exécuter :
   ```bash
   sqlplus microfinance/microfinance123@//localhost:1521/XEPDB1 @src/main/resources/schema-oracle.sql
   ```

### b) Backend

1. Ouvrir le dossier `microfinance-api/` dans IntelliJ
2. Attendre l'import Maven
3. Panneau **Maven** (à droite, icône `m`) → `microfinance-api` → `Plugins` → `spring-boot` → double-clic sur **`spring-boot:run`**
4. Vérifier dans la console : `Started MicrofinanceApplication in X seconds`
5. Au premier démarrage, **150 clients + ~200 comptes + 600 transactions fictives** sont générés.

### c) Frontend

Aucun build nécessaire — c'est du HTML/JS pur.

**Option 1 (rapide)** : double-clic sur `microfinance-frontend/index.html`

**Option 2 (recommandée — évite les blocages CORS du protocole `file://`)** :
```bash
cd microfinance-frontend
python -m http.server 5500
```
Puis ouvrir http://localhost:5500

---

## 4. Comptes par défaut

| Rôle | Email | Mot de passe |
|---|---|---|
| Administrateur système | admin@microfinance.local | Admin@2024 |
| Gestionnaire | gestionnaire@microfinance.local | Admin@2024 |
| Administrateur BD | adminbd@microfinance.local | Admin@2024 |

Les clients fictifs ont le mot de passe `Client@2024` et un email du type `prenom.nomXX@email.com`.

---

## 5. URLs utiles

| URL | Description |
|---|---|
| http://localhost:8080/swagger-ui/index.html | Documentation interactive de l'API |
| http://localhost:8080/v3/api-docs | Spec OpenAPI brute (JSON) |
| http://localhost:5500/login.html | Frontend (si servi via Python) |

---

## 6. Endpoints principaux

| Méthode | URL | Description |
|---|---|---|
| POST | `/api/auth/login` | Connexion → JWT |
| GET | `/api/clients` | Liste des clients |
| POST | `/api/clients` | Créer un client |
| POST | `/api/comptes/ouvrir` | Ouvrir un compte (`?idClient=&depotInitial=`) |
| PUT | `/api/comptes/{id}/{action}` | valider \| bloquer \| débloquer \| suspendre \| fermer |
| POST | `/api/transactions/versement` | Versement (body : `{idCompte, montant, description}`) |
| POST | `/api/transactions/retrait` | Retrait |
| POST | `/api/transactions/virement` | Virement entre comptes |
| GET | `/api/transactions/releve/{idCompte}` | Historique d'un compte |
| GET | `/api/transactions/surveillance` | Mouvements suspects |
| POST | `/api/rapports/generer` | Générer un rapport (`?type=&debut=&fin=`) |

> Tous les endpoints (sauf `/api/auth/**`) exigent l'en-tête `Authorization: Bearer <token>`.

---

## 7. Structure du projet

```
microfinance-api/
├── src/main/java/com/microfinance/
│   ├── MicrofinanceApplication.java
│   ├── config/         (SecurityConfig, SwaggerConfig, DataInitializer, DataGenerator)
│   ├── controller/     (REST controllers)
│   ├── dto/            (DTO Request/Response avec validation)
│   ├── exception/      (GlobalExceptionHandler)
│   ├── model/          (Entités JPA — héritage SINGLE_TABLE pour Transaction)
│   ├── repository/     (Spring Data JPA)
│   ├── security/       (JwtFilter, JwtUtil, UserDetailsService)
│   └── service/        (Logique métier)
├── src/main/resources/
│   ├── application.properties
│   └── schema-oracle.sql
├── pom.xml
├── README.md           ← ce fichier
└── CONTEXTE.md         ← document de transfert / handover

microfinance-frontend/
├── index.html          (redirection)
├── login.html
├── dashboard.html
├── clients.html
├── comptes.html
├── transactions.html
├── css/style.css
└── js/api.js           (wrapper fetch + JWT)
```

---

## 8. Sécurité

- **JWT** signé HS256, expiration 24h (configurable via `jwt.expiration` ms)
- **BCrypt** pour les mots de passe
- **Sessions stateless** (`SessionCreationPolicy.STATELESS`)
- **CORS** autorise `http://localhost*` et `http://192.168.1.*` (déploiement réseau)
- **Rôles** : ROLE_ADMIN_SYSTEME, ROLE_GESTIONNAIRE, ROLE_ADMIN_BD, ROLE_CLIENT (`@PreAuthorize` sur les controllers)

---

## 9. Déploiement réseau

Pour déployer sur la machine serveur du groupe (ex: 192.168.1.20) :

1. Dans `application.properties`, commenter la ligne `localhost` et décommenter :
   ```properties
   spring.datasource.url=jdbc:oracle:thin:@192.168.1.20:1521/XEPDB1
   ```
2. Vérifier que le pare-feu Windows autorise le port 1521 (Oracle) et 8080 (API)
3. Sur les machines clientes, modifier `microfinance-frontend/js/api.js` :
   ```js
   const API_BASE = "http://192.168.1.20:8080";
   ```

---

## 10. Pannes courantes

| Symptôme | Solution |
|---|---|
| `Port 8080 already in use` | `netstat -ano \| findstr :8080` puis `taskkill /PID <pid> /F` |
| Login renvoie 401 | Vérifier qu'`DataInitializer` s'est bien exécuté (logs au démarrage) |
| Swagger UI page blanche | Vérifier `/webjars/**` dans `permitAll()` de `SecurityConfig` |
| `ORA-00001` au démarrage | Voir `CONTEXTE.md` § 8 (déjà résolu via UUID) |
| Frontend "Erreur réseau" | Vérifier que le backend tourne, ouvrir le frontend via `http://localhost:5500` (pas `file://`) |

---

## 11. Auteurs

Projet réalisé en groupe — étudiants en informatique 2026.

Voir `CONTEXTE.md` pour le détail technique et l'historique du projet.
