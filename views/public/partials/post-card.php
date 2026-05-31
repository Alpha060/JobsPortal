<?php
/**
 * Post Card Partial
 * Variables expected: $post, $lang
 * Optional variables: $showCategory (defaults to true)
 */
$showCategory = isset($showCategory) ? $showCategory : true;
$categorySlug = isset($post['category_slug']) ? $post['category_slug'] : (isset($cat['slug']) ? $cat['slug'] : '');
$categoryIcon = isset($post['category_icon']) ? $post['category_icon'] : (isset($cat['icon']) ? $cat['icon'] : '💼');
$categoryName = isset($post['category_name_en']) ? $lang->field($post, 'category_name') : (isset($cat['name_en']) ? $lang->field($cat, 'name') : '');
$categoryColor = isset($post['category_color']) ? $post['category_color'] : (isset($cat['color']) ? $cat['color'] : '#6366F1');
$cardClass = isset($cardClass) ? $cardClass : '';
?>
<a href="<?= url($categorySlug . '/' . $post['slug']) ?>" class="post-card <?= $cardClass ?>"
   style="border-left: 3px solid <?= sanitize($categoryColor) ?>;">
    <div class="post-card-category" style="color: <?= sanitize($categoryColor) ?>; background: var(--bg-secondary);">
        <i data-lucide="<?= sanitize($categoryIcon) ?>"></i>
    </div>
    <div class="post-card-body">
        <h3 class="post-card-title"><?= sanitize($lang->field($post, 'title')) ?></h3>
        <div class="post-card-meta">
            <?php if ($showCategory && !empty($categoryName)): ?>
            <span class="badge badge-primary"><?= sanitize($categoryName) ?></span>
            <?php endif; ?>
            <?php if ($post['organization']): ?>
            <span><i data-lucide="building-2"></i> <?= sanitize($post['organization']) ?></span>
            <?php endif; ?>
            <?php if ($post['last_date']): ?>
            <span><i data-lucide="calendar"></i> <?= __('last_date') ?>: <?= formatDate($post['last_date']) ?></span>
            <?php endif; ?>
            <span><i data-lucide="clock"></i> <?= timeAgo($post['created_at']) ?></span>
            <?php if (!empty($post['total_vacancies'])): ?>
            <span><i data-lucide="users"></i> <?= formatNumber($post['total_vacancies']) ?> <?= __('total_vacancies') ?></span>
            <?php endif; ?>
            <?php if (isset($post['views'])): ?>
            <span><i data-lucide="eye"></i> <?= formatNumber($post['views']) ?></span>
            <?php endif; ?>
        </div>
    </div>
    <div class="post-card-arrow">
        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="9 18 15 12 9 6"/></svg>
    </div>
</a>
