<?php
require_once 'auth.php';
AdminAuth::requireAuth();

require_once '../config/database.php';

$database = new Database();
$conn = $database->getConnection();

// Handle service operations
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['add_service'])) {
        $category_id = intval($_POST['category_id']);
        $name = sanitizeInput($_POST['name']);
        $description = sanitizeInput($_POST['description']);
        $price = floatval($_POST['price']);
        $size_info = sanitizeInput($_POST['size_info']);
        $image_url = sanitizeInput($_POST['image_url']);
        $estimated_duration = intval($_POST['estimated_duration']);
        
        $stmt = $conn->prepare("INSERT INTO services (category_id, name, description, price, size_info, image_url, estimated_duration) VALUES (?, ?, ?, ?, ?, ?, ?)");
        $stmt->bind_param("issdsssi", $category_id, $name, $description, $price, $size_info, $image_url, $estimated_duration);
        
        if ($stmt->execute()) {
            $success_message = "Service added successfully!";
        } else {
            $error_message = "Failed to add service.";
        }
    }
    
    if (isset($_POST['update_service'])) {
        $service_id = intval($_POST['service_id']);
        $name = sanitizeInput($_POST['name']);
        $description = sanitizeInput($_POST['description']);
        $price = floatval($_POST['price']);
        $size_info = sanitizeInput($_POST['size_info']);
        $image_url = sanitizeInput($_POST['image_url']);
        $estimated_duration = intval($_POST['estimated_duration']);
        $is_active = isset($_POST['is_active']) ? 1 : 0;
        
        $stmt = $conn->prepare("UPDATE services SET name = ?, description = ?, price = ?, size_info = ?, image_url = ?, estimated_duration = ?, is_active = ?, updated_at = NOW() WHERE id = ?");
        $stmt->bind_param("ssdsssii", $name, $description, $price, $size_info, $image_url, $estimated_duration, $is_active, $service_id);
        
        if ($stmt->execute()) {
            $success_message = "Service updated successfully!";
        } else {
            $error_message = "Failed to update service.";
        }
    }
}

// Get categories for dropdown
$categories = [];
$result = $conn->query("SELECT * FROM service_categories WHERE is_active = 1 ORDER BY name");
while ($row = $result->fetch_assoc()) {
    $categories[] = $row;
}

