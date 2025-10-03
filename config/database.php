<?php
/**
 * Database Configuration for Narshimha Tattoo Studio
 */

class Database {
    private $host = 'localhost';
    private $db_name = 'narshimha_tattoo';
    private $username = 'root';
    private $password = '';
    private $conn;

    public function getConnection() {
        $this->conn = null;
        
        try {
            $this->conn = new mysqli($this->host, $this->username, $this->password, $this->db_name);
            
            if ($this->conn->connect_error) {
                throw new Exception("Connection failed: " . $this->conn->connect_error);
            }
            
            // Set charset to utf8
            $this->conn->set_charset("utf8");
            
        } catch(Exception $e) {
            echo "Database connection error: " . $e->getMessage();
            return null;
        }
        
        return $this->conn;
    }
    
    public function closeConnection() {
        if ($this->conn) {
            $this->conn->close();
        }
    }
}

// Database configuration constants
define('DB_HOST', 'localhost');
define('DB_NAME', 'narshimha_tattoo');
define('DB_USER', 'root');
define('DB_PASS', '');

// Studio configuration
define('STUDIO_NAME', 'Narshimha Tattoo');
define('STUDIO_EMAIL', 'info@narshimhatattoo.com');
define('STUDIO_PHONE', '(555) 123-TATT');
define('STUDIO_ADDRESS', '123 Ink Street, Art District');

// Business hours (24-hour format)
define('BUSINESS_START', 12); // 12 PM
define('BUSINESS_END', 20);   // 8 PM
define('BUSINESS_DAYS', [2, 3, 4, 5, 6]); // Tuesday to Saturday (1=Monday, 7=Sunday)

// Booking settings
define('MIN_ADVANCE_HOURS', 24);
define('MAX_ADVANCE_DAYS', 90);
define('SLOT_DURATION', 60); // minutes

// Email settings
define('SMTP_HOST', 'smtp.gmail.com');
define('SMTP_PORT', 587);
define('SMTP_USERNAME', 'your_email@gmail.com');
define('SMTP_PASSWORD', 'your_app_password');
define('FROM_EMAIL', 'noreply@narshimhatattoo.com');
define('FROM_NAME', 'Narshimha Tattoo');

?>

