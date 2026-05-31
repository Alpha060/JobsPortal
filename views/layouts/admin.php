<?php
/**
 * Admin Layout Template
 */
$auth = new Auth();
$admin = $auth->getAdmin();
$lang = Lang::getInstance();
$settings = new Setting();
$siteName = $settings->getLocalized('site_name');
$adminPageTitle = $adminPageTitle ?? __('dashboard');

$postModel = new Post();
$postStats = $postModel->getStats();
?>
<!DOCTYPE html>
<html lang="<?= $lang->current() ?>">
<head>
    <script>
        if (localStorage.getItem('theme') === 'light') {
            document.documentElement.classList.add('light-mode');
        }
        const APP_URL = "<?= url('/') ?>";
    </script>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= sanitize($adminPageTitle) ?> — <?= sanitize($siteName) ?> Admin</title>
    <meta name="robots" content="noindex, nofollow">
    <link rel="stylesheet" href="<?= asset('css/variables.css') ?>">
    <link rel="stylesheet" href="<?= asset('css/base.css') ?>">
    <link rel="stylesheet" href="<?= asset('css/components.css') ?>">
    <link rel="stylesheet" href="<?= asset('css/admin.css') ?>?v=1.4">
    <link rel="stylesheet" href="<?= asset('css/responsive.css') ?>">
    <script src="https://unpkg.com/lucide@latest"></script>
</head>
<body>
<div class="admin-wrapper">
    <!-- Sidebar -->
    <aside class="admin-sidebar" id="adminSidebar">
        <div class="admin-sidebar-header">
            <?php
            $logo = $settings->get('site_logo');
            $siteNameEn = $settings->get('site_name_en');
            $firstLetter = strtoupper(substr($siteNameEn ?: ($siteName ?: 'S'), 0, 1));
            ?>
            <a href="<?= url('/') ?>" class="site-logo" target="_blank">
                <div class="site-logo-icon-wrapper">
                    <?php if (!empty($logo)): ?>
                        <img src="<?= UPLOADS_URL . '/' . $logo ?>" alt="Logo" class="site-logo-img">
                    <?php else: ?>
                        <div class="site-logo-circle"><?= $firstLetter ?></div>
                    <?php endif; ?>
                </div>
                <div class="site-logo-text-wrapper">
                    <div class="site-logo-title"><?= sanitize($siteName) ?></div>
                    <div class="site-logo-subtitle">Admin Panel</div>
                </div>
            </a>
        </div>

        <nav class="admin-sidebar-nav">
            <a href="<?= url('admin/dashboard') ?>" class="admin-nav-link <?= isActivePath('/admin/dashboard') ? 'active' : '' ?>">
                <span class="nav-icon"><i data-lucide="gauge"></i></span> <?= __('dashboard') ?>
            </a>
            <a href="<?= url('admin/posts') ?>" class="admin-nav-link <?= isActivePath('/admin/posts') ? 'active' : '' ?>">
                <span class="nav-icon"><i data-lucide="file-text"></i></span> <?= __('manage_posts') ?>
                <span class="nav-badge"><?= $postStats['total'] ?></span>
            </a>
            <a href="<?= url('admin/posts/create') ?>" class="admin-nav-link <?= isActivePath('/admin/posts/create') ? 'active' : '' ?>">
                <span class="nav-icon"><i data-lucide="edit-3"></i></span> Create Post
            </a>
            <a href="<?= url('admin/categories') ?>" class="admin-nav-link <?= isActivePath('/admin/categories') ? 'active' : '' ?>">
                <span class="nav-icon"><i data-lucide="layout-grid"></i></span> <?= __('manage_categories') ?>
            </a>
            <a href="<?= url('admin/media') ?>" class="admin-nav-link <?= isActivePath('/admin/media') ? 'active' : '' ?>">
                <span class="nav-icon"><i data-lucide="image"></i></span> <?= __('media_library') ?>
            </a>
            <a href="<?= url('admin/settings') ?>" class="admin-nav-link <?= isActivePath('/admin/settings') ? 'active' : '' ?>">
                <span class="nav-icon"><i data-lucide="settings"></i></span> <?= __('site_settings') ?>
            </a>
            <a href="<?= url('/') ?>" class="admin-nav-link" target="_blank">
                <span class="nav-icon"><i data-lucide="globe"></i></span> View Site <i data-lucide="external-link" class="nav-link-external-icon"></i>
            </a>
        </nav>
    </aside>

    <!-- Main Content -->
    <div class="admin-main">
        <!-- Topbar -->
        <header class="admin-topbar">
            <div class="admin-topbar-left">
                <button class="admin-sidebar-toggle" id="sidebarToggle" aria-label="Toggle sidebar">
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="3" y1="12" x2="21" y2="12"/><line x1="3" y1="6" x2="21" y2="6"/><line x1="3" y1="18" x2="21" y2="18"/></svg>
                </button>
                <h1 class="admin-page-title"><?= sanitize($adminPageTitle) ?></h1>
            </div>
            <div class="admin-topbar-right">
                <!-- Theme Toggle -->
                <button class="theme-toggle" id="themeToggle" aria-label="Toggle theme" style="margin-right: var(--space-1);">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" class="theme-icon-dark"><path d="M21 12.79A9 9 0 1 1 11.21 3 7 7 0 0 0 21 12.79z"/></svg>
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" class="theme-icon-light" style="display:none"><circle cx="12" cy="12" r="5"/><line x1="12" y1="1" x2="12" y2="3"/><line x1="12" y1="21" x2="12" y2="23"/><line x1="4.22" y1="4.22" x2="5.64" y2="5.64"/><line x1="18.36" y1="18.36" x2="19.78" y2="19.78"/><line x1="1" y1="12" x2="3" y2="12"/><line x1="21" y1="12" x2="23" y2="12"/><line x1="4.22" y1="19.78" x2="5.64" y2="18.36"/><line x1="18.36" y1="5.64" x2="19.78" y2="4.22"/></svg>
                </button>
                <a href="<?= url('/') ?>" class="btn btn-ghost btn-sm" target="_blank"><i data-lucide="globe"></i> View Site</a>
                <!-- Profile Dropdown -->
                <div class="profile-dropdown" id="profileDropdown">
                    <button class="profile-dropdown-btn" id="profileDropdownBtn" aria-label="Toggle profile menu">
                        <div class="admin-avatar">
                            <?= strtoupper(substr($admin['username'] ?? 'A', 0, 1)) ?>
                        </div>
                        <span class="admin-user-name"><?= sanitize($admin['full_name'] ?: $admin['username'] ?? 'Admin') ?></span>
                        <i data-lucide="chevron-down" class="dropdown-arrow"></i>
                    </button>
                    <div class="dropdown-menu" id="profileDropdownMenu">
                        <button type="button" class="dropdown-item" id="changePasswordBtn">
                            <i data-lucide="key"></i> Change Password
                        </button>
                        <a href="<?= url('admin/logout') ?>" class="dropdown-item logout-link" style="color: var(--danger-light);">
                            <i data-lucide="log-out"></i> <?= __('logout') ?>
                        </a>
                    </div>
                </div>
            </div>
        </header>

        <!-- Flash Messages -->
        <?php if (hasFlash('success') || hasFlash('error')): ?>
        <div class="admin-alerts" style="padding: var(--space-4) var(--space-6) 0 var(--space-6);">
            <?php if (hasFlash('success')): ?>
            <div class="alert alert-success"><i data-lucide="check-circle-2"></i> <?= sanitize(getFlash('success')) ?></div>
            <?php endif; ?>
            <?php if (hasFlash('error')): ?>
            <div class="alert alert-danger"><i data-lucide="x-circle"></i> <?= sanitize(getFlash('error')) ?></div>
            <?php endif; ?>
        </div>
        <?php endif; ?>

        <!-- Page Content -->
        <div class="admin-content">
            <?= $adminContent ?>
        </div>
    </div>
