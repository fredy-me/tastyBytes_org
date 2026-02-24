<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Models\User;
use App\Support\Auth;
use App\Support\Csrf;
use App\Support\Request;
use App\Support\Response;
use App\Support\Session;
use App\Support\View;
use RuntimeException;

final class ProfileController
{
    public function show(): void
    {
        $user = Auth::user();
        if (!$user) {
            Response::redirect('?route=login');
        }

        View::render('profile/show', [
            'user' => $user,
            'csrf' => Csrf::token(),
            'errors' => Session::pullFlash('errors', []),
            'message' => Session::pullFlash('message', ''),
        ]);
    }

    public function update(): void
    {
        $user = Auth::user();
        if (!$user) {
            Response::redirect('?route=login');
        }
        if (!Request::isPost()) {
            Response::abort(405);
        }
        if (!Csrf::validate($_POST['csrf'] ?? null)) {
            Response::abort(419, 'Invalid CSRF token.');
        }

        $username = trim(Request::input('username'));
        $email = trim(Request::input('email'));
        $newPassword = Request::input('new_password');
        $confirm = Request::input('confirm_password');

        $errors = [];
        if ($username === '') {
            $errors[] = 'Username is required.';
        }
        if ($email === '' || filter_var($email, FILTER_VALIDATE_EMAIL) === false) {
            $errors[] = 'A valid email address is required.';
        }
        if ($newPassword !== '') {
            if (strlen($newPassword) < 8) {
                $errors[] = 'New password must be at least 8 characters.';
            }
            if ($confirm === '' || $confirm !== $newPassword) {
                $errors[] = 'Password confirmation does not match.';
            }
        }

        if ($errors !== []) {
            Session::flash('errors', $errors);
            Response::redirect('?route=profile');
        }

        try {
            User::updateProfile((string) $user['user_id'], $username, $email);
        } catch (RuntimeException $e) {
            Session::flash('errors', [$e->getMessage()]);
            Response::redirect('?route=profile');
        }

        if ($newPassword !== '') {
            $hash = password_hash($newPassword, PASSWORD_DEFAULT);
            User::updatePassword((string) $user['user_id'], $hash);
        }

        Session::flash('message', 'Profile updated.');
        Response::redirect('?route=profile');
    }
}

