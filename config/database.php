<?php
/**
 * Database Configuration
 * 
 * Supports environment-based config for dev (localhost) and production (Hostinger).
 * Uses PDO with prepared statements and UTF-8mb4 charset.
 */

// Load environment variables from .env if it exists
$envFile = dirname(__DIR__) . '/.env';
if (file_exists($envFile)) {
    $lines = file($envFile, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    foreach ($lines as $line) {
        $trimmed = trim($line);
        if (empty($trimmed) || strpos($trimmed, '#') === 0) {
            continue;
        }
        if (strpos($trimmed, '=') !== false) {
            list($name, $value) = explode('=', $trimmed, 2);
            $name = trim($name);
            $value = trim($value);
            
            // Remove optional surrounding quotes
            if (preg_match('/^([\'"])(.*)\1$/', $value, $matches)) {
                $value = $matches[2];
            }
            
            // Always populate local superglobals (crucial if putenv is blocked)
            $_ENV[$name] = $value;
            $_SERVER[$name] = $value;
            @putenv("{$name}={$value}");
        }
    }
}

// Helper function to get environment variables securely (handles putenv restrictions)
function get_env_config(string $key, $default = '') {
    if (isset($_ENV[$key]) && $_ENV[$key] !== '') {
        return $_ENV[$key];
    }
    if (isset($_SERVER[$key]) && $_SERVER[$key] !== '') {
        return $_SERVER[$key];
    }
    $val = getenv($key);
    return $val !== false ? $val : $default;
}

// Detect environment
define('APP_ENV', get_env_config('APP_ENV', 'development'));

// Select active config based on environment variables
define('DB_HOST',     get_env_config('DB_HOST', 'localhost'));
define('DB_PORT',     (int)get_env_config('DB_PORT', 3306));
define('DB_NAME',     get_env_config('DB_NAME', 'jobsportal'));
define('DB_USERNAME', get_env_config('DB_USER', 'root'));
define('DB_PASSWORD', get_env_config('DB_PASS', ''));
define('DB_CHARSET',  get_env_config('DB_CHARSET', 'utf8mb4'));

define('DB_DSN', sprintf(
    'mysql:host=%s;port=%d;dbname=%s;charset=%s',
    DB_HOST,
    DB_PORT,
    DB_NAME,
    DB_CHARSET
));

define('DB_OPTIONS', [
    PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    PDO::ATTR_EMULATE_PREPARES   => false,
    (defined('Pdo\Mysql::ATTR_INIT_COMMAND') ? \Pdo\Mysql::ATTR_INIT_COMMAND : 1002) => "SET NAMES utf8mb4 COLLATE utf8mb4_unicode_ci",
]);
