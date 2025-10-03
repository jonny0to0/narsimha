<?php
require_once 'auth.php';
AdminAuth::requireAuth();

require_once '../config/database.php';

$database = new Database();
$conn = $database->getConnection();

// Handle status updates
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_status'])) {
    $booking_id = intval($_POST['booking_id']);
    $new_status = sanitizeInput($_POST['status']);
    $notes = sanitizeInput($_POST['notes'] ?? '');
    
    $stmt = $conn->prepare("UPDATE bookings SET status = ?, notes = ?, updated_at = NOW() WHERE id = ?");
    $stmt->bind_param("ssi", $new_status, $notes, $booking_id);
    
    if ($stmt->execute()) {
        // Log status change
        $log_stmt = $conn->prepare("INSERT INTO booking_status_history (booking_id, new_status, notes, changed_by) VALUES (?, ?, ?, ?)");
        $admin_user = AdminAuth::getUsername();
        $log_stmt->bind_param("isss", $booking_id, $new_status, $notes, $admin_user);
        $log_stmt->execute();
        
        $success_message = "Booking status updated successfully!";
    } else {
        $error_message = "Failed to update booking status.";
    }
}

// Get filter parameters
$status_filter = $_GET['status'] ?? '';
$search = $_GET['search'] ?? '';
$page = max(1, intval($_GET['page'] ?? 1));
$per_page = 20;
$offset = ($page - 1) * $per_page;

// Build query
$where_conditions = [];
$params = [];
$types = "";

if ($status_filter) {
    $where_conditions[] = "b.status = ?";
    $params[] = $status_filter;
    $types .= "s";
}

if ($search) {
    $where_conditions[] = "(b.first_name LIKE ? OR b.last_name LIKE ? OR b.email LIKE ? OR b.booking_reference LIKE ?)";
    $search_term = "%$search%";
    $params = array_merge($params, [$search_term, $search_term, $search_term, $search_term]);
    $types .= "ssss";
}

$where_clause = $where_conditions ? "WHERE " . implode(" AND ", $where_conditions) : "";

// Get total count
$count_sql = "SELECT COUNT(*) as total FROM bookings b $where_clause";
$count_stmt = $conn->prepare($count_sql);
if ($params) {
    $count_stmt->bind_param($types, ...$params);
}
$count_stmt->execute();
$total_bookings = $count_stmt->get_result()->fetch_assoc()['total'];
$total_pages = ceil($total_bookings / $per_page);

// Get bookings
$sql = "
    SELECT b.*, a.name as artist_name, s.name as service_name 
    FROM bookings b 
    LEFT JOIN artists a ON b.preferred_artist_id = a.id 
    LEFT JOIN services s ON b.service_id = s.id 
    $where_clause 
    ORDER BY b.created_at DESC 
    LIMIT ? OFFSET ?
";

$params[] = $per_page;
$params[] = $offset;
$types .= "ii";

$stmt = $conn->prepare($sql);
$stmt->bind_param($types, ...$params);
$stmt->execute();
$bookings = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
?>
<!DOCTYPE html>
<html lang="en" class="h-full bg-gray-900">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Booking Management - Narshimha Tattoo</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;600;700;900&family=Dancing+Script:wght@400;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    fontFamily: {
                        'sans': ['Inter', 'sans-serif'],
                        'script': ['Dancing Script', 'cursive'],
                    },
                    colors: {
                        'neon-red': '#ff073a',
                        'blood-red': '#8b0000',
                        'dark-gray': '#1a1a1a',
                        'darker-gray': '#0d0d0d',
                    }
                }
            }
        }
    </script>
