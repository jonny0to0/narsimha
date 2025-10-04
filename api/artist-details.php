<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, OPTIONS');
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
        // Get artist ID from URL parameter
        $artist_id = isset($_GET['id']) ? intval($_GET['id']) : 0;
        
        if ($artist_id <= 0) {
            http_response_code(400);
            echo json_encode([
                'success' => false,
                'message' => 'Invalid artist ID'
            ]);
            exit();
        }
        
        // Get artist details
        $stmt = $conn->prepare("SELECT id, name, email, phone, specialties, experience_years, bio, image_url, created_at FROM artists WHERE id = ? AND is_active = 1");
        $stmt->bind_param("i", $artist_id);
        $stmt->execute();
        $result = $stmt->get_result();
        
        if ($result->num_rows === 0) {
            http_response_code(404);
            echo json_encode([
                'success' => false,
                'message' => 'Artist not found'
            ]);
            exit();
        }
        
        $artist = $result->fetch_assoc();
        
        // Get booking count for the artist
        $booking_stmt = $conn->prepare("SELECT COUNT(*) as booking_count FROM bookings WHERE preferred_artist_id = ?");
        $booking_stmt->bind_param("i", $artist_id);
        $booking_stmt->execute();
        $booking_result = $booking_stmt->get_result();
        $booking_count = $booking_result->fetch_assoc()['booking_count'];
        
        // Get recent bookings for the artist (last 10)
        $recent_bookings_stmt = $conn->prepare("
            SELECT b.booking_reference, b.first_name, b.last_name, b.tattoo_style, b.description, b.preferred_date, b.status, s.name as service_name
            FROM bookings b
            LEFT JOIN services s ON b.service_id = s.id
            WHERE b.preferred_artist_id = ?
            ORDER BY b.created_at DESC
            LIMIT 10
        ");
        $recent_bookings_stmt->bind_param("i", $artist_id);
        $recent_bookings_stmt->execute();
        $recent_bookings_result = $recent_bookings_stmt->get_result();
        
        $recent_bookings = [];
        while ($row = $recent_bookings_result->fetch_assoc()) {
            $recent_bookings[] = $row;
        }
        
        // Get services that match artist specialties
        $specialties = explode(',', $artist['specialties']);
        $specialty_conditions = [];
        $params = [];
        $param_types = '';
        
        foreach ($specialties as $specialty) {
            $specialty_conditions[] = "s.name LIKE ? OR sc.name LIKE ?";
            $specialty_param = '%' . trim($specialty) . '%';
            $params[] = $specialty_param;
            $params[] = $specialty_param;
            $param_types .= 'ss';
        }
        
        $recommended_services = [];
        if (!empty($specialty_conditions)) {
            $services_stmt = $conn->prepare("
                SELECT s.id, s.name, s.description, s.price, s.image_url, s.estimated_duration, sc.name as category_name
                FROM services s
                LEFT JOIN service_categories sc ON s.category_id = sc.id
                WHERE s.is_active = 1 AND (" . implode(' OR ', $specialty_conditions) . ")
                ORDER BY s.price ASC
                LIMIT 6
            ");
            $services_stmt->bind_param($param_types, ...$params);
            $services_stmt->execute();
            $services_result = $services_stmt->get_result();
            
            while ($row = $services_result->fetch_assoc()) {
                $recommended_services[] = $row;
            }
        }
        
        // Format the response
        $artist['booking_count'] = $booking_count;
        $artist['recent_bookings'] = $recent_bookings;
        $artist['recommended_services'] = $recommended_services;
        $artist['specialties'] = array_map('trim', explode(',', $artist['specialties']));
        
        echo json_encode([
            'success' => true,
            'data' => $artist
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
