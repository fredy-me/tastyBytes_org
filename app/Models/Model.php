<?php

declare(strict_types=1);

namespace App\Models;

use PDO;

abstract class Model
{
    final protected static function db(): PDO
    {
        return Database::pdo();
    }
}