</head>
<body class="h-full">
    <div class="min-h-full">
        <!-- Navigation -->
        <nav class="bg-darker-gray border-b border-gray-800">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="flex justify-between h-16">
                    <div class="flex items-center">
                        <div class="flex-shrink-0">
                            <h1 class="text-xl font-bold text-neon-red font-script">Narshimha Tattoo</h1>
                        </div>
                        <div class="hidden md:block ml-10">
                            <div class="flex items-baseline space-x-4">
                                <a href="dashboard.php" class="text-gray-300 hover:bg-gray-700 hover:text-white px-3 py-2 rounded-md text-sm font-medium">Dashboard</a>
                                <a href="bookings.php" class="bg-gray-800 text-white px-3 py-2 rounded-md text-sm font-medium">Bookings</a>
                                <a href="services.php" class="text-gray-300 hover:bg-gray-700 hover:text-white px-3 py-2 rounded-md text-sm font-medium">Services</a>
                                <a href="artists.php" class="text-gray-300 hover:bg-gray-700 hover:text-white px-3 py-2 rounded-md text-sm font-medium">Artists</a>
                            </div>
                        </div>
                    </div>
                    <div class="flex items-center space-x-4">
                        <span class="text-gray-300 text-sm">Welcome, <?php echo AdminAuth::getUsername(); ?></span>
                        <a href="../index.html" target="_blank" class="text-gray-300 hover:text-white">
                            <i class="fas fa-external-link-alt"></i>
                        </a>
                        <a href="auth.php?logout=1" class="bg-neon-red hover:bg-red-700 text-white px-3 py-2 rounded-md text-sm font-medium">
                            <i class="fas fa-sign-out-alt mr-1"></i>
                            Logout
                        </a>
                    </div>
                </div>
            </div>
        </nav>

        <!-- Main content -->
        <main class="max-w-7xl mx-auto py-6 sm:px-6 lg:px-8">
            <div class="px-4 py-6 sm:px-0">
                <!-- Header -->
                <div class="mb-6">
                    <h1 class="text-2xl font-bold text-white">Booking Management</h1>
                    <p class="text-gray-400">Manage customer bookings and appointments</p>
                </div>

                <!-- Success/Error Messages -->
                <?php if (isset($success_message)): ?>
                    <div class="bg-green-900 border border-green-700 text-green-100 px-4 py-3 rounded mb-4">
                        <i class="fas fa-check-circle mr-2"></i>
                        <?php echo $success_message; ?>
                    </div>
                <?php endif; ?>

                <?php if (isset($error_message)): ?>
                    <div class="bg-red-900 border border-red-700 text-red-100 px-4 py-3 rounded mb-4">
                        <i class="fas fa-exclamation-triangle mr-2"></i>
                        <?php echo $error_message; ?>
                    </div>
                <?php endif; ?>

                <!-- Filters -->
                <div class="bg-dark-gray rounded-lg p-6 mb-6">
                    <form method="GET" class="flex flex-wrap gap-4 items-end">
                        <div>
                            <label class="block text-sm font-medium text-gray-300 mb-2">Status Filter</label>
                            <select name="status" class="bg-darker-gray border border-gray-700 text-white rounded-md px-3 py-2">
                                <option value="">All Statuses</option>
                                <option value="pending" <?php echo $status_filter === 'pending' ? 'selected' : ''; ?>>Pending</option>
                                <option value="confirmed" <?php echo $status_filter === 'confirmed' ? 'selected' : ''; ?>>Confirmed</option>
                                <option value="in_progress" <?php echo $status_filter === 'in_progress' ? 'selected' : ''; ?>>In Progress</option>
                                <option value="completed" <?php echo $status_filter === 'completed' ? 'selected' : ''; ?>>Completed</option>
                                <option value="cancelled" <?php echo $status_filter === 'cancelled' ? 'selected' : ''; ?>>Cancelled</option>
                            </select>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-300 mb-2">Search</label>
                            <input type="text" name="search" value="<?php echo htmlspecialchars($search); ?>" 
                                   placeholder="Name, email, or reference..." 
                                   class="bg-darker-gray border border-gray-700 text-white rounded-md px-3 py-2">
                        </div>
                        <div>
                            <button type="submit" class="bg-neon-red hover:bg-red-700 text-white px-4 py-2 rounded-md">
                                <i class="fas fa-search mr-1"></i>
                                Filter
                            </button>
                            <a href="bookings.php" class="bg-gray-700 hover:bg-gray-600 text-white px-4 py-2 rounded-md ml-2">
                                Clear
                            </a>
                        </div>
                    </form>
                </div>

                <!-- Bookings Table -->
                <div class="bg-dark-gray rounded-lg overflow-hidden">
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-700">
                            <thead class="bg-darker-gray">
                                <tr>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-300 uppercase tracking-wider">Customer</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-300 uppercase tracking-wider">Contact</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-300 uppercase tracking-wider">Service</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-300 uppercase tracking-wider">Artist</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-300 uppercase tracking-wider">Status</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-300 uppercase tracking-wider">Date</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-300 uppercase tracking-wider">Actions</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-700">
                                <?php foreach ($bookings as $booking): ?>
                                    <tr class="hover:bg-gray-800">
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <div>
                                                <div class="text-sm font-medium text-white">
                                                    <?php echo htmlspecialchars($booking['first_name'] . ' ' . $booking['last_name']); ?>
                                                </div>
                                                <div class="text-sm text-gray-400">
                                                    <?php echo htmlspecialchars($booking['booking_reference']); ?>
                                                </div>
                                            </div>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <div class="text-sm text-white"><?php echo htmlspecialchars($booking['email']); ?></div>
                                            <div class="text-sm text-gray-400"><?php echo htmlspecialchars($booking['phone']); ?></div>
                                        </td>
                                        <td class="px-6 py-4">
                                            <div class="text-sm text-white">
                                                <?php echo $booking['service_name'] ? htmlspecialchars($booking['service_name']) : 'Custom'; ?>
                                            </div>
                                            <div class="text-sm text-gray-400">
                                                <?php echo $booking['tattoo_style'] ? ucfirst($booking['tattoo_style']) : 'N/A'; ?>
                                            </div>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-white">
                                            <?php echo $booking['artist_name'] ? htmlspecialchars($booking['artist_name']) : 'No preference'; ?>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
                                                <?php 
                                                switch($booking['status']) {
                                                    case 'pending': echo 'bg-yellow-900 text-yellow-200'; break;
                                                    case 'confirmed': echo 'bg-green-900 text-green-200'; break;
                                                    case 'in_progress': echo 'bg-blue-900 text-blue-200'; break;
                                                    case 'completed': echo 'bg-purple-900 text-purple-200'; break;
                                                    case 'cancelled': echo 'bg-red-900 text-red-200'; break;
                                                    default: echo 'bg-gray-900 text-gray-200';
                                                }
                                                ?>">
                                                <?php echo ucfirst(str_replace('_', ' ', $booking['status'])); ?>
                                            </span>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-300">
                                            <?php echo date('M j, Y', strtotime($booking['created_at'])); ?>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                            <button onclick="openStatusModal(<?php echo $booking['id']; ?>, '<?php echo $booking['status']; ?>', '<?php echo htmlspecialchars($booking['notes'] ?? '', ENT_QUOTES); ?>')" 
                                                    class="text-neon-red hover:text-red-400 mr-3">
                                                <i class="fas fa-edit"></i>
                                            </button>
                                            <button onclick="viewBooking(<?php echo $booking['id']; ?>)" 
                                                    class="text-blue-400 hover:text-blue-300">
                                                <i class="fas fa-eye"></i>
                                            </button>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>

                    <!-- Pagination -->
                    <?php if ($total_pages > 1): ?>
                        <div class="bg-darker-gray px-6 py-3 flex items-center justify-between border-t border-gray-700">
                            <div class="text-sm text-gray-400">
                                Showing <?php echo $offset + 1; ?> to <?php echo min($offset + $per_page, $total_bookings); ?> of <?php echo $total_bookings; ?> results
                            </div>
                            <div class="flex space-x-1">
                                <?php if ($page > 1): ?>
                                    <a href="?<?php echo http_build_query(array_merge($_GET, ['page' => $page - 1])); ?>" 
                                       class="px-3 py-2 text-sm bg-gray-700 text-white rounded hover:bg-gray-600">Previous</a>
                                <?php endif; ?>
                                
                                <?php for ($i = max(1, $page - 2); $i <= min($total_pages, $page + 2); $i++): ?>
                                    <a href="?<?php echo http_build_query(array_merge($_GET, ['page' => $i])); ?>" 
                                       class="px-3 py-2 text-sm <?php echo $i === $page ? 'bg-neon-red text-white' : 'bg-gray-700 text-white hover:bg-gray-600'; ?> rounded">
                                        <?php echo $i; ?>
                                    </a>
                                <?php endfor; ?>
                                
                                <?php if ($page < $total_pages): ?>
                                    <a href="?<?php echo http_build_query(array_merge($_GET, ['page' => $page + 1])); ?>" 
                                       class="px-3 py-2 text-sm bg-gray-700 text-white rounded hover:bg-gray-600">Next</a>
                                <?php endif; ?>
                            </div>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </main>
    </div>

    <!-- Status Update Modal -->
    <div id="statusModal" class="fixed inset-0 bg-black bg-opacity-50 hidden items-center justify-center z-50">
        <div class="bg-dark-gray rounded-lg p-6 w-full max-w-md mx-4">
            <h3 class="text-lg font-medium text-white mb-4">Update Booking Status</h3>
            <form method="POST">
                <input type="hidden" id="modalBookingId" name="booking_id">
                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-300 mb-2">Status</label>
                    <select id="modalStatus" name="status" class="w-full bg-darker-gray border border-gray-700 text-white rounded-md px-3 py-2">
                        <option value="pending">Pending</option>
                        <option value="confirmed">Confirmed</option>
                        <option value="in_progress">In Progress</option>
                        <option value="completed">Completed</option>
                        <option value="cancelled">Cancelled</option>
                    </select>
                </div>
                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-300 mb-2">Notes</label>
                    <textarea id="modalNotes" name="notes" rows="3" 
                              class="w-full bg-darker-gray border border-gray-700 text-white rounded-md px-3 py-2" 
                              placeholder="Add notes about this status change..."></textarea>
                </div>
                <div class="flex justify-end space-x-3">
                    <button type="button" onclick="closeStatusModal()" 
                            class="px-4 py-2 text-gray-300 hover:text-white">Cancel</button>
                    <button type="submit" name="update_status" 
                            class="px-4 py-2 bg-neon-red hover:bg-red-700 text-white rounded-md">Update</button>
                </div>
            </form>
        </div>
    </div>

    <script>
        function openStatusModal(bookingId, currentStatus, currentNotes) {
            document.getElementById('modalBookingId').value = bookingId;
            document.getElementById('modalStatus').value = currentStatus;
            document.getElementById('modalNotes').value = currentNotes;
            document.getElementById('statusModal').classList.remove('hidden');
            document.getElementById('statusModal').classList.add('flex');
        }

        function closeStatusModal() {
            document.getElementById('statusModal').classList.add('hidden');
            document.getElementById('statusModal').classList.remove('flex');
        }

        function viewBooking(bookingId) {
            // This would open a detailed view modal
            alert('Detailed view for booking ID: ' + bookingId + '\n(Feature to be implemented)');
        }

        // Close modal when clicking outside
        document.getElementById('statusModal').addEventListener('click', function(e) {
            if (e.target === this) {
                closeStatusModal();
            }
        });
    </script>
</body>
</html>

