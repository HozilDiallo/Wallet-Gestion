<?php

declare(strict_types=1);

namespace App\Model;

final class Transaction
{
    public function __construct(
        public readonly ?int $id,
        public readonly string $code,
        public readonly int $walletId,
        public readonly float $montant,
        public readonly float $frais,
        public readonly string $type,
        public readonly string $dateHeure,
        public readonly ?string $walletNom = null,
        public readonly ?string $walletPrenom = null,
        public readonly ?string $walletTelephone = null,
    ) {
    }

    public function getMontantTotal(): float
    {
        return $this->montant + $this->frais;
    }
}
