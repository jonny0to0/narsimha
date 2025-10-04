<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type');

// Handle preflight requests
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit();
}

require_once '../config/database.php';

$database = new Database();
$conn = $database->getConnection();

try {
    if ($_SERVER['REQUEST_METHOD'] === 'GET') {
        // Get all active artists
        $stmt = $conn->prepare("SELECT id, name, email, phone, specialties, experience_years, bio, image_url, created_at FROM artists WHERE is_active = 1 ORDER BY name");
        $stmt->execute();
        $result = $stmt->get_result();
        
        $artists = [];
        while ($row = $result->fetch_assoc()) {
            // Get booking count for each artist
            $booking_stmt = $conn->prepare("SELECT COUNT(*) as booking_count FROM bookings WHERE preferred_artist_id = ?");
            $booking_stmt->bind_param("i", $row['id']);
            $booking_stmt->execute();
            $booking_result = $booking_stmt->get_result();
            $booking_count = $booking_result->fetch_assoc()['booking_count'];
            
            $row['booking_count'] = $booking_count;
            $row['specialties'] = explode(',', $row['specialties']);
            $artists[] = $row;
        }
        
        echo json_encode([
            'success' => true,
            'data' => $artists
        ]);
        
    } else {
        http_response_code(405);
        echo json_encode([
            'success' => false,
            'message' => 'Method not allowed'
        ]);
    }
    
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'message' => 'Internal server error: ' . $e->getMessage()
    ]);
}
?>
