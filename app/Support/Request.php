<?php

declare(strict_types=1);

namespace App\Support;

final class Request
{
    public static function method(): string
    {
        return strtoupper($_SERVER['REQUEST_METHOD'] ?? 'GET');
    }

    public static function isPost(): bool
    {
        return self::method() === 'POST';
    }

    public static function input(string $key, string $default = ''): string
    {
        $value = $_POST[$key] ?? $_GET[$key] ?? $default;
        if (!is_string($value)) {
            return $default;
        }
        return $value;
    }

    public static function route(): string
    {
        $route = $_GET['route'] ?? 'home';
        return is_string($route) && $route !== '' ? $route : 'home';
    }
}

