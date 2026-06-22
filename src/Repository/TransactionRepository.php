<?php

declare(strict_types=1);

namespace App\Repository;

use App\Database\Connection;
use App\Model\Transaction;
use PDO;

final class TransactionRepository
{
    private PDO $pdo;

    public function __construct()
    {
        $this->pdo = Connection::getInstance();
    }

    public function create(
        string $code,
        int $walletId,
        float $montant,
        float $frais,
        string $type,
        ?string $dateHeure = null
    ): Transaction {
        $dateHeure = $dateHeure ?? date('Y-m-d H:i:s');

        $stmt = $this->pdo->prepare(
            'INSERT INTO transactions (code, wallet_id, montant, frais, type, date_heure)
             VALUES (:code, :wallet_id, :montant, :frais, :type, :date_heure)'
        );
        $stmt->execute([
            'code' => $code,
            'wallet_id' => $walletId,
            'montant' => $montant,
            'frais' => $frais,
            'type' => $type,
            'date_heure' => $dateHeure,
        ]);

        $id = (int) $this->pdo->lastInsertId();

        return new Transaction($id, $code, $walletId, $montant, $frais, $type, $dateHeure);
    }

    public function codeExists(string $code): bool
    {
        $stmt = $this->pdo->prepare('SELECT COUNT(*) FROM transactions WHERE code = :code');
        $stmt->execute(['code' => $code]);

        return (int) $stmt->fetchColumn() > 0;
    }

    /**
     * @return Transaction[]
     */
    public function findAll(): array
    {
        $stmt = $this->pdo->query(
            'SELECT t.*, w.nom AS wallet_nom, w.prenom AS wallet_prenom, w.telephone AS wallet_telephone
             FROM transactions t
             INNER JOIN wallets w ON w.id = t.wallet_id
             ORDER BY t.date_heure DESC'
        );

        $transactions = [];
        while ($row = $stmt->fetch()) {
            $transactions[] = $this->mapToTransaction($row);
        }

        return $transactions;
    }

    private function mapToTransaction(array $row): Transaction
    {
        return new Transaction(
            (int) $row['id'],
            $row['code'],
            (int) $row['wallet_id'],
            (float) $row['montant'],
            (float) $row['frais'],
            $row['type'],
            $row['date_heure'],
            $row['wallet_nom'] ?? null,
            $row['wallet_prenom'] ?? null,
            $row['wallet_telephone'] ?? null,
        );
    }
}
