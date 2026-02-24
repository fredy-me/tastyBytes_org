<?php
/** @var array<string,mixed> $user */
/** @var list<array<string,mixed>> $recipes */
/** @var string $csrf */
?>

<section class="tb-favorites">
    <div class="tb-hero tb-hero--recipes">
        <div>
            <h1 class="tb-hero__title">My Favorites</h1>
            <p class="tb-hero__subtitle">Recipes you've saved to try again later</p>
        </div>
    </div>

    <?php if ($recipes === []): ?>
        <div class="tb-empty">No favorites yet.</div>
    <?php else: ?>
        <div class="tb-grid">
            <?php foreach ($recipes as $r): ?>
                <?php
                $rid = (string) ($r['recipe_id'] ?? '');
                $img = (string) ($r['image_url'] ?? '');
                ?>
                <article class="tb-card">
                    <a class="tb-card__media" href="?route=recipe&id=<?= urlencode($rid) ?>">
                        <?php if ($img !== ''): ?>
                            <img class="tb-card__img" src="<?= htmlspecialchars($img) ?>" alt="<?= htmlspecialchars((string)($r['title'] ?? 'Recipe')) ?>" />
                        <?php else: ?>
                            <div class="tb-card__img tb-card__img--placeholder"></div>
                        <?php endif; ?>
                        <form class="tb-fav" method="post" action="?route=favorite_toggle">
                            <input type="hidden" name="csrf" value="<?= htmlspecialchars($csrf) ?>" />
                            <input type="hidden" name="recipe_id" value="<?= htmlspecialchars($rid) ?>" />
                            <button class="tb-fav__btn" type="submit" aria-label="Remove favorite">
                                <span class="tb-fav__heart is-on"></span>
                            </button>
                        </form>
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
