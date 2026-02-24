<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Models\Categories;
use App\Models\Favorite;
use App\Models\Recipe;
use App\Support\Auth;
use App\Support\Csrf;
use App\Support\Request;
use App\Support\Response;
use App\Support\Session;
use App\Support\View;
use RuntimeException;

final class RecipeController
{
    public function index(): void
    {
        $category = trim(Request::input('category'));
        $search = trim(Request::input('q'));
        $category = $category !== '' ? $category : null;

        $recipes = Recipe::listApproved($category, $search);

        $user = Auth::user();
        $favoriteIds = [];
        if ($user) {
            $favoriteIds = array_fill_keys(Favorite::recipeIdsForUser((string) $user['user_id']), true);
        }

        View::render('recipes/index', [
            'user' => $user,
            'recipes' => $recipes,
            'favoriteIds' => $favoriteIds,
            'categories' => Categories::all(),
            'selectedCategory' => $category ?? '',
            'search' => $search,
            'csrf' => Csrf::token(),
            'message' => Session::pullFlash('message', ''),
            'errors' => Session::pullFlash('errors', []),
        ]);
    }

    public function show(): void
    {
        $id = trim(Request::input('id'));
        if ($id === '') {
            Response::abort(404);
        }

        $recipe = Recipe::findById($id);
        if ($recipe === null) {
            Response::abort(404);
        }

        $user = Auth::user();
        $isOwner = $user && ((string) $recipe['user_id'] === (string) $user['user_id']);
        $isAdmin = $user && ((string) ($user['role'] ?? '') === 'admin');

        if (($recipe['status'] ?? '') !== 'approved' && !$isOwner && !$isAdmin) {
            Response::abort(403, 'Recipe not available.');
        }

        View::render('recipes/show', [
            'user' => $user,
            'recipe' => $recipe,
            'ingredients' => Recipe::decodeList($recipe['ingredients'] ?? ''),
            'steps' => Recipe::decodeList($recipe['steps'] ?? ''),
            'isFavorite' => $user ? Favorite::isFavorite((string) $user['user_id'], $id) : false,
            'csrf' => Csrf::token(),
        ]);
    }

    public function myRecipes(): void
    {
        $user = Auth::user();
        if (!$user) {
            Response::redirect('?route=login');
        }

        $recipes = Recipe::listByUser((string) $user['user_id']);

        View::render('recipes/my', [
            'user' => $user,
            'recipes' => $recipes,
            'message' => Session::pullFlash('message', ''),
            'errors' => Session::pullFlash('errors', []),
            'csrf' => Csrf::token(),
        ]);
    }

    public function myRecipeStatuses(): void
    {
        $user = Auth::user();
        if (!$user) {
            Response::abort(401);
        }

        $recipes = Recipe::listByUser((string) $user['user_id']);
        $out = [];
        foreach ($recipes as $r) {
            $rid = (string) ($r['recipe_id'] ?? '');
            if ($rid === '') {
                continue;
            }
            $out[$rid] = [
                'status' => (string) ($r['status'] ?? ''),
                'updated_at' => (string) ($r['updated_at'] ?? ''),
            ];
        }

        Response::json(['recipes' => $out]);
    }

    public function createForm(): void
    {
        $user = Auth::user();
        if (!$user) {
            Response::redirect('?route=login');
        }

        View::render('recipes/form', [
            'user' => $user,
            'mode' => 'create',
            'recipe' => null,
            'categories' => Categories::all(),
            'csrf' => Csrf::token(),
            'errors' => Session::pullFlash('errors', []),
            'old' => Session::pullFlash('old', []),
        ]);
    }

    public function create(): void
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

        $title = trim(Request::input('title'));
        $category = trim(Request::input('category'));
        $imageUrl = trim(Request::input('image_url'));

        $ingredients = $this->cleanList($_POST['ingredients'] ?? []);
        $steps = $this->cleanList($_POST['steps'] ?? []);

        $errors = $this->validateRecipe($title, $category, $ingredients, $steps, $imageUrl);
        if ($errors !== []) {
            Session::flash('errors', $errors);
            Session::flash('old', [
                'title' => $title,
                'category' => $category,
                'image_url' => $imageUrl,
                'ingredients' => $ingredients,
                'steps' => $steps,
            ]);
            Response::redirect('?route=recipe_create');
        }

        try {
            $id = Recipe::create((string) $user['user_id'], $title, $category, $ingredients, $steps, $imageUrl !== '' ? $imageUrl : null);
        } catch (RuntimeException $e) {
            Session::flash('errors', [$e->getMessage()]);
            Session::flash('old', [
                'title' => $title,
                'category' => $category,
                'image_url' => $imageUrl,
                'ingredients' => $ingredients,
                'steps' => $steps,
            ]);
            Response::redirect('?route=recipe_create');
        }

