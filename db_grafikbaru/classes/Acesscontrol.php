<?php
// classes/AccessControl.php
// File ini berisi helper untuk proteksi akses

class AccessControl {
    
    public static function requireLogin() {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        if (!isset($_SESSION['user']) || empty($_SESSION['user']['id'])) {
            $_SESSION['redirect_after_login'] = $_SERVER['REQUEST_URI'];
            header("Location: ../../public/login.php");
            exit;
        }
    }
    
    public static function requireRole($allowedRoles = []) {
        self::requireLogin();
        $userRole = $_SESSION['user']['role'] ?? 'customer';
        if (!in_array($userRole, $allowedRoles)) {
            http_response_code(403);
            die("❌ Akses ditolak: Anda tidak memiliki izin.");
        }
    }
    
    public static function generateToken() {
        if (session_status() === PHP_SESSION_NONE) session_start();
        if (empty($_SESSION['csrf_token'])) {
            $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
        }
        return $_SESSION['csrf_token'];
    }
    
    public static function validateToken($token) {
        if (session_status() === PHP_SESSION_NONE) session_start();
        return hash_equals($_SESSION['csrf_token'] ?? '', $token ?? '');
    }
}