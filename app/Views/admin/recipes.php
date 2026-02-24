<?php
/** @var array<string,mixed> $user */
/** @var list<array<string,mixed>> $recipes */
/** @var array{pending:int,approved:int,rejected:int,total:int} $counts */
/** @var string $selectedStatus */
/** @var string $search */
/** @var string $csrf */
/** @var list<string> $errors */
/** @var string $message */
?>

<section class="tb-admin">
    <div class="tb-admin__hero">
        <div>
            <h1 class="tb-admin__title">Manage Recipes</h1>
            <p class="tb-admin__subtitle">Review, approve/reject, or remove recipes</p>
        </div>
        <div class="tb-admin__hero-actions">
            <a class="tb-btn tb-btn--ghost" href="?route=admin">Back to Admin</a>
        </div>
    </div>

    <?php include __DIR__ . '/../partials/alerts.php'; ?>

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
        <div class="tb-stat">
            <div class="tb-stat__label">Rejected</div>
            <div class="tb-stat__value"><?= (int) ($counts['rejected'] ?? 0) ?></div>
        </div>
    </div>

    <form class="tb-filter" method="get" action="">
        <input type="hidden" name="route" value="admin_recipes" />
        <div class="tb-filter__row">
            <div class="tb-filter__search">
                <input class="tb-filter__input" type="text" name="q" placeholder="Search recipes..." value="<?= htmlspecialchars($search) ?>" />
            </div>
            <div class="tb-filter__select">
                <select class="tb-filter__input" name="status">
                    <option value="" <?= $selectedStatus === '' ? 'selected' : '' ?>>All statuses</option>
                    <option value="pending" <?= $selectedStatus === 'pending' ? 'selected' : '' ?>>Pending</option>
                    <option value="approved" <?= $selectedStatus === 'approved' ? 'selected' : '' ?>>Approved</option>
                    <option value="rejected" <?= $selectedStatus === 'rejected' ? 'selected' : '' ?>>Rejected</option>
                </select>
            </div>
            <button class="tb-btn tb-btn--ghost" type="submit">Filter</button>
        </div>
    </form>

    <?php if ($recipes === []): ?>
        <div class="tb-empty">No recipes found.</div>
    <?php else: ?>
        <div class="tb-table">
            <div class="tb-table__head tb-table__head--admin-recipes">
                <div>Title</div>
                <div>Author</div>
                <div>Status</div>
                <div></div>
            </div>
            <?php foreach ($recipes as $r): ?>
                <?php
                $rid = (string) ($r['recipe_id'] ?? '');
                $status = (string) ($r['status'] ?? '');
                ?>
                <div class="tb-table__row tb-table__row--admin-recipes">
                    <div>
                        <a class="tb-link" href="?route=recipe&id=<?= urlencode($rid) ?>"><?= htmlspecialchars((string) ($r['title'] ?? '')) ?></a>
                        <div class="tb-muted tb-small"><?= htmlspecialchars((string) ($r['category'] ?? '')) ?></div>
                    </div>
                    <div><?= htmlspecialchars((string) ($r['author_name'] ?? '')) ?></div>
                    <div>
                        <span class="tb-badge tb-badge--status tb-badge--<?= htmlspecialchars($status) ?>"><?= htmlspecialchars(ucfirst($status)) ?></span>
                    </div>
                    <div class="tb-table__actions">
                        <a class="tb-btn tb-btn--ghost" href="?route=recipe&id=<?= urlencode($rid) ?>">Preview</a>
                        <?php if ($status === 'pending'): ?>
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
                        <?php endif; ?>
                        <form method="post" action="?route=admin_recipe_delete" class="tb-inline" onsubmit="return confirm('Delete this recipe?');">
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

