<?php
/**
 * Home Controller
 */
class HomeController
{
    public function index(): void
    {
        include VIEWS_PATH . '/public/home.php';
    }

    /**
     * Lightweight update check — returns JSON instantly (no long-polling)
     */
    public function checkUpdates(): void
    {
        header('Content-Type: application/json');
        header('Cache-Control: no-cache');

        $filePath = ROOT_PATH . '/uploads/last_update.txt';
        $lastChecked = isset($_GET['last_time']) ? (int)$_GET['last_time'] : time();
        $currentModified = file_exists($filePath) ? (int)file_get_contents($filePath) : 0;

        echo json_encode([
            'hasUpdate' => $currentModified > $lastChecked,
            'timestamp' => $currentModified,
        ]);
    }
}
