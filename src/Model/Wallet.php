<?php

declare(strict_types=1);

namespace App\Model;

final class Wallet
{
    public function __construct(
        public readonly ?int $id,
        public readonly string $code,
        public readonly string $nom,
        public readonly string $prenom,
        public readonly string $telephone,
        public readonly float $solde,
        public readonly ?string $createdAt = null,
    ) {
    }

    public function getNomComplet(): string
    {
        return $this->prenom . ' ' . $this->nom;
    }
}
