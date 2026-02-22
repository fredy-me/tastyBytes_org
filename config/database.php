<?php

declare(strict_types=1);

return [
    'driver' => 'mysql',
    'host' => getenv('DB_HOST') ?: '127.0.0.1',
    'port' => (int) (getenv('DB_PORT') ?: 3306),
    // Defaults are set for a typical local XAMPP setup; override via env vars if needed.
    'database' => getenv('DB_NAME') ?: 'tastybytes',
    'username' => getenv('DB_USER') ?: 'root',
    'password' => getenv('DB_PASS') ?: '',
    'charset' => 'utf8mb4',
];
