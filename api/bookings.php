<?php
/**
 * Bookings API Endpoint
 * Handles booking appointments and consultations
 */

header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type, Authorization');

// Handle preflight OPTIONS request
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit();
}

require_once '../config/database.php';
require_once '../includes/functions.php';

$database = new Database();
$conn = $database->getConnection();

if (!$conn) {
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => 'Database connection failed']);
    exit();
}

$method = $_SERVER['REQUEST_METHOD'];
$input = json_decode(file_get_contents('php://input'), true);

switch ($method) {
    case 'GET':
        handleGetBookings($conn);
        break;
    case 'POST':
        handleCreateBooking($conn, $input);
        break;
    case 'PUT':
        handleUpdateBooking($conn, $input);
        break;
    case 'DELETE':
        handleDeleteBooking($conn);
        break;
    default:
        http_response_code(405);
        echo json_encode(['success' => false, 'message' => 'Method not allowed']);
        break;
}

function handleGetBookings($conn) {
    $booking_id = $_GET['id'] ?? null;
    
    if ($booking_id) {
        // Get specific booking
        $stmt = $conn->prepare("
            SELECT b.*, a.name as artist_name, s.name as service_name 
            FROM bookings b 
            LEFT JOIN artists a ON b.preferred_artist_id = a.id 
            LEFT JOIN services s ON b.service_id = s.id 
            WHERE b.id = ?
        ");
        $stmt->bind_param("i", $booking_id);
        $stmt->execute();
        $result = $stmt->get_result();
        
        if ($booking = $result->fetch_assoc()) {
            echo json_encode(['success' => true, 'data' => $booking]);
        } else {
            http_response_code(404);
            echo json_encode(['success' => false, 'message' => 'Booking not found']);
        }
    } else {
        // Get all bookings (admin view)
        $stmt = $conn->prepare("
            SELECT b.*, a.name as artist_name, s.name as service_name 
            FROM bookings b 
            LEFT JOIN artists a ON b.preferred_artist_id = a.id 
            LEFT JOIN services s ON b.service_id = s.id 
            ORDER BY b.created_at DESC 
            LIMIT 100
        ");
        $stmt->execute();
        $result = $stmt->get_result();
        
        $bookings = [];
        while ($row = $result->fetch_assoc()) {
            $bookings[] = $row;
        }
        
        echo json_encode(['success' => true, 'data' => $bookings]);
    }
}

function handleCreateBooking($conn, $input) {
    // Validate required fields
    $required_fields = ['firstName', 'lastName', 'email', 'phone', 'description'];
    foreach ($required_fields as $field) {
        if (empty($input[$field])) {
            http_response_code(400);
            echo json_encode(['success' => false, 'message' => "Field '$field' is required"]);
            return;
        }
    }
    
    // Validate email format
    if (!filter_var($input['email'], FILTER_VALIDATE_EMAIL)) {
        http_response_code(400);
        echo json_encode(['success' => false, 'message' => 'Invalid email format']);
        return;
    }
    
    // Generate unique booking reference
    $booking_reference = 'NT' . date('Y') . str_pad(rand(1, 9999), 4, '0', STR_PAD_LEFT);
    
    // Check if reference already exists (unlikely but possible)
    $check_stmt = $conn->prepare("SELECT id FROM bookings WHERE booking_reference = ?");
    $check_stmt->bind_param("s", $booking_reference);
    $check_stmt->execute();
    if ($check_stmt->get_result()->num_rows > 0) {
        $booking_reference = 'NT' . date('Y') . str_pad(rand(1, 9999), 4, '0', STR_PAD_LEFT);
    }
    
    // Prepare booking data
    $first_name = sanitizeInput($input['firstName']);
    $last_name = sanitizeInput($input['lastName']);
    $email = sanitizeInput($input['email']);
    $phone = sanitizeInput($input['phone']);
    $preferred_artist_id = !empty($input['artist']) ? getArtistIdByName($conn, $input['artist']) : null;
    $service_id = !empty($input['serviceId']) ? intval($input['serviceId']) : null;
    $tattoo_style = !empty($input['style']) ? sanitizeInput($input['style']) : null;
    $description = sanitizeInput($input['description']);
    $preferred_date = !empty($input['preferredDate']) ? $input['preferredDate'] : null;
    $preferred_time = !empty($input['preferredTime']) ? $input['preferredTime'] : null;
    
    // Calculate total amount if service is selected
    $total_amount = 0;
    if ($service_id) {
        $price_stmt = $conn->prepare("SELECT price FROM services WHERE id = ?");
        $price_stmt->bind_param("i", $service_id);
        $price_stmt->execute();
        $price_result = $price_stmt->get_result();
        if ($price_row = $price_result->fetch_assoc()) {
            $total_amount = $price_row['price'];
        }
    }
    
    // Insert booking
    $stmt = $conn->prepare("
        INSERT INTO bookings (
            booking_reference, first_name, last_name, email, phone, 
            preferred_artist_id, service_id, tattoo_style, description, 
            preferred_date, preferred_time, total_amount, status
        ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, 'pending')
    ");
    
    $stmt->bind_param(
        "sssssisssssd",
        $booking_reference, $first_name, $last_name, $email, $phone,
        $preferred_artist_id, $service_id, $tattoo_style, $description,
        $preferred_date, $preferred_time, $total_amount
    );
    
    if ($stmt->execute()) {
        $booking_id = $conn->insert_id;
        
        // Send confirmation email
        sendBookingConfirmationEmail($input, $booking_reference);
        
        // Log status change
        logBookingStatusChange($conn, $booking_id, null, 'pending', 'Booking created');
        
        echo json_encode([
            'success' => true,
            'message' => 'Booking created successfully',
            'data' => [
                'booking_id' => $booking_id,
                'booking_reference' => $booking_reference,
                'status' => 'pending'
            ]
        ]);
    } else {
        http_response_code(500);
        echo json_encode(['success' => false, 'message' => 'Failed to create booking']);
    }
}

function handleUpdateBooking($conn, $input) {
    $booking_id = $_GET['id'] ?? null;
    
    if (!$booking_id) {
        http_response_code(400);
        echo json_encode(['success' => false, 'message' => 'Booking ID is required']);
        return;
    }
    
    // Check if booking exists
    $check_stmt = $conn->prepare("SELECT status FROM bookings WHERE id = ?");
    $check_stmt->bind_param("i", $booking_id);
    $check_stmt->execute();
    $result = $check_stmt->get_result();
    
    if ($result->num_rows === 0) {
        http_response_code(404);
        echo json_encode(['success' => false, 'message' => 'Booking not found']);
        return;
    }
    
    $current_booking = $result->fetch_assoc();
    $old_status = $current_booking['status'];
    
    // Build update query dynamically
    $update_fields = [];
    $types = "";
    $values = [];
    
    $allowed_fields = [
        'status' => 's',
        'preferred_date' => 's',
        'preferred_time' => 's',
        'notes' => 's',
        'total_amount' => 'd',
        'deposit_amount' => 'd'
    ];
    
    foreach ($allowed_fields as $field => $type) {
        if (isset($input[$field])) {
            $update_fields[] = "$field = ?";
            $types .= $type;
            $values[] = $input[$field];
        }
    }
    
    if (empty($update_fields)) {
        http_response_code(400);
        echo json_encode(['success' => false, 'message' => 'No valid fields to update']);
        return;
    }
    
    // Add updated_at
    $update_fields[] = "updated_at = NOW()";
    
    $values[] = $booking_id;
    $types .= "i";
    
    $sql = "UPDATE bookings SET " . implode(', ', $update_fields) . " WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param($types, ...$values);
    
    if ($stmt->execute()) {
        // Log status change if status was updated
        if (isset($input['status']) && $input['status'] !== $old_status) {
            logBookingStatusChange($conn, $booking_id, $old_status, $input['status'], $input['notes'] ?? '');
        }
        
        echo json_encode(['success' => true, 'message' => 'Booking updated successfully']);
    } else {
        http_response_code(500);
        echo json_encode(['success' => false, 'message' => 'Failed to update booking']);
    }
}

function handleDeleteBooking($conn) {
    $booking_id = $_GET['id'] ?? null;
    
    if (!$booking_id) {
        http_response_code(400);
        echo json_encode(['success' => false, 'message' => 'Booking ID is required']);
        return;
    }
    
    // Check if booking exists
    $check_stmt = $conn->prepare("SELECT id FROM bookings WHERE id = ?");
    $check_stmt->bind_param("i", $booking_id);
    $check_stmt->execute();
    
    if ($check_stmt->get_result()->num_rows === 0) {
        http_response_code(404);
        echo json_encode(['success' => false, 'message' => 'Booking not found']);
        return;
    }
    
    // Soft delete by updating status to cancelled
    $stmt = $conn->prepare("UPDATE bookings SET status = 'cancelled', updated_at = NOW() WHERE id = ?");
    $stmt->bind_param("i", $booking_id);
    
    if ($stmt->execute()) {
        logBookingStatusChange($conn, $booking_id, null, 'cancelled', 'Booking cancelled');
        echo json_encode(['success' => true, 'message' => 'Booking cancelled successfully']);
    } else {
        http_response_code(500);
        echo json_encode(['success' => false, 'message' => 'Failed to cancel booking']);
    }
}

function getArtistIdByName($conn, $artist_name) {
    $artist_map = [
        'marcus' => 1,
        'luna' => 2,
        'jake' => 3,
        'aria' => 4
    ];
    
    return $artist_map[strtolower($artist_name)] ?? null;
}

function logBookingStatusChange($conn, $booking_id, $old_status, $new_status, $notes) {
    $stmt = $conn->prepare("
        INSERT INTO booking_status_history (booking_id, old_status, new_status, notes) 
        VALUES (?, ?, ?, ?)
    ");
    $stmt->bind_param("isss", $booking_id, $old_status, $new_status, $notes);
    $stmt->execute();
}

function sendBookingConfirmationEmail($booking_data, $booking_reference) {
    // Email implementation would go here
    // For now, we'll just log it
    error_log("Booking confirmation email sent for reference: $booking_reference");
}

$database->closeConnection();
?>

