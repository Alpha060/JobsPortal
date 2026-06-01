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

    <!-- Page Transition Loader -->
    <div class="page-loader-bar" id="pageLoaderBar"></div>
    <div class="skeleton-overlay" id="skeletonOverlay">
        <div class="skeleton-header">
            <div class="skeleton-logo"></div>
            <div class="skeleton-nav-items">
                <div class="skeleton-nav-item" style="width: 60px;"></div>
                <div class="skeleton-nav-item" style="width: 50px;"></div>
                <div class="skeleton-nav-item" style="width: 70px;"></div>
                <div class="skeleton-nav-item" style="width: 55px;"></div>
                <div class="skeleton-nav-item" style="width: 65px;"></div>
            </div>
        </div>
        <div class="skeleton-body">
            <div class="skeleton-title"></div>
            <div class="skeleton-cards">
                <div class="skeleton-card"></div>
                <div class="skeleton-card"></div>
                <div class="skeleton-card"></div>
            </div>
            <div class="skeleton-title" style="width: 35%; margin-top: 24px;"></div>
            <div class="skeleton-cards">
                <div class="skeleton-card" style="height: 160px;"></div>
                <div class="skeleton-card" style="height: 160px;"></div>
                <div class="skeleton-card" style="height: 160px;"></div>
            </div>
        </div>
    </div>

    <!-- Scripts -->
    <script src="<?= asset('js/components.js') ?>"></script>
    <script src="<?= asset('js/app.js') ?>"></script>
    <script>
        if (window.lucide) {
            lucide.createIcons();
        }

        /* ── SPA Page Transitions with Hover Pre-fetching and Caching ── */
        (function() {
            const loaderBar = document.getElementById('pageLoaderBar');
            const skeleton = document.getElementById('skeletonOverlay');
            if (!loaderBar || !skeleton) return;

            const pageCache = {};
            let activeAbortController = null;

            function getNormalizedUrl(href) {
                try {
                    return new URL(href, window.location.origin).href;
                } catch(e) {
                    return href;
                }
            }

            function isValidLink(href, link) {
                if (!href || href === '#' || href.startsWith('javascript:')
                    || href.startsWith('mailto:') || href.startsWith('tel:')
                    || link.target === '_blank'
                    || link.hasAttribute('download')) {
                    return false;
                }

                try {
                    const url = new URL(href, window.location.origin);
                    if (url.origin !== window.location.origin) return false;
                    if (url.pathname === window.location.pathname && url.hash) return false;
                    if (url.pathname.startsWith('/admin')) return false;
                    return true;
                } catch(e) {
                    return false;
                }
            }

            // Cache the initial landing page
            const initialUrl = getNormalizedUrl(window.location.href);
            pageCache[initialUrl] = {
                mainContentHtml: document.getElementById('mainContent')?.innerHTML || '',
                mainContentClass: document.getElementById('mainContent')?.className || '',
                title: document.title,
                mainNavHtml: document.getElementById('mainNav')?.innerHTML || '',
                langToggleHtml: document.querySelector('.lang-toggle')?.outerHTML || ''
            };

            function applyPageData(data) {
                // 1. Swap main content
                const currentMain = document.getElementById('mainContent');
                if (currentMain) {
                    currentMain.innerHTML = data.mainContentHtml;
                    currentMain.className = data.mainContentClass;
                }

                // 2. Update page title
                document.title = data.title;

                // 3. Swap navigation active states (preserve element reference)
                const currentNav = document.getElementById('mainNav');
                if (currentNav) {
                    currentNav.innerHTML = data.mainNavHtml;
                }

                // 4. Swap language switcher URL
                const currentLangToggle = document.querySelector('.lang-toggle');
                if (currentLangToggle && data.langToggleHtml) {
                    currentLangToggle.outerHTML = data.langToggleHtml;
                }

                // Close mobile menu if open
                const mainNavEl = document.getElementById('mainNav');
                const navOverlay = document.getElementById('navOverlay');
                if (mainNavEl && mainNavEl.classList.contains('open')) {
                    mainNavEl.classList.remove('open');
                    if (navOverlay) navOverlay.classList.remove('active');
                    document.body.style.overflow = '';
                }

                // Re-run dynamic front-end initializers
                if (window.lucide) {
                    window.lucide.createIcons();
                }
                if (typeof initTabs === 'function') initTabs();
                if (typeof initScrollAnimations === 'function') initScrollAnimations();
            }

            function navigateTo(href, push = true) {
                const urlKey = getNormalizedUrl(href);

                // Clear cache on language change
                const urlObj = new URL(href, window.location.origin);
                if (urlObj.pathname.startsWith('/lang')) {
                    for (const key in pageCache) {
                        delete pageCache[key];
                    }
                }

                const isCached = !!pageCache[urlKey];

                if (isCached) {
                    // Show cached data instantly! No skeleton, no wait.
                    applyPageData(pageCache[urlKey]);
                    
                    if (push) {
                        window.history.pushState(null, '', href);
                    }
                    window.scrollTo({ top: 0, behavior: 'instant' });

                    // Hide loader/skeleton immediately
                    loaderBar.classList.remove('active');
                    loaderBar.classList.add('done');
                    skeleton.classList.remove('active');
                } else {
                    // Show loader bar + skeleton instantly
                    loaderBar.classList.remove('done');
                    loaderBar.classList.add('active');
                    skeleton.classList.add('active');

                    if (activeAbortController) {
                        activeAbortController.abort();
                    }
                    activeAbortController = new AbortController();

                    fetch(href, { signal: activeAbortController.signal })
                        .then(response => {
                            if (!response.ok) throw new Error('HTTP ' + response.status);
                            return response.text();
                        })
                        .then(html => {
                            const parser = new DOMParser();
                            const doc = parser.parseFromString(html, 'text/html');

                            const freshData = {
                                mainContentHtml: doc.getElementById('mainContent')?.innerHTML || '',
                                mainContentClass: doc.getElementById('mainContent')?.className || '',
                                title: doc.querySelector('title')?.textContent || '',
                                mainNavHtml: doc.getElementById('mainNav')?.innerHTML || '',
                                langToggleHtml: doc.querySelector('.lang-toggle')?.outerHTML || ''
                            };

                            pageCache[urlKey] = freshData;

                            if (push) {
                                window.history.pushState(null, '', href);
                            }
                            applyPageData(freshData);

                            // Hide loaders
                            setTimeout(() => {
                                loaderBar.classList.remove('active');
                                loaderBar.classList.add('done');
                                skeleton.classList.remove('active');
                            }, 50);
                        })
                        .catch(err => {
                            if (err.name === 'AbortError') return;
                            console.error('SPA transition failed, falling back:', err);
                            window.location.href = href;
                        });
                }
            }

            let lastTriggeredUrl = null;
            let lastTriggeredTime = 0;

            function triggerNavigation(href) {
                const urlKey = getNormalizedUrl(href);
                const now = Date.now();
                
                // Prevent double execution on desktop (mousedown + click) within 100ms
                if (urlKey === lastTriggeredUrl && (now - lastTriggeredTime) < 100) {
                    return;
                }
                
                lastTriggeredUrl = urlKey;
                lastTriggeredTime = now;
                navigateTo(href, true);
            }

            // Intercept link clicks to prevent default browser navigation and handle keyboard / mobile taps
            document.addEventListener('click', function(e) {
                const link = e.target.closest('a[href]');
                if (!link) return;

                const href = link.getAttribute('href');
                if (!isValidLink(href, link)) return;

                if (e.ctrlKey || e.metaKey || e.shiftKey || (e.button !== undefined && e.button !== 0)) {
                    return; // Allow default behavior for new tabs/windows
                }

                e.preventDefault();

                const urlKey = getNormalizedUrl(href);
                if (urlKey === getNormalizedUrl(window.location.href)) return;

                triggerNavigation(href);
            });

            // Start navigation instantly on mousedown (left-click down, desktop only)
            document.addEventListener('mousedown', function(e) {
                if (e.button !== 0 || e.ctrlKey || e.metaKey || e.shiftKey) return;
                
                const link = e.target.closest('a[href]');
                if (!link) return;

                const href = link.getAttribute('href');
                if (!isValidLink(href, link)) return;

                const urlKey = getNormalizedUrl(href);
                if (urlKey === getNormalizedUrl(window.location.href)) return;

                triggerNavigation(href);
            });

            // Intercept form submissions (e.g. search form)
            document.addEventListener('submit', function(e) {
                const form = e.target;
                if (form.method.toLowerCase() !== 'get') return; // only handle GET forms

                const action = form.getAttribute('action');
                if (!action) return;

                const url = new URL(action, window.location.origin);
                if (url.origin !== window.location.origin) return;
                if (url.pathname.startsWith('/admin')) return;

                // Build query params
                const formData = new FormData(form);
                const params = new URLSearchParams(formData);
                const fullUrl = url.pathname + '?' + params.toString();

                e.preventDefault();
                
                // Hide search overlay if open
                const searchOverlay = document.getElementById('searchOverlay');
                if (searchOverlay) {
                    searchOverlay.classList.remove('active');
                }

                navigateTo(fullUrl, true);
            });

            // Handle back/forward buttons
            window.addEventListener('popstate', function() {
                navigateTo(window.location.href, false);
            });

            // Fallback for pageshow
            window.addEventListener('pageshow', function(e) {
                loaderBar.classList.remove('active');
                loaderBar.classList.add('done');
                skeleton.classList.remove('active');
            });
        })();
    </script>
</body>
</html>
