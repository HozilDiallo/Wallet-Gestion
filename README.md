# Wallet Gestion

Application web de gestion de portefeuilles électroniques en PHP avec base de données SQLite.

## Fonctionnalités

- **Création de portefeuille** : nom, prénom, téléphone (unique), code (unique), solde initial ≥ 0
- **Dépôt** : crédit d'un montant positif sur un portefeuille existant
- **Retrait** : débit avec frais de 1 % (plafonné à 5 000 CFA)
- **Historique** : liste de toutes les transactions

## Prérequis

- PHP 8.1 ou supérieur
- Extension PDO SQLite (`pdo_sqlite`)
- Serveur web Apache (mod_rewrite) **ou** serveur PHP intégré

## Installation

```bash
cd wallet-gestion
php setup.php
```

Ce script crée la base SQLite `database/wallet.db` et applique le schéma.

## Lancement

### Option 1 : Serveur PHP intégré (recommandé pour le développement)

```bash
cd wallet-gestion
php -S localhost:8000 -t public public/router.php
```

Ouvrez [http://localhost:8000](http://localhost:8000) dans votre navigateur.

## Routes

| Méthode | URL | Description |
|---------|-----|-------------|
| GET | `/` | Page unique : création, dépôt/retrait, historique |
| POST | `/wallet/create` | Enregistrement d'un nouveau portefeuille |
| POST | `/wallet/deposit` | Effectuer un dépôt |
| POST | `/wallet/withdraw` | Effectuer un retrait |

Les actions POST redirigent vers `/` avec un message flash.

### Option 2 : Apache

Configurez le `DocumentRoot` sur le dossier `public/`. Le fichier `public/.htaccess` redirige les requêtes vers `index.php`.

## Architecture

```
wallet-gestion/
├── public/              # Point d'entrée web
│   ├── index.php        # Routeur + bootstrap
│   └── css/style.css
├── config/
│   └── database.php     # Configuration PDO (SQLite/MySQL)
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
│   ├── schema.sql       # Schéma SQL
│   └── wallet.db        # Base SQLite (générée)
├── bootstrap.php        # Autoloader PSR-4
└── setup.php            # Script d'initialisation
```

### Flux de données

1. **Router** : associe URI + méthode HTTP au contrôleur
2. **Controller** : reçoit la requête, appelle le service, affiche la vue
3. **Service** : valide les règles métier, orchestre les opérations
4. **Repository** : exécute les requêtes SQL via PDO

## Configuration MySQL (optionnel)

Modifiez `config/database.php` :

1. Commentez la section `sqlite` et définissez `'driver' => 'mysql'`
2. Décommentez et configurez la section `mysql`
3. Créez la base `wallet_gestion` et adaptez `database/schema.sql` si nécessaire (types MySQL)
4. Relancez `php setup.php`

## Règles de validation

| Opération | Règles |
|-----------|--------|
| Création | Téléphone unique, nom/prénom/code obligatoires, solde ≥ 0 |
| Dépôt | Téléphone existant, montant > 0 |
| Retrait | Téléphone existant, montant > 0, solde suffisant (montant + frais) |

**Frais de retrait** : `min(montant × 0,01, 5000)` CFA
