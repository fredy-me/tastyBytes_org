<?php

declare(strict_types=1);

namespace App\Support;

use App\Models\User;

final class Auth
{
    public const SESSION_USER_ID = 'auth_user_id';

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

        $user = User::findById($id);
        if ($user === null) {
            self::logout();
            return null;
        }

        if (($user['status'] ?? '') !== 'active') {
            self::logout();
            return null;
        }

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
        Session::regenerate();
    }
}

