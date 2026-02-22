<?php
/** @var array<string,mixed>|null $user */
/** @var array<string,mixed> $recipe */
/** @var list<string> $ingredients */
/** @var list<string> $steps */
/** @var bool $isFavorite */
/** @var string $csrf */

$rid = (string) ($recipe['recipe_id'] ?? '');
$img = (string) ($recipe['image_url'] ?? '');
$status = (string) ($recipe['status'] ?? '');
$isOwner = $user && ((string) ($recipe['user_id'] ?? '') === (string) ($user['user_id'] ?? ''));
$isAdmin = $user && ((string) ($user['role'] ?? '') === 'admin');
?>

<section class="tb-recipe">
    <div class="tb-recipe__top">
        <a class="tb-link" href="?route=recipes">← Back to recipes</a>
        <div class="tb-recipe__actions">
            <?php if ($user): ?>
                <form method="post" action="?route=favorite_toggle">
                    <input type="hidden" name="csrf" value="<?= htmlspecialchars($csrf) ?>" />
                    <input type="hidden" name="recipe_id" value="<?= htmlspecialchars($rid) ?>" />
                    <button class="tb-btn tb-btn--ghost" type="submit"><?= $isFavorite ? 'Unfavorite' : 'Favorite' ?></button>
                </form>
            <?php endif; ?>
            <?php if ($isOwner): ?>
                <a class="tb-btn tb-btn--ghost" href="?route=recipe_edit&id=<?= urlencode($rid) ?>">Edit</a>
            <?php endif; ?>
        </div>
    </div>

    <div class="tb-recipe__hero">
        <div class="tb-recipe__hero-left">
            <div class="tb-recipe__badges">
                <span class="tb-badge"><?= htmlspecialchars((string) ($recipe['category'] ?? '')) ?></span>
                <?php if ($isOwner || $isAdmin): ?>
                    <span class="tb-badge tb-badge--status tb-badge--<?= htmlspecialchars($status) ?>"><?= htmlspecialchars(ucfirst($status)) ?></span>
                <?php endif; ?>
            </div>
            <h1 class="tb-recipe__title"><?= htmlspecialchars((string) ($recipe['title'] ?? '')) ?></h1>
            <?php if ($isOwner && $status !== 'approved'): ?>
                <p class="tb-recipe__note">This recipe is not public until approved.</p>
            <?php endif; ?>
        </div>
        <div class="tb-recipe__hero-right">
            <?php if ($img !== ''): ?>
                <img class="tb-recipe__img" src="<?= htmlspecialchars($img) ?>" alt="<?= htmlspecialchars((string) ($recipe['title'] ?? 'Recipe')) ?>" />
            <?php else: ?>
                <div class="tb-recipe__img tb-recipe__img--placeholder"></div>
            <?php endif; ?>
        </div>
    </div>

    <div class="tb-recipe__cols">
        <div class="tb-panel">
            <h2 class="tb-panel__title">Ingredients</h2>
            <?php if ($ingredients === []): ?>
                <div class="tb-empty">No ingredients.</div>
            <?php else: ?>
                <ul class="tb-list">
                    <?php foreach ($ingredients as $i): ?>
                        <li><?= htmlspecialchars($i) ?></li>
                    <?php endforeach; ?>
                </ul>
            <?php endif; ?>
        </div>

        <div class="tb-panel">
            <h2 class="tb-panel__title">Preparation Steps</h2>
            <?php if ($steps === []): ?>
                <div class="tb-empty">No steps.</div>
            <?php else: ?>
                <ol class="tb-list tb-list--ordered">
                    <?php foreach ($steps as $s): ?>
                        <li><?= htmlspecialchars($s) ?></li>
                    <?php endforeach; ?>
                </ol>
            <?php endif; ?>
        </div>
    </div>
</section>

