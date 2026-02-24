<?php

declare(strict_types=1);

require __DIR__ . '/../app/Support/Autoload.php';

\App\Support\Autoload::register();
\App\Support\Session::start();

// If a disabled user is already logged in, this call logs them out.
\App\Support\Auth::user();

$route = \App\Support\Request::route();

switch ($route) {
    case 'debug_session':
        $ip = $_SERVER['REMOTE_ADDR'] ?? '';
        if ($ip !== '127.0.0.1' && $ip !== '::1') {
            \App\Support\Response::abort(404);
        }
        header('Content-Type: text/plain; charset=utf-8');
        echo "method=" . (\App\Support\Request::method()) . "\n";
        echo "remote_addr=" . $ip . "\n";
        echo "open_basedir=" . (string) ini_get('open_basedir') . "\n";
        echo "session_cookie_path=" . (string) ini_get('session.cookie_path') . "\n";
        echo "session_status=" . session_status() . "\n";
        echo "session_name=" . session_name() . "\n";
        echo "session_id=" . session_id() . "\n";
        echo "cookie_value=" . (string) ($_COOKIE[session_name()] ?? '') . "\n";
        echo "cookie_matches_session_id=" . ((string) ($_COOKIE[session_name()] ?? '') === session_id() ? 'yes' : 'no') . "\n";
        echo "session_save_path=" . session_save_path() . "\n";
        echo "cookie_in_request=" . (isset($_COOKIE[session_name()]) ? 'yes' : 'no') . "\n";
        echo "cookie_keys=" . implode(',', array_keys($_COOKIE)) . "\n";
        echo "csrf_cookie=" . (string) ($_COOKIE['TBCSRF'] ?? '') . "\n";
        echo "csrf_in_session=" . (\App\Support\Session::get('csrf_token') ? 'yes' : 'no') . "\n";
        echo "csrf_token=" . (\App\Support\Csrf::token()) . "\n";
        $authUserId = \App\Support\Session::get(\App\Support\Auth::SESSION_USER_ID);
        echo "auth_user_id=" . (is_string($authUserId) ? $authUserId : '(missing)') . "\n";
        $authUser = \App\Support\Auth::user();
        echo "auth_user_loaded=" . ($authUser ? 'yes' : 'no') . "\n";
        if (is_array($authUser)) {
            echo "auth_username=" . (string) ($authUser['username'] ?? '') . "\n";
            echo "auth_role=" . (string) ($authUser['role'] ?? '') . "\n";
            echo "auth_status=" . (string) ($authUser['status'] ?? '') . "\n";
        }
        echo "response_headers=" . json_encode(headers_list()) . "\n";
        exit;
    case 'debug_csrf':
        $ip = $_SERVER['REMOTE_ADDR'] ?? '';
        if ($ip !== '127.0.0.1' && $ip !== '::1') {
            \App\Support\Response::abort(404);
        }
        if (\App\Support\Request::isPost()) {
            header('Content-Type: text/plain; charset=utf-8');
            $posted = $_POST['csrf'] ?? null;
            echo "posted_csrf=" . (is_string($posted) ? $posted : '(missing)') . "\n";
            echo "session_csrf=" . (\App\Support\Session::get('csrf_token') ?: '(missing)') . "\n";
            echo "cookie_csrf=" . (string) ($_COOKIE['TBCSRF'] ?? '') . "\n";
            echo "valid=" . (\App\Support\Csrf::validate(is_string($posted) ? $posted : null) ? 'yes' : 'no') . "\n";
            echo "session_id=" . session_id() . "\n";
            echo "cookie_value=" . (string) ($_COOKIE[session_name()] ?? '') . "\n";
            exit;
        }
        $token = \App\Support\Csrf::token();
        header('Content-Type: text/html; charset=utf-8');
        echo '<!doctype html><meta charset="utf-8"><title>CSRF Debug</title>';
        echo '<p>Submit to verify CSRF/session works.</p>';
        echo '<form method="post" action="?route=debug_csrf">';
        echo '<input type="hidden" name="csrf" value="' . htmlspecialchars($token) . '">';
        echo '<button type="submit">Submit</button>';
        echo '</form>';
        exit;
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
    case 'my_recipes_status':
        (new \App\Http\Controllers\RecipeController())->myRecipeStatuses();
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
    case 'admin_recipes':
        (new \App\Http\Controllers\AdminController())->recipes();
        break;
    case 'admin_recipe_delete':
        (new \App\Http\Controllers\AdminController())->deleteRecipe();
        break;
    case 'profile':
        (new \App\Http\Controllers\ProfileController())->show();
        break;
    case 'profile_update':
        (new \App\Http\Controllers\ProfileController())->update();
        break;
    default:
        \App\Support\Response::abort(404, 'Not found.');
}
