<?php
/**
 * Admin Media Controller
 */
class AdminMediaController
{
    private Media $mediaModel;
    private Auth $auth;

    public function __construct()
    {
        $this->auth = new Auth();
        $this->auth->requireAuth();
        $this->mediaModel = new Media();
    }

    /** List all media files */
    public function index(): void
    {
        $page = max(1, (int)($_GET['page'] ?? 1));
        $result = $this->mediaModel->getAll($page, 24);

        $adminPageTitle = __('media_library');
        include VIEWS_PATH . '/admin/media.php';
    }

    /** Upload a media file */
    public function upload(): void
    {
        if (!verify_csrf()) {
            setFlash('error', 'Invalid security token.');
            redirect(url('admin/media'));
        }

        if (empty($_FILES['media_file']['name'])) {
            setFlash('error', 'No file selected.');
            redirect(url('admin/media'));
        }

        $uploaded = $this->mediaModel->upload($_FILES['media_file']);

        if ($uploaded) {
            setFlash('success', 'File uploaded successfully!');
        } else {
            setFlash('error', 'Upload failed. Check file type and size (max 5MB).');
        }

        redirect(url('admin/media'));
    }

    /** Delete a media file */
    public function delete(string $id): void
    {
        if ($this->mediaModel->delete((int)$id)) {
            setFlash('success', 'File deleted successfully!');
        } else {
            setFlash('error', 'File not found.');
        }
        redirect(url('admin/media'));
    }
}
