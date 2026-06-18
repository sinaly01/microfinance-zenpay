# 🐳 ZEN-PAY · Stack Docker

Orchestration complète du projet ZEN-PAY (Microfinance) :
**Oracle XE 21c** + **Spring Boot 3.4.5** + **PHP 8.2 / Apache** dans 3 conteneurs reliés par un réseau Docker.

> *Votre argent, partout, simplement.*

---

## 📐 Architecture

```
                           Réseau zenpay-net (bridge)
   ┌──────────────────────────────────────────────────────────┐
   │                                                          │
   │  ┌───────────────┐   ┌────────────────┐   ┌───────────┐  │
   │  │   oracle      │←──│   spring-api   │←──│ php-front │  │
   │  │  (Oracle XE)  │   │ (Spring Boot)  │   │   (PHP)   │  │
   │  │   port 1521   │   │   port 8080    │   │  port 80  │  │
   │  └───────────────┘   └────────────────┘   └───────────┘  │
   │                                                          │
   └──────────────────────────────────────────────────────────┘
            ▲                    ▲                  ▲
            │                    │                  │
       hôte 1521            hôte 8080           hôte 5502
                                                (navigateur)
```

| Service | Image | Port hôte | Port conteneur |
|---|---|---|---|
| `oracle` | `gvenzl/oracle-xe:21-slim` | 1521 | 1521 |
| `spring-api` | build local | 8080 | 8080 |
| `php-front` | build local | 5502 | 80 |

---

## 🚀 Démarrage rapide

