<?php
/**
 * Homepage Template
 */
$lang = Lang::getInstance();
$settings = new Setting();
$postModel = new Post();
$categoryModel = new Category();

// Get data
$categoriesWithCount = $categoryModel->getWithPostCount();
$featuredPosts = $postModel->getFeatured(8); // Increased to 8 for the grid layout
$mostViewed = $postModel->getMostViewed(6);

// Per-category latest (for silos)
$categorySlugs = array_column($categoriesWithCount, 'slug');
$categoryPosts = $postModel->getLatestForCategories($categorySlugs, 10);

// Check if a post is new (within 48 hours)
function isPostNew($createdAt) {
    return (time() - strtotime($createdAt)) < (48 * 3600);
}

$pageTitle = null; // Use default (site name + tagline)
$pageDescription = $settings->getLocalized('site_description');

// Quick tags for popular filters
$quickTags = [
    ['name' => 'UPSC', 'url' => url('search?q=UPSC')],
    ['name' => 'SSC', 'url' => url('search?q=SSC')],
    ['name' => 'Railways', 'url' => url('search?q=Railway')],
    ['name' => 'Banking', 'url' => url('search?q=Bank')],
    ['name' => 'Police & Defense', 'url' => url('search?q=Police')],
    ['name' => 'Latest Jobs', 'url' => url('latest-jobs')],
    ['name' => 'Admit Card', 'url' => url('admit-card')],
    ['name' => 'Results', 'url' => url('results')],
];

// Start output buffer
ob_start();
?>

<!-- Sector & Organization Quick Tags -->
<div class="quick-tags-section animate-slide-up-fade stagger-1">
    <div class="container">
        <div class="quick-tags-grid">
            <?php foreach ($quickTags as $tag): ?>
            <a href="<?= $tag['url'] ?>" class="quick-tag-tile">
                <span><?= $tag['name'] ?></span>
            </a>
            <?php endforeach; ?>
        </div>
    </div>
</div>

<!-- Featured High-Priority Grid -->
<?php if (!empty($featuredPosts)): ?>
<section class="section section-featured animate-slide-up-fade stagger-2">
    <div class="container">
        <div class="section-header">
            <h2 class="section-title">
                <span class="icon"><i data-lucide="star"></i></span>
                <?= __('featured_posts') ?>
            </h2>
        </div>
        <div class="featured-sarkari-grid">
            <?php foreach ($featuredPosts as $i => $post): ?>
            <a href="<?= url($post['category_slug'] . '/' . $post['slug']) ?>" class="featured-sarkari-card glass-card"
               style="--feat-color: <?= $post['category_color'] ?? '#6366F1' ?>;">
                <div class="featured-sarkari-badge">
                    <span class="badge" style="background: <?= $post['category_color'] ?? '#6366F1' ?>20; color: <?= $post['category_color'] ?? '#6366F1' ?>;">
                        <?= sanitize($lang->field($post, 'category_name')) ?>
                    </span>
                    <?php if (isPostNew($post['created_at'])): ?>
                    <span class="badge-new-pulse"><?= __('new') ?></span>
                    <?php endif; ?>
                </div>
                <h3 class="featured-sarkari-title"><?= sanitize($lang->field($post, 'title')) ?></h3>
                <div class="featured-sarkari-footer">
                    <span><?= $post['organization'] ? sanitize($post['organization']) : '' ?></span>
                    <span><?= formatDate($post['created_at']) ?></span>
                </div>
            </a>
            <?php endforeach; ?>
        </div>
    </div>
</section>
<?php endif; ?>

