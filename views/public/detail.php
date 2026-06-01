<?php
/**
 * Post Detail Page Template
 */
$lang = Lang::getInstance();

// $post and $relatedPosts are set by the controller
$pageTitle = $lang->field($post, 'title');
$pageDescription = $post['meta_description'] ?: truncate($lang->field($post, 'excerpt') ?: $lang->field($post, 'title'), 160);

$importantLinks = $post['important_links'] ? json_decode($post['important_links'], true) : [];
$importantDates = $post['important_dates'] ? json_decode($post['important_dates'], true) : [];

ob_start();
?>

<!-- Page Header & Breadcrumb -->
<section class="page-header animate-slide-up-fade stagger-1">
    <div class="container">
        <nav class="breadcrumb">
            <a href="<?= url('/') ?>"><?= __('home') ?></a>
            <span class="breadcrumb-separator">/</span>
            <a href="<?= url($post['category_slug']) ?>"><?= sanitize($lang->field($post, 'category_name')) ?></a>
            <span class="breadcrumb-separator">/</span>
            <span class="breadcrumb-current"><?= sanitize(truncate($lang->field($post, 'title'), 60)) ?></span>
        </nav>
    </div>
</section>

<!-- Content -->
<section class="section-detail-body animate-slide-up-fade stagger-2">
    <div class="container">
        <div class="content-layout">
            <!-- Main Content -->
            <div class="content-main">
                <article class="post-detail glass-card">
                    <!-- Title & Header -->
                    <header class="post-detail-header">
                        <div class="post-detail-badges">
                            <span class="badge" style="background: <?= $post['category_color'] ?? '#6366F1' ?>20; color: <?= $post['category_color'] ?? '#6366F1' ?>;">
                                <i data-lucide="<?= getCategoryIcon($post['category_slug'], $post['category_icon']) ?>"></i> <?= sanitize($lang->field($post, 'category_name')) ?>
                            </span>
                            <?php if ($post['is_featured']): ?>
                            <span class="badge badge-warning"><i data-lucide="star"></i> <?= __('featured_posts') ?></span>
                            <?php endif; ?>
                            <?php if ($post['last_date'] && strtotime($post['last_date']) > time()): ?>
                            <span class="badge badge-success"><i data-lucide="clock"></i> Active</span>
                            <?php else: ?>
                            <span class="badge badge-danger"><i data-lucide="calendar-x"></i> Expired</span>
                            <?php endif; ?>
                        </div>
                        <h1 class="post-detail-title"><?= sanitize($lang->field($post, 'title')) ?></h1>
                        <div class="post-detail-meta">
                            <?php if ($post['organization']): ?>
                            <span><i data-lucide="building-2"></i> <?= sanitize($post['organization']) ?></span>
                            <?php endif; ?>
                            <span><i data-lucide="calendar"></i> <?= __('posted_on') ?> <?= formatDate($post['created_at']) ?></span>
                            <span><i data-lucide="eye"></i> <?= formatNumber($post['views']) ?> <?= __('views') ?></span>
                        </div>
                    </header>

                    <!-- Featured Banner Image -->
                    <?php if ($post['featured_image']): ?>
                    <div class="post-detail-banner">
                        <img src="<?= UPLOADS_URL . '/' . $post['featured_image'] ?>" alt="<?= sanitize($lang->field($post, 'title')) ?>">
                    </div>
                    <?php endif; ?>

                    <!-- Key Details Grid -->
                    <div class="detail-grid">
                        <?php if ($post['organization']): ?>
                        <div class="detail-info-card">
                            <span class="detail-info-icon"><i data-lucide="building"></i></span>
                            <div class="detail-info-content">
                                <span class="detail-info-label"><?= __('organization') ?></span>
                                <span class="detail-info-value"><?= sanitize($post['organization']) ?></span>
                            </div>
                        </div>
                        <?php endif; ?>

                        <?php if ($post['total_vacancies']): ?>
                        <div class="detail-info-card highlight">
                            <span class="detail-info-icon"><i data-lucide="users"></i></span>
                            <div class="detail-info-content">
                                <span class="detail-info-label"><?= __('total_vacancies') ?></span>
                                <span class="detail-info-value"><?= formatNumber($post['total_vacancies']) ?></span>
                            </div>
                        </div>
                        <?php endif; ?>

                        <?php if ($post['qualification']): ?>
                        <div class="detail-info-card">
                            <span class="detail-info-icon"><i data-lucide="award"></i></span>
                            <div class="detail-info-content">
                                <span class="detail-info-label"><?= __('qualification') ?></span>
                                <span class="detail-info-value"><?= sanitize($post['qualification']) ?></span>
                            </div>
                        </div>
                        <?php endif; ?>

                        <?php if ($post['age_limit']): ?>
                        <div class="detail-info-card">
                            <span class="detail-info-icon"><i data-lucide="user-check"></i></span>
                            <div class="detail-info-content">
                                <span class="detail-info-label"><?= __('age_limit') ?></span>
                                <span class="detail-info-value"><?= sanitize($post['age_limit']) ?></span>
                            </div>
                        </div>
                        <?php endif; ?>

                        <?php if ($post['application_fee']): ?>
                        <div class="detail-info-card">
                            <span class="detail-info-icon"><i data-lucide="credit-card"></i></span>
                            <div class="detail-info-content">
                                <span class="detail-info-label"><?= __('application_fee') ?></span>
                                <span class="detail-info-value"><?= sanitize($post['application_fee']) ?></span>
                            </div>
                        </div>
                        <?php endif; ?>
                    </div>

                    <!-- Important Dates Card Section -->
                    <div class="detail-section">
                        <h3 class="detail-section-title"><i data-lucide="calendar"></i> <?= __('important_dates') ?></h3>
                        <div class="dates-grid">
                            <?php if ($post['post_date']): ?>
                            <div class="date-tile">
                                <span class="date-tile-label"><?= __('post_date') ?></span>
                                <span class="date-tile-value"><?= formatDate($post['post_date']) ?></span>
                            </div>
                            <?php endif; ?>
                            
                            <?php if ($post['last_date']): ?>
                            <div class="date-tile highlight-date">
                                <span class="date-tile-label"><?= __('last_date') ?></span>
                                <span class="date-tile-value"><?= formatDate($post['last_date']) ?></span>
                            </div>
                            <?php endif; ?>

                            <?php if ($post['exam_date']): ?>
                            <div class="date-tile">
                                <span class="date-tile-label"><?= __('exam_date') ?></span>
                                <span class="date-tile-value"><?= formatDate($post['exam_date']) ?></span>
                            </div>
                            <?php endif; ?>

                            <?php foreach ($importantDates as $key => $val): ?>
                            <?php 
                            if (is_array($val)) {
                                $event = $val['label'] ?? $val['event'] ?? $key;
                                $date = $val['date'] ?? $val['value'] ?? '';
                            } else {
                                $event = $key;
                                $date = $val;
                            }
                            ?>
                            <div class="date-tile">
                                <span class="date-tile-label"><?= sanitize($event) ?></span>
                                <span class="date-tile-value"><?= sanitize($date) ?></span>
                            </div>
                            <?php endforeach; ?>
                        </div>
                    </div>

                    <!-- Content / Details Description -->
                    <?php if ($contentHtml = $lang->field($post, 'content')): ?>
                    <div class="detail-section">
                        <h3 class="detail-section-title"><i data-lucide="file-text"></i> Description</h3>
                        <div class="post-content">
                            <?= $contentHtml ?>
                        </div>
                    </div>
                    <?php endif; ?>

                    <!-- Important Links Section -->
                    <?php if (!empty($importantLinks)): ?>
                    <div class="detail-section">
                        <h3 class="detail-section-title"><i data-lucide="link"></i> <?= __('important_links') ?></h3>
                        <div class="links-cards-grid">
                            <?php foreach ($importantLinks as $key => $val): ?>
                            <?php 
                            if (is_array($val)) {
                                $label = $val['label'] ?? $key;
                                $linkUrl = $val['url'] ?? $val['link'] ?? '';
                            } else {
                                $label = $key;
                                $linkUrl = $val;
                            }
                            ?>
                            <a href="<?= sanitize($linkUrl) ?>" target="_blank" rel="noopener" class="link-action-card">
                                <div class="link-action-info">
                                    <span class="link-action-icon"><i data-lucide="external-link"></i></span>
                                    <span class="link-action-label"><?= sanitize($label) ?></span>
                                </div>
                                <span class="btn btn-primary btn-sm"><?= __('view_details') ?> <i data-lucide="chevron-right" style="width: 14px; height: 14px;"></i></span>
                            </a>
                            <?php endforeach; ?>
                        </div>
                    </div>
                    <?php endif; ?>

                    <!-- Share Post -->
                    <div class="post-share-footer">
                        <span class="share-title"><?= __('share_post') ?></span>
                        <div class="share-buttons">
                            <a href="https://wa.me/?text=<?= urlencode($lang->field($post, 'title') . ' ' . url($post['category_slug'] . '/' . $post['slug'])) ?>" target="_blank" class="share-btn wa" title="WhatsApp">
                                <svg width="18" height="18" viewBox="0 0 24 24" fill="currentColor"><path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413z"/></svg>
                            </a>
                            <a href="https://t.me/share/url?url=<?= urlencode(url($post['category_slug'] . '/' . $post['slug'])) ?>&text=<?= urlencode($lang->field($post, 'title')) ?>" target="_blank" class="share-btn tg" title="Telegram">
                                <svg width="18" height="18" viewBox="0 0 24 24" fill="currentColor"><path d="M21.198 2.433a2.242 2.242 0 0 0-1.022.215l-8.609 3.33c-2.068.8-4.133 1.598-5.724 2.21a405.15 405.15 0 0 1-2.849 1.09c-.42.147-.99.332-1.473.901-.728.855.075 1.644.357 1.898.225.203 4.758 1.57 4.758 1.57l1.846 6.048s.389 1.22 1.402.596l3.263-2.913 3.843 3.02s.894.755 1.812.143c.228-.152.395-.394.503-.735l3.397-16.66c.18-.7.017-1.357-.473-1.714z"/></svg>
                            </a>
                            <button class="share-btn copy" title="Copy Link" onclick="navigator.clipboard.writeText(window.location.href).then(()=>this.innerHTML='<svg width=\'18\' height=\'18\' viewBox=\'0 0 24 24\' fill=\'none\' stroke=\'currentColor\' stroke-width=\'2\'><polyline points=\'20 6 9 17 4 12\'></polyline></svg>')">
                                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="9" y="9" width="13" height="13" rx="2" ry="2"/><path d="M5 15H4a2 2 0 0 1-2-2V4a2 2 0 0 1 2-2h9a2 2 0 0 1 2 2v1"/></svg>
                            </button>
                        </div>
                    </div>
                </article>

                <!-- Related Posts Section -->
                <?php if (!empty($relatedPosts)): ?>
                <div class="related-section mt-8">
                    <h3 class="section-title mb-6">
                        <span class="icon"><i data-lucide="pin"></i></span>
                        <?= __('related_posts') ?>
                    </h3>
                    <div class="posts-list">
                        <?php 
                        $originalPost = $post;
                        foreach ($relatedPosts as $rPost): 
                            $post = $rPost;
                            $showCategory = false;
                            include VIEWS_PATH . '/public/partials/post-card.php';
                        endforeach; 
                        $post = $originalPost;
                        ?>
                    </div>
                </div>
                <?php endif; ?>
            </div>

            <!-- Sidebar -->
            <aside class="content-sidebar">
                <!-- Popular Posts widget -->
                <div class="sidebar-widget glass-card">
                    <h3 class="sidebar-widget-title">
                        <i data-lucide="trending-up" style="color: var(--primary-light);"></i> 
                        <?= __('trending_now') ?>
                    </h3>
                    <div class="sidebar-list">
                        <?php
                        $postModel = new Post();
                        $mostViewed = $postModel->getMostViewed(6);
                        foreach ($mostViewed as $mvPost): ?>
                        <a href="<?= url($mvPost['category_slug'] . '/' . $mvPost['slug']) ?>" class="sidebar-list-item">
                            <span class="sidebar-item-icon" style="background: <?= $mvPost['category_color'] ?? '#6366F1' ?>15; color: <?= $mvPost['category_color'] ?? '#6366F1' ?>;">
                                <i data-lucide="<?= getCategoryIcon($mvPost['category_slug'], $mvPost['category_icon']) ?>"></i>
                            </span>
                            <span class="sidebar-item-text"><?= sanitize(truncate($lang->field($mvPost, 'title'), 50)) ?></span>
                            <span class="sidebar-item-views"><?= formatNumber($mvPost['views']) ?></span>
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
