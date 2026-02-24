<?php
/** @var array<string,mixed> $user */
/** @var list<array<string,mixed>> $recipes */
/** @var list<string> $errors */
/** @var string $message */
/** @var string $csrf */
?>

<section class="tb-my">
    <div class="tb-hero tb-hero--recipes">
        <div>
            <h1 class="tb-hero__title">My Recipes</h1>
            <p class="tb-hero__subtitle">Track your submissions and approval status (updates automatically)</p>
        </div>
        <div class="tb-hero__actions">
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
                <div class="tb-table__row" data-recipe-row="<?= htmlspecialchars($rid) ?>">
                    <div>
                        <a class="tb-link" href="?route=recipe&id=<?= urlencode($rid) ?>"><?= htmlspecialchars((string)($r['title'] ?? '')) ?></a>
                    </div>
                    <div><?= htmlspecialchars((string)($r['category'] ?? '')) ?></div>
                    <?php $status = (string) ($r['status'] ?? ''); ?>
                    <div>
                        <span class="tb-badge tb-badge--status tb-badge--<?= htmlspecialchars($status) ?>" data-status-badge="<?= htmlspecialchars($rid) ?>">
                            <?= htmlspecialchars(ucfirst($status)) ?>
                        </span>
                    </div>
                    <div class="tb-table__actions">
                        <a class="tb-btn tb-btn--ghost" href="?route=recipe_edit&id=<?= urlencode($rid) ?>">Edit</a>
                        <form method="post" action="?route=recipe_delete&id=<?= urlencode($rid) ?>" class="tb-inline" onsubmit="return confirm('Delete this recipe?');">
                            <input type="hidden" name="csrf" value="<?= htmlspecialchars($csrf) ?>" />
                            <input type="hidden" name="id" value="<?= htmlspecialchars($rid) ?>" />
                            <button class="tb-btn tb-btn--ghost" type="submit">Delete</button>
                        </form>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
</section>
