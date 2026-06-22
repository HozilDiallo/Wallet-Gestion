<?php

declare(strict_types=1);

namespace App\Service;

use App\Model\Transaction;
use App\Model\Wallet;
use App\Repository\TransactionRepository;
use App\Repository\WalletRepository;
use InvalidArgumentException;
use RuntimeException;

final class WalletService
{
  private const FRAIS_MAX_CFA = 5000.0;
  private const FRAIS_TAUX = 0.01;

  public function __construct(
    private readonly WalletRepository $walletRepository = new WalletRepository(),
    private readonly TransactionRepository $transactionRepository = new TransactionRepository(),
  ) {
  }

  public function createWallet(array $data): Wallet
  {
    $code = trim($data['code'] ?? '');
    $nom = trim($data['nom'] ?? '');
    $prenom = trim($data['prenom'] ?? '');
    $telephone = trim($data['telephone'] ?? '');
    $solde = isset($data['solde']) ? (float) $data['solde'] : -1;

    if ($code === '') {
      throw new InvalidArgumentException('Le code du portefeuille est obligatoire.');
    }

    if ($nom === '') {
      throw new InvalidArgumentException('Le nom est obligatoire.');
    }

    if ($prenom === '') {
      throw new InvalidArgumentException('Le prénom est obligatoire.');
    }

    if ($telephone === '') {
      throw new InvalidArgumentException('Le numéro de téléphone est obligatoire.');
    }

    if ($solde < 0) {
      throw new InvalidArgumentException('Le solde doit être positif ou égal à zéro.');
    }

    if ($this->walletRepository->telephoneExists($telephone)) {
      throw new InvalidArgumentException('Ce numéro de téléphone est déjà associé à un portefeuille.');
    }

    if ($this->walletRepository->codeExists($code)) {
      throw new InvalidArgumentException('Ce code de portefeuille existe déjà.');
    }

    return $this->walletRepository->create($code, $nom, $prenom, $telephone, $solde);
  }

  public function deposit(array $data): Transaction
  {
    $telephone = trim($data['telephone'] ?? '');
    $montant = isset($data['montant']) ? (float) $data['montant'] : 0;

    if ($telephone === '') {
      throw new InvalidArgumentException('Le numéro de téléphone est obligatoire.');
    }

    $wallet = $this->walletRepository->findByTelephone($telephone);
    if ($wallet === null) {
      throw new InvalidArgumentException('Aucun portefeuille trouvé pour ce numéro de téléphone.');
    }

    if ($montant <= 0) {
      throw new InvalidArgumentException('Le montant du dépôt doit être strictement positif.');
    }

    $nouveauSolde = $wallet->solde + $montant;
    $this->walletRepository->updateSolde($wallet->id, $nouveauSolde);

    $code = $this->generateTransactionCode();

    return $this->transactionRepository->create(
      $code,
      $wallet->id,
      $montant,
      0.0,
      'Dépôt'
    );
  }

  public function withdraw(array $data): Transaction
  {
    $telephone = trim($data['telephone'] ?? '');
    $montant = isset($data['montant']) ? (float) $data['montant'] : 0;

    if ($telephone === '') {
      throw new InvalidArgumentException('Le numéro de téléphone est obligatoire.');
    }

    $wallet = $this->walletRepository->findByTelephone($telephone);
    if ($wallet === null) {
      throw new InvalidArgumentException('Aucun portefeuille trouvé pour ce numéro de téléphone.');
    }

    if ($montant <= 0) {
      throw new InvalidArgumentException('Le montant du retrait doit être strictement positif.');
    }

    $frais = min($montant * self::FRAIS_TAUX, self::FRAIS_MAX_CFA);
    $totalDeduit = $montant + $frais;

    if ($wallet->solde < $totalDeduit) {
      throw new InvalidArgumentException(
        sprintf(
          'Solde insuffisant. Solde disponible : %s CFA, montant requis (retrait + frais) : %s CFA.',
          number_format($wallet->solde, 0, ',', ' '),
          number_format($totalDeduit, 0, ',', ' ')
        )
      );
    }

    $nouveauSolde = $wallet->solde - $totalDeduit;
    $this->walletRepository->updateSolde($wallet->id, $nouveauSolde);

    $code = $this->generateTransactionCode();

    return $this->transactionRepository->create(
      $code,
      $wallet->id,
      $montant,
      $frais,
      'Retrait'
    );
  }

  /**
   * @return Transaction[]
   */
  public function listTransactions(): array
  {
    return $this->transactionRepository->findAll();
  }

  private function generateTransactionCode(): string
  {
    do {
      $code = 'TXN-' . strtoupper(bin2hex(random_bytes(4))) . '-' . time();
    } while ($this->transactionRepository->codeExists($code));

    return $code;
  }
}
