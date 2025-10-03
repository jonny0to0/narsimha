<?php
/**
 * Cart API Endpoint
 * Handles shopping cart operations
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

// Clean up expired cart sessions
cleanupExpiredCartSessions($conn);

switch ($method) {
    case 'GET':
        handleGetCart($conn);
        break;
    case 'POST':
        handleAddToCart($conn, $input);
        break;
    case 'PUT':
        handleUpdateCartItem($conn, $input);
        break;
    case 'DELETE':
        handleRemoveFromCart($conn);
        break;
    default:
        http_response_code(405);
        echo json_encode(['success' => false, 'message' => 'Method not allowed']);
        break;
}

function handleGetCart($conn) {
    $session_id = $_GET['session_id'] ?? null;
    
    if (!$session_id) {
        http_response_code(400);
        echo json_encode(['success' => false, 'message' => 'Session ID is required']);
        return;
    }
    
    $stmt = $conn->prepare("
        SELECT cs.*, s.name, s.description, s.size_info, s.image_url, sc.name as category_name 
        FROM cart_sessions cs 
        JOIN services s ON cs.service_id = s.id 
        JOIN service_categories sc ON s.category_id = sc.id 
        WHERE cs.session_id = ? AND cs.expires_at > NOW() 
        ORDER BY cs.created_at ASC
    ");
    $stmt->bind_param("s", $session_id);
    $stmt->execute();
    $result = $stmt->get_result();
    
    $cart_items = [];
    $total_amount = 0;
    $total_items = 0;
    
    while ($row = $result->fetch_assoc()) {
        $item_total = $row['price'] * $row['quantity'];
        $total_amount += $item_total;
        $total_items += $row['quantity'];
        
        $cart_items[] = [
            'id' => $row['service_id'],
            'name' => $row['name'],
            'description' => $row['description'],
            'size_info' => $row['size_info'],
            'image_url' => $row['image_url'],
            'category_name' => $row['category_name'],
            'price' => floatval($row['price']),
            'quantity' => intval($row['quantity']),
            'item_total' => $item_total
        ];
    }
    
    echo json_encode([
        'success' => true,
        'data' => [
            'items' => $cart_items,
            'total_amount' => $total_amount,
            'total_items' => $total_items,
            'session_id' => $session_id
        ]
    ]);
}

function handleAddToCart($conn, $input) {
    // Validate required fields
    if (empty($input['session_id']) || empty($input['service_id'])) {
        http_response_code(400);
        echo json_encode(['success' => false, 'message' => 'Session ID and Service ID are required']);
        return;
    }
    
    $session_id = sanitizeInput($input['session_id']);
    $service_id = intval($input['service_id']);
    $quantity = intval($input['quantity'] ?? 1);
    
    if ($quantity <= 0) {
        http_response_code(400);
        echo json_encode(['success' => false, 'message' => 'Quantity must be greater than 0']);
        return;
    }
    
    // Verify service exists and get price
    $service_stmt = $conn->prepare("SELECT price FROM services WHERE id = ? AND is_active = 1");
    $service_stmt->bind_param("i", $service_id);
    $service_stmt->execute();
    $service_result = $service_stmt->get_result();
    
    if ($service_result->num_rows === 0) {
        http_response_code(404);
        echo json_encode(['success' => false, 'message' => 'Service not found']);
        return;
    }
    
    $service = $service_result->fetch_assoc();
    $price = $service['price'];
    
    // Check if item already exists in cart
    $check_stmt = $conn->prepare("
        SELECT id, quantity FROM cart_sessions 
        WHERE session_id = ? AND service_id = ? AND expires_at > NOW()
    ");
    $check_stmt->bind_param("si", $session_id, $service_id);
    $check_stmt->execute();
    $check_result = $check_stmt->get_result();
    
    if ($check_result->num_rows > 0) {
        // Update existing item
        $existing_item = $check_result->fetch_assoc();
        $new_quantity = $existing_item['quantity'] + $quantity;
        
        $update_stmt = $conn->prepare("
            UPDATE cart_sessions 
            SET quantity = ?, expires_at = (NOW() + INTERVAL 24 HOUR) 
            WHERE id = ?
        ");
        $update_stmt->bind_param("ii", $new_quantity, $existing_item['id']);
        
        if ($update_stmt->execute()) {
            echo json_encode([
                'success' => true,
                'message' => 'Cart updated successfully',
                'data' => ['quantity' => $new_quantity]
            ]);
        } else {
            http_response_code(500);
            echo json_encode(['success' => false, 'message' => 'Failed to update cart']);
        }
    } else {
        // Add new item
        $insert_stmt = $conn->prepare("
            INSERT INTO cart_sessions (session_id, service_id, quantity, price) 
            VALUES (?, ?, ?, ?)
        ");
        $insert_stmt->bind_param("siid", $session_id, $service_id, $quantity, $price);
        
        if ($insert_stmt->execute()) {
            echo json_encode([
                'success' => true,
                'message' => 'Item added to cart successfully',
                'data' => ['cart_item_id' => $conn->insert_id]
            ]);
        } else {
            http_response_code(500);
            echo json_encode(['success' => false, 'message' => 'Failed to add item to cart']);
        }
    }
}

function handleUpdateCartItem($conn, $input) {
    if (empty($input['session_id']) || empty($input['service_id'])) {
        http_response_code(400);
        echo json_encode(['success' => false, 'message' => 'Session ID and Service ID are required']);
        return;
    }
    
    $session_id = sanitizeInput($input['session_id']);
    $service_id = intval($input['service_id']);
    $quantity = intval($input['quantity'] ?? 1);
    
    if ($quantity <= 0) {
        // Remove item if quantity is 0 or negative
        handleRemoveFromCart($conn, $session_id, $service_id);
        return;
    }
    
    $stmt = $conn->prepare("
        UPDATE cart_sessions 
        SET quantity = ?, expires_at = (NOW() + INTERVAL 24 HOUR) 
        WHERE session_id = ? AND service_id = ? AND expires_at > NOW()
    ");
    $stmt->bind_param("isi", $quantity, $session_id, $service_id);
    
    if ($stmt->execute()) {
        if ($stmt->affected_rows > 0) {
            echo json_encode(['success' => true, 'message' => 'Cart item updated successfully']);
        } else {
            http_response_code(404);
            echo json_encode(['success' => false, 'message' => 'Cart item not found']);
        }
    } else {
        http_response_code(500);
        echo json_encode(['success' => false, 'message' => 'Failed to update cart item']);
    }
}

function handleRemoveFromCart($conn, $session_id = null, $service_id = null) {
    if (!$session_id) {
        $session_id = $_GET['session_id'] ?? null;
    }
    if (!$service_id) {
        $service_id = $_GET['service_id'] ?? null;
    }
    
    if (!$session_id) {
        http_response_code(400);
        echo json_encode(['success' => false, 'message' => 'Session ID is required']);
        return;
    }
    
    if ($service_id) {
        // Remove specific item
        $stmt = $conn->prepare("DELETE FROM cart_sessions WHERE session_id = ? AND service_id = ?");
        $stmt->bind_param("si", $session_id, $service_id);
        $message = 'Item removed from cart successfully';
    } else {
        // Clear entire cart
        $stmt = $conn->prepare("DELETE FROM cart_sessions WHERE session_id = ?");
        $stmt->bind_param("s", $session_id);
        $message = 'Cart cleared successfully';
    }
    
    if ($stmt->execute()) {
        if ($stmt->affected_rows > 0) {
            echo json_encode(['success' => true, 'message' => $message]);
        } else {
            http_response_code(404);
            echo json_encode(['success' => false, 'message' => 'Cart item not found']);
        }
    } else {
        http_response_code(500);
        echo json_encode(['success' => false, 'message' => 'Failed to remove item from cart']);
    }
}

function cleanupExpiredCartSessions($conn) {
    $stmt = $conn->prepare("DELETE FROM cart_sessions WHERE expires_at < NOW()");
    $stmt->execute();
}

$database->closeConnection();
?>

