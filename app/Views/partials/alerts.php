<?php
/** @var list<string> $errors */
/** @var string $message */

$errors = is_array($errors ?? null) ? $errors : [];
$message = is_string($message ?? null) ? $message : '';
?>

<?php if ($message !== ''): ?>
    <div class="tb-alert tb-alert--success"><?= htmlspecialchars($message) ?></div>
<?php endif; ?>

<?php if ($errors !== []): ?>
    <div class="tb-alert tb-alert--error">
        <ul class="tb-alert__list">
            <?php foreach ($errors as $err): ?>
                <li><?= htmlspecialchars((string) $err) ?></li>
            <?php endforeach; ?>
        </ul>
    </div>
<?php endif; ?>

