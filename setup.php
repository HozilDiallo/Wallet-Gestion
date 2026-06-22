<?php

declare(strict_types=1);

/**
 * Script d'initialisation de la base de données MySQL.
 * Usage : php setup.php
 */

require __DIR__ . '/bootstrap.php';

use App\Database\Connection;

$config = Connection::loadConfig();
$schemaPath = __DIR__ . '/database/schema.sql';

if (!file_exists($schemaPath)) {
    fwrite(STDERR, "Fichier schema.sql introuvable.\n");
    exit(1);
}

try {
    $mysql = $config['mysql'];
    $database = $mysql['database'];

    echo "Connexion au serveur MySQL ({$mysql['host']}:{$mysql['port']})...\n";

    $serverPdo = Connection::createServerConnection($config);
    $serverPdo->exec(
        sprintf(
            'CREATE DATABASE IF NOT EXISTS `%s` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci',
            str_replace('`', '``', $database)
        )
    );
    echo "Base « {$database} » prête.\n";

    $pdo = Connection::getInstance();
    $schema = file_get_contents($schemaPath);

    if ($schema === false) {
        throw new RuntimeException('Impossible de lire le fichier schema.sql.');
    }

    executeSchema($pdo, $schema);

    echo "Schéma appliqué avec succès.\n";
    echo "Base de données initialisée : {$database}@{$mysql['host']}\n";
    echo "\nLancez l'application :\n";
    echo "  php -S localhost:8000 -t public public/router.php\n";
} catch (Throwable $e) {
    fwrite(STDERR, 'Erreur lors de l\'initialisation : ' . $e->getMessage() . "\n");
    exit(1);
}

/**
 * Exécute un script SQL multi-instructions (CREATE TABLE, INDEX, etc.).
 */
function executeSchema(\PDO $pdo, string $schema): void
{
    $schema = preg_replace('/--.*$/m', '', $schema) ?? $schema;

    foreach (explode(';', $schema) as $statement) {
        $statement = trim($statement);
        if ($statement !== '') {
            $pdo->exec($statement);
        }
    }
}
