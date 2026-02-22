<?php

declare(strict_types=1);

namespace App\Support;

final class Validation
{
    /** @return list<string> */
    public static function validateRegister(string $username, string $email, string $password, string $confirm): array
    {
        $errors = [];

        if ($username === '') {
            $errors[] = 'Username is required.';
        }
        if ($email === '' || filter_var($email, FILTER_VALIDATE_EMAIL) === false) {
            $errors[] = 'A valid email address is required.';
        }
        if ($password === '') {
            $errors[] = 'Password is required.';
        }
        if ($confirm === '' || $confirm !== $password) {
            $errors[] = 'Password confirmation does not match.';
        }

        return $errors;
    }

    /** @return list<string> */
    public static function validateLogin(string $email, string $password): array
    {
        $errors = [];
        if ($email === '' || filter_var($email, FILTER_VALIDATE_EMAIL) === false) {
            $errors[] = 'A valid email address is required.';
        }
        if ($password === '') {
            $errors[] = 'Password is required.';
        }
        return $errors;
    }
}

