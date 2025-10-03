<?php
/**
 * Admin Authentication System
 */

// Start session only if not already started
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Handle different calling contexts (from admin/ or from root)
$config_path = file_exists('../config/database.php') ? '../config/database.php' : 'config/database.php';
$functions_path = file_exists('../includes/functions.php') ? '../includes/functions.php' : 'includes/functions.php';

require_once $config_path;
require_once $functions_path;

// Default admin credentials (change these!)
define('ADMIN_USERNAME', 'admin');
define('ADMIN_PASSWORD', 'narshimha2024'); // Change this in production!

class AdminAuth {
    
    public static function login($username, $password) {
        if ($username === ADMIN_USERNAME && $password === ADMIN_PASSWORD) {
            $_SESSION['admin_logged_in'] = true;
            $_SESSION['admin_username'] = $username;
            $_SESSION['admin_login_time'] = time();
            return true;
        }
        return false;
    }
    
    public static function logout() {
        if (session_status() === PHP_SESSION_ACTIVE) {
            session_destroy();
        }
        return true;
    }
    
    public static function isLoggedIn() {
        return isset($_SESSION['admin_logged_in']) && $_SESSION['admin_logged_in'] === true;
    }
    
    public static function requireAuth() {
        if (!self::isLoggedIn()) {
            header('Location: login.php');
            exit();
        }
        
        // Check session timeout (4 hours)
        if (isset($_SESSION['admin_login_time']) && (time() - $_SESSION['admin_login_time']) > 14400) {
            self::logout();
            header('Location: login.php?timeout=1');
            exit();
        }
    }
    
    public static function getUsername() {
        return $_SESSION['admin_username'] ?? null;
    }
    
    public static function getLoginTime() {
        return $_SESSION['admin_login_time'] ?? null;
    }
}

// Handle login form submission
if (isset($_SERVER['REQUEST_METHOD']) && $_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['login'])) {
    $username = sanitizeInput($_POST['username'] ?? '');
    $password = $_POST['password'] ?? '';
    
    if (AdminAuth::login($username, $password)) {
        header('Location: dashboard.php');
        exit();
    } else {
        $login_error = 'Invalid username or password';
    }
}

// Handle logout
if (isset($_GET['logout'])) {
    AdminAuth::logout();
    header('Location: login.php?logged_out=1');
    exit();
}
?>

