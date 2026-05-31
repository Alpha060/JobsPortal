<?php
/**
 * Application Configuration
 * 
 * Core constants and paths for the application.
 */

// Base paths
define('ROOT_PATH', dirname(__DIR__));
define('CONFIG_PATH', ROOT_PATH . '/config');
define('CORE_PATH', ROOT_PATH . '/core');
define('MODELS_PATH', ROOT_PATH . '/models');
define('CONTROLLERS_PATH', ROOT_PATH . '/controllers');
define('VIEWS_PATH', ROOT_PATH . '/views');
define('ASSETS_PATH', ROOT_PATH . '/assets');
define('UPLOADS_PATH', ROOT_PATH . '/uploads');
define('LANG_PATH', ROOT_PATH . '/lang');

// Base URL — auto-detect or set manually
$protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? 'https' : 'http';
$host = $_SERVER['HTTP_HOST'] ?? 'localhost:8000';
$basePath = rtrim(dirname($_SERVER['SCRIPT_NAME']), '/\\');

define('BASE_URL', getenv('APP_URL') ?: ($protocol . '://' . $host . $basePath));
define('ASSETS_URL', BASE_URL . '/assets');
define('UPLOADS_URL', BASE_URL . '/uploads');

// Application settings
define('APP_NAME', 'JobsPortal');
define('APP_VERSION', '1.0.0');
define('DEFAULT_LANG', 'en');
define('SUPPORTED_LANGS', ['en', 'hi']);
define('POSTS_PER_PAGE', 20);

// Upload settings
define('MAX_UPLOAD_SIZE', 5 * 1024 * 1024); // 5MB
define('ALLOWED_IMAGE_TYPES', ['image/jpeg', 'image/png', 'image/gif', 'image/webp']);
define('ALLOWED_FILE_TYPES', ['application/pdf', 'image/jpeg', 'image/png', 'image/gif', 'image/webp']);

// Security
define('SESSION_LIFETIME', 7200); // 2 hours
define('CSRF_TOKEN_NAME', '_csrf_token');
define('MAX_LOGIN_ATTEMPTS', 5);
define('LOGIN_LOCKOUT_TIME', 900); // 15 minutes

// Admin path prefix
define('ADMIN_PREFIX', '/admin');

// Debug mode (disable in production!)
define('DEBUG_MODE', APP_ENV === 'development');

// Error reporting
if (DEBUG_MODE) {
    ini_set('display_errors', 1);
    ini_set('display_startup_errors', 1);
    error_reporting(E_ALL);
} else {
    ini_set('display_errors', 0);
    ini_set('display_startup_errors', 0);
    error_reporting(E_ALL & ~E_NOTICE & ~E_DEPRECATED);
}

// Timezone
date_default_timezone_set('Asia/Kolkata');

// Start session if not already started
if (session_status() === PHP_SESSION_NONE) {
    ini_set('session.cookie_httponly', 1);
    ini_set('session.use_strict_mode', 1);
    if (!DEBUG_MODE) {
        ini_set('session.cookie_secure', 1);
    }
    session_start();
}
