<?php

declare(strict_types=1);

namespace App\Support;

final class Session
{
    public static function start(): void
    {
        if (session_status() === PHP_SESSION_ACTIVE) {
            return;
        }

        // Must be set before session_start()
        ini_set('session.use_strict_mode', '1');
        ini_set('session.use_only_cookies', '1');

        // Prevent common "CSRF token invalid" issues caused by sessions not persisting.
        // Use a known-writable path (especially important on local XAMPP setups).
        $tmp = sys_get_temp_dir();
        if (is_string($tmp) && $tmp !== '' && is_dir($tmp) && is_writable($tmp)) {
            session_save_path($tmp);
        }

        $cookie = session_get_cookie_params();
        session_set_cookie_params([
            'lifetime' => 0,
            'path' => $cookie['path'] ?? '/',
            'domain' => $cookie['domain'] ?? '',
            'secure' => false,
            'httponly' => true,
            'samesite' => 'Lax',
        ]);

        @session_start();

        if (session_status() !== PHP_SESSION_ACTIVE) {
            throw new \RuntimeException('Failed to start session (check PHP session configuration).');
        }
    }

    public static function regenerate(): void
    {
        self::start();
        session_regenerate_id(true);
    }

    public static function get(string $key, mixed $default = null): mixed
    {
        self::start();
        return $_SESSION[$key] ?? $default;
    }

    public static function set(string $key, mixed $value): void
    {
        self::start();
        $_SESSION[$key] = $value;
    }

    public static function forget(string $key): void
    {
        self::start();
        unset($_SESSION[$key]);
    }

    public static function flash(string $key, mixed $value): void
    {
        self::set('__flash__:' . $key, $value);
    }

    public static function pullFlash(string $key, mixed $default = null): mixed
    {
        $flashKey = '__flash__:' . $key;
        $value = self::get($flashKey, $default);
        self::forget($flashKey);
        return $value;
    }
}