// Get services with category info
$services = [];
$result = $conn->query("
    SELECT s.*, sc.name as category_name 
    FROM services s 
    LEFT JOIN service_categories sc ON s.category_id = sc.id 
    ORDER BY sc.name, s.name
");
while ($row = $result->fetch_assoc()) {
    $services[] = $row;
}
?>
<!DOCTYPE html>
<html lang="en" class="h-full bg-gray-900">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Service Management - Narshimha Tattoo</title>
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
                                <a href="bookings.php" class="text-gray-300 hover:bg-gray-700 hover:text-white px-3 py-2 rounded-md text-sm font-medium">Bookings</a>
                                <a href="services.php" class="bg-gray-800 text-white px-3 py-2 rounded-md text-sm font-medium">Services</a>
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
                <div class="flex justify-between items-center mb-6">
                    <div>
                        <h1 class="text-2xl font-bold text-white">Service Management</h1>
                        <p class="text-gray-400">Manage tattoo services and categories</p>
                    </div>
                    <button onclick="openAddModal()" class="bg-neon-red hover:bg-red-700 text-white px-4 py-2 rounded-md">
                        <i class="fas fa-plus mr-2"></i>
                        Add Service
                    </button>
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

                <!-- Services Grid -->
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                    <?php foreach ($services as $service): ?>
                        <div class="bg-dark-gray rounded-lg overflow-hidden <?php echo $service['is_active'] ? '' : 'opacity-50'; ?>">
                            <div class="aspect-w-16 aspect-h-9">
                                <img src="<?php echo htmlspecialchars($service['image_url']); ?>" 
                                     alt="<?php echo htmlspecialchars($service['name']); ?>" 
                                     class="w-full h-48 object-cover">
                            </div>
                            <div class="p-4">
                                <div class="flex justify-between items-start mb-2">
                                    <h3 class="text-lg font-semibold text-white"><?php echo htmlspecialchars($service['name']); ?></h3>
                                    <span class="text-neon-red font-bold">$<?php echo number_format($service['price'], 2); ?></span>
                                </div>
                                <p class="text-sm text-gray-400 mb-2"><?php echo htmlspecialchars($service['category_name']); ?></p>
                                <p class="text-sm text-gray-300 mb-2"><?php echo htmlspecialchars($service['size_info']); ?></p>
                                <p class="text-sm text-gray-400 mb-4"><?php echo htmlspecialchars($service['description']); ?></p>
                                <div class="flex justify-between items-center">
                                    <div class="text-xs text-gray-500">
                                        <?php echo $service['estimated_duration']; ?> min
                                    </div>
                                    <div class="flex space-x-2">
                                        <button onclick="editService(<?php echo htmlspecialchars(json_encode($service)); ?>)" 
                                                class="text-blue-400 hover:text-blue-300">
                                            <i class="fas fa-edit"></i>
                                        </button>
                                        <span class="<?php echo $service['is_active'] ? 'text-green-400' : 'text-red-400'; ?>">
                                            <i class="fas fa-<?php echo $service['is_active'] ? 'eye' : 'eye-slash'; ?>"></i>
                                        </span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
        </main>
    </div>

    <!-- Add/Edit Service Modal -->
    <div id="serviceModal" class="fixed inset-0 bg-black bg-opacity-50 hidden items-center justify-center z-50">
        <div class="bg-dark-gray rounded-lg p-6 w-full max-w-2xl mx-4 max-h-[90vh] overflow-y-auto">
            <h3 id="modalTitle" class="text-lg font-medium text-white mb-4">Add New Service</h3>
            <form method="POST" id="serviceForm">
                <input type="hidden" id="serviceId" name="service_id">
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-300 mb-2">Category</label>
                        <select id="categoryId" name="category_id" required class="w-full bg-darker-gray border border-gray-700 text-white rounded-md px-3 py-2">
                            <option value="">Select Category</option>
                            <?php foreach ($categories as $category): ?>
                                <option value="<?php echo $category['id']; ?>"><?php echo htmlspecialchars($category['name']); ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-300 mb-2">Price ($)</label>
                        <input type="number" id="price" name="price" step="0.01" min="0" required 
                               class="w-full bg-darker-gray border border-gray-700 text-white rounded-md px-3 py-2">
                    </div>
                </div>

                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-300 mb-2">Service Name</label>
                    <input type="text" id="serviceName" name="name" required 
                           class="w-full bg-darker-gray border border-gray-700 text-white rounded-md px-3 py-2">
                </div>

                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-300 mb-2">Description</label>
                    <textarea id="description" name="description" rows="3" 
                              class="w-full bg-darker-gray border border-gray-700 text-white rounded-md px-3 py-2"></textarea>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-300 mb-2">Size Info</label>
                        <input type="text" id="sizeInfo" name="size_info" 
                               placeholder="e.g., Small (2-3 inches)"
                               class="w-full bg-darker-gray border border-gray-700 text-white rounded-md px-3 py-2">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-300 mb-2">Duration (minutes)</label>
                        <input type="number" id="estimatedDuration" name="estimated_duration" min="15" step="15" value="60"
                               class="w-full bg-darker-gray border border-gray-700 text-white rounded-md px-3 py-2">
                    </div>
                </div>

                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-300 mb-2">Image URL</label>
                    <input type="url" id="imageUrl" name="image_url" 
                           class="w-full bg-darker-gray border border-gray-700 text-white rounded-md px-3 py-2">
                </div>

                <div id="activeCheckbox" class="mb-4 hidden">
                    <label class="flex items-center">
                        <input type="checkbox" id="isActive" name="is_active" class="mr-2">
                        <span class="text-sm text-gray-300">Active (visible to customers)</span>
                    </label>
                </div>

                <div class="flex justify-end space-x-3">
                    <button type="button" onclick="closeServiceModal()" 
                            class="px-4 py-2 text-gray-300 hover:text-white">Cancel</button>
                    <button type="submit" id="submitBtn" name="add_service" 
                            class="px-4 py-2 bg-neon-red hover:bg-red-700 text-white rounded-md">Add Service</button>
                </div>
            </form>
        </div>
    </div>

    <script>
        function openAddModal() {
            document.getElementById('modalTitle').textContent = 'Add New Service';
            document.getElementById('serviceForm').reset();
            document.getElementById('serviceId').value = '';
            document.getElementById('submitBtn').name = 'add_service';
            document.getElementById('submitBtn').textContent = 'Add Service';
            document.getElementById('activeCheckbox').classList.add('hidden');
            document.getElementById('serviceModal').classList.remove('hidden');
            document.getElementById('serviceModal').classList.add('flex');
        }

        function editService(service) {
            document.getElementById('modalTitle').textContent = 'Edit Service';
            document.getElementById('serviceId').value = service.id;
            document.getElementById('categoryId').value = service.category_id;
            document.getElementById('serviceName').value = service.name;
            document.getElementById('description').value = service.description || '';
            document.getElementById('price').value = service.price;
            document.getElementById('sizeInfo').value = service.size_info || '';
            document.getElementById('estimatedDuration').value = service.estimated_duration;
            document.getElementById('imageUrl').value = service.image_url || '';
            document.getElementById('isActive').checked = service.is_active == 1;
            document.getElementById('submitBtn').name = 'update_service';
            document.getElementById('submitBtn').textContent = 'Update Service';
            document.getElementById('activeCheckbox').classList.remove('hidden');
            document.getElementById('serviceModal').classList.remove('hidden');
            document.getElementById('serviceModal').classList.add('flex');
        }

        function closeServiceModal() {
            document.getElementById('serviceModal').classList.add('hidden');
            document.getElementById('serviceModal').classList.remove('flex');
        }

        // Close modal when clicking outside
        document.getElementById('serviceModal').addEventListener('click', function(e) {
            if (e.target === this) {
                closeServiceModal();
            }
        });
    </script>
</body>
</html>

