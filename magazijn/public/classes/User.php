<?php
/**
 * User Class for ResQ
 * Handles user authentication, registration, and role management
 */
class User {
    private $db;

    public function __construct($db) {
        $this->db = $db;
    }

    /**
     * Register a new user
     */
    public function register($username, $email, $password, $role = 'employee') {
        // Validate role
        if (!in_array($role, ['employee', 'admin'])) {
            $role = 'employee';
        }

        $passwordHash = password_hash($password, PASSWORD_DEFAULT);
        
        try {
            $query = "INSERT INTO users (username, email, password_hash, role) VALUES (?, ?, ?, ?)";
            $stmt = $this->db->prepare($query);
            return $stmt->execute([$username, $email, $passwordHash, $role]);
        } catch (PDOException $e) {
            error_log("Registration error: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Login user and set session
     */
    public function login($email, $password) {
        $query = "SELECT * FROM users WHERE email = ?";
        $stmt = $this->db->prepare($query);
        $stmt->execute([$email]);
        $userData = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($userData && password_verify($password, $userData['password_hash'])) {
            if (!isset($_SESSION)) {
                session_start();
            }
            $_SESSION['user_id'] = $userData['id'];
            $_SESSION['username'] = $userData['username'];
            $_SESSION['email'] = $userData['email'];
            $_SESSION['role'] = $userData['role'];
            $_SESSION['last_activity'] = time();
            
            // Update last activity in database
            $updateQuery = "UPDATE users SET last_activity = NOW() WHERE id = ?";
            $updateStmt = $this->db->prepare($updateQuery);
            $updateStmt->execute([$userData['id']]);
            
            return true;
        }
        return false;
    }

    /**
     * Logout user
     */
    public static function logout() {
        if (!isset($_SESSION)) {
            session_start();
        }
        session_unset();
        session_destroy();
    }

    /**
     * Check if user is logged in
     */
    public static function isLoggedIn() {
        if (!isset($_SESSION)) {
            session_start();
        }
        return isset($_SESSION['user_id']);
    }

    /**
     * Check if user is admin
     */
    public static function isAdmin() {
        if (!isset($_SESSION)) {
            session_start();
        }
        return isset($_SESSION['role']) && $_SESSION['role'] === 'admin';
    }

    /**
     * Require login, redirect if not logged in
     */
    public static function requireLogin() {
        if (!self::isLoggedIn()) {
            header("Location: login.php");
            exit();
        }
    }

    /**
     * Require admin role, redirect if not admin
     */
    public static function requireAdmin() {
        self::requireLogin();
        if (!self::isAdmin()) {
            header("Location: index.php");
            exit();
        }
    }

    /**
     * Check session timeout (30 minutes inactivity)
     */
    public static function checkSessionTimeout() {
        if (!isset($_SESSION)) {
            session_start();
        }
        
        $timeout = 30 * 60; // 30 minutes in seconds
        
        if (isset($_SESSION['last_activity']) && (time() - $_SESSION['last_activity']) > $timeout) {
            self::logout();
            return false;
        }
        
        $_SESSION['last_activity'] = time();
        return true;
    }

    /**
     * Get user by ID
     */
    public function getUserById($id) {
        $query = "SELECT id, username, email, role, created_at FROM users WHERE id = ?";
        $stmt = $this->db->prepare($query);
        $stmt->execute([$id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    /**
     * Get all users (admin only)
     */
    public function getAllUsers() {
        $query = "SELECT id, username, email, role, created_at, last_activity FROM users ORDER BY created_at DESC";
        $stmt = $this->db->prepare($query);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Update user role (admin only)
     */
    public function updateUserRole($userId, $role) {
        if (!in_array($role, ['employee', 'admin'])) {
            return false;
        }
        
        $query = "UPDATE users SET role = ? WHERE id = ?";
        $stmt = $this->db->prepare($query);
        return $stmt->execute([$role, $userId]);
    }

    /**
     * Delete user (admin only)
     */
    public function deleteUser($userId) {
        // Prevent deleting yourself
        if (isset($_SESSION['user_id']) && $_SESSION['user_id'] == $userId) {
            return false;
        }
        
        $query = "DELETE FROM users WHERE id = ?";
        $stmt = $this->db->prepare($query);
        return $stmt->execute([$userId]);
    }
}
?>