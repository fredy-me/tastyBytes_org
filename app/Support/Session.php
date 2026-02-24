<?php

declare(strict_types=1);

namespace App\Support;

final class Session
{
    private const SESSION_NAME = 'TBSESSID';
    private const COOKIE_SAMESITE = 'Lax';

    public static function start(): void
    {
        if (session_status() === PHP_SESSION_ACTIVE) {
            return;
        }

        // Must be set before session_start()
        ini_set('session.use_strict_mode', '1');
        ini_set('session.use_only_cookies', '1');
        ini_set('session.use_cookies', '1');

        self::ensureWritableSavePath();

        session_name(self::SESSION_NAME);
        session_set_cookie_params([
            'lifetime' => 0,
            // Force '/' so the browser sends the cookie for /TasteBytes/public/ requests.
            'path' => '/',
            'domain' => '',
            'secure' => false,
            'httponly' => true,
            'samesite' => self::COOKIE_SAMESITE,
        ]);

        session_start();

        if (session_status() !== PHP_SESSION_ACTIVE) {
            throw new \RuntimeException('Failed to start session (check PHP session configuration).');
        }
    }

    private static function ensureWritableSavePath(): void
    {
        $candidates = [];

        $current = self::normalizeSavePath((string) session_save_path());
        if ($current !== '') {
            $candidates[] = $current;
        }

        // Common XAMPP locations (often included in open_basedir)
        $candidates[] = '/opt/lampp/temp';
        $candidates[] = '/opt/lampp/tmp';

        $tmp = sys_get_temp_dir();
        if (is_string($tmp) && $tmp !== '') {
            $candidates[] = $tmp;
        }

        $project = dirname(__DIR__, 2) . '/storage/sessions';
        self::tryPrepareDir($project);
        $candidates[] = $project;

        foreach ($candidates as $path) {
            $path = rtrim($path, '/');
            if ($path === '') {
                continue;
            }
            if (!self::isAllowedByOpenBasedir($path)) {
                continue;
            }
            if (!is_dir($path)) {
                self::tryPrepareDir($path);
            }
            if (!is_dir($path)) {
                continue;
            }
            if (!is_writable($path)) {
                continue;
            }

            session_save_path($path);
            return;
        }
    }

    private static function tryPrepareDir(string $path): void
    {
        $path = rtrim($path, '/');
        if ($path === '') {
            return;
        }
        if (!self::isAllowedByOpenBasedir($path)) {
            return;
        }
        if (!is_dir($path)) {
            @mkdir($path, 0777, true);
        }
        if (is_dir($path) && !is_writable($path)) {
            @chmod($path, 0777);
        }
    }

    private static function normalizeSavePath(string $savePath): string
    {
        $savePath = trim($savePath);
        if ($savePath === '') {
            return '';
        }

        // Some environments use "N;/path" format
        $parts = explode(';', $savePath);
        return trim((string) end($parts));
    }

    private static function isAllowedByOpenBasedir(string $path): bool
    {
        $ob = (string) ini_get('open_basedir');
        $ob = trim($ob);
        if ($ob === '') {
            return true;
        }

        $real = realpath($path);
        if ($real === false) {
            return false;
        }

        $allowedRoots = array_filter(array_map('trim', explode(':', $ob)));
        foreach ($allowedRoots as $root) {
            $rootReal = realpath($root);
            if ($rootReal === false) {
                continue;
            }
            if (str_starts_with($real, $rootReal)) {
                return true;
            }
        }
        return false;
    }

    public static function regenerate(): void
    {
        self::start();
        $ok = session_regenerate_id(true);
        if ($ok) {
            self::refreshCookie();
        }
    }

    public static function commit(): void
    {
        if (session_status() === PHP_SESSION_ACTIVE) {
            session_write_close();
        }
    }

    private static function refreshCookie(): void
    {
        if (headers_sent()) {
            return;
        }

        setcookie(session_name(), session_id(), [
            'expires' => 0,
            'path' => '/',
            'domain' => '',
            'secure' => false,
            'httponly' => true,
            'samesite' => self::COOKIE_SAMESITE,
        ]);
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
