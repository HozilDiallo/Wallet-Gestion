<?php

declare(strict_types=1);

/**
 * Script d'initialisation de la base de données.
 * Usage : php setup.php
 */

require __DIR__ . '/bootstrap.php';

use App\Database\Connection;

$config = require __DIR__ . '/config/database.php';
$schemaPath = __DIR__ . '/database/schema.sql';

if (!file_exists($schemaPath)) {
    fwrite(STDERR, "Fichier schema.sql introuvable.\n");
    exit(1);
}

try {
    $pdo = Connection::getInstance();
    $schema = file_get_contents($schemaPath);

    if ($schema === false) {
        throw new RuntimeException('Impossible de lire le fichier schema.sql.');
    }

    $pdo->exec($schema);

    $driver = $config['driver'];
    $location = $driver === 'sqlite'
        ? $config['sqlite']['path']
        : $config['mysql']['database'] . '@' . $config['mysql']['host'];

    echo "Base de données initialisée avec succès ({$driver}).\n";
    echo "Emplacement : {$location}\n";
} catch (Throwable $e) {
    fwrite(STDERR, 'Erreur lors de l\'initialisation : ' . $e->getMessage() . "\n");
    exit(1);
}
