<?php
/** @var array<string,mixed> $user */
/** @var string $csrf */
/** @var list<string> $errors */
/** @var string $message */
?>

<section class="tb-profile">
    <div class="tb-hero tb-hero--recipes">
        <div>
            <h1 class="tb-hero__title">My Profile</h1>
            <p class="tb-hero__subtitle">Update your account details</p>
        </div>
    </div>

    <?php include __DIR__ . '/../partials/alerts.php'; ?>

    <div class="tb-formpage__card">
        <form method="post" action="?route=profile_update">
            <input type="hidden" name="csrf" value="<?= htmlspecialchars($csrf) ?>" />

            <label class="tb-field">
                <span class="tb-field__label">Username</span>
                <input class="tb-field__input" type="text" name="username" value="<?= htmlspecialchars((string) ($user['username'] ?? '')) ?>" required />
            </label>

            <label class="tb-field">
                <span class="tb-field__label">Email Address</span>
                <input class="tb-field__input" type="email" name="email" value="<?= htmlspecialchars((string) ($user['email'] ?? '')) ?>" required />
            </label>

            <div class="tb-divider"></div>
            <div class="tb-muted tb-small">Change password (optional)</div>

            <label class="tb-field">
                <span class="tb-field__label">New Password</span>
                <input class="tb-field__input" type="password" name="new_password" minlength="8" />
            </label>

            <label class="tb-field">
                <span class="tb-field__label">Confirm New Password</span>
                <input class="tb-field__input" type="password" name="confirm_password" minlength="8" />
            </label>

            <div class="tb-formpage__actions">
                <button class="tb-btn tb-btn--primary" type="submit">Save Changes</button>
            </div>
        </form>
    </div>
</section>

