<?php

declare(strict_types=1);

namespace App\Support;

final class Csrf
{
    private const COOKIE_NAME = 'TBCSRF';

    public static function token(): string
    {
        $token = Session::get('csrf_token');
        if (is_string($token) && $token !== '') {
            self::setCookie($token);
            return $token;
        }

        $cookieToken = $_COOKIE[self::COOKIE_NAME] ?? null;
        if (is_string($cookieToken) && self::isValidFormat($cookieToken)) {
            Session::set('csrf_token', $cookieToken);
            self::setCookie($cookieToken);
            return $cookieToken;
        }

        $token = bin2hex(random_bytes(32));
        Session::set('csrf_token', $token);
        self::setCookie($token);
        return $token;
    }

    public static function validate(?string $token): bool
    {
        $sessionToken = Session::get('csrf_token');
        if (!is_string($sessionToken) || $sessionToken === '' || !is_string($token) || $token === '') {
            // If session token is missing, allow cookie-based validation (heals session).
            if (!is_string($token) || $token === '' || !self::isValidFormat($token)) {
                return false;
            }
            $cookieToken = $_COOKIE[self::COOKIE_NAME] ?? null;
            if (is_string($cookieToken) && hash_equals($cookieToken, $token)) {
                Session::set('csrf_token', $token);
                self::setCookie($token);
                return true;
            }
            return false;
        }

        if (hash_equals($sessionToken, $token)) {
            self::setCookie($sessionToken);
            return true;
        }

        $cookieToken = $_COOKIE[self::COOKIE_NAME] ?? null;
        if (is_string($cookieToken) && hash_equals($cookieToken, $token)) {
            // If cookie matches posted token but session drifted, heal the session.
            Session::set('csrf_token', $token);
            self::setCookie($token);
            return true;
        }

        return false;
    }

    private static function isValidFormat(string $token): bool
    {
        return (bool) preg_match('/^[a-f0-9]{64}$/', $token);
    }

    private static function setCookie(string $token): void
    {
        if (!self::isValidFormat($token) || headers_sent()) {
            return;
        }

        setcookie(self::COOKIE_NAME, $token, [
            'expires' => 0,
            'path' => '/',
            'domain' => '',
            'secure' => false,
            'httponly' => true,
            'samesite' => 'Lax',
        ]);
    }
}
