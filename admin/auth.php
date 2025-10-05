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
        // Get database connection
        $database = new Database();
        $conn = $database->getConnection();
        
        if (!$conn) {
            error_log("Database connection failed during login");
            return false;
        }
        
        // Prepare statement to get user from database
        $stmt = $conn->prepare("SELECT id, username, email, password_hash, is_active FROM admin_users WHERE username = ? AND is_active = 1");
        if (!$stmt) {
            error_log("Failed to prepare login statement: " . $conn->error);
            return false;
        }
        
        $stmt->bind_param("s", $username);
        $stmt->execute();
        $result = $stmt->get_result();
        
        if ($result->num_rows === 1) {
            $user = $result->fetch_assoc();
            
            // Verify password
            if (password_verify($password, $user['password_hash'])) {
                // Update last login time
                $update_stmt = $conn->prepare("UPDATE admin_users SET last_login = NOW() WHERE id = ?");
                $update_stmt->bind_param("i", $user['id']);
                $update_stmt->execute();
                $update_stmt->close();
                
                // Set session variables
                $_SESSION['admin_logged_in'] = true;
                $_SESSION['admin_username'] = $user['username'];
                $_SESSION['admin_email'] = $user['email'];
                $_SESSION['admin_id'] = $user['id'];
                $_SESSION['admin_login_time'] = time();
                
                $stmt->close();
                $conn->close();
                return true;
            }
        }
        
        $stmt->close();
        $conn->close();
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

// Login form submission is handled in login.php

// Handle logout
if (isset($_GET['logout'])) {
    AdminAuth::logout();
    header('Location: login.php?logged_out=1');
    exit();
}
?>

