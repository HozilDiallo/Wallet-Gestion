<?php

declare(strict_types=1);

namespace App\Controller;

use App\Service\WalletService;
use InvalidArgumentException;

final class WalletController
{
    private WalletService $walletService;

    public function __construct()
    {
        $this->walletService = new WalletService();
    }

    public function index(): void
    {
        $transactions = $this->walletService->listTransactions();

        $this->render('index', [
            'transactions' => $transactions,
            'pageTitle' => 'Gestion de Portefeuilles',
        ]);
    }

    public function createWallet(): void
    {
        try {
            $wallet = $this->walletService->createWallet($_POST);
            $this->setFlash(
                'success',
                sprintf(
                    'Portefeuille créé avec succès pour %s %s (code : %s, solde : %s CFA).',
                    $wallet->prenom,
                    $wallet->nom,
                    $wallet->code,
                    number_format($wallet->solde, 0, ',', ' ')
                )
            );
        } catch (InvalidArgumentException $e) {
            $this->setFlash('error', $e->getMessage());
        }

        $this->redirect('/');
    }

    public function deposit(): void
    {
        try {
            $transaction = $this->walletService->deposit($_POST);
            $this->setFlash(
                'success',
                sprintf(
                    'Dépôt de %s CFA effectué avec succès (réf. : %s).',
                    number_format($transaction->montant, 0, ',', ' '),
                    $transaction->code
                )
            );
        } catch (InvalidArgumentException $e) {
            $this->setFlash('error', $e->getMessage());
        }

        $this->redirect('/');
    }

    public function withdraw(): void
    {
        try {
            $transaction = $this->walletService->withdraw($_POST);
            $message = sprintf(
                'Retrait de %s CFA effectué avec succès (frais : %s CFA, réf. : %s).',
                number_format($transaction->montant, 0, ',', ' '),
                number_format($transaction->frais, 0, ',', ' '),
                $transaction->code
            );
            $this->setFlash('success', $message);
        } catch (InvalidArgumentException $e) {
            $this->setFlash('error', $e->getMessage());
        }

        $this->redirect('/');
    }

    private function render(string $view, array $data = []): void
    {
        $data['flash'] = $this->getFlash();

        extract($data, EXTR_SKIP);

        ob_start();
        require __DIR__ . '/../../views/' . $view . '.php';
        $content = ob_get_clean();

        require __DIR__ . '/../../views/layout.php';
    }

    private function redirect(string $path): void
    {
        header('Location: ' . $path);
        exit;
    }

    private function setFlash(string $type, string $message): void
    {
        $_SESSION['flash'] = ['type' => $type, 'message' => $message];
    }

    /** @return array{type: string, message: string}|null */
    private function getFlash(): ?array
    {
        $flash = $_SESSION['flash'] ?? null;
        unset($_SESSION['flash']);

        return $flash;
    }
}
