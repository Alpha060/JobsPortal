<?php
/**
 * Seed Demo Media Items
 */
require_once dirname(dirname(__DIR__)) . '/config/database.php';
require_once dirname(dirname(__DIR__)) . '/config/app.php';
require_once dirname(dirname(__DIR__)) . '/core/Database.php';

$db = Database::getInstance();

$subDir = date('Y/m');
$uploadPath = ROOT_PATH . '/uploads/' . $subDir;

if (!is_dir($uploadPath)) {
    mkdir($uploadPath, 0755, true);
}

// Generate simple colored banners
$images = [
    'upsc_banner.png' => [76, 201, 240, 'UPSC Exam News'],
    'ssc_notification.png' => [72, 149, 239, 'SSC Recruitment'],
    'admit_card_released.png' => [78, 12, 150, 'Admit Cards Out']
];

foreach ($images as $name => $info) {
    $filename = $subDir . '/' . $name;
    $fullPath = ROOT_PATH . '/uploads/' . $filename;
    
    // Create image using GD
    if (function_exists('imagecreatetruecolor')) {
        $img = imagecreatetruecolor(600, 300);
        $bg = imagecolorallocate($img, $info[0], $info[1], $info[2]);
        imagefill($img, 0, 0, $bg);
        imagepng($img, $fullPath);
        if (PHP_VERSION_ID < 80500) {
            imagedestroy($img);
        }
    } else {
        // Fallback dummy file if GD is not present
        file_put_contents($fullPath, "PNG dummy file content");
    }
    
    // Check if record already exists
    $exists = $db->fetch("SELECT id FROM media WHERE filename = ?", [$filename]);
    if (!$exists) {
        $db->insert('media', [
            'filename'      => $filename,
            'original_name' => $name,
            'mime_type'     => 'image/png',
            'size'          => file_exists($fullPath) ? filesize($fullPath) : 1024,
        ]);
        echo "Inserted database record for: " . $name . "\n";
    } else {
        echo "Record already exists for: " . $name . "\n";
    }
}

echo "Demo media seeding completed.\n";
