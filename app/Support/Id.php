<?php

declare(strict_types=1);

namespace App\Support;

use RuntimeException;

final class Id
{
    public static function user(): string
    {
        return self::prefixed('USR_');
    }

    public static function recipe(): string
    {
        return self::prefixed('RCP_');
    }

    public static function favorite(): string
    {
        return self::prefixed('FAV_');
    }

    private static function prefixed(string $prefix): string
    {
        // 4 + 32 = 36 chars (fits VARCHAR(50))
        try {
            return $prefix . bin2hex(random_bytes(16));
        } catch (\Throwable $e) {
            throw new RuntimeException('Failed to generate ID.', 0, $e);
        }
    }
}

