-- Schéma MySQL pour la gestion de portefeuilles

CREATE TABLE IF NOT EXISTS wallets (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    code VARCHAR(50) NOT NULL,
    nom VARCHAR(100) NOT NULL,
    prenom VARCHAR(100) NOT NULL,
    telephone VARCHAR(20) NOT NULL,
    solde DECIMAL(15, 2) NOT NULL DEFAULT 0.00,
    created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT uq_wallets_code UNIQUE (code),
    CONSTRAINT uq_wallets_telephone UNIQUE (telephone),
    CONSTRAINT chk_wallets_solde CHECK (solde >= 0)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS transactions (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    code VARCHAR(50) NOT NULL,
    wallet_id INT UNSIGNED NOT NULL,
    montant DECIMAL(15, 2) NOT NULL,
    frais DECIMAL(15, 2) NOT NULL DEFAULT 0.00,
    type VARCHAR(10) NOT NULL,
    date_heure DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT uq_transactions_code UNIQUE (code),
    CONSTRAINT chk_transactions_montant CHECK (montant > 0),
    CONSTRAINT chk_transactions_frais CHECK (frais >= 0),
    CONSTRAINT chk_transactions_type CHECK (type IN ('Dépôt', 'Retrait')),
    CONSTRAINT fk_transactions_wallet FOREIGN KEY (wallet_id) REFERENCES wallets(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE INDEX idx_transactions_wallet_id ON transactions(wallet_id);
CREATE INDEX idx_transactions_date ON transactions(date_heure DESC);
