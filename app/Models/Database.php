<?php

declare(strict_types=1);

namespace App\Models;

use PDO;
use PDOException;
use RuntimeException;

final class Database
{
    private static ?PDO $pdo = null;

    public static function pdo(): PDO
    {
        if (self::$pdo instanceof PDO) {
            return self::$pdo;
        }

        $configPath = dirname(__DIR__, 2) . '/config/database.php';
        if (!is_file($configPath)) {
            throw new RuntimeException('Missing database config at: ' . $configPath);
        }

        /** @var array{driver:string,host:string,port:int,database:string,username:string,password:string,charset:string} $config */
        $config = require $configPath;

        if (($config['database'] ?? '') === '' || ($config['username'] ?? '') === '') {
            throw new RuntimeException('Database config missing DB_NAME/DB_USER (set env vars or edit config/database.php).');
        }

        $dsn = sprintf(
            '%s:host=%s;port=%d;dbname=%s;charset=%s',
            $config['driver'],
            $config['host'],
            (int) $config['port'],
            $config['database'],
            $config['charset']
        );

        try {
            self::$pdo = new PDO($dsn, $config['username'], $config['password'], [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                PDO::ATTR_EMULATE_PREPARES => false,
            ]);
        } catch (PDOException $e) {
            throw new RuntimeException('Database connection failed: ' . $e->getMessage(), 0, $e);
        }

        return self::$pdo;
    }
}

