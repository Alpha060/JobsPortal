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

    public function streamUpdates(): void
    {
        header('Content-Type: text/event-stream');
        header('Cache-Control: no-cache');
        header('Connection: keep-alive');
        header('X-Accel-Buffering: no');

        $filePath = ROOT_PATH . '/uploads/last_update.txt';
        $lastChecked = isset($_GET['last_time']) ? (int)$_GET['last_time'] : time();

        // Prevent execution timeout
        set_time_limit(0);

        // Turn off output buffering
        while (ob_get_level() > 0) {
            ob_end_flush();
        }
        ob_implicit_flush(true);

        while (true) {
            if (connection_aborted()) {
                break;
            }

            $currentModified = file_exists($filePath) ? (int)file_get_contents($filePath) : 0;
            if ($currentModified > $lastChecked) {
                echo "event: update\n";
                echo "data: " . json_encode(['timestamp' => $currentModified]) . "\n\n";
                $lastChecked = $currentModified;
            } else {
                // Keep-alive heartbeat
                echo ": heartbeat\n\n";
            }

            if (ob_get_level() > 0) {
                ob_flush();
            }
            flush();
            sleep(2);
        }
    }
}
