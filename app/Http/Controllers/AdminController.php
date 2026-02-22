<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Models\Recipe;
use App\Models\User;
use App\Support\Auth;
use App\Support\Csrf;
use App\Support\Request;
use App\Support\Response;
use App\Support\Session;
use App\Support\View;

final class AdminController
{
    public function dashboard(): void
    {
        $user = Auth::user();
        if (!$user) {
            Response::redirect('?route=login');
        }
        if ((string) ($user['role'] ?? '') !== 'admin') {
            Response::abort(403);
        }

        $pending = Recipe::listPending();

        View::render('admin/dashboard', [
            'user' => $user,
            'pending' => $pending,
            'csrf' => Csrf::token(),
            'message' => Session::pullFlash('message', ''),
            'errors' => Session::pullFlash('errors', []),
        ]);
    }

    public function approve(): void
    {
        $this->requireAdminPost();
        $id = trim(Request::input('id'));
        if ($id === '') {
            Response::abort(400);
        }
        Recipe::setStatus($id, 'approved');
        Session::flash('message', 'Recipe approved.');
        Response::redirect('?route=admin');
    }

    public function reject(): void
    {
        $this->requireAdminPost();
        $id = trim(Request::input('id'));
        if ($id === '') {
            Response::abort(400);
        }
        Recipe::setStatus($id, 'rejected');
        Session::flash('message', 'Recipe rejected.');
        Response::redirect('?route=admin');
    }

    public function users(): void
    {
        $user = Auth::user();
        if (!$user) {
            Response::redirect('?route=login');
        }
        if ((string) ($user['role'] ?? '') !== 'admin') {
            Response::abort(403);
        }

        $stmt = \App\Models\Database::pdo()->query('SELECT user_id, username, email, role, status, created_at FROM users ORDER BY created_at DESC');
        $rows = $stmt ? $stmt->fetchAll() : [];

        View::render('admin/users', [
            'user' => $user,
            'users' => is_array($rows) ? $rows : [],
            'csrf' => Csrf::token(),
            'message' => Session::pullFlash('message', ''),
            'errors' => Session::pullFlash('errors', []),
        ]);
    }

    public function disableUser(): void
    {
        $this->requireAdminPost();
        $id = trim(Request::input('id'));
        if ($id === '') {
            Response::abort(400);
        }
        User::disable($id);
        Session::flash('message', 'User disabled.');
        Response::redirect('?route=admin_users');
    }

    public function deleteUser(): void
    {
        $this->requireAdminPost();
        $id = trim(Request::input('id'));
        if ($id === '') {
            Response::abort(400);
        }
        User::delete($id);
        Session::flash('message', 'User deleted.');
        Response::redirect('?route=admin_users');
    }

    private function requireAdminPost(): void
    {
        $user = Auth::user();
        if (!$user) {
            Response::redirect('?route=login');
        }
        if ((string) ($user['role'] ?? '') !== 'admin') {
            Response::abort(403);
        }
        if (!Request::isPost()) {
            Response::abort(405);
        }
        if (!Csrf::validate($_POST['csrf'] ?? null)) {
            Response::abort(419, 'Invalid CSRF token.');
        }
    }
}

