<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Models\Categories;
use App\Models\Favorite;
use App\Models\Recipe;
use App\Support\Auth;
use App\Support\Csrf;
use App\Support\View;

final class HomeController
{
    public function index(): void
    {
        $user = Auth::user();
        $recipes = Recipe::listApproved(null, '');
        $favoriteIds = [];
        if ($user) {
            $favoriteIds = array_fill_keys(Favorite::recipeIdsForUser((string) $user['user_id']), true);
        }

        View::render('home', [
            'user' => $user,
            'categories' => Categories::all(),
            'featured' => Recipe::listFeatured(3),
            'recipes' => $recipes,
            'favoriteIds' => $favoriteIds,
            'csrf' => Csrf::token(),
        ]);
    }
}
