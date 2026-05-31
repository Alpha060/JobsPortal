<?php
/**
 * Marquee / News Ticker Partial
 */
$settings = new Setting();
$lang = Lang::getInstance();
$postModel = new Post();

$trendingPosts = $postModel->getTrending(10);
$tickerItems = [];

foreach ($trendingPosts as $tPost) {
    $title = sanitize($lang->field($tPost, 'title'));
    $url = url($tPost['category_slug'] . '/' . $tPost['slug']);
    $tickerItems[] = '<a href="' . $url . '" class="ticker-link" style="color: inherit; text-decoration: none; font-weight: var(--weight-semibold); transition: color var(--transition-fast);">' . $title . '</a>';
}

if (!empty($tickerItems)) {
    // Generate text separated by bullet dots with extra spaces
    $tickerText = implode(' &nbsp;&nbsp;&bull;&nbsp;&nbsp; ', $tickerItems);
} else {
    $tickerText = sanitize($settings->getLocalized('ticker_text') ?? '');
}
?>
<?php if (!empty($tickerText)): ?>
<div class="ticker" id="newsTicker">
    <div class="ticker-label"><i data-lucide="zap" style="width: 14px; height: 14px; margin-right: 4px; fill: currentColor;"></i> <?= __('trending_now') ?></div>
    <div class="ticker-content">
        <div class="ticker-scroll">
            <span class="ticker-text"><?= $tickerText ?></span>
            <span class="ticker-text"><?= $tickerText ?></span>
        </div>
    </div>
</div>
<?php endif; ?>
