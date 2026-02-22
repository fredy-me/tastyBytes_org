<?php

declare(strict_types=1);

namespace App\Models;

use App\Support\Id;
use DateTimeImmutable;
use PDOException;
use RuntimeException;

final class Recipe extends Model
{
    /** @return array<string, mixed>|null */
    public static function findById(string $recipeId): ?array
    {
        $stmt = self::db()->prepare('SELECT * FROM recipes WHERE recipe_id = :id LIMIT 1');
        $stmt->execute(['id' => $recipeId]);
        $row = $stmt->fetch();
        return is_array($row) ? $row : null;
    }

    /** @return list<array<string, mixed>> */
    public static function listApproved(?string $category = null, string $search = ''): array
    {
        $search = trim($search);
        $params = ['status' => 'approved'];

        $sql = 'SELECT r.*, u.username AS author_name
                FROM recipes r
                INNER JOIN users u ON u.user_id = r.user_id
                WHERE r.status = :status';

        if ($category !== null && $category !== '') {
            $sql .= ' AND r.category = :category';
            $params['category'] = $category;
        }

        if ($search !== '') {
            $sql .= ' AND r.title LIKE :search';
            $params['search'] = '%' . $search . '%';
        }

        $sql .= ' ORDER BY r.created_at DESC';

        $stmt = self::db()->prepare($sql);
        $stmt->execute($params);
        $rows = $stmt->fetchAll();
        return is_array($rows) ? $rows : [];
    }

    /** @return list<array<string, mixed>> */
    public static function listByUser(string $userId): array
    {
        $stmt = self::db()->prepare('SELECT * FROM recipes WHERE user_id = :id ORDER BY created_at DESC');
        $stmt->execute(['id' => $userId]);
        $rows = $stmt->fetchAll();
        return is_array($rows) ? $rows : [];
    }

    /** @return list<array<string, mixed>> */
    public static function listPending(): array
    {
        $stmt = self::db()->prepare(
            "SELECT r.*, u.username AS author_name
             FROM recipes r
             INNER JOIN users u ON u.user_id = r.user_id
             WHERE r.status = 'pending'
             ORDER BY r.created_at ASC"
        );
        $stmt->execute();
        $rows = $stmt->fetchAll();
        return is_array($rows) ? $rows : [];
    }

    /** @param list<string> $ingredients @param list<string> $steps */
    public static function create(
        string $userId,
        string $title,
        string $category,
        array $ingredients,
        array $steps,
        ?string $imageUrl
    ): string {
        $recipeId = Id::recipe();
        $now = (new DateTimeImmutable())->format('Y-m-d H:i:s');

        $stmt = self::db()->prepare(
            'INSERT INTO recipes (recipe_id, title, category, ingredients, steps, image_url, status, user_id, created_at, updated_at)
             VALUES (:recipe_id, :title, :category, :ingredients, :steps, :image_url, :status, :user_id, :created_at, :updated_at)'
        );

        try {
            $stmt->execute([
                'recipe_id' => $recipeId,
                'title' => $title,
                'category' => $category,
                'ingredients' => json_encode(array_values($ingredients), JSON_UNESCAPED_UNICODE),
                'steps' => json_encode(array_values($steps), JSON_UNESCAPED_UNICODE),
                'image_url' => $imageUrl,
                'status' => 'pending',
                'user_id' => $userId,
                'created_at' => $now,
                'updated_at' => $now,
            ]);
        } catch (PDOException $e) {
            if ($e->getCode() === '23000') {
                throw new RuntimeException('Recipe title already exists.');
            }
            throw $e;
        }

        return $recipeId;
    }

    /** @param list<string> $ingredients @param list<string> $steps */
    public static function update(
        string $recipeId,
        string $userId,
        string $title,
        string $category,
        array $ingredients,
        array $steps,
        ?string $imageUrl
    ): void {
        $now = (new DateTimeImmutable())->format('Y-m-d H:i:s');

        $stmt = self::db()->prepare(
            'UPDATE recipes
             SET title = :title,
                 category = :category,
                 ingredients = :ingredients,
                 steps = :steps,
                 image_url = :image_url,
                 status = :status,
                 updated_at = :updated_at
             WHERE recipe_id = :recipe_id AND user_id = :user_id'
        );

        try {
            $stmt->execute([
                'recipe_id' => $recipeId,
                'user_id' => $userId,
                'title' => $title,
                'category' => $category,
                'ingredients' => json_encode(array_values($ingredients), JSON_UNESCAPED_UNICODE),
                'steps' => json_encode(array_values($steps), JSON_UNESCAPED_UNICODE),
                'image_url' => $imageUrl,
                // Any edit returns the recipe to pending for re-approval
                'status' => 'pending',
                'updated_at' => $now,
            ]);
        } catch (PDOException $e) {
            if ($e->getCode() === '23000') {
                throw new RuntimeException('Recipe title already exists.');
            }
            throw $e;
        }
    }

    public static function delete(string $recipeId, string $userId): void
    {
        $db = self::db();
        $db->beginTransaction();
        try {
            $db->prepare('DELETE FROM favorites WHERE recipe_id = :rid')->execute(['rid' => $recipeId]);
            $db->prepare('DELETE FROM recipes WHERE recipe_id = :rid AND user_id = :uid')->execute([
                'rid' => $recipeId,
                'uid' => $userId,
            ]);
            $db->commit();
        } catch (\Throwable $e) {
            $db->rollBack();
            throw $e;
        }
    }

    public static function setStatus(string $recipeId, string $status): void
    {
        $now = (new DateTimeImmutable())->format('Y-m-d H:i:s');
        $stmt = self::db()->prepare('UPDATE recipes SET status = :status, updated_at = :updated_at WHERE recipe_id = :id');
        $stmt->execute(['status' => $status, 'updated_at' => $now, 'id' => $recipeId]);
    }

    /** @return list<string> */
    public static function decodeList(mixed $json): array
    {
        if (!is_string($json) || trim($json) === '') {
            return [];
        }
        $decoded = json_decode($json, true);
        if (!is_array($decoded)) {
            return [];
        }
        $out = [];
        foreach ($decoded as $item) {
            if (is_string($item)) {
                $t = trim($item);
                if ($t !== '') {
                    $out[] = $t;
                }
            }
        }
        return $out;
    }
}

