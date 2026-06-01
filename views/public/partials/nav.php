<?php
/**
 * Main Navigation Partial
 * Variables expected: $categories, $lang
 */
?>
<nav class="main-nav" id="mainNav">
    <a href="<?= url('/') ?>" class="nav-link <?= currentPath() === '/' ? 'active' : '' ?>">
        <span class="nav-icon"><i data-lucide="home"></i></span>
        <?= __('home') ?>
    </a>
    <?php foreach ($categories as $cat): ?>
    <a href="<?= url($cat['slug']) ?>"
       class="nav-link <?= isActivePath('/' . $cat['slug']) ? 'active' : '' ?>">
        <span class="nav-icon"><i data-lucide="<?= getCategoryIcon($cat['slug'], $cat['icon']) ?>"></i></span>
        <?= sanitize($lang->field($cat, 'name')) ?>
    </a>
    <?php endforeach; ?>
</nav>
