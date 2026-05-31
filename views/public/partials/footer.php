<?php
/**
 * Footer Partial
 * Variables expected: $siteName, $settings, $categories, $lang, $isHindi
 */
?>
<footer class="site-footer">
    <div class="container">
        <div class="footer-inner">
            <!-- About -->
            <div class="footer-about">
                <a href="<?= url('/') ?>" class="footer-logo">
                    <div class="site-logo-icon-wrapper">
                        <?php if ($logo = $settings->get('site_logo')): ?>
                            <img src="<?= UPLOADS_URL . '/' . $logo ?>" alt="Logo" class="site-logo-img">
                        <?php else: ?>
                            <div class="site-logo-circle"><?= strtoupper(substr($settings->get('site_name_en') ?: $siteName, 0, 1)) ?></div>
                        <?php endif; ?>
                    </div>
                    <div class="site-logo-text"><?= sanitize($siteName) ?></div>
                </a>
                <p class="footer-desc"><?= __('about_description') ?></p>
                <div class="footer-social">
                    <?php if ($fb = $settings->get('social_facebook')): ?>
                    <a href="<?= sanitize($fb) ?>" target="_blank" rel="noopener" aria-label="Facebook">
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="currentColor"><path d="M18 2h-3a5 5 0 0 0-5 5v3H7v4h3v8h4v-8h3l1-4h-4V7a1 1 0 0 1 1-1h3z"/></svg>
                    </a>
                    <?php endif; ?>
                    <?php if ($tw = $settings->get('social_twitter')): ?>
                    <a href="<?= sanitize($tw) ?>" target="_blank" rel="noopener" aria-label="Twitter">
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="currentColor"><path d="M23 3a10.9 10.9 0 0 1-3.14 1.53 4.48 4.48 0 0 0-7.86 3v1A10.66 10.66 0 0 1 3 4s-4 9 5 13a11.64 11.64 0 0 1-7 2c9 5 20 0 20-11.5a4.5 4.5 0 0 0-.08-.83A7.72 7.72 0 0 0 23 3z"/></svg>
                    </a>
                    <?php endif; ?>
                    <?php if ($tg = $settings->get('social_telegram')): ?>
                    <a href="<?= sanitize($tg) ?>" target="_blank" rel="noopener" aria-label="Telegram">
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="currentColor"><path d="M21.198 2.433a2.242 2.242 0 0 0-1.022.215l-8.609 3.33c-2.068.8-4.133 1.598-5.724 2.21a405.15 405.15 0 0 1-2.849 1.09c-.42.147-.99.332-1.473.901-.728.855.075 1.644.357 1.898.225.203 4.758 1.57 4.758 1.57l1.846 6.048s.389 1.22 1.402.596l3.263-2.913 3.843 3.02s.894.755 1.812.143c.228-.152.395-.394.503-.735l3.397-16.66c.18-.7.017-1.357-.473-1.714z"/></svg>
                    </a>
                    <?php endif; ?>
                </div>
            </div>

            <!-- Categories -->
            <div>
                <h4 class="footer-heading"><?= __('browse_categories') ?></h4>
                <div class="footer-links">
                    <?php foreach ($categories as $cat): ?>
                    <a href="<?= url($cat['slug']) ?>" style="display: inline-flex; align-items: center; gap: 6px;">
                        <i data-lucide="<?= sanitize($cat['icon']) ?>" style="width: 14px; height: 14px;"></i> <?= sanitize($lang->field($cat, 'name')) ?>
                    </a>
                    <?php endforeach; ?>
                </div>
            </div>

            <!-- Quick Links -->
            <div>
                <h4 class="footer-heading"><?= __('quick_links') ?></h4>
                <div class="footer-links">
                    <a href="<?= url('/') ?>"><?= __('home') ?></a>
                    <a href="<?= url('search') ?>"><?= __('search') ?></a>
                    <a href="<?= url('about') ?>"><?= __('about_us') ?></a>
                    <a href="<?= url('contact') ?>"><?= __('contact_us') ?></a>
                    <a href="<?= url('privacy') ?>"><?= __('privacy_policy') ?></a>
                </div>
            </div>

            <!-- Contact -->
            <div>
                <h4 class="footer-heading"><?= __('contact_us') ?></h4>
                <div class="footer-links">
                    <?php if ($email = $settings->get('contact_email')): ?>
                    <a href="mailto:<?= sanitize($email) ?>"><?= sanitize($email) ?></a>
                    <?php endif; ?>
                </div>
            </div>
        </div>

        <div class="footer-bottom">
            <span><?= $settings->getLocalized('footer_text') ?></span>
            <span>
                <a href="<?= $lang->switchUrl($isHindi ? 'en' : 'hi') ?>">
                    <?= $isHindi ? '🇬🇧 English' : '🇮🇳 हिन्दी' ?>
                </a>
            </span>
        </div>
    </div>
</footer>
