<?php
/**
 * Header Partial
 * Variables expected: $siteName, $categories, $lang, $isHindi
 */
?>
<header class="site-header" id="siteHeader">
    <div class="container">
        <div class="header-inner">
            <!-- Logo -->
            <a href="<?= url('/') ?>" class="site-logo">
                <div class="site-logo-icon-wrapper">
                    <?php if ($logo = $settings->get('site_logo')): ?>
                        <img src="<?= UPLOADS_URL . '/' . $logo ?>" alt="Logo" class="site-logo-img">
                    <?php else: ?>
                        <div class="site-logo-circle"><?= strtoupper(substr($settings->get('site_name_en') ?: $siteName, 0, 1)) ?></div>
                    <?php endif; ?>
                </div>
                <div class="site-logo-text"><?= sanitize($siteName) ?></div>
            </a>

            <!-- Main Navigation -->
            <?php include VIEWS_PATH . '/public/partials/nav.php'; ?>

            <!-- Header Actions -->
            <div class="header-actions">
                <!-- Search -->
                <button class="search-toggle" id="searchToggle" aria-label="<?= __('search') ?>">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/></svg>
                </button>

                <!-- Language Toggle -->
                <a href="<?= $lang->switchUrl($isHindi ? 'en' : 'hi') ?>" class="lang-toggle" title="<?= __('language') ?>">
                    <?= $isHindi ? 'EN' : 'हि' ?>
                </a>

                <!-- Theme Toggle -->
                <button class="theme-toggle" id="themeToggle" aria-label="Toggle theme">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" class="theme-icon-dark"><path d="M21 12.79A9 9 0 1 1 11.21 3 7 7 0 0 0 21 12.79z"/></svg>
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" class="theme-icon-light" style="display:none"><circle cx="12" cy="12" r="5"/><line x1="12" y1="1" x2="12" y2="3"/><line x1="12" y1="21" x2="12" y2="23"/><line x1="4.22" y1="4.22" x2="5.64" y2="5.64"/><line x1="18.36" y1="18.36" x2="19.78" y2="19.78"/><line x1="1" y1="12" x2="3" y2="12"/><line x1="21" y1="12" x2="23" y2="12"/><line x1="4.22" y1="19.78" x2="5.64" y2="18.36"/><line x1="18.36" y1="5.64" x2="19.78" y2="4.22"/></svg>
                </button>

                <!-- Mobile Nav Toggle -->
                <button class="nav-toggle" id="navToggle" aria-label="Toggle navigation">
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="3" y1="12" x2="21" y2="12"/><line x1="3" y1="6" x2="21" y2="6"/><line x1="3" y1="18" x2="21" y2="18"/></svg>
                </button>
            </div>
        </div>
    </div>
    <!-- Nav Overlay (mobile) -->
    <div class="nav-overlay" id="navOverlay"></div>
</header>

<!-- Search Overlay -->
<div class="search-overlay" id="searchOverlay">
    <div class="search-box">
        <form action="<?= url('search') ?>" method="GET" class="search-input-wrapper">
            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/></svg>
            <input type="text" name="q" class="search-input" placeholder="<?= __('search_placeholder') ?>" autocomplete="off" autofocus>
            <button type="button" class="search-close" id="searchClose" aria-label="Close search">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg>
            </button>
        </form>
    </div>
</div>