<!-- Main Silo Grid Section -->
<section class="section section-silos">
    <div class="container">
        <!-- Primary Grid: Results, Admit Card, Latest Jobs -->
        <div class="silo-grid animate-slide-up-fade stagger-3">
            
            <!-- Silo: Latest Jobs -->
            <div class="silo-column glass-card" style="--silo-theme: #6366F1;">
                <div class="silo-column-header">
                    <h3 class="silo-title"><i data-lucide="briefcase"></i> <?= __('Latest Jobs') ?? 'Latest Jobs' ?></h3>
                    <a href="<?= url('latest-jobs') ?>" class="silo-view-all"><?= __('view_all') ?></a>
                </div>
                <div class="silo-list">
                    <?php if (empty($categoryPosts['latest-jobs'])): ?>
                    <div class="silo-empty"><?= __('no_posts') ?></div>
                    <?php else: ?>
                    <?php foreach ($categoryPosts['latest-jobs'] as $post): ?>
                    <a href="<?= url('latest-jobs/' . $post['slug']) ?>" class="silo-item">
                        <span class="silo-item-title"><?= sanitize($lang->field($post, 'title')) ?></span>
                        <?php if (isPostNew($post['created_at'])): ?>
                        <span class="badge-new-pulse"><?= __('new') ?></span>
                        <?php endif; ?>
                    </a>
                    <?php endforeach; ?>
                    <?php endif; ?>
                </div>
            </div>

            <!-- Silo: Admit Card -->
            <div class="silo-column glass-card" style="--silo-theme: #F59E0B;">
                <div class="silo-column-header">
                    <h3 class="silo-title"><i data-lucide="ticket"></i> <?= __('Admit Card') ?? 'Admit Card' ?></h3>
                    <a href="<?= url('admit-card') ?>" class="silo-view-all"><?= __('view_all') ?></a>
                </div>
                <div class="silo-list">
                    <?php if (empty($categoryPosts['admit-card'])): ?>
                    <div class="silo-empty"><?= __('no_posts') ?></div>
                    <?php else: ?>
                    <?php foreach ($categoryPosts['admit-card'] as $post): ?>
                    <a href="<?= url('admit-card/' . $post['slug']) ?>" class="silo-item">
                        <span class="silo-item-title"><?= sanitize($lang->field($post, 'title')) ?></span>
                        <?php if (isPostNew($post['created_at'])): ?>
                        <span class="badge-new-pulse"><?= __('new') ?></span>
                        <?php endif; ?>
                    </a>
                    <?php endforeach; ?>
                    <?php endif; ?>
                </div>
            </div>

            <!-- Silo: Results -->
            <div class="silo-column glass-card" style="--silo-theme: #10B981;">
                <div class="silo-column-header">
                    <h3 class="silo-title"><i data-lucide="award"></i> <?= __('Results') ?? 'Results' ?></h3>
                    <a href="<?= url('results') ?>" class="silo-view-all"><?= __('view_all') ?></a>
                </div>
                <div class="silo-list">
                    <?php if (empty($categoryPosts['results'])): ?>
                    <div class="silo-empty"><?= __('no_posts') ?></div>
                    <?php else: ?>
                    <?php foreach ($categoryPosts['results'] as $post): ?>
                    <a href="<?= url('results/' . $post['slug']) ?>" class="silo-item">
                        <span class="silo-item-title"><?= sanitize($lang->field($post, 'title')) ?></span>
                        <?php if (isPostNew($post['created_at'])): ?>
                        <span class="badge-new-pulse"><?= __('new') ?></span>
                        <?php endif; ?>
                    </a>
                    <?php endforeach; ?>
                    <?php endif; ?>
                </div>
            </div>

        </div>

        <!-- Secondary Grid: Answer Key, Syllabus, Admission -->
        <div class="silo-grid mt-8 animate-slide-up-fade stagger-4">
            
            <!-- Silo: Answer Key -->
            <div class="silo-column glass-card" style="--silo-theme: #EF4444;">
                <div class="silo-column-header">
                    <h3 class="silo-title"><i data-lucide="key-round"></i> <?= __('Answer Key') ?? 'Answer Key' ?></h3>
                    <a href="<?= url('answer-key') ?>" class="silo-view-all"><?= __('view_all') ?></a>
                </div>
                <div class="silo-list">
                    <?php if (empty($categoryPosts['answer-key'])): ?>
                    <div class="silo-empty"><?= __('no_posts') ?></div>
                    <?php else: ?>
                    <?php foreach ($categoryPosts['answer-key'] as $post): ?>
                    <a href="<?= url('answer-key/' . $post['slug']) ?>" class="silo-item">
                        <span class="silo-item-title"><?= sanitize($lang->field($post, 'title')) ?></span>
                        <?php if (isPostNew($post['created_at'])): ?>
                        <span class="badge-new-pulse"><?= __('new') ?></span>
                        <?php endif; ?>
                    </a>
                    <?php endforeach; ?>
                    <?php endif; ?>
                </div>
            </div>

            <!-- Silo: Syllabus -->
            <div class="silo-column glass-card" style="--silo-theme: #3B82F6;">
                <div class="silo-column-header">
                    <h3 class="silo-title"><i data-lucide="book-open"></i> <?= __('Syllabus') ?? 'Syllabus' ?></h3>
                    <a href="<?= url('syllabus') ?>" class="silo-view-all"><?= __('view_all') ?></a>
                </div>
                <div class="silo-list">
                    <?php if (empty($categoryPosts['syllabus'])): ?>
                    <div class="silo-empty"><?= __('no_posts') ?></div>
                    <?php else: ?>
                    <?php foreach ($categoryPosts['syllabus'] as $post): ?>
                    <a href="<?= url('syllabus/' . $post['slug']) ?>" class="silo-item">
                        <span class="silo-item-title"><?= sanitize($lang->field($post, 'title')) ?></span>
                        <?php if (isPostNew($post['created_at'])): ?>
                        <span class="badge-new-pulse"><?= __('new') ?></span>
                        <?php endif; ?>
                    </a>
                    <?php endforeach; ?>
                    <?php endif; ?>
                </div>
            </div>

            <!-- Silo: Admission -->
            <div class="silo-column glass-card" style="--silo-theme: #EC4899;">
                <div class="silo-column-header">
                    <h3 class="silo-title"><i data-lucide="graduation-cap"></i> <?= __('Admission') ?? 'Admission' ?></h3>
                    <a href="<?= url('admission') ?>" class="silo-view-all"><?= __('view_all') ?></a>
                </div>
                <div class="silo-list">
                    <?php if (empty($categoryPosts['admission'])): ?>
                    <div class="silo-empty"><?= __('no_posts') ?></div>
                    <?php else: ?>
                    <?php foreach ($categoryPosts['admission'] as $post): ?>
                    <a href="<?= url('admission/' . $post['slug']) ?>" class="silo-item">
                        <span class="silo-item-title"><?= sanitize($lang->field($post, 'title')) ?></span>
                        <?php if (isPostNew($post['created_at'])): ?>
                        <span class="badge-new-pulse"><?= __('new') ?></span>
                        <?php endif; ?>
                    </a>
                    <?php endforeach; ?>
                    <?php endif; ?>
                </div>
            </div>

        </div>
    </div>
</section>

<!-- Trending / Most Viewed Bottom Section -->
<?php if (!empty($mostViewed)): ?>
<section class="section section-trending animate-slide-up-fade stagger-5">
    <div class="container">
        <div class="section-header">
            <h2 class="section-title">
                <span class="icon"><i data-lucide="trending-up"></i></span>
                <?= __('trending_now') ?>
            </h2>
        </div>
        <div class="trending-row">
            <?php foreach ($mostViewed as $post): ?>
            <a href="<?= url($post['category_slug'] . '/' . $post['slug']) ?>" class="trending-tile glass-card">
                <span class="trending-tile-title"><?= sanitize($lang->field($post, 'title')) ?></span>
                <span class="trending-views"><i data-lucide="eye" style="width: 12px; height: 12px;"></i> <?= formatNumber($post['views']) ?></span>
            </a>
            <?php endforeach; ?>
        </div>
    </div>
</section>
<?php endif; ?>

<?php
$content = ob_get_clean();
include VIEWS_PATH . '/layouts/public.php';
?>
