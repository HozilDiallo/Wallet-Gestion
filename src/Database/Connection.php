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
            $config = require __DIR__ . '/../../config/database.php';
            self::$instance = self::createConnection($config);
        }

        return self::$instance;
    }

    private static function createConnection(array $config): PDO
    {
        try {
            if ($config['driver'] === 'sqlite') {
                $path = $config['sqlite']['path'];
                $dir = dirname($path);
                if (!is_dir($dir)) {
                    mkdir($dir, 0755, true);
                }
                $pdo = new PDO('sqlite:' . $path);
            } else {
                $mysql = $config['mysql'];
                $dsn = sprintf(
                    'mysql:host=%s;port=%d;dbname=%s;charset=%s',
                    $mysql['host'],
                    $mysql['port'],
                    $mysql['database'],
                    $mysql['charset']
                );
                $pdo = new PDO($dsn, $mysql['username'], $mysql['password']);
            }

            $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            $pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);

            if ($config['driver'] === 'sqlite') {
                $pdo->exec('PRAGMA foreign_keys = ON');
            }

            return $pdo;
        } catch (PDOException $e) {
            throw new PDOException('Erreur de connexion à la base de données : ' . $e->getMessage());
        }
    }
}
