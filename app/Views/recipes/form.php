<?php
/** @var array<string,mixed> $user */
/** @var 'create'|'edit' $mode */
/** @var array<string,mixed>|null $recipe */
/** @var list<string> $categories */
/** @var string $csrf */
/** @var list<string> $errors */
/** @var array<string,mixed> $old */
/** @var list<string>|null $ingredients */
/** @var list<string>|null $steps */

$old = is_array($old ?? null) ? $old : [];
$recipe = is_array($recipe ?? null) ? $recipe : null;
$isEdit = $mode === 'edit';

$rid = $isEdit ? (string) ($recipe['recipe_id'] ?? '') : '';
$title = (string) ($old['title'] ?? ($recipe['title'] ?? ''));
$category = (string) ($old['category'] ?? ($recipe['category'] ?? ''));
$imageUrl = (string) ($old['image_url'] ?? ($recipe['image_url'] ?? ''));

$ingredients = isset($old['ingredients']) && is_array($old['ingredients']) ? $old['ingredients'] : ($ingredients ?? []);
$steps = isset($old['steps']) && is_array($old['steps']) ? $old['steps'] : ($steps ?? []);

if (!is_array($categories ?? null) || $categories === []) {
    $categories = \App\Models\Categories::all();
}

if ($ingredients === []) {
    $ingredients = [''];
}
if ($steps === []) {
    $steps = [''];
}
?>

<section class="tb-formpage">
    <div class="tb-formpage__top">
        <a class="tb-link" href="<?= $isEdit ? '?route=my_recipes' : '?route=recipes' ?>">← Back</a>
    </div>

    <div class="tb-formpage__card">
        <h1 class="tb-formpage__title"><?= $isEdit ? 'Edit Recipe' : 'Add New Recipe' ?></h1>
        <p class="tb-formpage__subtitle">Recipes require admin approval before appearing publicly.</p>

        <?php
        $message = '';
        include __DIR__ . '/../partials/alerts.php';
        ?>

        <form method="post" action="<?= $isEdit ? '?route=recipe_update&id=' . urlencode($rid) : '?route=recipe_store' ?>">
            <input type="hidden" name="csrf" value="<?= htmlspecialchars($csrf) ?>" />

            <label class="tb-field">
                <span class="tb-field__label">Recipe Title</span>
                <input class="tb-field__input" type="text" name="title" value="<?= htmlspecialchars($title) ?>" required />
            </label>

            <label class="tb-field">
                <span class="tb-field__label">Category</span>
                <select class="tb-field__input" name="category" required>
                    <option value="" disabled <?= $category === '' ? 'selected' : '' ?>>Choose a category</option>
                    <?php foreach ($categories as $c): ?>
                        <option value="<?= htmlspecialchars($c) ?>" <?= $category === $c ? 'selected' : '' ?>><?= htmlspecialchars($c) ?></option>
                    <?php endforeach; ?>
                </select>
            </label>

            <label class="tb-field">
                <span class="tb-field__label">Image URL</span>
                <input class="tb-field__input" type="url" name="image_url" placeholder="https://..." value="<?= htmlspecialchars($imageUrl) ?>" />
            </label>

            <div class="tb-repeater" data-repeater="ingredients">
                <div class="tb-repeater__head">
                    <h2 class="tb-repeater__title">Ingredients</h2>
                    <button class="tb-btn tb-btn--ghost tb-btn--small" type="button" data-add>Add Ingredient</button>
                </div>
                <div class="tb-repeater__list" data-list>
                    <?php foreach ($ingredients as $idx => $val): ?>
                        <div class="tb-repeater__row">
                            <input class="tb-field__input" type="text" name="ingredients[]" value="<?= htmlspecialchars((string)$val) ?>" placeholder="Ingredient <?= (int) ($idx + 1) ?>" />
                            <button class="tb-iconbtn" type="button" data-remove aria-label="Remove ingredient">×</button>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>

            <div class="tb-repeater" data-repeater="steps">
                <div class="tb-repeater__head">
                    <h2 class="tb-repeater__title">Preparation Steps</h2>
                    <button class="tb-btn tb-btn--ghost tb-btn--small" type="button" data-add>Add Step</button>
                </div>
                <div class="tb-repeater__list" data-list>
                    <?php foreach ($steps as $idx => $val): ?>
                        <div class="tb-repeater__row">
                            <input class="tb-field__input" type="text" name="steps[]" value="<?= htmlspecialchars((string)$val) ?>" placeholder="Step <?= (int) ($idx + 1) ?>" />
                            <button class="tb-iconbtn" type="button" data-remove aria-label="Remove step">×</button>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>

            <div class="tb-formpage__actions">
                <a class="tb-btn tb-btn--ghost" href="<?= $isEdit ? '?route=my_recipes' : '?route=recipes' ?>">Cancel</a>
                <button class="tb-btn tb-btn--primary" type="submit"><?= $isEdit ? 'Save & Resubmit' : 'Submit Recipe' ?></button>
            </div>
        </form>
    </div>
</section>
