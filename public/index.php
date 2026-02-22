<?php

declare(strict_types=1);

require __DIR__ . '/../app/Support/Autoload.php';

\App\Support\Autoload::register();
\App\Support\Session::start();

// If a disabled user is already logged in, this call logs them out.
\App\Support\Auth::user();

$route = \App\Support\Request::route();

switch ($route) {
    case 'home':
        (new \App\Http\Controllers\HomeController())->index();
        break;
    case 'login':
        $controller = new \App\Http\Controllers\AuthController();
        if (\App\Support\Request::isPost()) {
            $controller->login();
        } else {
            $controller->showLogin();
        }
        break;
    case 'register':
        $controller = new \App\Http\Controllers\AuthController();
        if (\App\Support\Request::isPost()) {
            $controller->register();
        } else {
            $controller->showRegister();
        }
        break;
    case 'logout':
        (new \App\Http\Controllers\AuthController())->logout();
        break;
    case 'recipes':
        \App\Support\Response::abort(501, 'Recipes coming next (Feature C).');
        break;
    default:
        \App\Support\Response::abort(404, 'Not found.');
}

