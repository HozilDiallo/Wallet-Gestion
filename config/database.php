<?php

declare(strict_types=1);

/**
 * Configuration de la base de données SQLite.
 * Pour MySQL, décommentez la section MySQL et commentez SQLite.
 */

return [
    'driver' => 'sqlite',
    'sqlite' => [
        'path' => __DIR__ . '/../database/wallet.db',
    ],
    // 'driver' => 'mysql',
    // 'mysql' => [
    //     'host' => '127.0.0.1',
    //     'port' => 3306,
    //     'database' => 'wallet_gestion',
    //     'username' => 'root',
    //     'password' => '',
    //     'charset' => 'utf8mb4',
    // ],
];