### Prérequis
- **Docker Desktop** installé et lancé ([download](https://www.docker.com/products/docker-desktop))
- 4 Go de RAM dispo (Oracle XE en consomme ~2 Go)

### Étape 1 — Cloner le projet

Tes coéquipiers doivent avoir cette arborescence :
```
microfinance-projet/
├── microfinance-api/             ← backend Spring Boot
├── microfinance-frontend-php/    ← frontend PHP
└── microfinance-docker/          ← ce dossier (compose)
```

### Étape 2 — Configurer (optionnel)
```bash
cd microfinance-docker
cp .env.example .env
# Édite .env si tu veux changer les mots de passe
```

### Étape 3 — Lancer

```bash
docker compose up -d --build
```

⏱️ **Premier démarrage : ~5 minutes** (download de l'image Oracle ~1.5 Go + initialisation BD).

Pour suivre l'init en direct :
```bash
docker compose logs -f oracle
# Attends "DATABASE IS READY TO USE!"
```

### Étape 4 — Vérifier

| Service | URL |
|---|---|
| 🌐 **Frontend ZEN-PAY** | http://localhost:5502 |
| 🚀 **API REST** | http://localhost:8080 |
| 📘 **Swagger UI** | http://localhost:8080/swagger-ui/index.html |
| 🗄️ **Oracle (DBeaver)** | `localhost:1521/XEPDB1` user `microfinance` mdp `microfinance123` |

---

## 🛠️ Commandes utiles

```bash
# Voir les logs d'un service
docker compose logs -f spring-api
docker compose logs -f php-front
docker compose logs -f oracle

# Statut des conteneurs
docker compose ps

# Redémarrer un service après modif de code
docker compose up -d --build spring-api

# Tout arrêter (préserve les données)
docker compose down

# Tout effacer (⚠️ supprime aussi les données Oracle)
docker compose down -v

# Console SQL Oracle dans le conteneur
docker exec -it zenpay-oracle sqlplus microfinance/microfinance123@//localhost:1521/XEPDB1

# Shell dans le conteneur Spring
docker exec -it zenpay-api sh

# Shell dans le conteneur PHP
docker exec -it zenpay-front bash
```

---

## 🔐 Comptes de démonstration (générés automatiquement)

| Rôle | Email | Mot de passe |
|---|---|---|
| Admin Système | admin@microfinance.local | Admin@2024 |
| Gestionnaire | gestionnaire@microfinance.local | Admin@2024 |
| Admin BD | adminbd@microfinance.local | Admin@2024 |
| **Client test** | **client@microfinance.local** | **Client@2024** |

➕ 150 clients fictifs avec mdp `Client@2024` (générés par `DataGenerator`).

---

## 🌍 Pour un déploiement LAN (cahier des charges)

Si vous voulez héberger sur **2 machines distinctes** dans le réseau du groupe (cf. cahier des charges) :

### Machine 1 (serveur BD) — IP fixe `192.168.1.20`
```bash
docker compose up -d oracle
# Ou installer Oracle XE natif si déjà en place
```

### Machine 2 (serveur app) — IP fixe `192.168.1.21`
Édite le `docker-compose.yml` :
```yaml
spring-api:
  environment:
    SPRING_DATASOURCE_URL: jdbc:oracle:thin:@192.168.1.20:1521/XEPDB1
```
Puis :
```bash
docker compose up -d --build spring-api php-front
```

### Postes clients
Aucune installation. Ouvrir : `http://192.168.1.21:5502`

Si l'API n'est pas sur la même machine que le PHP, ouvrir la console du navigateur (F12) sur la page de login :
```js
localStorage.setItem("mf_api_base", "http://192.168.1.21:8080");
location.reload();
```

---

## 🐛 Dépannage

| Symptôme | Solution |
|---|---|
| `oracle: error while loading shared libraries` | Image Oracle ARM ↔ AMD64 incompatible. Utilise `gvenzl/oracle-xe:21-slim-faststart` ou installe `--platform linux/amd64` |
| Spring Boot crash : `ORA-12541 No listener` | Oracle pas encore prêt. Le `depends_on: condition: service_healthy` devrait gérer, sinon `docker compose restart spring-api` |
| Port 1521 / 8080 / 5502 already in use | Arrête tes services locaux : `Get-Process -Id (Get-NetTCPConnection -LocalPort 1521).OwningProcess \| Stop-Process` |
| Frontend ne joint pas l'API | Vérifie l'URL en console : `localStorage.getItem("mf_api_base")` — par défaut `http://localhost:8080` |
| Oracle prend > 5 min au premier boot | Normal sur HDD. SSD recommandé. Suivre via `docker compose logs -f oracle` |
| Image Oracle download bloqué | Connexion réseau lente ou proxy. L'image fait ~1.5 Go |

---

## 📂 Structure des fichiers

```
microfinance-docker/
├── docker-compose.yml         ← orchestre les 3 services
├── .env.example               ← variables (mdp, secrets)
├── .env                       ← copie locale (à NE PAS commiter)
├── oracle-init/               ← scripts SQL exécutés au 1er boot Oracle
└── README.md                  ← ce fichier

microfinance-api/
├── Dockerfile                 ← build multi-stage : maven → JRE 21 alpine
├── .dockerignore
└── src/main/resources/application.properties  ← env vars SPRING_DATASOURCE_*

microfinance-frontend-php/
├── Dockerfile                 ← php:8.2-apache
└── .dockerignore
```

---

## 🔁 Workflow équipe

1. **Tirer les dernières modifs** :
   ```bash
   git pull
   ```
2. **Reconstruire après changement de code** :
   ```bash
   docker compose up -d --build
   ```
3. **Pousser ses changements** :
   ```bash
   git add .
   git commit -m "feat: ..."
   git push
   ```

> ⚠️ Le volume `zenpay-oracle-data` est **local** à chaque machine. Les données ne se synchronisent pas via Git. Si un membre veut voir les vraies données d'un autre, il faut soit :
> - Tourner sur la **même** instance (LAN)
> - Faire un **dump** Oracle (`expdp`) et l'importer
> - Ou laisser `DataGenerator` recréer les 150 clients fictifs (déterministe via `Random(42)`)

---

## ✅ Avantages de cette config

- **Aucune install locale** côté coéquipiers (ni Oracle, ni Java, ni PHP)
- **Reproductible** sur Windows / macOS / Linux
- **Isolé** : pas de pollution du système hôte
- **Cohérent** avec le déploiement LAN final demandé par le cahier
- **Versionnable** : tout le setup est dans Git
