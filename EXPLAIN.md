# ZEN-PAY — Documentation complète

## Qu'est-ce que ZEN-PAY ?

ZEN-PAY est une plateforme de microfinance académique composée de trois couches :

| Composant | Technologie | Rôle |
|---|---|---|
| Base de données | Oracle XE 21c | Stockage des comptes, transactions, clients |
| API back-end | Spring Boot 3.4.5 (Java) | Logique métier, sécurité JWT, envoi d'emails |
| Interface web | PHP 8.2 + Apache | Pages HTML servies au navigateur |

---

## Architecture de déploiement

```
Navigateur de l'utilisateur
        │
        ▼
[Cloudflare Tunnel]  ←  lancer-tunnel.bat
        │
        ▼
localhost:5502  →  Conteneur PHP/Apache (zenpay-front)
        │
        │  ProxyPass /api/ → http://zenpay-api:8080/api/
        ▼
Conteneur Spring Boot (zenpay-api) → port 8080
        │
        ▼
Conteneur Oracle XE (zenpay-oracle) → port 1521 interne
```

---

## Utilisateurs et rôles

### 1. CLIENT (`ROLE_CLIENT`)

Particulier qui utilise les services bancaires de ZEN-PAY.

**Accès** : `login.php` → connexion directe (pas d'OTP)

**Ce qu'il peut faire :**
- Consulter ses comptes et son solde
- Effectuer des transactions (dépôt, retrait, virement)
- Voir son relevé de compte
- Télécharger son RIB (si offre OFFRE_2 ou OFFRE_1 activée)
- Soumettre ses documents KYC (carte d'identité)
- Ouvrir un ticket de support
- Changer d'offre d'abonnement
- Modifier ses paramètres (mot de passe, opérateur Mobile Money)

**Condition** : le compte KYC doit être **VALIDE** pour accéder aux pages transactionnelles.

---

### 2. GESTIONNAIRE (`ROLE_GESTIONNAIRE`)

Agent de terrain qui gère les clients au quotidien.

**Accès** : `login.php` → OTP par email requis (si IP autorisée)

**Ce qu'il peut faire :**
- Consulter la liste des clients et leurs comptes
- Ouvrir/clôturer des comptes
- Valider ou rejeter des dossiers KYC
- Traiter les transactions manuelles
- Gérer les tickets de support

---

### 3. SUPERVISEUR (`ROLE_SUPERVISOR`)

Encadrant des gestionnaires avec une visibilité élargie.

**Accès** : `login.php` → OTP par email requis

**Ce qu'il peut faire :**
- Tout ce que le gestionnaire peut faire
- Accès aux rapports et statistiques
- Surveillance des activités des gestionnaires

---

### 4. ADMIN SYSTÈME (`ROLE_ADMIN_SYSTEME`)

Administrateur technique de la plateforme.

**Accès** : `login.php` → OTP par email requis

**Ce qu'il peut faire :**
- Gestion du personnel (créer/modifier des gestionnaires)
- Gestion des IPs autorisées (whitelist réseau)
- Consultation des journaux d'audit
- Gestion des paramètres système
- Approbation des demandes d'accès réseau

---

### 5. ADMIN BASE DE DONNÉES (`ROLE_ADMIN_BD`)

Responsable des données et de l'intégrité de la base.

**Accès** : `login.php` → OTP par email requis

**Ce qu'il peut faire :**
- Accès avancé aux données
- Génération de rapports détaillés

---

### 6. SUPER ADMINISTRATEUR (`ROLE_SUPER_ADMIN`)

Niveau hiérarchique le plus élevé. Accès total à la plateforme.

**Accès** : `login.php` → OTP par email → puis portail dédié (`superadmin-portal.php`) avec **clé secrète**

**Ce qu'il peut faire :**
- Tout ce que les autres rôles peuvent faire
- Accès au portail super admin (gestion globale)
- Approbation/refus des demandes d'accès réseau temporaires
- Activation/désactivation des IPs de la whitelist
- Gestion des abonnements et offres
- Consultation de toutes les sessions actives

---

## Identifiants de démonstration

> ⚠️ Ces comptes sont créés automatiquement au premier démarrage. Ne pas utiliser en production.

### Clients

| Email | Mot de passe | Offre | KYC | Opérateur |
|---|---|---|---|---|
| `client@microfinance.local` | `Client@2024` | STANDARD | Validé ✅ | WAVE |
| `clientoffre1@microfinance.local` | `Client@2024` | OFFRE_1 | Validé ✅ | ORANGE |

### Personnel back-office

| Email | Mot de passe | Rôle |
|---|---|---|
| `gestionnaire@microfinance.local` | `Admin@2024` | Gestionnaire |
| `superviseur@microfinance.local` | `Admin@2024` | Superviseur |
| `admin@microfinance.local` | `Admin@2024` | Admin Système |
| `adminbd@microfinance.local` | `Admin@2024` | Admin Base de données |

### Super Administrateurs

| Email | Mot de passe | Clé secrète | Remarque |
|---|---|---|---|
| `superadmin@microfinance.local` | `SuperAdmin@2024` | `SuperKey@2024` | Compte principal |
| `demo.admin@zenpay.local` | `Demo@ZenPay2024` | `ZenPay#Demo2024` | Compte démo |

---

## Processus de connexion selon le rôle

### Client (connexion directe)
```
1. Aller sur login.php
2. Entrer email + mot de passe
3. Accès immédiat au tableau de bord client
```

### Personnel back-office (connexion à double facteur)
```
1. Aller sur login.php
2. Entrer email + mot de passe
3. Si IP connue → code OTP envoyé par email (ou récupérable dans les logs)
4. Entrer le code OTP à 6 chiffres
5. Accès au tableau de bord admin
```

### IP inconnue (réseau non autorisé)
```
1. Tentative de connexion depuis un réseau inconnu
2. Le système bloque et envoie une demande d'accès temporaire au Super Admin
3. Le Super Admin approuve depuis son portail
4. L'employé peut alors se reconnecter et recevoir son OTP
```

### Super Admin (triple facteur)
```
1. login.php → email + mot de passe
2. Code OTP envoyé par email
3. Saisir le OTP → accès au dashboard normal
4. Pour le portail étendu : aller sur superadmin-portal.php
5. Saisir la clé secrète (≠ du mot de passe)
```

---

## Commandes essentielles

### Démarrer l'application

```powershell
# Depuis PowerShell dans le dossier microfinance-docker
cd C:\microfinance-zenpay\microfinance-docker
docker compose up -d
```

### Vérifier que tout tourne

```powershell
docker ps
# Doit afficher : zenpay-oracle, zenpay-api, zenpay-front
```

### Arrêter l'application

```powershell
cd C:\microfinance-zenpay\microfinance-docker
docker compose down
```

### Accéder à l'application en local

Ouvrir dans le navigateur : http://localhost:5502

---

## Récupérer les codes OTP et reset en local (sans email)

Puisque la configuration SMTP n'est pas activée en local, tous les codes sont écrits dans les logs de l'API.

### Récupérer le code OTP (connexion personnel)

```powershell
# Voir les derniers codes OTP générés
docker logs zenpay-api 2>&1 | Select-String "OTP_ENVOYE|Token de confirmation"

# Ou voir les 50 dernières lignes de log (plus rapide)
docker logs zenpay-api --tail 50
```

### Récupérer le code de réinitialisation de mot de passe

```powershell
# Le code apparaît sous la forme : ===> Code reset [email] : 123456
docker logs zenpay-api 2>&1 | Select-String "Code reset"

# Exemple de sortie :
# INFO ===> Code reset [client@microfinance.local] : 847291 (valable 15 min)
```

### Voir les logs en temps réel (pratique pendant les tests)

```powershell
docker logs zenpay-api -f
# Appuyer sur Ctrl+C pour quitter
```

---

## Activer l'envoi d'emails réels (optionnel)

Créer un fichier `.env` dans `C:\microfinance-zenpay\microfinance-docker\` :

```env
MAIL_HOST=smtp.gmail.com
MAIL_PORT=587
MAIL_USERNAME=votre.email@gmail.com
MAIL_PASSWORD=votre_mot_de_passe_app_gmail
```

> Pour Gmail, utiliser un **mot de passe d'application** (pas le mot de passe habituel).
> Paramètres Google → Sécurité → Mots de passe des applications.

Puis redémarrer l'API :
```powershell
cd C:\microfinance-zenpay\microfinance-docker
docker compose restart spring-api
```

---

## Exposer l'application sur Internet (Cloudflare Tunnel)

```
1. Double-cliquer sur : C:\microfinance-zenpay\microfinance-docker\lancer-tunnel.bat
2. Attendre ~10 secondes
3. Copier l'URL affichée : https://xxxxx-yyyy.trycloudflare.com
4. Partager cette URL
```

> ⚠️ L'URL change à chaque démarrage du tunnel. Garder la fenêtre ouverte tant que le partage est actif.

---

## Structure des fichiers

```
C:\microfinance-zenpay\
├── microfinance-frontend-php\     # Interface web PHP
│   ├── public\                    # Pages accessibles depuis le navigateur
│   │   ├── login.php              # Page de connexion
│   │   ├── forgot-password.php    # Réinitialisation mot de passe
│   │   ├── register.php           # Auto-inscription client
│   │   ├── client-dashboard.php   # Tableau de bord client
│   │   ├── dashboard.php          # Tableau de bord back-office
│   │   ├── superadmin-portal.php  # Portail super admin
│   │   ├── reseau.php             # Gestion IPs whitelist
│   │   ├── kyc.php                # Validation KYC
│   │   └── js/api.js              # Wrapper API central
│   └── Dockerfile
├── microfinance-api\              # API Spring Boot (sources Java)
├── microfinance-docker\
│   ├── docker-compose.yml         # Configuration des conteneurs
│   ├── lancer-tunnel.bat          # Script tunnel Cloudflare
│   └── oracle-init\               # Scripts SQL d'initialisation Oracle
└── EXPLAIN.md                     # Ce fichier
```

---

## Dépannage rapide

| Problème | Solution |
|---|---|
| Page blanche ou erreur 502 | `docker ps` → vérifier que les 3 conteneurs tournent |
| "Failed to fetch" | Vérifier que `zenpay-api` est healthy : `docker ps` |
| OTP non reçu | `docker logs zenpay-api --tail 20` pour voir le code |
| Code reset non reçu | `docker logs zenpay-api 2>&1 \| Select-String "Code reset"` |
| IP bloquée | Se connecter en super admin → Réseau → Approuver la demande |
| Tunnel coupé | Relancer `lancer-tunnel.bat` (nouvelle URL générée) |
