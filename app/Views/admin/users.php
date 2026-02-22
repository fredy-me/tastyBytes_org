<?php
/** @var array<string,mixed> $user */
/** @var list<array<string,mixed>> $users */
/** @var string $csrf */
/** @var list<string> $errors */
/** @var string $message */
?>

<section class="tb-admin">
    <div class="tb-admin__hero tb-admin__hero--users">
        <div>
            <h1 class="tb-admin__title">Manage Users</h1>
            <p class="tb-admin__subtitle">Disable or delete accounts</p>
        </div>
        <div class="tb-admin__hero-actions">
            <a class="tb-btn tb-btn--ghost" href="?route=admin">Back to Admin</a>
        </div>
    </div>

    <?php include __DIR__ . '/../partials/alerts.php'; ?>

    <div class="tb-table">
        <div class="tb-table__head tb-table__head--users">
            <div>User</div>
            <div>Role</div>
            <div>Status</div>
            <div></div>
        </div>
        <?php foreach ($users as $u): ?>
            <?php
            $uid = (string) ($u['user_id'] ?? '');
            $isSelf = $uid !== '' && $uid === (string) ($user['user_id'] ?? '');
            ?>
            <div class="tb-table__row tb-table__row--users">
                <div>
                    <div class="tb-users__name"><?= htmlspecialchars((string) ($u['username'] ?? '')) ?></div>
                    <div class="tb-users__email"><?= htmlspecialchars((string) ($u['email'] ?? '')) ?></div>
                </div>
                <div><span class="tb-badge"><?= htmlspecialchars((string) ($u['role'] ?? '')) ?></span></div>
                <div><span class="tb-badge tb-badge--status tb-badge--<?= htmlspecialchars((string) ($u['status'] ?? '')) ?>"><?= htmlspecialchars((string) ($u['status'] ?? '')) ?></span></div>
                <div class="tb-table__actions">
                    <?php if (!$isSelf): ?>
                        <form method="post" action="?route=admin_user_disable" class="tb-inline">
                            <input type="hidden" name="csrf" value="<?= htmlspecialchars($csrf) ?>" />
                            <input type="hidden" name="id" value="<?= htmlspecialchars($uid) ?>" />
                            <button class="tb-btn tb-btn--ghost" type="submit">Disable</button>
                        </form>
                        <form method="post" action="?route=admin_user_delete" class="tb-inline" onsubmit="return confirm('Delete this user and all their recipes?');">
                            <input type="hidden" name="csrf" value="<?= htmlspecialchars($csrf) ?>" />
                            <input type="hidden" name="id" value="<?= htmlspecialchars($uid) ?>" />
                            <button class="tb-btn tb-btn--ghost" type="submit">Delete</button>
                        </form>
                    <?php else: ?>
                        <span class="tb-muted">You</span>
                    <?php endif; ?>
                </div>
            </div>
        <?php endforeach; ?>
    </div>
</section>

