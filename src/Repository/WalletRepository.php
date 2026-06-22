<?php

declare(strict_types=1);

namespace App\Repository;

use App\Database\Connection;
use App\Model\Wallet;
use PDO;

final class WalletRepository
{
    private PDO $pdo;

    public function __construct()
    {
        $this->pdo = Connection::getInstance();
    }

    public function findByTelephone(string $telephone): ?Wallet
    {
        $stmt = $this->pdo->prepare('SELECT * FROM wallets WHERE telephone = :telephone LIMIT 1');
        $stmt->execute(['telephone' => $telephone]);
        $row = $stmt->fetch();

        return $row ? $this->mapToWallet($row) : null;
    }

    public function findByCode(string $code): ?Wallet
    {
        $stmt = $this->pdo->prepare('SELECT * FROM wallets WHERE code = :code LIMIT 1');
        $stmt->execute(['code' => $code]);
        $row = $stmt->fetch();

        return $row ? $this->mapToWallet($row) : null;
    }

    public function telephoneExists(string $telephone): bool
    {
        return $this->findByTelephone($telephone) !== null;
    }

    public function codeExists(string $code): bool
    {
        return $this->findByCode($code) !== null;
    }

    public function create(string $code, string $nom, string $prenom, string $telephone, float $solde): Wallet
    {
        $stmt = $this->pdo->prepare(
            'INSERT INTO wallets (code, nom, prenom, telephone, solde) VALUES (:code, :nom, :prenom, :telephone, :solde)'
        );
        $stmt->execute([
            'code' => $code,
            'nom' => $nom,
            'prenom' => $prenom,
            'telephone' => $telephone,
            'solde' => $solde,
        ]);

        $id = (int) $this->pdo->lastInsertId();

        return new Wallet($id, $code, $nom, $prenom, $telephone, $solde);
    }

    public function updateSolde(int $walletId, float $nouveauSolde): void
    {
        $stmt = $this->pdo->prepare('UPDATE wallets SET solde = :solde WHERE id = :id');
        $stmt->execute(['solde' => $nouveauSolde, 'id' => $walletId]);
    }

    private function mapToWallet(array $row): Wallet
    {
        return new Wallet(
            (int) $row['id'],
            $row['code'],
            $row['nom'],
            $row['prenom'],
            $row['telephone'],
            (float) $row['solde'],
            $row['created_at'] ?? null,
        );
    }
}
