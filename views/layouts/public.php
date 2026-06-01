<?php
/**
 * Public Layout Template
 * 
 * Wraps all public pages with header, nav, ticker, and footer.
 * Variables expected: $pageTitle, $pageDescription, $content (buffered output)
 */

$lang = Lang::getInstance();
$settings = new Setting();
$siteName = $settings->getLocalized('site_name');
$siteTagline = $settings->getLocalized('site_tagline');
$siteDescription = $settings->getLocalized('site_description');
$tickerText = $settings->getLocalized('ticker_text');

$pageTitle = isset($pageTitle) ? $pageTitle . ' — ' . $siteName : $siteName . ' — ' . $siteTagline;
$pageDescription = $pageDescription ?? $siteDescription;

$categoryModel = new Category();
$categories = $categoryModel->getAll();

$currentLang = $lang->current();
$isHindi = $lang->is('hi');
?>
<!DOCTYPE html>
<html lang="<?= $currentLang ?>" dir="ltr">
<head>
    <script>
        if (localStorage.getItem('theme') !== 'dark') {
            document.documentElement.classList.add('light-mode');
        }
    </script>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    
    <!-- SEO -->
    <title><?= sanitize($pageTitle) ?></title>
    <meta name="description" content="<?= sanitize($pageDescription) ?>">
    <meta name="keywords" content="government jobs, sarkari result, sarkari naukri, exam results, admit card, answer key">
    <meta name="robots" content="index, follow">
    
    <!-- Open Graph -->
    <meta property="og:title" content="<?= sanitize($pageTitle) ?>">
    <meta property="og:description" content="<?= sanitize($pageDescription) ?>">
    <meta property="og:type" content="website">
    <meta property="og:url" content="<?= sanitize(url(currentPath())) ?>">
    
    <!-- Favicon -->
    <link rel="icon" type="image/svg+xml" href="<?= asset('images/favicon.svg') ?>">
    
    <!-- Stylesheets -->
    <link rel="stylesheet" href="<?= asset('css/variables.css') ?>">
    <link rel="stylesheet" href="<?= asset('css/base.css') ?>">
    <link rel="stylesheet" href="<?= asset('css/components.css') ?>">
    <link rel="stylesheet" href="<?= asset('css/public.css') ?>">
    <link rel="stylesheet" href="<?= asset('css/responsive.css') ?>">
    <script src="https://unpkg.com/lucide@latest"></script>
</head>
<body id="top">

    <!-- News Ticker -->
    <?php include VIEWS_PATH . '/public/partials/marquee.php'; ?>

    <!-- Site Header -->
    <?php include VIEWS_PATH . '/public/partials/header.php'; ?>

    <!-- Flash Messages -->
    <?php if (hasFlash('success')): ?>
    <div class="container"><div class="alert alert-success mt-4"><i data-lucide="check-circle-2"></i> <?= sanitize(getFlash('success')) ?></div></div>
    <?php endif; ?>
    <?php if (hasFlash('error')): ?>
    <div class="container"><div class="alert alert-danger mt-4"><i data-lucide="x-circle"></i> <?= sanitize(getFlash('error')) ?></div></div>
    <?php endif; ?>

    <!-- Main Content -->
    <main id="mainContent">
        <?= $content ?>
    </main>

    <!-- Footer -->
    <?php include VIEWS_PATH . '/public/partials/footer.php'; ?>

    <!-- Scripts -->
    <script src="<?= asset('js/components.js') ?>"></script>
    <script src="<?= asset('js/app.js') ?>"></script>
    <script>
        if (window.lucide) {
            lucide.createIcons();
        }
    </script>
</body>
</html>
