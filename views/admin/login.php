<?php
/**
 * Admin Login Page
 */
$lang = Lang::getInstance();
$settings = new Setting();
$siteName = $settings->getLocalized('site_name');
?>
<!DOCTYPE html>
<html lang="<?= $lang->current() ?>" class="light-mode">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= __('login') ?> — <?= sanitize($siteName) ?> Admin</title>
    <meta name="robots" content="noindex, nofollow">
    <link rel="stylesheet" href="<?= asset('css/variables.css') ?>">
    <link rel="stylesheet" href="<?= asset('css/base.css') ?>">
    <link rel="stylesheet" href="<?= asset('css/components.css') ?>">
    <link rel="stylesheet" href="<?= asset('css/admin.css') ?>">
    <script src="https://unpkg.com/lucide@latest"></script>
</head>
<body>
<div class="login-wrapper">
    <!-- Animated Background Bubbles -->
    <div class="login-bg-bubbles">
        <div class="bubble b-1"></div>
        <div class="bubble b-2"></div>
        <div class="bubble b-3"></div>
        <div class="bubble b-4"></div>
        <div class="bubble b-5"></div>
        <div class="bubble b-6"></div>
        <div class="bubble b-7"></div>
        <div class="bubble b-8"></div>
        <div class="bubble b-9"></div>
        <div class="bubble b-10"></div>
    </div>

    <div class="login-hero">
        <!-- Decorative Glass Shapes -->
        <div class="hero-glass-shape glass-1"></div>
        <div class="hero-glass-shape glass-2"></div>
        <div class="hero-glass-shape glass-3"></div>

        <div class="login-hero-content animate-fade-in-up">
            <div class="hero-badge">Admin Portal</div>
            <h1 class="login-hero-title">Welcome to <?= sanitize($siteName) ?></h1>
            <p class="login-hero-subtitle">The ultimate control panel to manage jobs, results, admit cards, and everything else with absolute ease and security.</p>
            
            <div class="login-hero-features">
                <div class="hero-feature">
                    <div class="hero-feature-icon"><i data-lucide="zap"></i></div>
                    <span>Lightning Fast Management</span>
                </div>
                <div class="hero-feature">
                    <div class="hero-feature-icon"><i data-lucide="shield-check"></i></div>
                    <span>Secure & Encrypted Platform</span>
                </div>
                <div class="hero-feature">
                    <div class="hero-feature-icon"><i data-lucide="bar-chart-3"></i></div>
                    <span>Real-time Analytics & Data</span>
                </div>
            </div>
        </div>
    </div>
    
    <div class="login-form-container">
        <div class="login-card animate-fade-in-up" style="animation-delay: 0.1s;">
        <div class="login-header">
            <div class="login-logo">
                <div class="login-logo-icon"><i data-lucide="lock" style="width:24px;height:24px;"></i></div>
            </div>
            <h1 class="login-title"><?= __('admin_panel') ?></h1>
            <p class="login-subtitle"><?= sanitize($siteName) ?></p>
        </div>

        <?php if (hasFlash('error')): ?>
        <div class="alert alert-danger" style="margin-bottom: var(--space-5);"><i data-lucide="x-circle"></i> <?= sanitize(getFlash('error')) ?></div>
        <?php endif; ?>
        <?php if (hasFlash('success')): ?>
        <div class="alert alert-success" style="margin-bottom: var(--space-5);"><i data-lucide="check-circle-2"></i> <?= sanitize(getFlash('success')) ?></div>
        <?php endif; ?>

        <form method="POST" action="<?= url('admin/login') ?>">
            <?= csrf_field() ?>

            <div class="form-group">
                <label class="form-label" for="username"><?= __('username') ?></label>
                <input type="text" id="username" name="username" class="form-input"
                       placeholder="Enter your username" required autocomplete="username" autofocus>
            </div>

            <div class="form-group">
                <label class="form-label" for="password"><?= __('password') ?></label>
                <div class="password-input-wrapper">
                    <input type="password" id="password" name="password" class="form-input"
                           placeholder="Enter your password" required autocomplete="current-password">
                    <button type="button" class="password-toggle-btn">
                        <i data-lucide="eye"></i>
                    </button>
                </div>
            </div>

            <button type="submit" class="btn btn-primary btn-lg" style="width: 100%; margin-top: var(--space-2);">
                <?= __('login_submit') ?>
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="9 18 15 12 9 6"/></svg>
            </button>
        </form>

        <p style="text-align: center; margin-top: var(--space-6); font-size: var(--text-xs); color: var(--text-tertiary);">
            <a href="<?= url('/') ?>" style="color: var(--text-tertiary);">← Back to <?= sanitize($siteName) ?></a>
        </p>
    </div>
    </div>
</div>
<script>
    if (window.lucide) {
        lucide.createIcons();
    }

    document.addEventListener('DOMContentLoaded', function() {
        const toggleBtn = document.querySelector('.password-toggle-btn');
        const passwordInput = document.querySelector('#password');

        if (toggleBtn && passwordInput) {
            toggleBtn.addEventListener('click', function(e) {
                e.preventDefault(); 
                
                const type = passwordInput.getAttribute('type') === 'password' ? 'text' : 'password';
                passwordInput.setAttribute('type', type);
                
                // Replace the content completely so Lucide renders the new SVG
                toggleBtn.innerHTML = type === 'text' 
                    ? '<i data-lucide="eye-off"></i>' 
                    : '<i data-lucide="eye"></i>';
                
                if (window.lucide) {
                    lucide.createIcons();
                }
            });
        }
    });
</script>
</body>
</html>
