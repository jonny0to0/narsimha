<?php
/**
 * Database Setup Script for Narshimha Tattoo Studio
 * This script will create the database and tables safely
 */

require_once 'config/database.php';

echo "<h2>Narshimha Tattoo Studio - Database Setup</h2>";
echo "<style>body { font-family: Arial, sans-serif; margin: 40px; } .success { color: green; } .error { color: red; } .info { color: blue; }</style>";

try {
    // Connect to MySQL server (without database)
    $host = 'localhost';
    $username = 'root';
    $password = '';
    
    $conn = new mysqli($host, $username, $password);
    
    if ($conn->connect_error) {
        throw new Exception("Connection failed: " . $conn->connect_error);
    }
    
    echo "<p class='info'>✅ Connected to MySQL server</p>";
    
    // Read and execute the schema file
    $schema_file = 'database/schema.sql';
    
    if (!file_exists($schema_file)) {
        throw new Exception("Schema file not found: $schema_file");
    }
    
    $sql = file_get_contents($schema_file);
    
    // Split SQL into individual statements
    $statements = array_filter(array_map('trim', explode(';', $sql)));
    
    $success_count = 0;
    $error_count = 0;
    
    foreach ($statements as $statement) {
        if (empty($statement) || strpos($statement, '--') === 0) {
            continue; // Skip empty statements and comments
        }
        
        if ($conn->query($statement)) {
            $success_count++;
            
            // Show what was executed
            $first_words = implode(' ', array_slice(explode(' ', trim($statement)), 0, 3));
            echo "<p class='success'>✅ Executed: $first_words...</p>";
        } else {
            $error_count++;
            echo "<p class='error'>❌ Error: " . $conn->error . "</p>";
            echo "<p class='error'>Statement: " . substr($statement, 0, 100) . "...</p>";
        }
    }
    
    $conn->close();
    
    echo "<hr>";
    echo "<h3>Setup Summary:</h3>";
    echo "<p class='success'>✅ Successful operations: $success_count</p>";
    echo "<p class='error'>❌ Errors: $error_count</p>";
    
    if ($error_count === 0) {
        echo "<h3 class='success'>🎉 Database setup completed successfully!</h3>";
        echo "<p><strong>Next steps:</strong></p>";
        echo "<ul>";
        echo "<li>Access admin panel: <a href='admin/login.php'>admin/login.php</a></li>";
        echo "<li>Default credentials: admin / narshimha2024</li>";
        echo "<li>Visit main website: <a href='index.html'>index.html</a></li>";
        echo "</ul>";
    } else {
        echo "<h3 class='error'>⚠️ Setup completed with errors. Please check the errors above.</h3>";
    }
    
} catch (Exception $e) {
    echo "<p class='error'>❌ Fatal Error: " . $e->getMessage() . "</p>";
}

echo "<hr>";
echo "<p><em>Narshimha Tattoo Studio - Database Setup Script</em></p>";
?>
