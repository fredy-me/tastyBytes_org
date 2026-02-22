<?php

declare(strict_types=1);

namespace App\Models;

final class Categories
{
    public const BREAKFAST = 'Breakfast';
    public const LUNCH = 'Lunch';
    public const DINNER = 'Dinner';
    public const DESSERT = 'Dessert';
    public const SNACK = 'Snack';

    /** @return list<string> */
    public static function all(): array
    {
        return [
            self::BREAKFAST,
            self::LUNCH,
            self::DINNER,
            self::DESSERT,
            self::SNACK,
        ];
    }
}

