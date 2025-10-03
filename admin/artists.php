<?php
require_once 'auth.php';
AdminAuth::requireAuth();

require_once '../config/database.php';

$database = new Database();
$conn = $database->getConnection();

// Handle artist operations
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['add_artist'])) {
        $name = sanitizeInput($_POST['name']);
        $email = sanitizeInput($_POST['email']);
        $phone = sanitizeInput($_POST['phone']);
        $specialties = sanitizeInput($_POST['specialties']);
        $experience_years = intval($_POST['experience_years']);
        $bio = sanitizeInput($_POST['bio']);
        $image_url = sanitizeInput($_POST['image_url']);
        
        $stmt = $conn->prepare("INSERT INTO artists (name, email, phone, specialties, experience_years, bio, image_url) VALUES (?, ?, ?, ?, ?, ?, ?)");
        $stmt->bind_param("ssssiss", $name, $email, $phone, $specialties, $experience_years, $bio, $image_url);
        
        if ($stmt->execute()) {
            $success_message = "Artist added successfully!";
        } else {
            $error_message = "Failed to add artist.";
        }
    }
    
    if (isset($_POST['update_artist'])) {
        $artist_id = intval($_POST['artist_id']);
        $name = sanitizeInput($_POST['name']);
        $email = sanitizeInput($_POST['email']);
        $phone = sanitizeInput($_POST['phone']);
        $specialties = sanitizeInput($_POST['specialties']);
        $experience_years = intval($_POST['experience_years']);
        $bio = sanitizeInput($_POST['bio']);
        $image_url = sanitizeInput($_POST['image_url']);
        $is_active = isset($_POST['is_active']) ? 1 : 0;
        
        $stmt = $conn->prepare("UPDATE artists SET name = ?, email = ?, phone = ?, specialties = ?, experience_years = ?, bio = ?, image_url = ?, is_active = ?, updated_at = NOW() WHERE id = ?");
        $stmt->bind_param("sssssissi", $name, $email, $phone, $specialties, $experience_years, $bio, $image_url, $is_active, $artist_id);
        
        if ($stmt->execute()) {
            $success_message = "Artist updated successfully!";
        } else {
            $error_message = "Failed to update artist.";
        }
    }
}

// Get artists
$artists = [];
$result = $conn->query("SELECT * FROM artists ORDER BY name");
while ($row = $result->fetch_assoc()) {
    $artists[] = $row;
}

