/**
 * JobsPortal — Shared Interactive Components & Micro-animations
 * Handled: Ripple effects, Scroll-to-top button, Tooltips, Copy to Clipboard helpers
 */

document.addEventListener('DOMContentLoaded', () => {
    initRipples();
    initScrollToTop();
    initTooltips();
});

/**
 * ── Material Ripple Effect ──
 * Adds micro-interaction feedback ripples to premium glass buttons
 */
function initRipples() {
    const buttons = document.querySelectorAll('.btn, .glass-button, .tab-btn, .lang-toggle');
    
    buttons.forEach(button => {
        button.addEventListener('click', function(e) {
            // Remove any old ripples
            const oldRipple = this.querySelector('.ripple');
            if (oldRipple) oldRipple.remove();

            const rect = this.getBoundingClientRect();
            const x = e.clientX - rect.left;
            const y = e.clientY - rect.top;

            const ripple = document.createElement('span');
            ripple.classList.add('ripple');
            ripple.style.left = `${x}px`;
            ripple.style.top = `${y}px`;

            this.appendChild(ripple);

            // Clean up ripple after animation
            setTimeout(() => {
                ripple.remove();
            }, 600);
        });
    });
}

/**
 * ── Scroll To Top Button ──
 * Smoothly scrolls user back to top with progressive scroll indicator circle
 */
function initScrollToTop() {
    // Disable floating scroll-to-top button on admin pages to prevent overlap with wizard/form buttons
    if (document.querySelector('.admin-wrapper')) return;

    const btn = document.createElement('button');
    btn.id = 'scrollToTopBtn';
    btn.className = 'scroll-to-top-btn';
    btn.innerHTML = `
        <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
            <polyline points="18 15 12 9 6 15"></polyline>
        </svg>
    `;
    document.body.appendChild(btn);

    window.addEventListener('scroll', () => {
        if (window.scrollY > 400) {
            btn.classList.add('visible');
        } else {
            btn.classList.remove('visible');
        }
    });

    btn.addEventListener('click', () => {
        window.scrollTo({
            top: 0,
            behavior: 'smooth'
        });
    });
}

/**
 * ── Interactive Tooltips ──
 * Renders custom tooltips on items containing data-tooltip attribute
 */
function initTooltips() {
    const tooltipTargets = document.querySelectorAll('[data-tooltip]');
    
    tooltipTargets.forEach(target => {
        target.addEventListener('mouseenter', function() {
            const text = this.getAttribute('data-tooltip');
            if (!text) return;

            const tooltip = document.createElement('div');
            tooltip.className = 'glass-tooltip';
            tooltip.innerText = text;
            document.body.appendChild(tooltip);

            const rect = this.getBoundingClientRect();
            const tooltipRect = tooltip.getBoundingClientRect();

            // Position at top center of target
            tooltip.style.top = `${rect.top - tooltipRect.height - 8 + window.scrollY}px`;
            tooltip.style.left = `${rect.left + (rect.width - tooltipRect.width) / 2 + window.scrollX}px`;
            tooltip.classList.add('visible');

            this.addEventListener('mouseleave', () => {
                tooltip.remove();
            }, { once: true });
        });
    });
}

/**
 * ── Utility: Copy Text to Clipboard ──
 * Offers a premium notification overlay when copy completes
 */
window.copyToClipboardText = function(text, successMessage = 'Copied to clipboard!') {
    navigator.clipboard.writeText(text).then(() => {
        showNotification(successMessage, 'success');
    }).catch(err => {
        showNotification('Failed to copy text', 'error');
        console.error('Clipboard copy failed: ', err);
    });
};

/**
 * ── Premium Notification Overlay ──
 */
window.showNotification = function(message, type = 'success') {
    const oldNotification = document.querySelector('.glass-notification');
    if (oldNotification) oldNotification.remove();

    const notif = document.createElement('div');
    notif.className = `glass-notification ${type}`;
    notif.innerHTML = `
        <span class="notif-icon">${type === 'success' ? '✅' : '❌'}</span>
        <span class="notif-text">${message}</span>
    `;
    
    document.body.appendChild(notif);
    
    setTimeout(() => notif.classList.add('visible'), 50);
    
    setTimeout(() => {
        notif.classList.remove('visible');
        setTimeout(() => notif.remove(), 400);
    }, 3000);
};
