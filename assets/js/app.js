/**
 * JobsPortal — Public JavaScript
 * Handles: search overlay, theme toggle, language toggle,
 * mobile nav, scroll animations, tabs
 */

document.addEventListener('DOMContentLoaded', () => {
    initThemeToggle();
    initSearchOverlay();
    initMobileNav();
    initTabs();
    initScrollAnimations();
    initRealtimeSSE();
});

/* ── Lightweight polling for updates (replaces SSE to avoid holding PHP processes) ── */
function initRealtimeSSE() {
    let lastTime = Math.floor(Date.now() / 1000);

    setInterval(() => {
        fetch('/api/updates/check?last_time=' + lastTime)
            .then(res => res.json())
            .then(data => {
                if (data.hasUpdate) {
                    lastTime = data.timestamp;
                    // Reload page content
                    window.location.reload();
                }
            })
            .catch(() => { /* silently ignore polling errors */ });
    }, 60000); // Check every 60 seconds
}

/* ── Theme Toggle (Dark/Light) ── */
function initThemeToggle() {
    const toggle = document.getElementById('themeToggle');
    if (!toggle) return;

    const savedTheme = localStorage.getItem('theme') || 'light';
    if (savedTheme !== 'dark') {
        document.documentElement.classList.add('light-mode');
        updateThemeIcons(true);
    } else {
        document.documentElement.classList.remove('light-mode');
        updateThemeIcons(false);
    }

    toggle.addEventListener('click', () => {
        const isLight = document.documentElement.classList.toggle('light-mode');
        localStorage.setItem('theme', isLight ? 'light' : 'dark');
        updateThemeIcons(isLight);
    });
}

function updateThemeIcons(isLight) {
    const darkIcon = document.querySelector('.theme-icon-dark');
    const lightIcon = document.querySelector('.theme-icon-light');
    if (darkIcon) darkIcon.style.display = isLight ? 'none' : 'block';
    if (lightIcon) lightIcon.style.display = isLight ? 'block' : 'none';
}

/* ── Search Overlay ── */
function initSearchOverlay() {
    const searchToggle = document.getElementById('searchToggle');
    const searchOverlay = document.getElementById('searchOverlay');
    const searchClose = document.getElementById('searchClose');

    if (!searchToggle || !searchOverlay) return;

    searchToggle.addEventListener('click', () => {
        searchOverlay.classList.add('active');
        setTimeout(() => {
            const input = searchOverlay.querySelector('input');
            if (input) input.focus();
        }, 100);
    });

    if (searchClose) {
        searchClose.addEventListener('click', () => {
            searchOverlay.classList.remove('active');
        });
    }

    searchOverlay.addEventListener('click', (e) => {
        if (e.target === searchOverlay) {
            searchOverlay.classList.remove('active');
        }
    });

    // Close on Escape
    document.addEventListener('keydown', (e) => {
        if (e.key === 'Escape' && searchOverlay.classList.contains('active')) {
            searchOverlay.classList.remove('active');
        }
    });

    // Open with Ctrl+K
    document.addEventListener('keydown', (e) => {
        if ((e.ctrlKey || e.metaKey) && e.key === 'k') {
            e.preventDefault();
            searchOverlay.classList.add('active');
            setTimeout(() => {
                const input = searchOverlay.querySelector('input');
                if (input) input.focus();
            }, 100);
        }
    });
}

/* ── Mobile Navigation ── */
function initMobileNav() {
    const navToggle = document.getElementById('navToggle');
    const mainNav = document.getElementById('mainNav');
    const navOverlay = document.getElementById('navOverlay');

    if (!navToggle || !mainNav) return;

    navToggle.addEventListener('click', () => {
        mainNav.classList.toggle('open');
        if (navOverlay) navOverlay.classList.toggle('active');
        document.body.style.overflow = mainNav.classList.contains('open') ? 'hidden' : '';
    });

    if (navOverlay) {
        navOverlay.addEventListener('click', () => {
            mainNav.classList.remove('open');
            navOverlay.classList.remove('active');
            document.body.style.overflow = '';
        });
    }
}

/* ── Category Tabs ── */
function initTabs() {
    const tabContainer = document.getElementById('categoryTabs');
    if (!tabContainer) return;

    const tabs = tabContainer.querySelectorAll('.tab-btn');
    const contents = document.querySelectorAll('.tab-content');

    tabs.forEach(tab => {
        tab.addEventListener('click', () => {
            const targetId = 'tab-' + tab.dataset.tab;

            // Deactivate all
            tabs.forEach(t => t.classList.remove('active'));
            contents.forEach(c => c.classList.remove('active'));

            // Activate selected
            tab.classList.add('active');
            const target = document.getElementById(targetId);
            if (target) target.classList.add('active');
        });
    });
}

/* ── Scroll Animations (IntersectionObserver) ── */
function initScrollAnimations() {
    const animatedElements = document.querySelectorAll('.animate-fade-in-up, .animate-fade-in, .animate-slide-in-right');

    if (!animatedElements.length) return;

    // Set initial opacity to 0 for scroll-triggered animations
    animatedElements.forEach(el => {
        if (!isInViewport(el)) {
            el.style.opacity = '0';
        }
    });

    const observer = new IntersectionObserver((entries) => {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                entry.target.style.opacity = '';
                entry.target.style.animation = entry.target.style.animation || '';
                observer.unobserve(entry.target);
            }
        });
    }, {
        threshold: 0.1,
        rootMargin: '0px 0px -40px 0px'
    });

    animatedElements.forEach(el => observer.observe(el));
}

function isInViewport(element) {
    const rect = element.getBoundingClientRect();
    return (
        rect.top < window.innerHeight &&
        rect.bottom > 0
    );
}
