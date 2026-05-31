<?php
/**
 * Category Listing Page Template
 * Shows all posts in a category with pagination.
 */
$lang = Lang::getInstance();

// $category, $result are set by the controller
$posts = $result['posts'];
$pagination = $result['pagination'];
$total = $result['total'];

$pageTitle = $lang->field($category, 'name');
$pageDescription = $lang->field($category, 'description') ?: $pageTitle;

$categoryModel = new Category();
$categoriesWithCount = $categoryModel->getWithPostCount();

$postModel = new Post();
$mostViewed = $postModel->getMostViewed(5);

ob_start();
?>

<!-- Page Header -->
<section class="page-header">
    <div class="container">
        <!-- Breadcrumb -->
        <nav class="breadcrumb">
            <a href="<?= url('/') ?>"><?= __('home') ?></a>
            <span class="breadcrumb-separator">›</span>
            <span class="breadcrumb-current"><?= sanitize($lang->field($category, 'name')) ?></span>
        </nav>

        <div class="page-header-inner animate-fade-in-up">
            <div class="page-header-icon" style="background: linear-gradient(135deg, <?= $category['gradient_from'] ?>22, <?= $category['gradient_to'] ?>22); display: flex; align-items: center; justify-content: center;">
                <i data-lucide="<?= sanitize($category['icon']) ?>"></i>
            </div>
            <div class="page-header-content">
                <h1><?= sanitize($lang->field($category, 'name')) ?></h1>
                <p><?= sanitize($lang->field($category, 'description')) ?> · <?= $total ?> <?= __('entries') ?></p>
            </div>
        </div>
    </div>
</section>

<!-- Content -->
<section>
    <div class="container">
        <div class="content-layout">
            <!-- Main -->
            <div class="content-main">
                <div class="posts-list">
                    <?php if (empty($posts)): ?>
                    <div class="empty-state glass-card-static">
                        <div class="empty-state-icon"><i data-lucide="inbox" style="width: 48px; height: 48px; opacity: 0.4;"></i></div>
                        <h3 class="empty-state-title"><?= __('no_posts') ?></h3>
                        <p class="empty-state-desc"><?= __('try_again') ?></p>
                    </div>
                    <?php else: ?>
                    <?php foreach ($posts as $i => $post): ?>
                        <?php 
                            $showCategory = false;
                            $cat = $category;
                            $cardClass = 'animate-fade-in-up stagger-' . min($i + 1, 6);
                            include VIEWS_PATH . '/public/partials/post-card.php'; 
                        ?>
                    <?php endforeach; ?>
                    <?php endif; ?>
                </div>

                <!-- Pagination -->
                <?= $pagination->render() ?>
            </div>

            <!-- Sidebar -->
            <aside class="content-sidebar">
                <!-- Trending -->
                <div class="sidebar-widget">
                    <h3 class="sidebar-widget-title"><i data-lucide="trending-up" style="width: 14px; height: 14px; margin-right: 4px;"></i> <?= __('trending_now') ?></h3>
                    <div class="sidebar-list">
                        <?php foreach ($mostViewed as $post): ?>
                        <a href="<?= url($post['category_slug'] . '/' . $post['slug']) ?>">
                             <span class="nav-icon"><i data-lucide="<?= sanitize($post['category_icon']) ?>"></i></span>
                            <span style="flex:1;font-size:var(--text-sm);line-height:1.3;"><?= sanitize(truncate($lang->field($post, 'title'), 55)) ?></span>
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
