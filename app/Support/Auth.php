<?php

declare(strict_types=1);

namespace App\Support;

use App\Models\User;

final class Auth
{
    public const SESSION_USER_ID = 'auth_user_id';
    private const SESSION_LAST_ACTIVITY = 'last_activity';
    private const IDLE_TIMEOUT_SECONDS = 900;

    public static function userId(): ?string
    {
        $id = Session::get(self::SESSION_USER_ID);
        return is_string($id) && $id !== '' ? $id : null;
    }

    /** @return array<string, mixed>|null */
    public static function user(): ?array
    {
        $id = self::userId();
        if ($id === null) {
            return null;
        }

        $last = Session::get(self::SESSION_LAST_ACTIVITY);
        $lastInt = null;
        if (is_int($last)) {
            $lastInt = $last;
        } elseif (is_string($last) && ctype_digit($last)) {
            $lastInt = (int) $last;
        }
        if ($lastInt !== null && (time() - $lastInt) > self::IDLE_TIMEOUT_SECONDS) {
            Session::flash('errors', ['Your session expired due to inactivity. Please log in again.']);
            self::logout();
            return null;
        }

        $user = User::findById($id);
        if ($user === null) {
            self::logout();
            return null;
        }

        if (($user['status'] ?? '') !== 'active') {
            self::logout();
            return null;
        }

        Session::set(self::SESSION_LAST_ACTIVITY, time());

        return $user;
    }

    public static function login(string $userId): void
    {
        Session::regenerate();
        Session::set(self::SESSION_USER_ID, $userId);
    }

    public static function logout(): void
    {
        Session::forget(self::SESSION_USER_ID);
        Session::forget(self::SESSION_LAST_ACTIVITY);
        Session::regenerate();
    }
}
