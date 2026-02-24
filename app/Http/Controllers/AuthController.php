<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Models\User;
use App\Support\Auth;
use App\Support\Csrf;
use App\Support\Request;
use App\Support\Response;
use App\Support\Session;
use App\Support\Validation;
use App\Support\View;

final class AuthController
{
    public function showLogin(): void
    {
        View::render('auth/login', [
            'errors' => Session::pullFlash('errors', []),
            'message' => Session::pullFlash('message', ''),
            'old' => Session::pullFlash('old', []),
            'csrf' => Csrf::token(),
        ]);
    }

    public function login(): void
    {
        if (!Request::isPost()) {
            Response::abort(405);
        }
        if (!Csrf::validate($_POST['csrf'] ?? null)) {
            Session::flash('errors', ['Invalid CSRF token. Please refresh and try again.']);
            Response::redirect('?route=login');
        }

        $email = trim(Request::input('email'));
        $password = Request::input('password');

        $errors = Validation::validateLogin($email, $password);
        if ($errors !== []) {
            Session::flash('errors', $errors);
            Session::flash('old', ['email' => $email]);
            Response::redirect('?route=login');
        }

        $user = User::findByEmail($email);
        if ($user === null || !is_string($user['password'] ?? null) || !password_verify($password, (string) $user['password'])) {
            Session::flash('errors', ['Invalid email or password.']);
            Session::flash('old', ['email' => $email]);
            Response::redirect('?route=login');
        }

        if (($user['status'] ?? '') !== 'active') {
            Session::flash('errors', ['Your account is disabled.']);
            Response::redirect('?route=login');
        }

        Auth::login((string) $user['user_id']);
        Response::redirect('?route=home');
    }

    public function showRegister(): void
    {
        View::render('auth/register', [
            'errors' => Session::pullFlash('errors', []),
            'message' => Session::pullFlash('message', ''),
            'old' => Session::pullFlash('old', []),
            'csrf' => Csrf::token(),
        ]);
    }

    public function register(): void
    {
        if (!Request::isPost()) {
            Response::abort(405);
        }
        if (!Csrf::validate($_POST['csrf'] ?? null)) {
            Session::flash('errors', ['Invalid CSRF token. Please refresh and try again.']);
            Response::redirect('?route=register');
        }

        $username = trim(Request::input('username'));
        $email = trim(Request::input('email'));
        $password = Request::input('password');
        $confirm = Request::input('confirm_password');

        $errors = Validation::validateRegister($username, $email, $password, $confirm);
        if ($errors !== []) {
            Session::flash('errors', $errors);
            Session::flash('old', ['username' => $username, 'email' => $email]);
            Response::redirect('?route=register');
        }

        $hash = password_hash($password, PASSWORD_DEFAULT);
        $userId = User::create($username, $email, $hash, 'user');

        Auth::login($userId);
        Response::redirect('?route=home');
    }

    public function logout(): void
    {
        Auth::logout();
        Session::flash('message', 'Logged out successfully.');
        Response::redirect('?route=login');
    }
}