// Get booking counts for each artist
$booking_counts = [];
$result = $conn->query("
    SELECT preferred_artist_id, COUNT(*) as booking_count 
    FROM bookings 
    WHERE preferred_artist_id IS NOT NULL 
    GROUP BY preferred_artist_id
");
while ($row = $result->fetch_assoc()) {
    $booking_counts[$row['preferred_artist_id']] = $row['booking_count'];
}
?>
<!DOCTYPE html>
<html lang="en" class="h-full bg-gray-900">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Artist Management - Narshimha Tattoo</title>
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
                                <a href="services.php" class="text-gray-300 hover:bg-gray-700 hover:text-white px-3 py-2 rounded-md text-sm font-medium">Services</a>
                                <a href="artists.php" class="bg-gray-800 text-white px-3 py-2 rounded-md text-sm font-medium">Artists</a>
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
                        <h1 class="text-2xl font-bold text-white">Artist Management</h1>
                        <p class="text-gray-400">Manage tattoo artists and their profiles</p>
                    </div>
                    <button onclick="openAddModal()" class="bg-neon-red hover:bg-red-700 text-white px-4 py-2 rounded-md">
                        <i class="fas fa-plus mr-2"></i>
                        Add Artist
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

                <!-- Artists Grid -->
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                    <?php foreach ($artists as $artist): ?>
                        <div class="bg-dark-gray rounded-lg overflow-hidden <?php echo $artist['is_active'] ? '' : 'opacity-50'; ?>">
                            <div class="aspect-w-16 aspect-h-9">
                                <img src="<?php echo htmlspecialchars($artist['image_url']); ?>" 
                                     alt="<?php echo htmlspecialchars($artist['name']); ?>" 
                                     class="w-full h-48 object-cover">
                            </div>
                            <div class="p-6">
                                <div class="flex justify-between items-start mb-2">
                                    <h3 class="text-xl font-bold text-white"><?php echo htmlspecialchars($artist['name']); ?></h3>
                                    <span class="<?php echo $artist['is_active'] ? 'text-green-400' : 'text-red-400'; ?>">
                                        <i class="fas fa-<?php echo $artist['is_active'] ? 'check-circle' : 'times-circle'; ?>"></i>
                                    </span>
                                </div>
                                
                                <div class="mb-3">
                                    <p class="text-sm text-gray-400">
                                        <i class="fas fa-envelope mr-1"></i>
                                        <?php echo htmlspecialchars($artist['email']); ?>
                                    </p>
                                    <?php if ($artist['phone']): ?>
                                        <p class="text-sm text-gray-400">
                                            <i class="fas fa-phone mr-1"></i>
                                            <?php echo htmlspecialchars($artist['phone']); ?>
                                        </p>
                                    <?php endif; ?>
                                </div>

                                <div class="mb-3">
                                    <p class="text-sm text-gray-300 font-medium">Specialties:</p>
                                    <p class="text-sm text-gray-400"><?php echo htmlspecialchars($artist['specialties']); ?></p>
                                </div>

                                <div class="mb-4">
                                    <p class="text-sm text-gray-300 font-medium">Experience:</p>
                                    <p class="text-sm text-gray-400"><?php echo $artist['experience_years']; ?>+ years</p>
                                </div>

                                <?php if ($artist['bio']): ?>
                                    <div class="mb-4">
                                        <p class="text-sm text-gray-400 line-clamp-3"><?php echo htmlspecialchars($artist['bio']); ?></p>
                                    </div>
                                <?php endif; ?>

                                <div class="flex justify-between items-center">
                                    <div class="text-xs text-gray-500">
                                        <?php echo $booking_counts[$artist['id']] ?? 0; ?> bookings
                                    </div>
                                    <div class="flex space-x-2">
                                        <button onclick="editArtist(<?php echo htmlspecialchars(json_encode($artist)); ?>)" 
                                                class="text-blue-400 hover:text-blue-300">
                                            <i class="fas fa-edit"></i>
                                        </button>
                                        <button onclick="viewBookings(<?php echo $artist['id']; ?>)" 
                                                class="text-green-400 hover:text-green-300">
                                            <i class="fas fa-calendar-alt"></i>
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
        </main>
    </div>

    <!-- Add/Edit Artist Modal -->
    <div id="artistModal" class="fixed inset-0 bg-black bg-opacity-50 hidden items-center justify-center z-50">
        <div class="bg-dark-gray rounded-lg p-6 w-full max-w-2xl mx-4 max-h-[90vh] overflow-y-auto">
            <h3 id="modalTitle" class="text-lg font-medium text-white mb-4">Add New Artist</h3>
            <form method="POST" id="artistForm">
                <input type="hidden" id="artistId" name="artist_id">
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-300 mb-2">Full Name *</label>
                        <input type="text" id="artistName" name="name" required 
                               class="w-full bg-darker-gray border border-gray-700 text-white rounded-md px-3 py-2">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-300 mb-2">Email</label>
                        <input type="email" id="email" name="email" 
                               class="w-full bg-darker-gray border border-gray-700 text-white rounded-md px-3 py-2">
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-300 mb-2">Phone</label>
                        <input type="tel" id="phone" name="phone" 
                               class="w-full bg-darker-gray border border-gray-700 text-white rounded-md px-3 py-2">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-300 mb-2">Experience (years)</label>
                        <input type="number" id="experienceYears" name="experience_years" min="0" max="50" value="0"
                               class="w-full bg-darker-gray border border-gray-700 text-white rounded-md px-3 py-2">
                    </div>
                </div>

                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-300 mb-2">Specialties</label>
                    <input type="text" id="specialties" name="specialties" 
                           placeholder="e.g., Blackwork, Realism, Portraits"
                           class="w-full bg-darker-gray border border-gray-700 text-white rounded-md px-3 py-2">
                </div>

                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-300 mb-2">Bio</label>
                    <textarea id="bio" name="bio" rows="4" 
                              placeholder="Tell us about the artist's background and style..."
                              class="w-full bg-darker-gray border border-gray-700 text-white rounded-md px-3 py-2"></textarea>
                </div>

                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-300 mb-2">Profile Image URL</label>
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
                    <button type="button" onclick="closeArtistModal()" 
                            class="px-4 py-2 text-gray-300 hover:text-white">Cancel</button>
                    <button type="submit" id="submitBtn" name="add_artist" 
                            class="px-4 py-2 bg-neon-red hover:bg-red-700 text-white rounded-md">Add Artist</button>
                </div>
            </form>
        </div>
    </div>

    <script>
        function openAddModal() {
            document.getElementById('modalTitle').textContent = 'Add New Artist';
            document.getElementById('artistForm').reset();
            document.getElementById('artistId').value = '';
            document.getElementById('submitBtn').name = 'add_artist';
            document.getElementById('submitBtn').textContent = 'Add Artist';
            document.getElementById('activeCheckbox').classList.add('hidden');
            document.getElementById('artistModal').classList.remove('hidden');
            document.getElementById('artistModal').classList.add('flex');
        }

        function editArtist(artist) {
            document.getElementById('modalTitle').textContent = 'Edit Artist';
            document.getElementById('artistId').value = artist.id;
            document.getElementById('artistName').value = artist.name;
            document.getElementById('email').value = artist.email || '';
            document.getElementById('phone').value = artist.phone || '';
            document.getElementById('specialties').value = artist.specialties || '';
            document.getElementById('experienceYears').value = artist.experience_years;
            document.getElementById('bio').value = artist.bio || '';
            document.getElementById('imageUrl').value = artist.image_url || '';
            document.getElementById('isActive').checked = artist.is_active == 1;
            document.getElementById('submitBtn').name = 'update_artist';
            document.getElementById('submitBtn').textContent = 'Update Artist';
            document.getElementById('activeCheckbox').classList.remove('hidden');
            document.getElementById('artistModal').classList.remove('hidden');
            document.getElementById('artistModal').classList.add('flex');
        }

        function closeArtistModal() {
            document.getElementById('artistModal').classList.add('hidden');
            document.getElementById('artistModal').classList.remove('flex');
        }

        function viewBookings(artistId) {
            window.open('bookings.php?artist_id=' + artistId, '_blank');
        }

        // Close modal when clicking outside
        document.getElementById('artistModal').addEventListener('click', function(e) {
            if (e.target === this) {
                closeArtistModal();
            }
        });
    </script>
</body>
</html>

