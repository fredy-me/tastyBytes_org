<?php
/** @var string $content */
?>
<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>TastyBytes</title>
    <link rel="stylesheet" href="assets/css/app.css" />
</head>
<body class="tb-page">
    <header class="tb-topbar">
        <div class="tb-topbar__inner">
            <a class="tb-brand" href="?route=home">
                <span class="tb-brand__mark">TB</span>
                <span class="tb-brand__name">TastyBytes</span>
            </a>
            <?php $navUser = \App\Support\Auth::user(); ?>
            <nav class="tb-nav">
                <a class="tb-nav__link" href="?route=home">Home</a>
                <a class="tb-nav__link" href="?route=recipes">Recipes</a>
                <?php if ($navUser): ?>
                    <a class="tb-nav__link" href="?route=my_recipes">My Recipes</a>
                    <a class="tb-nav__link" href="?route=favorites">Favorites</a>
                    <a class="tb-nav__link" href="?route=profile">Profile</a>
                <?php endif; ?>
                <?php if ($navUser && (string)($navUser['role'] ?? '') === 'admin'): ?>
                    <a class="tb-nav__link" href="?route=admin">Admin</a>
                <?php endif; ?>
            </nav>
            <div class="tb-actions">
                <?php if ($navUser): ?>
                    <span class="tb-user"><?= htmlspecialchars((string) ($navUser['username'] ?? '')) ?></span>
                    <a class="tb-btn tb-btn--ghost" href="?route=logout">Logout</a>
                <?php else: ?>
                    <a class="tb-btn tb-btn--primary" href="?route=login">Login</a>
                <?php endif; ?>
            </div>
        </div>
    </header>
    <main class="tb-main">
        <?= $content ?>
    </main>
    <script src="assets/js/app.js"></script>
</body>
</html>
