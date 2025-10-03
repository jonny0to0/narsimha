<?php
/**
 * Setup script for Narshimha Tattoo Studio
 * Run this once to set up the database
 */

require_once 'config/database.php';

echo "<h1>Narshimha Tattoo Studio - Database Setup</h1>";

try {
    // Create database connection
    $database = new Database();
    $conn = $database->getConnection();
    
    if (!$conn) {
        throw new Exception("Could not connect to database. Please check your database configuration in config/database.php");
    }
    
    echo "<p>✅ Database connection successful!</p>";
    
    // Read and execute schema
    $schema_file = 'database/schema.sql';
    if (!file_exists($schema_file)) {
        throw new Exception("Schema file not found: $schema_file");
    }
    
    $schema_sql = file_get_contents($schema_file);
    
    // Split SQL statements
    $statements = array_filter(array_map('trim', explode(';', $schema_sql)));
    
    $success_count = 0;
    $error_count = 0;
    
    foreach ($statements as $statement) {
        if (empty($statement) || strpos($statement, '--') === 0) {
            continue;
        }
        
        try {
            if ($conn->query($statement)) {
                $success_count++;
            } else {
                echo "<p>⚠️ Warning: " . $conn->error . "</p>";
                $error_count++;
            }
        } catch (Exception $e) {
            echo "<p>⚠️ Warning: " . $e->getMessage() . "</p>";
            $error_count++;
        }
    }
    
    echo "<p>✅ Database setup completed!</p>";
    echo "<p>📊 Executed $success_count statements successfully";
    if ($error_count > 0) {
        echo " ($error_count warnings)";
    }
    echo "</p>";
    
    // Test the setup
    echo "<h2>Testing Setup</h2>";
    
    // Test artists table
    $result = $conn->query("SELECT COUNT(*) as count FROM artists");
    $row = $result->fetch_assoc();
    echo "<p>✅ Artists table: " . $row['count'] . " records</p>";
    
    // Test service categories
    $result = $conn->query("SELECT COUNT(*) as count FROM service_categories");
    $row = $result->fetch_assoc();
    echo "<p>✅ Service categories: " . $row['count'] . " records</p>";
    
    // Test services
    $result = $conn->query("SELECT COUNT(*) as count FROM services");
    $row = $result->fetch_assoc();
    echo "<p>✅ Services: " . $row['count'] . " records</p>";
    
    echo "<h2>Next Steps</h2>";
    echo "<ul>";
    echo "<li>✅ Database is ready!</li>";
    echo "<li>🌐 Your website should now work with the backend</li>";
    echo "<li>📧 Configure email settings in config/database.php for booking confirmations</li>";
    echo "<li>🔒 For production, update database credentials and enable security features</li>";
    echo "</ul>";
    
    echo "<p><a href='index.html'>🚀 Go to your website</a></p>";
    
} catch (Exception $e) {
    echo "<p>❌ Error: " . $e->getMessage() . "</p>";
    echo "<h2>Troubleshooting</h2>";
    echo "<ul>";
    echo "<li>Make sure MySQL is running</li>";
    echo "<li>Check database credentials in config/database.php</li>";
    echo "<li>Ensure the database user has CREATE and INSERT permissions</li>";
    echo "</ul>";
}
?>

<style>
body {
    font-family: Arial, sans-serif;
    max-width: 800px;
    margin: 50px auto;
    padding: 20px;
    background-color: #f5f5f5;
}
h1, h2 {
    color: #ff073a;
}
p {
    background: white;
    padding: 10px;
    border-radius: 5px;
    margin: 10px 0;
}
ul {
    background: white;
    padding: 20px;
    border-radius: 5px;
}
a {
    color: #ff073a;
    text-decoration: none;
    font-weight: bold;
}
a:hover {
    text-decoration: underline;
}
</style>

