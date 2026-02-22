<?php
/** @var array<string,mixed> $user */
/** @var list<array<string,mixed>> $recipes */
/** @var list<string> $errors */
/** @var string $message */
?>

<section class="tb-my">
    <div class="tb-recipes__header">
        <h1 class="tb-recipes__title">My Recipes</h1>
        <div class="tb-recipes__header-actions">
            <a class="tb-btn tb-btn--primary" href="?route=recipe_create">Add Recipe</a>
        </div>
    </div>

    <?php include __DIR__ . '/../partials/alerts.php'; ?>

    <?php if ($recipes === []): ?>
        <div class="tb-empty">You haven't created any recipes yet.</div>
    <?php else: ?>
        <div class="tb-table">
            <div class="tb-table__head">
                <div>Title</div>
                <div>Category</div>
                <div>Status</div>
                <div></div>
            </div>
            <?php foreach ($recipes as $r): ?>
                <?php $rid = (string) ($r['recipe_id'] ?? ''); ?>
                <div class="tb-table__row">
                    <div>
                        <a class="tb-link" href="?route=recipe&id=<?= urlencode($rid) ?>"><?= htmlspecialchars((string)($r['title'] ?? '')) ?></a>
                    </div>
                    <div><?= htmlspecialchars((string)($r['category'] ?? '')) ?></div>
                    <div><span class="tb-badge tb-badge--status tb-badge--<?= htmlspecialchars((string)($r['status'] ?? '')) ?>"><?= htmlspecialchars(ucfirst((string)($r['status'] ?? ''))) ?></span></div>
                    <div class="tb-table__actions">
                        <a class="tb-btn tb-btn--ghost" href="?route=recipe_edit&id=<?= urlencode($rid) ?>">Edit</a>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
</section>

