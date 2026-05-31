<?php
/**
 * Admin Dashboard Controller
 */
class AdminDashboardController
{
    public function index(): void
    {
        $auth = new Auth();
        $auth->requireAuth();

        $postModel = new Post();
        $stats = $postModel->getStats();
        $latestPosts = $postModel->getLatest(10);

        $adminPageTitle = __('dashboard');
        include VIEWS_PATH . '/admin/dashboard.php';
    }
}
