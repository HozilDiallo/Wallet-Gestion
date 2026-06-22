<?php

declare(strict_types=1);

/**
 * Configuration de la base de données MySQL.
 * Surchargez via config/database.local.php ou variables d'environnement (DB_*).
 */

$defaults = [
    'driver' => 'mysql',
    'mysql' => [
        'host' => getenv('DB_HOST') !== false && getenv('DB_HOST') !== '' ? getenv('DB_HOST') : '127.0.0.1',
        'port' => (int) (getenv('DB_PORT') !== false && getenv('DB_PORT') !== '' ? getenv('DB_PORT') : 3306),
        'database' => getenv('DB_NAME') !== false && getenv('DB_NAME') !== '' ? getenv('DB_NAME') : 'wallet_gestion',
        'username' => getenv('DB_USER') !== false && getenv('DB_USER') !== '' ? getenv('DB_USER') : 'root',
        'password' => getenv('DB_PASSWORD') !== false ? getenv('DB_PASSWORD') : '',
        'charset' => 'utf8mb4',
    ],
];

$localPath = __DIR__ . '/database.local.php';
if (file_exists($localPath)) {
    /** @var array<string, mixed> $local */
    $local = require $localPath;

    if (isset($local['mysql']) && is_array($local['mysql'])) {
        $defaults['mysql'] = array_merge($defaults['mysql'], $local['mysql']);
    }

    if (isset($local['driver'])) {
        $defaults['driver'] = $local['driver'];
    }
}

return $defaults;
