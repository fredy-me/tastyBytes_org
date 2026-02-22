<?php
/** @var list<string> $errors */
/** @var string $message */
/** @var array{username?:string,email?:string} $old */
/** @var string $csrf */
?>

<section class="tb-auth">
    <div class="tb-auth__card">
        <div class="tb-auth__icon">🧑‍🍳</div>
        <h1 class="tb-auth__title">Join TastyBytes</h1>
        <p class="tb-auth__subtitle">Create your account and start cooking</p>

        <?php include __DIR__ . '/../partials/alerts.php'; ?>

        <form method="post" action="?route=register">
            <input type="hidden" name="csrf" value="<?= htmlspecialchars($csrf) ?>" />

            <label class="tb-field">
                <span class="tb-field__label">Username</span>
                <input class="tb-field__input" type="text" name="username" value="<?= htmlspecialchars((string)($old['username'] ?? '')) ?>" required />
            </label>

            <label class="tb-field">
                <span class="tb-field__label">Email Address</span>
                <input class="tb-field__input" type="email" name="email" value="<?= htmlspecialchars((string)($old['email'] ?? '')) ?>" required />
            </label>

            <label class="tb-field">
                <span class="tb-field__label">Password</span>
                <input class="tb-field__input" type="password" name="password" required />
            </label>

            <label class="tb-field">
                <span class="tb-field__label">Confirm Password</span>
                <input class="tb-field__input" type="password" name="confirm_password" required />
            </label>

            <button class="tb-btn tb-btn--primary tb-btn--block" type="submit">Create Account</button>
        </form>

        <p class="tb-auth__footer">
            Already have an account?
            <a class="tb-link" href="?route=login">Login</a>
        </p>
    </div>
</section>

