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
        (new \App\Http\Controllers\RecipeController())->index();
        break;
    case 'recipe':
        (new \App\Http\Controllers\RecipeController())->show();
        break;
    case 'my_recipes':
        (new \App\Http\Controllers\RecipeController())->myRecipes();
        break;
    case 'recipe_create':
        $c = new \App\Http\Controllers\RecipeController();
        if (\App\Support\Request::isPost()) {
            \App\Support\Response::abort(405);
        }
        $c->createForm();
        break;
    case 'recipe_store':
        (new \App\Http\Controllers\RecipeController())->create();
        break;
    case 'recipe_edit':
        (new \App\Http\Controllers\RecipeController())->editForm();
        break;
    case 'recipe_update':
        (new \App\Http\Controllers\RecipeController())->update();
        break;
    case 'recipe_delete':
        (new \App\Http\Controllers\RecipeController())->delete();
        break;
    case 'favorites':
        (new \App\Http\Controllers\RecipeController())->favorites();
        break;
    case 'favorite_toggle':
        (new \App\Http\Controllers\RecipeController())->favoriteToggle();
        break;
    case 'admin':
        (new \App\Http\Controllers\AdminController())->dashboard();
        break;
    case 'admin_approve':
        (new \App\Http\Controllers\AdminController())->approve();
        break;
    case 'admin_reject':
        (new \App\Http\Controllers\AdminController())->reject();
        break;
    case 'admin_users':
        (new \App\Http\Controllers\AdminController())->users();
        break;
    case 'admin_user_disable':
        (new \App\Http\Controllers\AdminController())->disableUser();
        break;
    case 'admin_user_delete':
        (new \App\Http\Controllers\AdminController())->deleteUser();
        break;
    default:
        \App\Support\Response::abort(404, 'Not found.');
}
