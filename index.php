<?php
/**
 * JobsPortal — Main Entry Point
 * 
 * All requests are routed through this file via .htaccess rewrite rules.
 * Bootstraps the application: loads config, core classes, models, controllers,
 * then dispatches the request to the appropriate controller action.
 */

// ── Load Configuration ──
require_once __DIR__ . '/config/database.php';
require_once __DIR__ . '/config/app.php';

// ── Load Core Classes ──
require_once CORE_PATH . '/Database.php';
require_once CORE_PATH . '/Auth.php';
require_once CORE_PATH . '/Helpers.php';
require_once CORE_PATH . '/Lang.php';
require_once CORE_PATH . '/Pagination.php';
require_once CORE_PATH . '/Router.php';

// ── Load Models ──
require_once MODELS_PATH . '/Post.php';
require_once MODELS_PATH . '/Category.php';
require_once MODELS_PATH . '/Setting.php';
require_once MODELS_PATH . '/Media.php';

// ── Load Controllers ──
require_once CONTROLLERS_PATH . '/HomeController.php';
require_once CONTROLLERS_PATH . '/PostController.php';
require_once CONTROLLERS_PATH . '/SearchController.php';
require_once CONTROLLERS_PATH . '/admin/AuthController.php';
require_once CONTROLLERS_PATH . '/admin/DashboardController.php';
require_once CONTROLLERS_PATH . '/admin/PostController.php';
require_once CONTROLLERS_PATH . '/admin/CategoryController.php';
require_once CONTROLLERS_PATH . '/admin/MediaController.php';
require_once CONTROLLERS_PATH . '/admin/SettingController.php';

// ── Initialize Router ──
$router = new Router();

// ── Load Routes ──
require_once __DIR__ . '/config/routes.php';

// ── Dispatch ──
$router->dispatch();
