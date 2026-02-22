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
            <nav class="tb-nav">
                <a class="tb-nav__link" href="?route=home">Home</a>
                <a class="tb-nav__link" href="?route=recipes">Recipes</a>
            </nav>
            <div class="tb-actions">
                <?php if (\App\Support\Auth::user()): ?>
                    <a class="tb-btn tb-btn--ghost" href="?route=logout">Logout</a>
                <?php else: ?>
                    <a class="tb-btn tb-btn--ghost" href="?route=login">Login</a>
                <?php endif; ?>
            </div>
        </div>
    </header>
    <main class="tb-main">
        <?= $content ?>
    </main>
</body>
</html>
