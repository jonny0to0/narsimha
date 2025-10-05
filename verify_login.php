<?php
// Simple verification that login system is working
session_start();
require_once 'admin/auth.php';

echo "<h1>Login System Verification</h1>";

// Test login
$result = AdminAuth::login('admin', 'narshimha2024');

if ($result) {
    echo "<p style='color: green;'>✓ Login system is working correctly!</p>";
    echo "<p>Session ID: " . session_id() . "</p>";
    echo "<p>isLoggedIn(): " . (AdminAuth::isLoggedIn() ? 'TRUE' : 'FALSE') . "</p>";
    echo "<p>Username: " . AdminAuth::getUsername() . "</p>";
    echo "<p><a href='admin/dashboard.php' style='background: #ff073a; color: white; padding: 10px 20px; text-decoration: none; border-radius: 5px;'>Go to Dashboard</a></p>";
} else {
    echo "<p style='color: red;'>✗ Login system is not working!</p>";
}

echo "<h2>Test Instructions:</h2>";
echo "<ol>";
echo "<li>Click the 'Go to Dashboard' button above</li>";
echo "<li>If you can access the dashboard, the login system is working</li>";
echo "<li>If you get redirected back to login, there's still an issue</li>";
echo "</ol>";

echo "<h2>Manual Test:</h2>";
echo "<p><a href='admin/login.php'>Go to Login Page</a></p>";
echo "<p>Use these credentials:</p>";
echo "<ul>";
echo "<li>Username: admin</li>";
echo "<li>Password: narshimha2024</li>";
echo "</ul>";
?>
