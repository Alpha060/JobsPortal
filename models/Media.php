<?php
/**
 * Media Model
 * 
 * Handles file upload, storage, and management.
 */
class Media
{
    private Database $db;

    public function __construct()
    {
        $this->db = Database::getInstance();
    }

    /**
     * Upload a file
     */
    public function upload(array $file): ?array
    {
        // Validate
        if ($file['error'] !== UPLOAD_ERR_OK) {
            return null;
        }

        if ($file['size'] > MAX_UPLOAD_SIZE) {
            return null;
        }

        if (!in_array($file['type'], ALLOWED_FILE_TYPES)) {
            return null;
        }

        // Ensure upload directory exists
        if (!is_dir(UPLOADS_PATH)) {
            mkdir(UPLOADS_PATH, 0755, true);
        }

        // Create year/month subdirectory
        $subdir = date('Y/m');
        $uploadDir = UPLOADS_PATH . '/' . $subdir;
        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0755, true);
        }

        // Generate unique filename
        $ext = pathinfo($file['name'], PATHINFO_EXTENSION);
        $filename = $subdir . '/' . randomString(16) . '.' . strtolower($ext);
        $fullPath = UPLOADS_PATH . '/' . $filename;

        // Move uploaded file
        if (!move_uploaded_file($file['tmp_name'], $fullPath)) {
            return null;
        }

        // Store in database
        $id = $this->db->insert('media', [
            'filename'      => $filename,
            'original_name' => $file['name'],
            'mime_type'     => $file['type'],
            'size'          => $file['size'],
        ]);

        return [
            'id'       => $id,
            'filename' => $filename,
            'url'      => UPLOADS_URL . '/' . $filename,
            'original' => $file['name'],
        ];
    }

    /**
     * Get all media files (paginated)
     */
    public function getAll(int $page = 1, int $perPage = 24): array
    {
        $total = $this->db->count('media');
        $pagination = new Pagination($total, $page, $perPage);

        $files = $this->db->fetchAll(
            "SELECT * FROM `media` ORDER BY `uploaded_at` DESC LIMIT ? OFFSET ?",
            [$pagination->limit(), $pagination->offset()]
        );

        // Add URL to each file
        foreach ($files as &$file) {
            $file['url'] = UPLOADS_URL . '/' . $file['filename'];
        }

        return [
            'files'      => $files,
            'pagination' => $pagination,
            'total'      => $total,
        ];
    }

    /**
     * Get a single media file by ID
     */
    public function getById(int $id): ?array
    {
        $file = $this->db->fetch("SELECT * FROM `media` WHERE `id` = ?", [$id]);
        if ($file) {
            $file['url'] = UPLOADS_URL . '/' . $file['filename'];
        }
        return $file;
    }

    /**
     * Delete a media file
     */
    public function delete(int $id): bool
    {
        $file = $this->getById($id);
        if (!$file) return false;

        // Delete physical file
        $filePath = UPLOADS_PATH . '/' . $file['filename'];
        if (file_exists($filePath)) {
            unlink($filePath);
        }

        // Delete from database
        $this->db->delete('media', 'id = ?', [$id]);
        return true;
    }
}
