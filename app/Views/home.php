<?php
/** @var array<string,mixed>|null $user */
?>

<section class="tb-home">
    <div class="tb-home__hero">
        <h1 class="tb-home__title">Welcome to TastyBytes</h1>
        <p class="tb-home__subtitle">Your digital recipe companion — discover, create, and share delicious recipes.</p>
        <?php if (!$user): ?>
            <div class="tb-home__cta">
                <a class="tb-btn tb-btn--primary" href="?route=register">Sign up</a>
                <a class="tb-btn tb-btn--ghost" href="?route=login">Login</a>
            </div>
        <?php else: ?>
            <p class="tb-home__subtitle">Logged in as <strong><?= htmlspecialchars((string)($user['username'] ?? '')) ?></strong>.</p>
        <?php endif; ?>
    </div>
</section>

