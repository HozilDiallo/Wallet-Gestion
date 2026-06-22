<?php

declare(strict_types=1);

namespace App\Database;

use PDO;
use PDOException;

final class Connection
{
    private static ?PDO $instance = null;

    public static function getInstance(): PDO
    {
        if (self::$instance === null) {
            $config = self::loadConfig();
            self::$instance = self::createConnection($config);
        }

        return self::$instance;
    }

    /**
     * @return array<string, mixed>
     */
    public static function loadConfig(): array
    {
        return require __DIR__ . '/../../config/database.php';
    }

    /**
     * Connexion au serveur MySQL sans sélectionner de base (pour la création initiale).
     */
    public static function createServerConnection(?array $config = null): PDO
    {
        $config ??= self::loadConfig();
        $mysql = $config['mysql'];

        $dsn = sprintf(
            'mysql:host=%s;port=%d;charset=%s',
            $mysql['host'],
            $mysql['port'],
            $mysql['charset']
        );

        return self::connect($dsn, $mysql['username'], $mysql['password']);
    }

    private static function createConnection(array $config): PDO
    {
        $mysql = $config['mysql'];

        $dsn = sprintf(
            'mysql:host=%s;port=%d;dbname=%s;charset=%s',
            $mysql['host'],
            $mysql['port'],
            $mysql['database'],
            $mysql['charset']
        );

        return self::connect($dsn, $mysql['username'], $mysql['password']);
    }

    private static function connect(string $dsn, string $username, string $password): PDO
    {
        try {
            $pdo = new PDO($dsn, $username, $password);
            $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            $pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);

            return $pdo;
        } catch (PDOException $e) {
            throw new PDOException('Erreur de connexion à la base de données : ' . $e->getMessage());
        }
    }
}
