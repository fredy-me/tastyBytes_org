<?php

declare(strict_types=1);

namespace App\Models;

use App\Support\Id;
use PDO;
use PDOException;
use RuntimeException;

final class User extends Model
{
    public string $user_id;
    public string $username;
    public string $email;
    public string $password;
    public string $role;
    public string $status;
    public string $created_at;

    /** @return array<string, mixed>|null */
    public static function findById(string $userId): ?array
    {
        $stmt = self::db()->prepare('SELECT * FROM users WHERE user_id = :id LIMIT 1');
        $stmt->execute(['id' => $userId]);
        $row = $stmt->fetch();
        return is_array($row) ? $row : null;
    }

    /** @return array<string, mixed>|null */
    public static function findByEmail(string $email): ?array
    {
        $stmt = self::db()->prepare('SELECT * FROM users WHERE email = :email LIMIT 1');
        $stmt->execute(['email' => $email]);
        $row = $stmt->fetch();
        return is_array($row) ? $row : null;
    }

    /**
     * @return string Newly created user_id
     */
    public static function create(string $username, string $email, string $passwordHash, string $role = 'user'): string
    {
        $userId = Id::user();
        $db = self::db();

        $stmt = $db->prepare(
            'INSERT INTO users (user_id, username, email, password, role, status) 
             VALUES (:user_id, :username, :email, :password, :role, :status)'
        );

        try {
            $stmt->execute([
                'user_id' => $userId,
                'username' => $username,
                'email' => $email,
                'password' => $passwordHash,
                'role' => $role,
                'status' => 'active',
            ]);
        } catch (PDOException $e) {
            // 23000 = integrity constraint violation (e.g. duplicate email)
            if ($e->getCode() === '23000') {
                throw new RuntimeException('Email already exists.');
            }
            throw $e;
        }

        return $userId;
    }

    public static function disable(string $userId): void
    {
        $stmt = self::db()->prepare("UPDATE users SET status = 'disabled' WHERE user_id = :id");
        $stmt->execute(['id' => $userId]);
    }

    public static function updateProfile(string $userId, string $username, string $email): void
    {
        $stmt = self::db()->prepare(
            'UPDATE users SET username = :username, email = :email WHERE user_id = :id'
        );

        try {
            $stmt->execute([
                'id' => $userId,
                'username' => $username,
                'email' => $email,
            ]);
        } catch (PDOException $e) {
            if ($e->getCode() === '23000') {
                throw new RuntimeException('Email already exists.');
            }
            throw $e;
        }
    }

    public static function updatePassword(string $userId, string $passwordHash): void
    {
        $stmt = self::db()->prepare('UPDATE users SET password = :password WHERE user_id = :id');
        $stmt->execute([
            'id' => $userId,
            'password' => $passwordHash,
        ]);
    }

    public static function delete(string $userId): void
    {
        $db = self::db();
        $db->beginTransaction();
        try {
            // Requirement: deleting a user deletes their recipes too.
            $db->prepare('DELETE FROM favorites WHERE user_id = :id')->execute(['id' => $userId]);
            $db->prepare(
                'DELETE f FROM favorites f INNER JOIN recipes r ON r.recipe_id = f.recipe_id WHERE r.user_id = :id'
            )->execute(['id' => $userId]);
            $db->prepare('DELETE FROM recipes WHERE user_id = :id')->execute(['id' => $userId]);
            $db->prepare('DELETE FROM users WHERE user_id = :id')->execute(['id' => $userId]);
            $db->commit();
        } catch (\Throwable $e) {
            $db->rollBack();
            throw $e;
        }
    }
}
