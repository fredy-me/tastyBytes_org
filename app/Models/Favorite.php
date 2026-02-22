<?php

declare(strict_types=1);

namespace App\Models;

use App\Support\Id;
use DateTimeImmutable;
use PDOException;

final class Favorite extends Model
{
    public static function isFavorite(string $userId, string $recipeId): bool
    {
        $stmt = self::db()->prepare('SELECT 1 FROM favorites WHERE user_id = :uid AND recipe_id = :rid LIMIT 1');
        $stmt->execute(['uid' => $userId, 'rid' => $recipeId]);
        return (bool) $stmt->fetchColumn();
    }

    /** @return list<string> */
    public static function recipeIdsForUser(string $userId): array
    {
        $stmt = self::db()->prepare('SELECT recipe_id FROM favorites WHERE user_id = :uid ORDER BY created_at DESC');
        $stmt->execute(['uid' => $userId]);
        $rows = $stmt->fetchAll();
        if (!is_array($rows)) {
            return [];
        }
        $ids = [];
        foreach ($rows as $row) {
            if (is_array($row) && is_string($row['recipe_id'] ?? null)) {
                $ids[] = (string) $row['recipe_id'];
            }
        }
        return $ids;
    }

    /** @return list<array<string, mixed>> */
    public static function listForUser(string $userId): array
    {
        $stmt = self::db()->prepare(
            "SELECT r.*, u.username AS author_name
             FROM favorites f
             INNER JOIN recipes r ON r.recipe_id = f.recipe_id
             INNER JOIN users u ON u.user_id = r.user_id
             WHERE f.user_id = :uid AND r.status = 'approved'
             ORDER BY f.created_at DESC"
        );
        $stmt->execute(['uid' => $userId]);
        $rows = $stmt->fetchAll();
        return is_array($rows) ? $rows : [];
    }

    public static function add(string $userId, string $recipeId): void
    {
        $id = Id::favorite();
        $now = (new DateTimeImmutable())->format('Y-m-d H:i:s');
        $stmt = self::db()->prepare(
            'INSERT INTO favorites (favorite_id, user_id, recipe_id, created_at) VALUES (:id, :uid, :rid, :created_at)'
        );
        try {
            $stmt->execute(['id' => $id, 'uid' => $userId, 'rid' => $recipeId, 'created_at' => $now]);
        } catch (PDOException $e) {
            // Ignore duplicates (UNIQUE user_id, recipe_id)
            if ($e->getCode() === '23000') {
                return;
            }
            throw $e;
        }
    }

    public static function remove(string $userId, string $recipeId): void
    {
        $stmt = self::db()->prepare('DELETE FROM favorites WHERE user_id = :uid AND recipe_id = :rid');
        $stmt->execute(['uid' => $userId, 'rid' => $recipeId]);
    }
}

