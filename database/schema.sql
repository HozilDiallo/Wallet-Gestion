-- Schéma SQLite pour la gestion de portefeuilles

PRAGMA foreign_keys = ON;

CREATE TABLE IF NOT EXISTS wallets (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    code VARCHAR(50) NOT NULL UNIQUE,
    nom VARCHAR(100) NOT NULL,
    prenom VARCHAR(100) NOT NULL,
    telephone VARCHAR(20) NOT NULL UNIQUE,
    solde DECIMAL(15, 2) NOT NULL DEFAULT 0 CHECK (solde >= 0),
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE IF NOT EXISTS transactions (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    code VARCHAR(50) NOT NULL UNIQUE,
    wallet_id INTEGER NOT NULL,
    montant DECIMAL(15, 2) NOT NULL CHECK (montant > 0),
    frais DECIMAL(15, 2) NOT NULL DEFAULT 0 CHECK (frais >= 0),
    type VARCHAR(10) NOT NULL CHECK (type IN ('Dépôt', 'Retrait')),
    date_heure DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (wallet_id) REFERENCES wallets(id) ON DELETE CASCADE
);

CREATE INDEX IF NOT EXISTS idx_transactions_wallet_id ON transactions(wallet_id);
CREATE INDEX IF NOT EXISTS idx_transactions_date ON transactions(date_heure DESC);
