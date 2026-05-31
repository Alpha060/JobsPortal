<?php
/**
 * Category Box Partial
 * Variables expected: $cat, $i, $lang
 */
?>
<a href="<?= url($cat['slug']) ?>" class="category-card animate-fade-in-up stagger-<?= $i + 1 ?>"
   style="border-left: 3px solid <?= sanitize($cat['color']) ?>;">
    <div class="category-card-icon" style="color: <?= sanitize($cat['color']) ?>; background: var(--bg-secondary);">
        <i data-lucide="<?= sanitize($cat['icon']) ?>"></i>
    </div>
    <div class="category-card-name"><?= sanitize($lang->field($cat, 'name')) ?></div>
    <div class="category-card-count"><?= (int)$cat['post_count'] ?></div>
</a>
