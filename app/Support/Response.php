<?php

declare(strict_types=1);

namespace App\Support;

final class Response
{
    public static function redirect(string $path): never
    {
        \App\Support\Session::commit();
        header('Location: ' . $path);
        exit;
    }

    /**
     * @param array<string, mixed> $data
     */
    public static function json(array $data, int $status = 200): never
    {
        http_response_code($status);
        header('Content-Type: application/json; charset=utf-8');
        echo (string) json_encode($data, JSON_UNESCAPED_UNICODE);
        exit;
    }

    public static function abort(int $status, string $message = ''): never
    {
        http_response_code($status);
        if ($message !== '') {
            echo $message;
        }
        exit;
    }
}
