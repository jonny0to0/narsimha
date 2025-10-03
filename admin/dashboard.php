<?php
require_once 'auth.php';
AdminAuth::requireAuth();

require_once '../config/database.php';

$database = new Database();
$conn = $database->getConnection();

// Get dashboard statistics
$stats = [];

// Total bookings
$result = $conn->query("SELECT COUNT(*) as count FROM bookings");
$stats['total_bookings'] = $result->fetch_assoc()['count'];

// Pending bookings
$result = $conn->query("SELECT COUNT(*) as count FROM bookings WHERE status = 'pending'");
$stats['pending_bookings'] = $result->fetch_assoc()['count'];

// Confirmed bookings
$result = $conn->query("SELECT COUNT(*) as count FROM bookings WHERE status = 'confirmed'");
$stats['confirmed_bookings'] = $result->fetch_assoc()['count'];

// Total services
$result = $conn->query("SELECT COUNT(*) as count FROM services WHERE is_active = 1");
$stats['total_services'] = $result->fetch_assoc()['count'];

// Recent bookings
$recent_bookings = [];
$result = $conn->query("
    SELECT b.*, a.name as artist_name 
    FROM bookings b 
    LEFT JOIN artists a ON b.preferred_artist_id = a.id 
    ORDER BY b.created_at DESC 
    LIMIT 10
");
while ($row = $result->fetch_assoc()) {
    $recent_bookings[] = $row;
}

// Booking status distribution
$status_stats = [];
$result = $conn->query("
    SELECT status, COUNT(*) as count 
    FROM bookings 
    GROUP BY status 
    ORDER BY count DESC
");
while ($row = $result->fetch_assoc()) {
    $status_stats[] = $row;
}
?>
<!DOCTYPE html>
<html lang="en" class="h-full bg-gray-900">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard - Narshimha Tattoo</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;600;700;900&family=Dancing+Script:wght@400;600;700&family=Orbitron:wght@400;700;900&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    fontFamily: {
                        'sans': ['Inter', 'sans-serif'],
                        'script': ['Dancing Script', 'cursive'],
                        'orbitron': ['Orbitron', 'monospace'],
                    },
                    colors: {
                        'neon-red': '#ff073a',
                        'blood-red': '#8b0000',
                        'dark-gray': '#1a1a1a',
                        'darker-gray': '#0d0d0d',
                        'electric-blue': '#00d4ff',
                        'purple-glow': '#8a2be2',
                    }
                }
            }
        }
    </script>
    <style>
        .glass-morphism {
            background: rgba(26, 26, 26, 0.25);
            backdrop-filter: blur(20px);
            border: 1px solid rgba(255, 255, 255, 0.1);
        }
        
        .neon-glow {
            box-shadow: 0 0 20px rgba(255, 7, 58, 0.5);
        }
        
        .gradient-text {
            background: linear-gradient(45deg, #ff073a, #00d4ff, #8a2be2);
            background-size: 300% 300%;
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
            animation: gradient-shift 4s ease infinite;
        }
        
        @keyframes gradient-shift {
            0%, 100% { background-position: 0% 50%; }
            50% { background-position: 100% 50%; }
        }
        
        .cyber-grid {
            background-image: 
                linear-gradient(rgba(255, 7, 58, 0.05) 1px, transparent 1px),
                linear-gradient(90deg, rgba(255, 7, 58, 0.05) 1px, transparent 1px);
            background-size: 50px 50px;
        }
        
        .hover-lift {
            transition: all 0.3s cubic-bezier(0.25, 0.46, 0.45, 0.94);
        }
        
        .hover-lift:hover {
            transform: translateY(-5px);
            box-shadow: 0 20px 40px rgba(0, 0, 0, 0.3);
        }
        
        .pulse-glow {
            animation: pulse-glow 2s ease-in-out infinite alternate;
        }
        
        @keyframes pulse-glow {
            from { box-shadow: 0 0 20px rgba(255, 7, 58, 0.5); }
            to { box-shadow: 0 0 30px rgba(255, 7, 58, 0.8), 0 0 60px rgba(255, 7, 58, 0.5); }
        }
    </style>
</head>
<body class="h-full cyber-grid">
    <div class="min-h-full">
        <!-- Navigation -->
        <nav class="glass-morphism border-b border-gray-700/50 sticky top-0 z-50">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="flex justify-between h-16">
                    <div class="flex items-center">
                        <div class="flex-shrink-0">
                            <h1 class="text-2xl font-bold gradient-text font-script">Narshimha Tattoo</h1>
                        </div>
                        <div class="hidden md:block ml-10">
                            <div class="flex items-baseline space-x-2">
                                <a href="dashboard.php" class="bg-neon-red/20 text-neon-red border border-neon-red/30 px-4 py-2 rounded-lg text-sm font-medium neon-glow">
                                    <i class="fas fa-tachometer-alt mr-2"></i>Dashboard
                                </a>
                                <a href="bookings.php" class="text-gray-300 hover:bg-gray-700/50 hover:text-white px-4 py-2 rounded-lg text-sm font-medium transition-all">
                                    <i class="fas fa-calendar-check mr-2"></i>Bookings
                                </a>
                                <a href="services.php" class="text-gray-300 hover:bg-gray-700/50 hover:text-white px-4 py-2 rounded-lg text-sm font-medium transition-all">
                                    <i class="fas fa-paint-brush mr-2"></i>Services
                                </a>
                                <a href="artists.php" class="text-gray-300 hover:bg-gray-700/50 hover:text-white px-4 py-2 rounded-lg text-sm font-medium transition-all">
                                    <i class="fas fa-users mr-2"></i>Artists
                                </a>
                            </div>
                        </div>
                    </div>
                    <div class="flex items-center space-x-4">
                        <div class="hidden sm:flex items-center space-x-2 text-gray-300 text-sm">
                            <i class="fas fa-user-circle text-neon-red"></i>
                            <span>Welcome, <span class="font-semibold text-white"><?php echo AdminAuth::getUsername(); ?></span></span>
                        </div>
                        <a href="../index.html" target="_blank" class="text-gray-300 hover:text-neon-red transition-colors p-2 rounded-lg hover:bg-gray-700/50">
                            <i class="fas fa-external-link-alt"></i>
                        </a>
                        <a href="auth.php?logout=1" class="bg-gradient-to-r from-neon-red to-red-600 hover:from-red-600 hover:to-neon-red text-white px-4 py-2 rounded-lg text-sm font-medium transition-all hover:scale-105">
                            <i class="fas fa-sign-out-alt mr-1"></i>
                            Logout
                        </a>
                    </div>
                </div>
            </div>
        </nav>

        <!-- Main content -->
        <main class="max-w-7xl mx-auto py-6 sm:px-6 lg:px-8">
            <!-- Dashboard header -->
            <div class="px-4 py-6 sm:px-0">
                <div class="glass-morphism rounded-2xl p-8 hover-lift">
                    <div class="flex items-center justify-between">
                        <div>
                            <h1 class="text-4xl font-bold gradient-text font-orbitron mb-2">DASHBOARD OVERVIEW</h1>
                            <p class="text-gray-300 text-lg">Welcome to the Narshimha Tattoo command center</p>
                        </div>
                        <div class="text-right">
                            <div class="text-sm text-gray-400">Last Login</div>
                            <div class="text-neon-red font-semibold"><?php echo date('M j, Y g:i A', AdminAuth::getLoginTime()); ?></div>
                        </div>
                    </div>
                    <div class="mt-6 flex space-x-4">
                        <div class="h-1 bg-neon-red rounded flex-1"></div>
                        <div class="h-1 bg-electric-blue rounded flex-1"></div>
                        <div class="h-1 bg-purple-glow rounded flex-1"></div>
                    </div>
                </div>
            </div>

            <!-- Stats grid -->
            <div class="px-4 py-6 sm:px-0">
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
                    <!-- Total Bookings -->
                    <div class="glass-morphism rounded-xl p-6 hover-lift group">
                        <div class="flex items-center">
                            <div class="flex-shrink-0">
                                <div class="w-12 h-12 bg-gradient-to-r from-blue-500 to-blue-600 rounded-xl flex items-center justify-center group-hover:scale-110 transition-transform neon-glow">
                                    <i class="fas fa-calendar-alt text-white text-lg"></i>
                                </div>
                            </div>
                            <div class="ml-4">
                                <p class="text-sm font-medium text-gray-400 uppercase tracking-wide">Total Bookings</p>
                                <p class="text-3xl font-bold text-white font-orbitron"><?php echo $stats['total_bookings']; ?></p>
                                <p class="text-xs text-blue-400 mt-1">All time</p>
                            </div>
                        </div>
                    </div>

                    <!-- Pending Bookings -->
                    <div class="glass-morphism rounded-xl p-6 hover-lift group">
                        <div class="flex items-center">
                            <div class="flex-shrink-0">
                                <div class="w-12 h-12 bg-gradient-to-r from-yellow-500 to-orange-500 rounded-xl flex items-center justify-center group-hover:scale-110 transition-transform pulse-glow">
                                    <i class="fas fa-clock text-white text-lg"></i>
                                </div>
                            </div>
                            <div class="ml-4">
                                <p class="text-sm font-medium text-gray-400 uppercase tracking-wide">Pending</p>
                                <p class="text-3xl font-bold text-white font-orbitron"><?php echo $stats['pending_bookings']; ?></p>
                                <p class="text-xs text-yellow-400 mt-1">Needs attention</p>
                            </div>
                        </div>
                    </div>

                    <!-- Confirmed Bookings -->
                    <div class="glass-morphism rounded-xl p-6 hover-lift group">
                        <div class="flex items-center">
                            <div class="flex-shrink-0">
                                <div class="w-12 h-12 bg-gradient-to-r from-green-500 to-emerald-500 rounded-xl flex items-center justify-center group-hover:scale-110 transition-transform">
                                    <i class="fas fa-check text-white text-lg"></i>
                                </div>
                            </div>
                            <div class="ml-4">
                                <p class="text-sm font-medium text-gray-400 uppercase tracking-wide">Confirmed</p>
                                <p class="text-3xl font-bold text-white font-orbitron"><?php echo $stats['confirmed_bookings']; ?></p>
                                <p class="text-xs text-green-400 mt-1">Ready to go</p>
                            </div>
                        </div>
                    </div>

                    <!-- Total Services -->
                    <div class="glass-morphism rounded-xl p-6 hover-lift group">
                        <div class="flex items-center">
                            <div class="flex-shrink-0">
                                <div class="w-12 h-12 bg-gradient-to-r from-neon-red to-pink-500 rounded-xl flex items-center justify-center group-hover:scale-110 transition-transform neon-glow">
                                    <i class="fas fa-paint-brush text-white text-lg"></i>
                                </div>
                            </div>
                            <div class="ml-4">
                                <p class="text-sm font-medium text-gray-400 uppercase tracking-wide">Active Services</p>
                                <p class="text-3xl font-bold text-white font-orbitron"><?php echo $stats['total_services']; ?></p>
                                <p class="text-xs text-neon-red mt-1">Available now</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Recent bookings and status chart -->
            <div class="px-4 py-6 sm:px-0">
                <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                    <!-- Recent Bookings -->
                    <div class="bg-dark-gray rounded-lg p-6">
                        <h3 class="text-lg font-medium text-white mb-4">Recent Bookings</h3>
                        <div class="space-y-4">
                            <?php foreach (array_slice($recent_bookings, 0, 5) as $booking): ?>
                                <div class="flex items-center justify-between p-3 bg-darker-gray rounded-lg">
                                    <div>
                                        <p class="text-white font-medium"><?php echo htmlspecialchars($booking['first_name'] . ' ' . $booking['last_name']); ?></p>
                                        <p class="text-gray-400 text-sm"><?php echo htmlspecialchars($booking['email']); ?></p>
                                        <p class="text-gray-500 text-xs"><?php echo date('M j, Y g:i A', strtotime($booking['created_at'])); ?></p>
                                    </div>
                                    <div class="text-right">
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
                                            <?php 
                                            switch($booking['status']) {
                                                case 'pending': echo 'bg-yellow-900 text-yellow-200'; break;
                                                case 'confirmed': echo 'bg-green-900 text-green-200'; break;
                                                case 'completed': echo 'bg-blue-900 text-blue-200'; break;
                                                case 'cancelled': echo 'bg-red-900 text-red-200'; break;
                                                default: echo 'bg-gray-900 text-gray-200';
                                            }
                                            ?>">
                                            <?php echo ucfirst($booking['status']); ?>
                                        </span>
                                        <p class="text-gray-400 text-xs mt-1"><?php echo $booking['booking_reference']; ?></p>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                        <div class="mt-4">
                            <a href="bookings.php" class="text-neon-red hover:text-red-400 text-sm font-medium">
                                View all bookings <i class="fas fa-arrow-right ml-1"></i>
                            </a>
                        </div>
                    </div>

                    <!-- Status Distribution -->
                    <div class="bg-dark-gray rounded-lg p-6">
                        <h3 class="text-lg font-medium text-white mb-4">Booking Status Distribution</h3>
                        <div class="space-y-3">
                            <?php foreach ($status_stats as $status): ?>
                                <div class="flex items-center justify-between">
                                    <div class="flex items-center">
                                        <div class="w-3 h-3 rounded-full mr-3
                                            <?php 
                                            switch($status['status']) {
                                                case 'pending': echo 'bg-yellow-500'; break;
                                                case 'confirmed': echo 'bg-green-500'; break;
                                                case 'completed': echo 'bg-blue-500'; break;
                                                case 'cancelled': echo 'bg-red-500'; break;
                                                default: echo 'bg-gray-500';
                                            }
                                            ?>"></div>
                                        <span class="text-white capitalize"><?php echo $status['status']; ?></span>
                                    </div>
                                    <span class="text-gray-400"><?php echo $status['count']; ?></span>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Quick Actions -->
            <div class="px-4 py-6 sm:px-0">
                <div class="bg-dark-gray rounded-lg p-6">
                    <h3 class="text-lg font-medium text-white mb-4">Quick Actions</h3>
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                        <a href="bookings.php?status=pending" class="bg-yellow-900 hover:bg-yellow-800 text-yellow-100 p-4 rounded-lg text-center transition-colors">
                            <i class="fas fa-clock text-2xl mb-2"></i>
                            <p class="font-medium">Review Pending Bookings</p>
                            <p class="text-sm opacity-75"><?php echo $stats['pending_bookings']; ?> waiting</p>
                        </a>
                        <a href="services.php?action=add" class="bg-neon-red hover:bg-red-700 text-white p-4 rounded-lg text-center transition-colors">
                            <i class="fas fa-plus text-2xl mb-2"></i>
                            <p class="font-medium">Add New Service</p>
                            <p class="text-sm opacity-75">Expand offerings</p>
                        </a>
                        <a href="artists.php" class="bg-blue-900 hover:bg-blue-800 text-blue-100 p-4 rounded-lg text-center transition-colors">
                            <i class="fas fa-users text-2xl mb-2"></i>
                            <p class="font-medium">Manage Artists</p>
                            <p class="text-sm opacity-75">Update profiles</p>
                        </a>
                    </div>
                </div>
            </div>
        </main>
    </div>
</body>
</html>