</div>

<!-- Change Password Modal -->
<div class="pw-modal" id="changePasswordModal">
    <div class="pw-modal-backdrop" id="modalBackdrop"></div>
    <div class="pw-modal-content animate-fade-in-up">
        <div class="pw-modal-header">
            <h3 class="pw-modal-title"><i data-lucide="key" style="margin-right: var(--space-2);"></i> Change Password</h3>
            <button class="pw-modal-close" id="modalClose" aria-label="Close modal"><i data-lucide="x" style="width: 18px; height: 18px;"></i></button>
        </div>
        <div class="pw-modal-body">
            <form id="changePasswordForm" class="admin-form">
                <div class="form-group">
                    <label class="form-label">Current Password</label>
                    <div class="password-input-wrapper">
                        <input type="password" name="current_password" class="form-input" required>
                        <button type="button" class="password-toggle-btn" aria-label="Toggle password visibility">
                            <i data-lucide="eye"></i>
                        </button>
                    </div>
                </div>
                <div class="form-group">
                    <label class="form-label">New Password</label>
                    <div class="password-input-wrapper">
                        <input type="password" name="new_password" class="form-input" minlength="6" required>
                        <button type="button" class="password-toggle-btn" aria-label="Toggle password visibility">
                            <i data-lucide="eye"></i>
                        </button>
                    </div>
                </div>
                <div class="form-group">
                    <label class="form-label">Confirm New Password</label>
                    <div class="password-input-wrapper">
                        <input type="password" name="confirm_password" class="form-input" minlength="6" required>
                        <button type="button" class="password-toggle-btn" aria-label="Toggle password visibility">
                            <i data-lucide="eye"></i>
                        </button>
                    </div>
                </div>
                <div id="modalAlert" class="alert" style="display: none; margin-top: var(--space-3); padding: var(--space-2) var(--space-3); font-size: var(--text-xs);"></div>
                <div class="form-actions" style="margin-top: var(--space-4); display: flex; gap: var(--space-2); justify-content: flex-end;">
                    <button type="button" class="btn btn-ghost" id="modalCancelBtn">Cancel</button>
                    <button type="submit" class="btn btn-primary" id="modalSubmitBtn">Update Password</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script src="<?= asset('js/components.js') ?>"></script>
<script src="<?= asset('js/admin.js') ?>"></script>
<script>
    if (window.lucide) {
        lucide.createIcons();
    }
</script>
</body>
</html>
