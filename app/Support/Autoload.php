<?php

declare(strict_types=1);

namespace App\Support;

final class Autoload
{
    public static function register(): void
    {
        spl_autoload_register(static function (string $class): void {
            $prefix = 'App\\';
            if (!str_starts_with($class, $prefix)) {
                return;
            }

            $relative = substr($class, strlen($prefix));
            $relativePath = str_replace('\\', DIRECTORY_SEPARATOR, $relative) . '.php';
            $baseDir = dirname(__DIR__) . DIRECTORY_SEPARATOR; // app/
            $path = $baseDir . $relativePath;

            if (is_file($path)) {
                require $path;
            }
        });
    }
}

