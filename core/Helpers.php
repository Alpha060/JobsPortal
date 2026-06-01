<?php
/**
 * Helper Functions
 * 
 * Utility functions used throughout the application.
 */

/**
 * Generate a URL-safe slug from a string
 */
function slugify(string $text): string
{
    $text = preg_replace('~[^\pL\d]+~u', '-', $text);
    $text = transliterator_transliterate('Any-Latin; Latin-ASCII; Lower()', $text);
    $text = preg_replace('~[^a-z\d-]+~', '', $text);
    $text = preg_replace('~-+~', '-', $text);
    return trim($text, '-');
}

/**
 * Format a date for display
 */
function formatDate(?string $date, string $format = 'd M Y'): string
{
    if (empty($date)) return '—';
    try {
        return (new DateTime($date))->format($format);
    } catch (Exception $e) {
        return $date;
    }
}

/**
 * Format relative time (e.g., "2 hours ago")
 */
function timeAgo(string $datetime): string
{
    $time = strtotime($datetime);
    $diff = time() - $time;

    if ($diff < 60) return 'just now';
    if ($diff < 3600) return floor($diff / 60) . ' min ago';
    if ($diff < 86400) return floor($diff / 3600) . ' hours ago';
    if ($diff < 604800) return floor($diff / 86400) . ' days ago';
    return formatDate($datetime);
}

/**
 * Truncate text to a specified length
 */
function truncate(string $text, int $length = 150, string $suffix = '...'): string
{
    $text = strip_tags($text);
    if (mb_strlen($text) <= $length) return $text;
    return mb_substr($text, 0, $length) . $suffix;
}

/**
 * Sanitize user input
 */
function sanitize(string $input): string
{
    return htmlspecialchars(trim($input), ENT_QUOTES, 'UTF-8');
}

/**
 * Sanitize input preserving HTML (for rich text editor content)
 */
function sanitizeHtml(string $html): string
{
    // Allow safe HTML tags
    $allowed = '<h1><h2><h3><h4><h5><h6><p><br><strong><b><em><i><u><s><a><ul><ol><li><table><thead><tbody><tr><th><td><img><blockquote><pre><code><hr><div><span>';
    return strip_tags($html, $allowed);
}

/**
 * Generate CSRF hidden field
 */
function csrf_field(): string
{
    $token = Auth::generateCsrfToken();
    return '<input type="hidden" name="' . CSRF_TOKEN_NAME . '" value="' . sanitize($token) . '">';
}

/**
 * Verify CSRF token from POST request
 */
function verify_csrf(): bool
{
    $token = $_POST[CSRF_TOKEN_NAME] ?? null;
    return Auth::validateCsrfToken($token);
}

/**
 * Redirect to a URL
 */
function redirect(string $url, int $statusCode = 302): void
{
    header("Location: {$url}", true, $statusCode);
    exit;
}

/**
 * Get the previous form input value (for form repopulation on error)
 */
function old(string $key, string $default = ''): string
{
    return sanitize($_SESSION['_old_input'][$key] ?? $default);
}

/**
 * Store form input in session for repopulation
 */
function flashOldInput(array $data): void
{
    $_SESSION['_old_input'] = $data;
}

/**
 * Clear old input from session
 */
function clearOldInput(): void
{
    unset($_SESSION['_old_input']);
}

/**
 * Set a flash message
 */
function setFlash(string $type, string $message): void
{
    $_SESSION['_flash'][$type] = $message;
}

/**
 * Get and clear a flash message
 */
function getFlash(string $type): ?string
{
    $message = $_SESSION['_flash'][$type] ?? null;
    unset($_SESSION['_flash'][$type]);
    return $message;
}

/**
 * Check if a flash message exists
 */
function hasFlash(string $type): bool
{
    return isset($_SESSION['_flash'][$type]);
}

/**
 * Generate a versioned asset URL for cache busting
 */
function asset(string $path): string
{
    $filePath = ASSETS_PATH . '/' . ltrim($path, '/');
    $version = file_exists($filePath) ? filemtime($filePath) : APP_VERSION;
    return ASSETS_URL . '/' . ltrim($path, '/') . '?v=' . $version;
}

/**
 * Generate a URL relative to base
 */
function url(string $path = ''): string
{
    return BASE_URL . '/' . ltrim($path, '/');
}

/**
 * Get the current URL path
 */
function currentPath(): string
{
    $uri = $_SERVER['REQUEST_URI'] ?? '/';
    if (($pos = strpos($uri, '?')) !== false) {
        $uri = substr($uri, 0, $pos);
    }
    return $uri;
}

/**
 * Check if the current path matches a given pattern
 */
function isActivePath(string $path): bool
{
    $current = currentPath();
    return $current === $path || str_starts_with($current, $path . '/');
}

/**
 * Format a number with Indian numbering system
 */
function formatNumber(int $number): string
{
    if ($number < 1000) return (string) $number;
    $formatted = number_format($number);
    return $formatted;
}

/**
 * Generate a random string
 */
function randomString(int $length = 16): string
{
    return bin2hex(random_bytes($length / 2));
}

/**
 * Get file size in human-readable format
 */
function formatFileSize(int $bytes): string
{
    $units = ['B', 'KB', 'MB', 'GB'];
    $i = 0;
    while ($bytes >= 1024 && $i < count($units) - 1) {
        $bytes /= 1024;
        $i++;
    }
    return round($bytes, 2) . ' ' . $units[$i];
}

/**
 * Clean and validate a URL
 */
function cleanUrl(string $url): string
{
    $url = trim($url);
    if (empty($url)) return '';
    if (!preg_match('#^https?://#i', $url)) {
        $url = 'https://' . $url;
    }
    return filter_var($url, FILTER_VALIDATE_URL) ? $url : '';
}

/**
 * Update the last change timestamp file
 */
function triggerRealtimeUpdate(): void
{
    $filePath = ROOT_PATH . '/uploads/last_update.txt';
    $dir = dirname($filePath);
    if (!is_dir($dir)) {
        mkdir($dir, 0755, true);
    }
    file_put_contents($filePath, time());
}

/**
 * Map a category slug or custom icon to a valid Lucide icon name
 */
function getCategoryIcon(?string $slug, ?string $customIcon = null): string
{
    if ($customIcon && preg_match('/^[a-z0-9-]+$/', $customIcon)) {
        return $customIcon;
    }

    $icons = [
        'latest-jobs' => 'briefcase',
        'results'     => 'award',
        'admit-card'  => 'ticket',
        'answer-key'  => 'key-round',
        'syllabus'    => 'book-open',
        'admission'   => 'graduation-cap'
    ];
    return $icons[$slug] ?? 'briefcase';
}
