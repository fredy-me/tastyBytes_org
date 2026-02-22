<?php
/** @var list<string> $errors */
/** @var string $message */
/** @var array{email?:string} $old */
/** @var string $csrf */
?>

<section class="tb-auth">
    <div class="tb-auth__card">
        <div class="tb-auth__icon">👤</div>
        <h1 class="tb-auth__title">Welcome Back!</h1>
        <p class="tb-auth__subtitle">Login to your TastyBytes account</p>

        <?php include __DIR__ . '/../partials/alerts.php'; ?>

        <form method="post" action="?route=login">
            <input type="hidden" name="csrf" value="<?= htmlspecialchars($csrf) ?>" />

            <label class="tb-field">
                <span class="tb-field__label">Email Address</span>
                <input class="tb-field__input" type="email" name="email" value="<?= htmlspecialchars((string)($old['email'] ?? '')) ?>" required />
            </label>

            <label class="tb-field">
                <span class="tb-field__label">Password</span>
                <input class="tb-field__input" type="password" name="password" required />
            </label>

            <button class="tb-btn tb-btn--primary tb-btn--block" type="submit">Log In</button>
        </form>

        <p class="tb-auth__footer">
            Don't have an account?
            <a class="tb-link" href="?route=register">Sign up</a>
        </p>
    </div>
</section>

