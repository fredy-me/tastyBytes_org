<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Models\Categories;
use App\Models\Recipe;
use App\Support\Auth;
use App\Support\View;

final class HomeController
{
    public function index(): void
    {
        View::render('home', [
            'user' => Auth::user(),
            'categories' => Categories::all(),
            'featured' => Recipe::listFeatured(3),
        ]);
    }
}
