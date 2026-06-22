# Wallet Gestion

Application web de gestion de portefeuilles électroniques en PHP avec base de données MySQL/MariaDB.

## Fonctionnalités

- **Création de portefeuille** : nom, prénom, téléphone (unique), code (unique), solde initial ≥ 0
- **Dépôt** : crédit d'un montant positif sur un portefeuille existant
- **Retrait** : débit avec frais de 1 % (plafonné à 5 000 CFA)
- **Historique** : liste de toutes les transactions

## Prérequis

- PHP 8.1 ou supérieur
- Extension PDO MySQL (`pdo_mysql`)
- MySQL 8.0+ ou MariaDB 10.5+
- Serveur web Apache (mod_rewrite) **ou** serveur PHP intégré

## Installation MySQL

### 1. Installer et démarrer MySQL/MariaDB

**macOS (Homebrew) :**
```bash
brew install mysql
brew services start mysql
```

**Ubuntu/Debian :**
```bash
sudo apt update
sudo apt install mysql-server
sudo systemctl start mysql
```

### 2. Configurer les identifiants

Choisissez l'une des options suivantes :

**Option A — Fichier local (recommandé en développement)**

```bash
cp config/database.local.php.example config/database.local.php
```

Éditez `config/database.local.php` avec vos identifiants (host, utilisateur, mot de passe).

**Option B — Variables d'environnement**

```bash
cp .env.example .env
# Exportez les variables DB_* dans votre shell ou via votre outil de déploiement
export DB_HOST=127.0.0.1
export DB_PORT=3306
export DB_NAME=wallet_gestion
export DB_USER=root
export DB_PASSWORD=
```

Les valeurs par défaut (sans surcharge) : `127.0.0.1:3306`, base `wallet_gestion`, utilisateur `root`, mot de passe vide.

### 3. Initialiser la base de données

```bash
cd wallet-gestion
php setup.php
```

Ce script :
1. Se connecte au serveur MySQL
2. Crée la base `wallet_gestion` si elle n'existe pas
3. Applique le schéma (`database/schema.sql`)

## Lancement

### Option 1 : Serveur PHP intégré (recommandé pour le développement)

```bash
cd wallet-gestion
php -S localhost:8000 -t public public/router.php
```

Ouvrez [http://localhost:8000](http://localhost:8000) dans votre navigateur.

### Option 2 : Apache

Configurez le `DocumentRoot` sur le dossier `public/`. Le fichier `public/.htaccess` redirige les requêtes vers `index.php`.

## Routes

| Méthode | URL | Description |
|---------|-----|-------------|
| GET | `/` | Page unique : création, dépôt/retrait, historique |
| POST | `/wallet/create` | Enregistrement d'un nouveau portefeuille |
| POST | `/wallet/deposit` | Effectuer un dépôt |
| POST | `/wallet/withdraw` | Effectuer un retrait |

Les actions POST redirigent vers `/` avec un message flash.

## Architecture

```
wallet-gestion/
├── public/              # Point d'entrée web
│   ├── index.php        # Routeur + bootstrap
│   └── css/style.css
├── config/
│   ├── database.php              # Configuration PDO MySQL
│   └── database.local.php.example # Modèle de config locale
├── src/
│   ├── Router/          # Routage GET/POST
│   ├── Controller/      # Gestion des requêtes HTTP
│   ├── Service/         # Logique métier et validations
│   ├── Repository/      # Accès aux données (PDO)
│   ├── Model/           # Entités Wallet et Transaction
│   └── Database/        # Connexion PDO
├── views/
│   ├── layout.php       # En-tête, messages flash
│   └── index.php        # Page unique (3 sections)
├── database/
│   └── schema.sql       # Schéma MySQL
├── bootstrap.php        # Autoloader PSR-4
└── setup.php            # Script d'initialisation
```

### Flux de données

1. **Router** : associe URI + méthode HTTP au contrôleur
2. **Controller** : reçoit la requête, appelle le service, affiche la vue
3. **Service** : valide les règles métier, orchestre les opérations
4. **Repository** : exécute les requêtes SQL via PDO

## Règles de validation

| Opération | Règles |
|-----------|--------|
| Création | Téléphone unique, nom/prénom/code obligatoires, solde ≥ 0 |
| Dépôt | Téléphone existant, montant > 0 |
| Retrait | Téléphone existant, montant > 0, solde suffisant (montant + frais) |

**Frais de retrait** : `min(montant × 0,01, 5000)` CFA
