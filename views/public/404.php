<?php
/**
 * 404 Not Found Page
 */
$pageTitle = __('page_not_found');
ob_start();
?>

<div class="page-404 animate-fade-in-up">
    <div class="page-404-code">404</div>
    <h1 style="font-size: var(--text-2xl); margin-bottom: var(--space-3);"><?= __('page_not_found') ?></h1>
    <p style="color: var(--text-tertiary); margin-bottom: var(--space-6); max-width: 400px;"><?= __('page_not_found_desc') ?></p>
    <a href="<?= url('/') ?>" class="btn btn-primary btn-lg"><?= __('go_home') ?></a>
</div>

<?php
$content = ob_get_clean();
include VIEWS_PATH . '/layouts/public.php';
?>
