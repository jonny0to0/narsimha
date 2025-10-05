<?php
session_start();
require_once 'admin/auth.php';

// Handle login form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['login'])) {
    $username = sanitizeInput($_POST['username'] ?? '');
    $password = $_POST['password'] ?? '';
    
    if (AdminAuth::login($username, $password)) {
        echo "<h2>Login Successful!</h2>";
        echo "<p>Session ID: " . session_id() . "</p>";
        echo "<p>Session data:</p>";
        echo "<pre>" . print_r($_SESSION, true) . "</pre>";
        echo "<p>isLoggedIn(): " . (AdminAuth::isLoggedIn() ? 'TRUE' : 'FALSE') . "</p>";
        echo "<p><a href='admin/dashboard.php'>Go to Dashboard</a></p>";
    } else {
        echo "<h2>Login Failed!</h2>";
    }
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Test Login</title>
</head>
<body>
    <h1>Test Login Form</h1>
    <form method="POST">
        <p>
            <label>Username:</label><br>
            <input type="text" name="username" value="admin" required>
        </p>
        <p>
            <label>Password:</label><br>
            <input type="password" name="password" value="narshimha2024" required>
        </p>
        <p>
            <button type="submit" name="login" value="1">Login</button>
        </p>
    </form>
    
    <h2>Current Session Info</h2>
    <p>Session ID: <?php echo session_id(); ?></p>
    <p>Session data:</p>
    <pre><?php print_r($_SESSION); ?></pre>
    <p>isLoggedIn(): <?php echo AdminAuth::isLoggedIn() ? 'TRUE' : 'FALSE'; ?></p>
</body>
</html>
