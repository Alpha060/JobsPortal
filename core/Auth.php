<?php
/**
 * Admin Authentication Handler
 * 
 * Session-based auth with bcrypt hashing, CSRF protection,
 * and login attempt rate limiting.
 */
class Auth
{
    private Database $db;

    public function __construct()
    {
        $this->db = Database::getInstance();
    }

    /**
     * Attempt to log in an admin user
     */
    public function login(string $username, string $password): bool
    {
        // Check rate limiting
        if ($this->isLockedOut()) {
            return false;
        }

        $admin = $this->db->fetch(
            "SELECT * FROM `admins` WHERE `username` = ? OR `email` = ? LIMIT 1",
            [$username, $username]
        );

        if ($admin && password_verify($password, $admin['password_hash'])) {
            // Successful login
            $this->clearLoginAttempts();
            $this->createSession($admin);

            // Update last login time
            $this->db->update('admins', [
                'last_login' => date('Y-m-d H:i:s')
            ], 'id = ?', [$admin['id']]);

            return true;
        }

        // Failed login — record attempt
        $this->recordLoginAttempt();
        return false;
    }

    /**
     * Log out the current admin
     */
    public function logout(): void
    {
        $_SESSION = [];

        if (ini_get("session.use_cookies")) {
            $params = session_get_cookie_params();
            setcookie(
                session_name(),
                '',
                time() - 42000,
                $params["path"],
                $params["domain"],
                $params["secure"],
                $params["httponly"]
            );
        }

        session_destroy();
    }

    /**
     * Check if an admin is currently logged in
     */
    public function isLoggedIn(): bool
    {
        return isset($_SESSION['admin_id']) && isset($_SESSION['admin_username']);
    }

    /**
     * Get the current admin's data
     */
    public function getAdmin(): ?array
    {
        if (!$this->isLoggedIn()) {
            return null;
        }

        return [
            'id'        => $_SESSION['admin_id'],
            'username'  => $_SESSION['admin_username'],
            'email'     => $_SESSION['admin_email'] ?? '',
            'full_name' => $_SESSION['admin_name'] ?? '',
        ];
    }

    /**
     * Require admin authentication — redirect to login if not authenticated
     */
    public function requireAuth(): void
    {
        if (!$this->isLoggedIn()) {
            redirect(BASE_URL . '/admin/login');
        }
    }

    /**
     * Generate a CSRF token
     */
    public static function generateCsrfToken(): string
    {
        if (empty($_SESSION[CSRF_TOKEN_NAME])) {
            $_SESSION[CSRF_TOKEN_NAME] = bin2hex(random_bytes(32));
        }
        return $_SESSION[CSRF_TOKEN_NAME];
    }

    /**
     * Validate a CSRF token
     */
    public static function validateCsrfToken(?string $token): bool
    {
        if (empty($token) || empty($_SESSION[CSRF_TOKEN_NAME])) {
            return false;
        }
        $valid = hash_equals($_SESSION[CSRF_TOKEN_NAME], $token);
        // Regenerate after validation
        unset($_SESSION[CSRF_TOKEN_NAME]);
        return $valid;
    }

    /**
     * Create admin session
     */
    private function createSession(array $admin): void
    {
        session_regenerate_id(true);
        $_SESSION['admin_id']       = $admin['id'];
        $_SESSION['admin_username'] = $admin['username'];
        $_SESSION['admin_email']    = $admin['email'];
        $_SESSION['admin_name']     = $admin['full_name'];
        $_SESSION['login_time']     = time();
    }

    /**
     * Check if the IP is locked out due to too many login attempts
     */
    private function isLockedOut(): bool
    {
        $ip = $_SERVER['REMOTE_ADDR'] ?? '0.0.0.0';
        $cutoff = date('Y-m-d H:i:s', time() - LOGIN_LOCKOUT_TIME);

        $attempts = $this->db->fetchColumn(
            "SELECT COUNT(*) FROM `login_attempts` WHERE `ip_address` = ? AND `attempted_at` > ?",
            [$ip, $cutoff]
        );

        return $attempts >= MAX_LOGIN_ATTEMPTS;
    }

    /**
     * Record a failed login attempt
     */
    private function recordLoginAttempt(): void
    {
        $ip = $_SERVER['REMOTE_ADDR'] ?? '0.0.0.0';
        $this->db->insert('login_attempts', [
            'ip_address'   => $ip,
            'attempted_at' => date('Y-m-d H:i:s'),
        ]);
    }

    /**
     * Clear login attempts for the current IP
     */
    private function clearLoginAttempts(): void
    {
        $ip = $_SERVER['REMOTE_ADDR'] ?? '0.0.0.0';
        $this->db->delete('login_attempts', 'ip_address = ?', [$ip]);
    }

    /**
     * Hash a password using bcrypt
     */
    public static function hashPassword(string $password): string
    {
        return password_hash($password, PASSWORD_BCRYPT, ['cost' => 12]);
    }
}
