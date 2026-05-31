<?php
/**
 * Admin Settings Page
 */
$lang = Lang::getInstance();

ob_start();
?>

<form method="POST" action="<?= url('admin/settings') ?>" enctype="multipart/form-data" class="admin-form">
    <?= csrf_field() ?>

    <!-- Branding -->
    <div class="admin-card mb-6">
        <div class="admin-card-header">
            <h2 class="admin-card-title"><i data-lucide="palette"></i> Branding</h2>
        </div>
        <div class="admin-card-body">
            <div class="form-row">
                <div class="form-group">
                    <label class="form-label">Site Name (English)</label>
                    <input type="text" name="site_name_en" class="form-input" value="<?= sanitize($settings->get('site_name_en')) ?>">
                </div>
                <div class="form-group">
                    <label class="form-label">Site Name (Hindi)</label>
                    <input type="text" name="site_name_hi" class="form-input" value="<?= sanitize($settings->get('site_name_hi')) ?>">
                </div>
            </div>
            <div class="form-row">
                <div class="form-group">
                    <label class="form-label">Tagline (English)</label>
                    <input type="text" name="site_tagline_en" class="form-input" value="<?= sanitize($settings->get('site_tagline_en')) ?>">
                </div>
                <div class="form-group">
                    <label class="form-label">Tagline (Hindi)</label>
                    <input type="text" name="site_tagline_hi" class="form-input" value="<?= sanitize($settings->get('site_tagline_hi')) ?>">
                </div>
            </div>
            <div class="form-group">
                <label class="form-label">Site Logo</label>
                <?php if ($logo = $settings->get('site_logo')): ?>
                <div style="margin-bottom: var(--space-3);">
                    <img src="<?= UPLOADS_URL . '/' . $logo ?>" style="max-height: 60px; border-radius: var(--radius-md);">
                </div>
                <?php endif; ?>
                <input type="file" name="site_logo" class="form-input" accept="image/*">
            </div>
        </div>
    </div>

    <!-- SEO -->
    <div class="admin-card mb-6">
        <div class="admin-card-header">
            <h2 class="admin-card-title"><i data-lucide="search"></i> SEO</h2>
        </div>
        <div class="admin-card-body">
            <div class="form-row">
                <div class="form-group">
                    <label class="form-label">Site Description (English)</label>
                    <textarea name="site_description_en" class="form-textarea" rows="3"><?= sanitize($settings->get('site_description_en')) ?></textarea>
                </div>
                <div class="form-group">
                    <label class="form-label">Site Description (Hindi)</label>
                    <textarea name="site_description_hi" class="form-textarea" rows="3"><?= sanitize($settings->get('site_description_hi')) ?></textarea>
                </div>
            </div>
        </div>
    </div>

    <!-- Ticker -->
    <div class="admin-card mb-6">
        <div class="admin-card-header">
            <h2 class="admin-card-title"><i data-lucide="megaphone"></i> News Ticker</h2>
        </div>
        <div class="admin-card-body">
            <div class="form-row">
                <div class="form-group">
                    <label class="form-label">Ticker Text (English)</label>
                    <input type="text" name="ticker_text_en" class="form-input" value="<?= sanitize($settings->get('ticker_text_en')) ?>">
                </div>
                <div class="form-group">
                    <label class="form-label">Ticker Text (Hindi)</label>
                    <input type="text" name="ticker_text_hi" class="form-input" value="<?= sanitize($settings->get('ticker_text_hi')) ?>">
                </div>
            </div>
        </div>
    </div>

    <!-- Social -->
    <div class="admin-card mb-6">
        <div class="admin-card-header">
            <h2 class="admin-card-title"><i data-lucide="share-2"></i> Social Links</h2>
        </div>
        <div class="admin-card-body">
            <div class="form-row">
                <div class="form-group">
                    <label class="form-label">Facebook URL</label>
                    <input type="url" name="social_facebook" class="form-input" value="<?= sanitize($settings->get('social_facebook')) ?>" placeholder="https://facebook.com/...">
                </div>
                <div class="form-group">
                    <label class="form-label">Twitter URL</label>
                    <input type="url" name="social_twitter" class="form-input" value="<?= sanitize($settings->get('social_twitter')) ?>" placeholder="https://twitter.com/...">
                </div>
            </div>
            <div class="form-row">
                <div class="form-group">
                    <label class="form-label">Telegram URL</label>
                    <input type="url" name="social_telegram" class="form-input" value="<?= sanitize($settings->get('social_telegram')) ?>" placeholder="https://t.me/...">
                </div>
                <div class="form-group">
                    <label class="form-label">YouTube URL</label>
                    <input type="url" name="social_youtube" class="form-input" value="<?= sanitize($settings->get('social_youtube')) ?>" placeholder="https://youtube.com/...">
                </div>
            </div>
        </div>
    </div>

    <!-- Footer & Contact -->
    <div class="admin-card mb-6">
        <div class="admin-card-header">
            <h2 class="admin-card-title"><i data-lucide="mail"></i> Footer & Contact</h2>
        </div>
        <div class="admin-card-body">
            <div class="form-group">
                <label class="form-label">Contact Email</label>
                <input type="email" name="contact_email" class="form-input" value="<?= sanitize($settings->get('contact_email')) ?>">
            </div>
            <div class="form-row">
                <div class="form-group">
                    <label class="form-label">Footer Text (English)</label>
                    <input type="text" name="footer_text_en" class="form-input" value="<?= sanitize($settings->get('footer_text_en')) ?>">
                </div>
                <div class="form-group">
                    <label class="form-label">Footer Text (Hindi)</label>
                    <input type="text" name="footer_text_hi" class="form-input" value="<?= sanitize($settings->get('footer_text_hi')) ?>">
                </div>
            </div>
        </div>
    </div>

    <!-- General -->
    <div class="admin-card mb-6">
        <div class="admin-card-header">
            <h2 class="admin-card-title"><i data-lucide="sliders"></i> General</h2>
        </div>
        <div class="admin-card-body">
            <div class="form-row">
                <div class="form-group">
                    <label class="form-label">Posts Per Page</label>
                    <input type="number" name="posts_per_page" class="form-input" value="<?= sanitize($settings->get('posts_per_page', '20')) ?>" min="5" max="100">
                </div>
                <div class="form-group">
                    <label class="form-label">Default Language</label>
                    <select name="default_language" class="form-select">
                        <option value="en" <?= $settings->get('default_language') === 'en' ? 'selected' : '' ?>>English</option>
                        <option value="hi" <?= $settings->get('default_language') === 'hi' ? 'selected' : '' ?>>Hindi</option>
                    </select>
                </div>
            </div>
        </div>
    </div>

    <div style="display: flex; justify-content: flex-end;">
        <button type="submit" class="btn btn-success btn-lg">
            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="20 6 9 17 4 12"/></svg>
            Save Settings
        </button>
    </div>
</form>

<?php
$adminContent = ob_get_clean();
include VIEWS_PATH . '/layouts/admin.php';
?>
