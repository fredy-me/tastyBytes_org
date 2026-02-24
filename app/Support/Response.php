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

    public static function abort(int $status, string $message = ''): never
    {
        http_response_code($status);
        if ($message !== '') {
            echo $message;
        }
        exit;
    }
}
