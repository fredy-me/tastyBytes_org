<?php
/** @var array<string,mixed>|null $user */
/** @var list<array<string,mixed>> $recipes */
/** @var array<string,bool> $favoriteIds */
/** @var list<string> $categories */
/** @var string $selectedCategory */
/** @var string $search */
/** @var string $csrf */
/** @var list<string> $errors */
/** @var string $message */
?>

<section class="tb-recipes">
    <div class="tb-recipes__header">
        <h1 class="tb-recipes__title">Discover Recipes</h1>
        <div class="tb-recipes__header-actions">
            <?php if ($user): ?>
                <a class="tb-btn tb-btn--primary" href="?route=recipe_create">Add Recipe</a>
            <?php endif; ?>
        </div>
    </div>

    <?php include __DIR__ . '/../partials/alerts.php'; ?>

    <form class="tb-filter" method="get" action="">
        <input type="hidden" name="route" value="recipes" />
        <div class="tb-filter__row">
            <div class="tb-filter__search">
                <input class="tb-filter__input" type="text" name="q" placeholder="Search recipes..." value="<?= htmlspecialchars($search) ?>" />
            </div>
            <div class="tb-filter__select">
                <select class="tb-filter__input" name="category">
                    <option value="">All</option>
                    <?php foreach ($categories as $c): ?>
                        <option value="<?= htmlspecialchars($c) ?>" <?= $selectedCategory === $c ? 'selected' : '' ?>>
                            <?= htmlspecialchars($c) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
            <button class="tb-btn tb-btn--ghost" type="submit">Filter</button>
        </div>
    </form>

    <?php if ($recipes === []): ?>
        <div class="tb-empty">No approved recipes found.</div>
    <?php else: ?>
        <div class="tb-grid">
            <?php foreach ($recipes as $r): ?>
                <?php
                $rid = (string) ($r['recipe_id'] ?? '');
                $img = (string) ($r['image_url'] ?? '');
                $isFav = $user ? (bool) ($favoriteIds[$rid] ?? false) : false;
                ?>
                <article class="tb-card">
                    <a class="tb-card__media" href="?route=recipe&id=<?= urlencode($rid) ?>">
                        <?php if ($img !== ''): ?>
                            <img class="tb-card__img" src="<?= htmlspecialchars($img) ?>" alt="<?= htmlspecialchars((string)($r['title'] ?? 'Recipe')) ?>" />
                        <?php else: ?>
                            <div class="tb-card__img tb-card__img--placeholder"></div>
                        <?php endif; ?>
                        <?php if ($user): ?>
                            <form class="tb-fav" method="post" action="?route=favorite_toggle">
                                <input type="hidden" name="csrf" value="<?= htmlspecialchars($csrf) ?>" />
                                <input type="hidden" name="recipe_id" value="<?= htmlspecialchars($rid) ?>" />
                                <button class="tb-fav__btn" type="submit" aria-label="<?= $isFav ? 'Remove favorite' : 'Add favorite' ?>">
                                    <span class="tb-fav__heart <?= $isFav ? 'is-on' : '' ?>"></span>
                                </button>
                            </form>
                        <?php endif; ?>
                    </a>
                    <div class="tb-card__body">
                        <div class="tb-card__meta">
                            <span class="tb-badge"><?= htmlspecialchars((string)($r['category'] ?? '')) ?></span>
                            <span class="tb-card__author"><?= htmlspecialchars((string)($r['author_name'] ?? '')) ?></span>
                        </div>
                        <a class="tb-card__title" href="?route=recipe&id=<?= urlencode($rid) ?>">
                            <?= htmlspecialchars((string)($r['title'] ?? '')) ?>
                        </a>
                    </div>
                </article>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
</section>

