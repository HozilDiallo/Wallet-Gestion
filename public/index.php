<?php

declare(strict_types=1);

require __DIR__ . '/../bootstrap.php';

use App\Controller\WalletController;
use App\Router\Router;

session_start();

$router = new Router();
$controller = WalletController::class;

$router->get('/', $controller, 'index');

$router->post('/wallet/create', $controller, 'createWallet');
$router->post('/wallet/deposit', $controller, 'deposit');
$router->post('/wallet/withdraw', $controller, 'withdraw');

$method = $_SERVER['REQUEST_METHOD'] ?? 'GET';
$uri = $_SERVER['REQUEST_URI'] ?? '/';

$router->dispatch($method, $uri);
