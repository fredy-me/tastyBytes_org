<?php

declare(strict_types=1);

namespace App\Support;

use RuntimeException;

final class View
{
    /**
     * @param array<string, mixed> $data
     */
    public static function render(string $template, array $data = []): void
    {
        $viewsDir = dirname(__DIR__) . '/Views';
        $templatePath = $viewsDir . '/' . ltrim($template, '/') . '.php';
        $layoutPath = $viewsDir . '/layout.php';

        if (!is_file($templatePath)) {
            throw new RuntimeException('Missing view: ' . $templatePath);
        }
        if (!is_file($layoutPath)) {
            throw new RuntimeException('Missing layout: ' . $layoutPath);
        }

        extract($data, EXTR_SKIP);

        $content = (static function () use ($templatePath, $data): string {
            extract($data, EXTR_SKIP);
            ob_start();
            include $templatePath;
            return (string) ob_get_clean();
        })();

        include $layoutPath;
    }
}
