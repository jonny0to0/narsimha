<?php
/**
 * Services API Endpoint
 * Handles service categories and individual services
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
$path_info = $_SERVER['PATH_INFO'] ?? '';

switch ($method) {
    case 'GET':
        if (strpos($path_info, '/categories') !== false) {
            handleGetCategories($conn);
        } elseif (strpos($path_info, '/category/') !== false) {
            $category_slug = basename($path_info);
            handleGetCategoryServices($conn, $category_slug);
        } else {
            handleGetServices($conn);
        }
        break;
    case 'POST':
        handleCreateService($conn);
        break;
    case 'PUT':
        handleUpdateService($conn);
        break;
    case 'DELETE':
        handleDeleteService($conn);
        break;
    default:
        http_response_code(405);
        echo json_encode(['success' => false, 'message' => 'Method not allowed']);
        break;
}

function handleGetCategories($conn) {
    $stmt = $conn->prepare("
        SELECT sc.*, COUNT(s.id) as service_count 
        FROM service_categories sc 
        LEFT JOIN services s ON sc.id = s.category_id AND s.is_active = 1 
        WHERE sc.is_active = 1 
        GROUP BY sc.id 
        ORDER BY sc.name
    ");
    $stmt->execute();
    $result = $stmt->get_result();
    
    $categories = [];
    while ($row = $result->fetch_assoc()) {
        $categories[] = $row;
    }
    
    echo json_encode(['success' => true, 'data' => $categories]);
}

function handleGetCategoryServices($conn, $category_slug) {
    // First get the category
    $cat_stmt = $conn->prepare("SELECT * FROM service_categories WHERE slug = ? AND is_active = 1");
    $cat_stmt->bind_param("s", $category_slug);
    $cat_stmt->execute();
    $cat_result = $cat_stmt->get_result();
    
    if ($cat_result->num_rows === 0) {
        http_response_code(404);
        echo json_encode(['success' => false, 'message' => 'Category not found']);
        return;
    }
    
    $category = $cat_result->fetch_assoc();
    
    // Get services in this category
    $services_stmt = $conn->prepare("
        SELECT * FROM services 
        WHERE category_id = ? AND is_active = 1 
        ORDER BY price ASC
    ");
    $services_stmt->bind_param("i", $category['id']);
    $services_stmt->execute();
    $services_result = $services_stmt->get_result();
    
    $services = [];
    while ($row = $services_result->fetch_assoc()) {
        $services[] = $row;
    }
    
    echo json_encode([
        'success' => true,
        'data' => [
            'category' => $category,
            'services' => $services
        ]
    ]);
}

function handleGetServices($conn) {
    $category_id = $_GET['category_id'] ?? null;
    $service_id = $_GET['id'] ?? null;
    
    if ($service_id) {
        // Get specific service
        $stmt = $conn->prepare("
            SELECT s.*, sc.name as category_name, sc.slug as category_slug 
            FROM services s 
            LEFT JOIN service_categories sc ON s.category_id = sc.id 
            WHERE s.id = ? AND s.is_active = 1
        ");
        $stmt->bind_param("i", $service_id);
        $stmt->execute();
        $result = $stmt->get_result();
        
        if ($service = $result->fetch_assoc()) {
            echo json_encode(['success' => true, 'data' => $service]);
        } else {
            http_response_code(404);
            echo json_encode(['success' => false, 'message' => 'Service not found']);
        }
    } else {
        // Get all services or services by category
        $sql = "
            SELECT s.*, sc.name as category_name, sc.slug as category_slug 
            FROM services s 
            LEFT JOIN service_categories sc ON s.category_id = sc.id 
            WHERE s.is_active = 1
        ";
        $params = [];
        $types = "";
        
        if ($category_id) {
            $sql .= " AND s.category_id = ?";
            $params[] = $category_id;
            $types .= "i";
        }
        
        $sql .= " ORDER BY sc.name, s.price ASC";
        
        $stmt = $conn->prepare($sql);
        if (!empty($params)) {
            $stmt->bind_param($types, ...$params);
        }
        $stmt->execute();
        $result = $stmt->get_result();
        
        $services = [];
        while ($row = $result->fetch_assoc()) {
            $services[] = $row;
        }
        
        echo json_encode(['success' => true, 'data' => $services]);
    }
}

function handleCreateService($conn) {
    $input = json_decode(file_get_contents('php://input'), true);
    
    // Validate required fields
    $required_fields = ['category_id', 'name', 'price'];
    foreach ($required_fields as $field) {
        if (empty($input[$field])) {
            http_response_code(400);
            echo json_encode(['success' => false, 'message' => "Field '$field' is required"]);
            return;
        }
    }
    
    // Validate category exists
    $cat_stmt = $conn->prepare("SELECT id FROM service_categories WHERE id = ? AND is_active = 1");
    $cat_stmt->bind_param("i", $input['category_id']);
    $cat_stmt->execute();
    if ($cat_stmt->get_result()->num_rows === 0) {
        http_response_code(400);
        echo json_encode(['success' => false, 'message' => 'Invalid category']);
        return;
    }
    
    $category_id = intval($input['category_id']);
    $name = sanitizeInput($input['name']);
    $description = sanitizeInput($input['description'] ?? '');
    $price = floatval($input['price']);
    $size_info = sanitizeInput($input['size_info'] ?? '');
    $image_url = sanitizeInput($input['image_url'] ?? '');
    $estimated_duration = intval($input['estimated_duration'] ?? 60);
    
    $stmt = $conn->prepare("
        INSERT INTO services (category_id, name, description, price, size_info, image_url, estimated_duration) 
        VALUES (?, ?, ?, ?, ?, ?, ?)
    ");
    $stmt->bind_param("issdsssi", $category_id, $name, $description, $price, $size_info, $image_url, $estimated_duration);
    
    if ($stmt->execute()) {
        $service_id = $conn->insert_id;
        echo json_encode([
            'success' => true,
            'message' => 'Service created successfully',
            'data' => ['service_id' => $service_id]
        ]);
    } else {
        http_response_code(500);
        echo json_encode(['success' => false, 'message' => 'Failed to create service']);
    }
}

function handleUpdateService($conn) {
    $service_id = $_GET['id'] ?? null;
    
    if (!$service_id) {
        http_response_code(400);
        echo json_encode(['success' => false, 'message' => 'Service ID is required']);
        return;
    }
    
    $input = json_decode(file_get_contents('php://input'), true);
    
    // Check if service exists
    $check_stmt = $conn->prepare("SELECT id FROM services WHERE id = ?");
    $check_stmt->bind_param("i", $service_id);
    $check_stmt->execute();
    
    if ($check_stmt->get_result()->num_rows === 0) {
        http_response_code(404);
        echo json_encode(['success' => false, 'message' => 'Service not found']);
        return;
    }
    
    // Build update query dynamically
    $update_fields = [];
    $types = "";
    $values = [];
    
    $allowed_fields = [
        'name' => 's',
        'description' => 's',
        'price' => 'd',
        'size_info' => 's',
        'image_url' => 's',
        'estimated_duration' => 'i',
        'is_active' => 'i'
    ];
    
    foreach ($allowed_fields as $field => $type) {
        if (isset($input[$field])) {
            $update_fields[] = "$field = ?";
            $types .= $type;
            if ($type === 's') {
                $values[] = sanitizeInput($input[$field]);
            } else {
                $values[] = $input[$field];
            }
        }
    }
    
    if (empty($update_fields)) {
        http_response_code(400);
        echo json_encode(['success' => false, 'message' => 'No valid fields to update']);
        return;
    }
    
    $update_fields[] = "updated_at = NOW()";
    $values[] = $service_id;
    $types .= "i";
    
    $sql = "UPDATE services SET " . implode(', ', $update_fields) . " WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param($types, ...$values);
    
    if ($stmt->execute()) {
        echo json_encode(['success' => true, 'message' => 'Service updated successfully']);
    } else {
        http_response_code(500);
        echo json_encode(['success' => false, 'message' => 'Failed to update service']);
    }
}

function handleDeleteService($conn) {
    $service_id = $_GET['id'] ?? null;
    
    if (!$service_id) {
        http_response_code(400);
        echo json_encode(['success' => false, 'message' => 'Service ID is required']);
        return;
    }
    
    // Soft delete by setting is_active to 0
    $stmt = $conn->prepare("UPDATE services SET is_active = 0, updated_at = NOW() WHERE id = ?");
    $stmt->bind_param("i", $service_id);
    
    if ($stmt->execute()) {
        if ($stmt->affected_rows > 0) {
            echo json_encode(['success' => true, 'message' => 'Service deleted successfully']);
        } else {
            http_response_code(404);
            echo json_encode(['success' => false, 'message' => 'Service not found']);
        }
    } else {
        http_response_code(500);
        echo json_encode(['success' => false, 'message' => 'Failed to delete service']);
    }
}

$database->closeConnection();
?>

