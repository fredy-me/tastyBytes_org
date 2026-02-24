<?php
/** @var array<string,mixed> $user */
/** @var list<array<string,mixed>> $pending */
/** @var array{pending:int,approved:int,rejected:int,total:int} $counts */
/** @var string $csrf */
/** @var list<string> $errors */
/** @var string $message */
?>

<section class="tb-admin">
    <div class="tb-admin__hero">
        <div>
            <h1 class="tb-admin__title">Admin Dashboard</h1>
            <p class="tb-admin__subtitle">Review and approve submitted recipes</p>
        </div>
        <div class="tb-admin__hero-actions">
            <a class="tb-btn tb-btn--ghost" href="?route=admin_recipes">Manage Recipes</a>
            <a class="tb-btn tb-btn--ghost" href="?route=admin_users">Manage Users</a>
        </div>
    </div>

    <?php include __DIR__ . '/../partials/alerts.php'; ?>

    <div class="tb-admin__grid">
        <div class="tb-stats">
            <div class="tb-stat">
                <div class="tb-stat__label">Submitted</div>
                <div class="tb-stat__value"><?= (int) ($counts['total'] ?? 0) ?></div>
            </div>
            <div class="tb-stat">
                <div class="tb-stat__label">Pending</div>
                <div class="tb-stat__value"><?= (int) ($counts['pending'] ?? 0) ?></div>
            </div>
            <div class="tb-stat">
                <div class="tb-stat__label">Approved</div>
                <div class="tb-stat__value"><?= (int) ($counts['approved'] ?? 0) ?></div>
            </div>
        </div>

        <div class="tb-panel">
            <h2 class="tb-panel__title">Pending Recipes</h2>
            <?php if ($pending === []): ?>
                <div class="tb-empty">No pending recipes.</div>
            <?php else: ?>
                <div class="tb-adminlist">
                    <?php foreach ($pending as $r): ?>
                        <?php $rid = (string) ($r['recipe_id'] ?? ''); ?>
                        <div class="tb-adminlist__item">
                            <div class="tb-adminlist__main">
                                <div class="tb-adminlist__title"><?= htmlspecialchars((string) ($r['title'] ?? '')) ?></div>
                                <div class="tb-adminlist__meta">
                                    <span class="tb-badge"><?= htmlspecialchars((string) ($r['category'] ?? '')) ?></span>
                                    <span class="tb-adminlist__author">by <?= htmlspecialchars((string) ($r['author_name'] ?? '')) ?></span>
                                </div>
                            </div>
                            <div class="tb-adminlist__actions">
                                <a class="tb-btn tb-btn--ghost" href="?route=recipe&id=<?= urlencode($rid) ?>">Preview</a>
                                <form method="post" action="?route=admin_approve" class="tb-inline">
                                    <input type="hidden" name="csrf" value="<?= htmlspecialchars($csrf) ?>" />
                                    <input type="hidden" name="id" value="<?= htmlspecialchars($rid) ?>" />
                                    <button class="tb-btn tb-btn--primary" type="submit">Approve</button>
                                </form>
                                <form method="post" action="?route=admin_reject" class="tb-inline">
                                    <input type="hidden" name="csrf" value="<?= htmlspecialchars($csrf) ?>" />
                                    <input type="hidden" name="id" value="<?= htmlspecialchars($rid) ?>" />
                                    <button class="tb-btn tb-btn--ghost" type="submit">Reject</button>
                                </form>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </div>
    </div>
</section>
