<?php
/**
 * Search Results Page
 */
$lang = Lang::getInstance();

$posts = $result['posts'];
$pagination = $result['pagination'];
$total = $result['total'];
$query = $result['query'];

$pageTitle = __('search_results_for', ['query' => $query]);

$categoryModel = new Category();
$categoriesWithCount = $categoryModel->getWithPostCount();

ob_start();
?>

<section class="page-header">
    <div class="container">
        <nav class="breadcrumb">
            <a href="<?= url('/') ?>"><?= __('home') ?></a>
            <span class="breadcrumb-separator">›</span>
            <span class="breadcrumb-current"><?= __('search_results') ?></span>
        </nav>
        <div class="page-header-inner animate-fade-in-up">
            <div class="page-header-icon" style="background: var(--primary-50); display: flex; align-items: center; justify-content: center;"><i data-lucide="search"></i></div>
            <div class="page-header-content">
                <h1><?= __('search_results_for', ['query' => sanitize($query)]) ?></h1>
                <p><?= $total ?> <?= __('entries') ?></p>
            </div>
        </div>
    </div>
</section>

<section>
    <div class="container">
        <div class="content-layout">
            <div class="content-main">
                <!-- Search Form -->
                <form action="<?= url('search') ?>" method="GET" class="search-input-wrapper mb-6" style="max-width: 100%;">
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/></svg>
                    <input type="text" name="q" class="search-input" value="<?= sanitize($query) ?>" placeholder="<?= __('search_placeholder') ?>">
                </form>

                <div class="posts-list">
                    <?php if (empty($posts)): ?>
                    <div class="empty-state glass-card-static">
                        <div class="empty-state-icon"><i data-lucide="search" style="width: 48px; height: 48px; opacity: 0.4;"></i></div>
                        <h3 class="empty-state-title"><?= __('no_results') ?></h3>
                        <p class="empty-state-desc"><?= __('try_again') ?></p>
                    </div>
                    <?php else: ?>
                    <?php foreach ($posts as $post): ?>
                        <?php 
                            $showCategory = true;
                            include VIEWS_PATH . '/public/partials/post-card.php'; 
                        ?>
                    <?php endforeach; ?>
                    <?php endif; ?>
                </div>

                <?= $pagination->render() ?>
            </div>

            <aside class="content-sidebar">
                <!-- Popular Posts -->
                <div class="sidebar-widget">
                    <h3 class="sidebar-widget-title"><i data-lucide="trending-up" style="width: 14px; height: 14px; margin-right: 4px;"></i> <?= __('trending_now') ?></h3>
                    <div class="sidebar-list">
                        <?php
                        $postModel = new Post();
                        $mostViewed = $postModel->getMostViewed(5);
                        foreach ($mostViewed as $mvPost): ?>
                        <a href="<?= url($mvPost['category_slug'] . '/' . $mvPost['slug']) ?>">
                            <span class="nav-icon"><i data-lucide="<?= getCategoryIcon($mvPost['category_slug'], $mvPost['category_icon']) ?>"></i></span>
                            <span style="flex:1;font-size:var(--text-sm);line-height:1.3;"><?= sanitize(truncate($lang->field($mvPost, 'title'), 55)) ?></span>
                            <span class="count"><?= formatNumber($mvPost['views']) ?></span>
                        </a>
                        <?php endforeach; ?>
                    </div>
                </div>
            </aside>
        </div>
    </div>
</section>

<?php
$content = ob_get_clean();
include VIEWS_PATH . '/layouts/public.php';
?>
