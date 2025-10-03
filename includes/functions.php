<?php
/**
 * Common functions for Narshimha Tattoo Studio
 */

/**
 * Sanitize input data
 */
function sanitizeInput($data) {
    if (is_array($data)) {
        return array_map('sanitizeInput', $data);
    }
    
    $data = trim($data);
    $data = stripslashes($data);
    $data = htmlspecialchars($data, ENT_QUOTES, 'UTF-8');
    return $data;
}

/**
 * Validate email address
 */
function validateEmail($email) {
    return filter_var($email, FILTER_VALIDATE_EMAIL) !== false;
}

/**
 * Validate phone number (basic validation)
 */
function validatePhone($phone) {
    // Remove all non-digit characters
    $phone = preg_replace('/[^0-9]/', '', $phone);
    // Check if it's between 10-15 digits
    return strlen($phone) >= 10 && strlen($phone) <= 15;
}

/**
 * Generate unique session ID
 */
function generateSessionId() {
    return 'cart_' . uniqid() . '_' . time();
}

/**
 * Generate booking reference
 */
function generateBookingReference() {
    return 'NT' . date('Y') . str_pad(rand(1, 9999), 4, '0', STR_PAD_LEFT);
}

/**
 * Format price for display
 */
function formatPrice($price) {
    return '$' . number_format($price, 2);
}

/**
 * Format date for display
 */
function formatDate($date, $format = 'M j, Y') {
    return date($format, strtotime($date));
}

/**
 * Format time for display
 */
function formatTime($time, $format = 'g:i A') {
    return date($format, strtotime($time));
}

/**
 * Check if date is within business hours
 */
function isBusinessDay($date) {
    $day_of_week = date('N', strtotime($date)); // 1 = Monday, 7 = Sunday
    return in_array($day_of_week, BUSINESS_DAYS);
}

/**
 * Check if time is within business hours
 */
function isBusinessHours($time) {
    $hour = intval(date('H', strtotime($time)));
    return $hour >= BUSINESS_START && $hour < BUSINESS_END;
}

/**
 * Get available time slots for a given date
 */
function getAvailableTimeSlots($conn, $date, $artist_id = null) {
    $slots = [];
    $start_hour = BUSINESS_START;
    $end_hour = BUSINESS_END;
    $slot_duration = SLOT_DURATION; // minutes
    
    // Generate all possible slots
    for ($hour = $start_hour; $hour < $end_hour; $hour++) {
        for ($minute = 0; $minute < 60; $minute += $slot_duration) {
            $time = sprintf('%02d:%02d:00', $hour, $minute);
            $slots[] = $time;
        }
    }
    
    // Remove booked slots
    $booked_query = "
        SELECT preferred_time 
        FROM bookings 
        WHERE preferred_date = ? 
        AND status IN ('confirmed', 'in_progress') 
        AND preferred_time IS NOT NULL
    ";
    
    $params = [$date];
    $types = "s";
    
    if ($artist_id) {
        $booked_query .= " AND preferred_artist_id = ?";
        $params[] = $artist_id;
        $types .= "i";
    }
    
    $stmt = $conn->prepare($booked_query);
    $stmt->bind_param($types, ...$params);
    $stmt->execute();
    $result = $stmt->get_result();
    
    $booked_times = [];
    while ($row = $result->fetch_assoc()) {
        $booked_times[] = $row['preferred_time'];
    }
    
    // Filter out booked slots
    $available_slots = array_diff($slots, $booked_times);
    
    return array_values($available_slots);
}

/**
 * Send email using PHP mail function
 */
function sendEmail($to, $subject, $message, $headers = null) {
    if (!$headers) {
        $headers = "From: " . FROM_NAME . " <" . FROM_EMAIL . ">\r\n";
        $headers .= "Reply-To: " . FROM_EMAIL . "\r\n";
        $headers .= "Content-Type: text/html; charset=UTF-8\r\n";
    }
    
    return mail($to, $subject, $message, $headers);
}

/**
 * Send booking confirmation email
 */
