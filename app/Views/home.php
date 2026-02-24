<?php
/** @var array<string,mixed>|null $user */
/** @var list<string> $categories */
/** @var list<array<string,mixed>> $featured */
?>

<section class="tb-home">
    <div class="tb-hero tb-hero--home">
        <div class="tb-hero__icon" aria-hidden="true"></div>
        <h1 class="tb-hero__title">Welcome to TastyBytes</h1>
        <p class="tb-hero__subtitle">Your Digital Recipe Companion — Discover, Create, and Share Delicious Recipes</p>
        <?php if (!$user): ?>
            <div class="tb-hero__actions">
                <a class="tb-btn tb-btn--primary" href="?route=register">Sign up</a>
                <a class="tb-btn tb-btn--ghost" href="?route=login">Login</a>
            </div>
        <?php else: ?>
            <p class="tb-hero__subtitle">Logged in as <strong><?= htmlspecialchars((string) ($user['username'] ?? '')) ?></strong>.</p>
        <?php endif; ?>
    </div>

    <div class="tb-section">
        <div class="tb-section__head">
            <h2 class="tb-section__title">Browse by Category</h2>
        </div>
        <div class="tb-cats">
            <?php foreach ($categories as $c): ?>
                <a class="tb-cat" href="?route=recipes&category=<?= urlencode($c) ?>">
                    <span class="tb-cat__icon" aria-hidden="true"></span>
                    <span class="tb-cat__name"><?= htmlspecialchars($c) ?></span>
                </a>
            <?php endforeach; ?>
        </div>
    </div>

    <div class="tb-section">
        <div class="tb-section__head">
            <h2 class="tb-section__title">Featured Recipes</h2>
            <a class="tb-link" href="?route=recipes">View All →</a>
        </div>
        <?php if ($featured === []): ?>
            <div class="tb-empty">No approved recipes yet.</div>
        <?php else: ?>
            <div class="tb-grid">
                <?php foreach ($featured as $r): ?>
                    <?php
                    $rid = (string) ($r['recipe_id'] ?? '');
                    $img = (string) ($r['image_url'] ?? '');
                    ?>
                    <article class="tb-card">
                        <a class="tb-card__media" href="?route=recipe&id=<?= urlencode($rid) ?>">
                            <?php if ($img !== ''): ?>
                                <img class="tb-card__img" src="<?= htmlspecialchars($img) ?>" alt="<?= htmlspecialchars((string) ($r['title'] ?? 'Recipe')) ?>" />
                            <?php else: ?>
                                <div class="tb-card__img tb-card__img--placeholder"></div>
                            <?php endif; ?>
                        </a>
                        <div class="tb-card__body">
                            <div class="tb-card__meta">
                                <span class="tb-badge"><?= htmlspecialchars((string) ($r['category'] ?? '')) ?></span>
                                <span class="tb-card__author"><?= htmlspecialchars((string) ($r['author_name'] ?? '')) ?></span>
                            </div>
                            <a class="tb-card__title" href="?route=recipe&id=<?= urlencode($rid) ?>">
                                <?= htmlspecialchars((string) ($r['title'] ?? '')) ?>
                            </a>
                        </div>
                    </article>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </div>

    <?php if (!$user): ?>
        <div class="tb-cta">
            <h2 class="tb-cta__title">Join TastyBytes Today!</h2>
            <p class="tb-cta__subtitle">Start saving and sharing your favorite recipes.</p>
            <div class="tb-cta__actions">
                <a class="tb-btn tb-btn--primary" href="?route=register">Sign up</a>
                <a class="tb-btn tb-btn--ghost" href="?route=login">Login</a>
            </div>
        </div>
    <?php endif; ?>
</section>
