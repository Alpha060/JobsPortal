<?php
/**
 * Admin Auth Controller
 */
class AdminAuthController
{
    public function loginPage(): void
    {
        $auth = new Auth();
        if ($auth->isLoggedIn()) {
            redirect(url('admin/dashboard'));
        }
        include VIEWS_PATH . '/admin/login.php';
    }

    public function login(): void
    {
        if (!verify_csrf()) {
            setFlash('error', 'Invalid security token. Please try again.');
            redirect(url('admin/login'));
        }

        $username = trim($_POST['username'] ?? '');
        $password = $_POST['password'] ?? '';

        if (empty($username) || empty($password)) {
            setFlash('error', __('login_error'));
            redirect(url('admin/login'));
        }

        $auth = new Auth();
        if ($auth->login($username, $password)) {
            setFlash('success', __('welcome_back') . ', ' . $username . '!');
            redirect(url('admin/dashboard'));
        } else {
            setFlash('error', __('login_error'));
            redirect(url('admin/login'));
        }
    }

    public function logout(): void
    {
        $auth = new Auth();
        $auth->logout();
        redirect(url('admin/login'));
    }

    public function changePassword(): void
    {
        header('Content-Type: application/json');
        
        $auth = new Auth();
        if (!$auth->isLoggedIn()) {
            http_response_code(401);
            echo json_encode(['success' => false, 'message' => 'Unauthorized.']);
            return;
        }

        if (!Auth::validateCsrfToken($_POST[CSRF_TOKEN_NAME] ?? null)) {
            http_response_code(403);
            echo json_encode(['success' => false, 'message' => 'Invalid security token. Please refresh and try again.']);
            return;
        }

        $currentPassword = $_POST['current_password'] ?? '';
        $newPassword = $_POST['new_password'] ?? '';
        $confirmPassword = $_POST['confirm_password'] ?? '';

        if (empty($currentPassword) || empty($newPassword) || empty($confirmPassword)) {
            echo json_encode(['success' => false, 'message' => 'All fields are required.']);
            return;
        }

        if ($newPassword !== $confirmPassword) {
            echo json_encode(['success' => false, 'message' => 'New passwords do not match.']);
            return;
        }

        if (strlen($newPassword) < 6) {
            echo json_encode(['success' => false, 'message' => 'New password must be at least 6 characters.']);
            return;
        }

        $db = Database::getInstance();
        $admin = $auth->getAdmin();
        
        $adminRow = $db->fetch("SELECT * FROM `admins` WHERE `id` = ? LIMIT 1", [$admin['id']]);
        if (!$adminRow || !password_verify($currentPassword, $adminRow['password_hash'])) {
            echo json_encode(['success' => false, 'message' => 'Current password is incorrect.']);
            return;
        }

        $newHash = password_hash($newPassword, PASSWORD_BCRYPT, ['cost' => 12]);
        $db->update('admins', ['password_hash' => $newHash], 'id = ?', [$admin['id']]);

        echo json_encode(['success' => true, 'message' => 'Password updated successfully!']);
    }
}