        Session::flash('message', 'Recipe submitted for approval.');
        Response::redirect('?route=my_recipes');
    }

    public function editForm(): void
    {
        $user = Auth::user();
        if (!$user) {
            Response::redirect('?route=login');
        }

        $id = trim(Request::input('id'));
        $recipe = $id !== '' ? Recipe::findById($id) : null;
        if ($recipe === null || (string) $recipe['user_id'] !== (string) $user['user_id']) {
            Response::abort(404);
        }

        View::render('recipes/form', [
            'user' => $user,
            'mode' => 'edit',
            'recipe' => $recipe,
            'categories' => Categories::all(),
            'csrf' => Csrf::token(),
            'errors' => Session::pullFlash('errors', []),
            'old' => Session::pullFlash('old', []),
            'ingredients' => Recipe::decodeList($recipe['ingredients'] ?? ''),
            'steps' => Recipe::decodeList($recipe['steps'] ?? ''),
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

        $id = trim(Request::input('id'));
        $recipe = $id !== '' ? Recipe::findById($id) : null;
        if ($recipe === null || (string) $recipe['user_id'] !== (string) $user['user_id']) {
            Response::abort(404);
        }

        $title = trim(Request::input('title'));
        $category = trim(Request::input('category'));
        $imageUrl = trim(Request::input('image_url'));
        $ingredients = $this->cleanList($_POST['ingredients'] ?? []);
        $steps = $this->cleanList($_POST['steps'] ?? []);

        $errors = $this->validateRecipe($title, $category, $ingredients, $steps, $imageUrl);
        if ($errors !== []) {
            Session::flash('errors', $errors);
            Session::flash('old', [
                'title' => $title,
                'category' => $category,
                'image_url' => $imageUrl,
                'ingredients' => $ingredients,
                'steps' => $steps,
            ]);
            Response::redirect('?route=recipe_edit&id=' . urlencode($id));
        }

        try {
            Recipe::update($id, (string) $user['user_id'], $title, $category, $ingredients, $steps, $imageUrl !== '' ? $imageUrl : null);
        } catch (RuntimeException $e) {
            Session::flash('errors', [$e->getMessage()]);
            Session::flash('old', [
                'title' => $title,
                'category' => $category,
                'image_url' => $imageUrl,
                'ingredients' => $ingredients,
                'steps' => $steps,
            ]);
            Response::redirect('?route=recipe_edit&id=' . urlencode($id));
        }

        Session::flash('message', 'Recipe updated and resubmitted for approval.');
        Response::redirect('?route=my_recipes');
    }

    public function delete(): void
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

        $id = trim(Request::input('id'));
        $recipe = $id !== '' ? Recipe::findById($id) : null;
        if ($recipe === null || (string) $recipe['user_id'] !== (string) $user['user_id']) {
            Response::abort(404);
        }

        Recipe::delete($id, (string) $user['user_id']);
        Session::flash('message', 'Recipe deleted.');
        Response::redirect('?route=my_recipes');
    }

    public function favorites(): void
    {
        $user = Auth::user();
        if (!$user) {
            Response::redirect('?route=login');
        }

        View::render('favorites/index', [
            'user' => $user,
            'recipes' => Favorite::listForUser((string) $user['user_id']),
            'csrf' => Csrf::token(),
        ]);
    }

    public function favoriteToggle(): void
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

        $recipeId = trim(Request::input('recipe_id'));
        if ($recipeId === '') {
            Response::abort(400);
        }

        $recipe = Recipe::findById($recipeId);
        if ($recipe === null || (string) ($recipe['status'] ?? '') !== 'approved') {
            Response::abort(404);
        }

        $uid = (string) $user['user_id'];
        if (Favorite::isFavorite($uid, $recipeId)) {
            Favorite::remove($uid, $recipeId);
        } else {
            Favorite::add($uid, $recipeId);
        }

        $back = $_SERVER['HTTP_REFERER'] ?? '?route=recipes';
        Response::redirect($back);
    }

    /** @param mixed $raw @return list<string> */
    private function cleanList(mixed $raw): array
    {
        if (!is_array($raw)) {
            return [];
        }
        $out = [];
        foreach ($raw as $item) {
            if (!is_string($item)) {
                continue;
            }
            $t = trim($item);
            if ($t !== '') {
                $out[] = $t;
            }
        }
        return $out;
    }

    /** @param list<string> $ingredients @param list<string> $steps @return list<string> */
    private function validateRecipe(string $title, string $category, array $ingredients, array $steps, string $imageUrl): array
    {
        $errors = [];
        if ($title === '') {
            $errors[] = 'Title is required.';
        }
        if (!in_array($category, Categories::all(), true)) {
            $errors[] = 'Please choose a valid category.';
        }
        if ($ingredients === []) {
            $errors[] = 'At least one ingredient is required.';
        }
        if ($steps === []) {
            $errors[] = 'At least one preparation step is required.';
        }
        if ($imageUrl !== '' && filter_var($imageUrl, FILTER_VALIDATE_URL) === false) {
            $errors[] = 'Image URL must be a valid URL.';
        }
        return $errors;
    }
}
