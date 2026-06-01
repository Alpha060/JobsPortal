<?php
/**
 * Route Definitions
 * 
 * Separated from index.php for cleaner architecture.
 * $router is passed in from index.php.
 */

// ── Public Routes ──
$router->get('/', [HomeController::class, 'index']);
$router->get('/search', [SearchController::class, 'index']);
$router->get('/api/updates/check', [HomeController::class, 'checkUpdates']);

// ── Admin Routes ──
$router->get('/admin', function() { redirect(url('admin/dashboard')); });
$router->get('/admin/login', [AdminAuthController::class, 'loginPage']);
$router->post('/admin/login', [AdminAuthController::class, 'login']);
$router->get('/admin/logout', [AdminAuthController::class, 'logout']);
$router->post('/admin/change-password', [AdminAuthController::class, 'changePassword']);
$router->get('/admin/dashboard', [AdminDashboardController::class, 'index']);

// Admin Posts
$router->get('/admin/posts', [AdminPostController::class, 'index']);
$router->get('/admin/posts/create', [AdminPostController::class, 'create']);
$router->post('/admin/posts/store', [AdminPostController::class, 'store']);
$router->get('/admin/posts/edit/:id', [AdminPostController::class, 'edit']);
$router->post('/admin/posts/update/:id', [AdminPostController::class, 'update']);
$router->get('/admin/posts/delete/:id', [AdminPostController::class, 'delete']);
$router->get('/admin/posts/toggle/:id', [AdminPostController::class, 'toggleActive']);
$router->get('/admin/posts/toggle-featured/:id', [AdminPostController::class, 'toggleFeatured']);
$router->get('/admin/posts/toggle-trending/:id', [AdminPostController::class, 'toggleTrending']);

// Admin Categories
$router->get('/admin/categories', [AdminCategoryController::class, 'index']);
$router->post('/admin/categories/store', [AdminCategoryController::class, 'store']);
$router->post('/admin/categories/update/:id', [AdminCategoryController::class, 'update']);
$router->get('/admin/categories/delete/:id', [AdminCategoryController::class, 'delete']);
$router->get('/admin/categories/toggle/:id', [AdminCategoryController::class, 'toggleActive']);

// Admin Media
$router->get('/admin/media', [AdminMediaController::class, 'index']);
$router->post('/admin/media/upload', [AdminMediaController::class, 'upload']);
$router->get('/admin/media/delete/:id', [AdminMediaController::class, 'delete']);

// Admin Settings
$router->any('/admin/settings', [AdminSettingController::class, 'index']);

// ── Category & Post Detail Routes (must be last — catch-all) ──
$router->get('/:categorySlug', [PostController::class, 'listing']);
$router->get('/:categorySlug/:postSlug', [PostController::class, 'detail']);
