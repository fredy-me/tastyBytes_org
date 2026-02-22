<?php

declare(strict_types=1);

require __DIR__ . '/../../app/Support/Autoload.php';

\App\Support\Autoload::register();

/**
 * Usage:
 *   DB_HOST=127.0.0.1 DB_NAME=tastybytes DB_USER=root DB_PASS=... \
 *   /opt/lampp/bin/php database/seeds/seed_admin.php --username=Admin --email=admin@example.com --password=ChangeMe123
 */

$args = [];
foreach ($argv as $arg) {
    if (str_starts_with($arg, '--') && str_contains($arg, '=')) {
        [$k, $v] = explode('=', substr($arg, 2), 2);
        $args[$k] = $v;
    }
}

$username = trim((string) ($args['username'] ?? ''));
$email = trim((string) ($args['email'] ?? ''));
$password = (string) ($args['password'] ?? '');

if ($username === '' || $email === '' || $password === '') {
    fwrite(STDERR, "Missing required args: --username= --email= --password=\n");
    exit(1);
}
if (filter_var($email, FILTER_VALIDATE_EMAIL) === false) {
    fwrite(STDERR, "Invalid email.\n");
    exit(1);
}

$existing = \App\Models\User::findByEmail($email);
if ($existing !== null) {
    fwrite(STDERR, "User already exists with that email.\n");
    exit(1);
}

$hash = password_hash($password, PASSWORD_DEFAULT);
$id = \App\Models\User::create($username, $email, $hash, 'admin');

fwrite(STDOUT, "Seeded admin user_id: {$id}\n");