function sendBookingConfirmationEmail($booking_data, $booking_reference) {
    $to = $booking_data['email'];
    $subject = "Booking Confirmation - " . STUDIO_NAME;
    
    $message = "
    <html>
    <head>
        <title>Booking Confirmation</title>
        <style>
            body { font-family: Arial, sans-serif; line-height: 1.6; color: #333; }
            .header { background-color: #ff073a; color: white; padding: 20px; text-align: center; }
            .content { padding: 20px; }
            .booking-details { background-color: #f9f9f9; padding: 15px; margin: 20px 0; border-radius: 5px; }
            .footer { background-color: #1a1a1a; color: white; padding: 20px; text-align: center; }
        </style>
    </head>
    <body>
        <div class='header'>
            <h1>" . STUDIO_NAME . "</h1>
            <p>Booking Confirmation</p>
        </div>
        
        <div class='content'>
            <h2>Thank you for your booking request!</h2>
            <p>Dear " . $booking_data['firstName'] . " " . $booking_data['lastName'] . ",</p>
            <p>We have received your booking request and will contact you within 24 hours to confirm your appointment.</p>
            
            <div class='booking-details'>
                <h3>Booking Details:</h3>
                <p><strong>Reference Number:</strong> " . $booking_reference . "</p>
                <p><strong>Name:</strong> " . $booking_data['firstName'] . " " . $booking_data['lastName'] . "</p>
                <p><strong>Email:</strong> " . $booking_data['email'] . "</p>
                <p><strong>Phone:</strong> " . $booking_data['phone'] . "</p>
                <p><strong>Description:</strong> " . nl2br($booking_data['description']) . "</p>
            </div>
            
            <p>If you have any questions, please don't hesitate to contact us:</p>
            <p>Phone: " . STUDIO_PHONE . "<br>
            Email: " . STUDIO_EMAIL . "<br>
            Address: " . STUDIO_ADDRESS . "</p>
        </div>
        
        <div class='footer'>
            <p>&copy; 2024 " . STUDIO_NAME . ". All rights reserved.</p>
        </div>
    </body>
    </html>
    ";
    
    return sendEmail($to, $subject, $message);
}

/**
 * Log error to file
 */
function logError($message, $file = 'error.log') {
    $timestamp = date('Y-m-d H:i:s');
    $log_message = "[$timestamp] $message" . PHP_EOL;
    error_log($log_message, 3, $file);
}

/**
 * Generate API response
 */
function apiResponse($success, $message, $data = null, $http_code = 200) {
    http_response_code($http_code);
    
    $response = [
        'success' => $success,
        'message' => $message
    ];
    
    if ($data !== null) {
        $response['data'] = $data;
    }
    
    return json_encode($response);
}

/**
 * Validate required fields
 */
function validateRequiredFields($data, $required_fields) {
    $missing_fields = [];
    
    foreach ($required_fields as $field) {
        if (empty($data[$field])) {
            $missing_fields[] = $field;
        }
    }
    
    return $missing_fields;
}

/**
 * Check if date is in the future
 */
function isFutureDate($date) {
    return strtotime($date) > time();
}

/**
 * Check if booking is within advance notice period
 */
function isValidAdvanceNotice($date, $time = null) {
    $booking_datetime = $date;
    if ($time) {
        $booking_datetime .= ' ' . $time;
    }
    
    $booking_timestamp = strtotime($booking_datetime);
    $min_advance = time() + (MIN_ADVANCE_HOURS * 3600);
    $max_advance = time() + (MAX_ADVANCE_DAYS * 24 * 3600);
    
    return $booking_timestamp >= $min_advance && $booking_timestamp <= $max_advance;
}

/**
 * Get client IP address
 */
function getClientIP() {
    $ip_keys = ['HTTP_CLIENT_IP', 'HTTP_X_FORWARDED_FOR', 'REMOTE_ADDR'];
    
    foreach ($ip_keys as $key) {
        if (array_key_exists($key, $_SERVER) === true) {
            foreach (explode(',', $_SERVER[$key]) as $ip) {
                $ip = trim($ip);
                if (filter_var($ip, FILTER_VALIDATE_IP, FILTER_FLAG_NO_PRIV_RANGE | FILTER_FLAG_NO_RES_RANGE) !== false) {
                    return $ip;
                }
            }
        }
    }
    
    return $_SERVER['REMOTE_ADDR'] ?? '0.0.0.0';
}

/**
 * Rate limiting check
 */
function checkRateLimit($conn, $ip, $action, $limit = 10, $window = 3600) {
    // This would implement rate limiting logic
    // For now, just return true
    return true;
}

/**
 * Clean filename for uploads
 */
function cleanFilename($filename) {
    $filename = preg_replace('/[^a-zA-Z0-9._-]/', '', $filename);
    return $filename;
}

/**
 * Get file extension
 */
function getFileExtension($filename) {
    return strtolower(pathinfo($filename, PATHINFO_EXTENSION));
}

/**
 * Check if file type is allowed
 */
function isAllowedFileType($filename, $allowed_types = ['jpg', 'jpeg', 'png', 'gif', 'webp']) {
    $extension = getFileExtension($filename);
    return in_array($extension, $allowed_types);
}

?>

