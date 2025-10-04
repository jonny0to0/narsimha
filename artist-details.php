<?php
// Get artist ID from URL parameter
$artist_id = isset($_GET['id']) ? intval($_GET['id']) : 0;

if ($artist_id <= 0) {
    header('Location: index.html');
    exit();
}
?>
<!DOCTYPE html>
<html lang="en" class="h-full bg-gray-900">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Artist Details - Narshimha Tattoo</title>
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
                        'purple-glow': '#8b5cf6',
                        'electric-blue': '#06b6d4',
                    }
                }
            }
        }
    </script>
    <style>
        .gradient-text {
            background: linear-gradient(135deg, #ff073a, #8b5cf6, #06b6d4);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }
        .glass-morphism {
            background: rgba(255, 255, 255, 0.05);
            backdrop-filter: blur(10px);
            border: 1px solid rgba(255, 255, 255, 0.1);
        }
        .neon-glow {
            text-shadow: 0 0 10px currentColor, 0 0 20px currentColor, 0 0 30px currentColor;
        }
        .reveal {
            opacity: 0;
            transform: translateY(30px);
            transition: all 0.6s ease;
        }
        .reveal.active {
            opacity: 1;
            transform: translateY(0);
        }
        .loading-skeleton {
            background: linear-gradient(90deg, #2a2a2a 25%, #3a3a3a 50%, #2a2a2a 75%);
            background-size: 200% 100%;
            animation: loading 1.5s infinite;
        }
        @keyframes loading {
            0% { background-position: 200% 0; }
            100% { background-position: -200% 0; }
        }
        
        /* Clean Book Now Button */
        .book-now-btn {
            display: inline-flex;
            align-items: center;
            padding: 10px 20px;
            border-radius: 8px;
            font-weight: 600;
            font-size: 14px;
            text-decoration: none;
            color: white;
            background: #ff3333;
            border: 2px solid #ff3333;
            transition: all 0.3s ease;
            position: relative;
            overflow: hidden;
        }
        
        .book-now-btn::before {
            content: '';
            position: absolute;
            top: 0;
            left: -100%;
            width: 100%;
            height: 100%;
            background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.2), transparent);
            transition: left 0.5s ease;
        }
        
        .book-now-btn:hover {
            background: #e62e2e;
            border-color: #e62e2e;
            transform: translateY(-1px);
            box-shadow: 0 4px 12px rgba(255, 51, 51, 0.3);
        }
        
        .book-now-btn:hover::before {
            left: 100%;
        }
        
        .book-now-btn:active {
            transform: translateY(0);
            transition: all 0.1s ease;
        }
        
        .book-now-text {
            display: flex;
            align-items: center;
            position: relative;
            z-index: 2;
        }
        
        /* Book Now Button Mobile Styles */
        @media (max-width: 768px) {
            .book-now-btn {
                padding: 9px 18px;
                font-size: 13px;
            }
        }
        
        @media (max-width: 480px) {
            .book-now-btn {
                padding: 8px 16px;
                font-size: 12px;
            }
        }
        
        /* Navbar scroll effect */
        #navbar {
            background: rgba(13, 13, 13, 0.95);
            backdrop-filter: blur(10px);
            border-bottom: 1px solid rgba(255, 7, 58, 0.1);
        }
        
        #navbar.scrolled {
            background: rgba(13, 13, 13, 0.98);
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.3);
        }
        
        /* Line clamp utility */
        .line-clamp-2 {
            display: -webkit-box;
            -webkit-line-clamp: 2;
            -webkit-box-orient: vertical;
            overflow: hidden;
        }
        
        /* Enhanced card hover effects */
        .group:hover .group-hover\:scale-110 {
            transform: scale(1.1);
        }
        
        .group:hover .group-hover\:translate-y-0 {
            transform: translateY(0);
        }
        
        .group:hover .group-hover\:opacity-100 {
            opacity: 1;
        }
        
        .group:hover .group-hover\:translate-x-1 {
            transform: translateX(0.25rem);
        }
        
        .group:hover .group-hover\:text-neon-red {
            color: #ff073a;
        }
        
        .group:hover .group-hover\:border-neon-red\/30 {
            border-color: rgba(255, 7, 58, 0.3);
        }
    </style>
</head>
<body class="h-full">
    <!-- Navigation -->
    <nav class="fixed top-0 w-full z-50 transition-all duration-300" id="navbar">
        <div class="w-full px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between items-center w-full py-1">
                <!-- Logo -->
                <a href="index.html" class="transition-all duration-300 flex items-center flex-shrink-0 group">
                    <!-- Head Photo -->
                    <div class="relative mr-2">
                        <img src="img/main-logo/narshimha-head.png" alt="Narshimha Head" class="h-16 md:h-18 lg:h-20 w-auto rounded-lg group-hover:shadow-[inset_0_0_25px_rgba(255,7,58,0.4)] transition-all duration-300">
                    </div>
                    
                    <!-- Text Logo -->
                    <div class="flex flex-col">
                        <!-- Main Name -->
                        <h1 class="text-lg md:text-xl lg:text-2xl font-bold text-white group-hover:text-neon-red transition-colors duration-300 font-serif">
                            Narshimha
                        </h1>
                        
                        <!-- Studio Text with Decorative Lines -->
                        <div class="flex items-center justify-center mt-0.5">
                            <!-- Left decorative line -->
                            <div class="w-4 h-0.5 bg-neon-red transition-all duration-300"></div>
                            
                            <!-- Studio Text -->
                            <span class="text-xs text-gray-400 group-hover:text-gray-300 transition-colors duration-300 font-sans uppercase tracking-wider mx-1">
                                TATTOO STUDIO
                            </span>
                            
                            <!-- Right decorative line -->
                            <div class="w-4 h-0.5 bg-neon-red transition-all duration-300"></div>
                        </div>
                        
                        <!-- Bottom Left Accent - Only this line extends on hover -->
                        <div class="flex items-center mt-0.5">
                            <div class="w-1 h-1 bg-neon-red rounded-full transition-transform duration-300"></div>
                            <div class="w-2 h-0.5 bg-neon-red ml-1 group-hover:w-12 transition-all duration-700 ease-out"></div>
                        </div>
                    </div>
                </a>
                
                <div class="hidden md:flex items-center space-x-8 flex-grow justify-end">
                    <a href="index.html#home" class="text-white hover:text-neon-red transition-colors duration-300 font-medium px-3 py-2 rounded-md hover:bg-neon-red/10">Home</a>
                    <a href="index.html#artists" class="text-white hover:text-neon-red transition-colors duration-300 font-medium px-3 py-2 rounded-md hover:bg-neon-red/10">Artists</a>
                    <a href="index.html#gallery" class="text-white hover:text-neon-red transition-colors duration-300 font-medium px-3 py-2 rounded-md hover:bg-neon-red/10">Gallery</a>
                    <a href="index.html#services" class="text-white hover:text-neon-red transition-colors duration-300 font-medium px-3 py-2 rounded-md hover:bg-neon-red/10">Services</a>
                    
                    <!-- Clean Book Now Button -->
                    <a href="index.html#booking" class="book-now-btn flex-shrink-0">
                        <span class="book-now-text">
                            <i class="fas fa-calendar-plus mr-2"></i>
                            Book Now
                        </span>
                    </a>
                </div>
                <div class="md:hidden flex-shrink-0">
                    <button id="mobile-menu-btn" class="text-white hover:text-neon-red transition-colors duration-300 p-2 rounded-md hover:bg-neon-red/10">
                        <i class="fas fa-bars text-xl"></i>
                    </button>
                </div>
            </div>
        </div>
        <!-- Mobile Menu -->
        <div id="mobile-menu" class="hidden md:hidden bg-black/95 backdrop-blur-md border-t border-gray-800">
            <div class="w-full px-4 py-6 space-y-4">
                <!-- Mobile Logo -->
                <a href="index.html" class="pb-4 border-b border-gray-700 transition-all duration-300 flex flex-col items-center group">
                    <!-- Head Photo -->
                    <div class="mb-3">
                        <img src="img/main-logo/narshimha-head.png" alt="Narshimha Head" class="h-18 w-auto rounded-lg group-hover:shadow-[inset_0_0_20px_rgba(255,7,58,0.4)] transition-all duration-300">
                    </div>
                    
                    <!-- Text Logo -->
                    <div class="flex flex-col items-center">
                        <!-- Main Name -->
                        <h1 class="text-lg font-bold text-white group-hover:text-neon-red transition-colors duration-300 font-serif">
                            Narshimha
                        </h1>
                        
                        <!-- Studio Text with Decorative Lines -->
                        <div class="flex items-center justify-center mt-0.5">
                            <!-- Left decorative line -->
                            <div class="w-3 h-0.5 bg-neon-red transition-all duration-300"></div>
                            
                            <!-- Studio Text -->
                            <span class="text-xs text-gray-400 group-hover:text-gray-300 transition-colors duration-300 font-sans uppercase tracking-wider mx-1">
                                TATTOO STUDIO
                            </span>
                            
                            <!-- Right decorative line -->
                            <div class="w-3 h-0.5 bg-neon-red transition-all duration-300"></div>
                        </div>
                        
                        <!-- Bottom Left Accent - Only this line extends on hover -->
                        <div class="flex items-center mt-0.5">
                            <div class="w-1 h-1 bg-neon-red rounded-full transition-transform duration-300"></div>
                            <div class="w-2 h-0.5 bg-neon-red ml-1 group-hover:w-10 transition-all duration-700 ease-out"></div>
                        </div>
                    </div>
                </a>
                
                <a href="index.html#home" class="block text-white hover:text-neon-red transition-colors duration-300 font-medium py-2 px-3 rounded-md hover:bg-neon-red/10">Home</a>
                <a href="index.html#artists" class="block text-white hover:text-neon-red transition-colors duration-300 font-medium py-2 px-3 rounded-md hover:bg-neon-red/10">Artists</a>
                <a href="index.html#gallery" class="block text-white hover:text-neon-red transition-colors duration-300 font-medium py-2 px-3 rounded-md hover:bg-neon-red/10">Gallery</a>
                <a href="index.html#services" class="block text-white hover:text-neon-red transition-colors duration-300 font-medium py-2 px-3 rounded-md hover:bg-neon-red/10">Services</a>
                
                <!-- Mobile Book Now Button -->
                <div class="pt-2">
                    <a href="index.html#booking" class="book-now-btn w-full justify-center">
                        <span class="book-now-text">
                            <i class="fas fa-calendar-plus mr-2"></i>
                            Book Now
                        </span>
                    </a>
                </div>
            </div>
        </div>
    </nav>

    <!-- Loading State -->
    <div id="loadingState" class="min-h-screen bg-gray-900 flex items-center justify-center">
        <div class="text-center">
            <div class="animate-spin rounded-full h-12 w-12 border-b-2 border-neon-red mx-auto mb-4"></div>
            <p class="text-gray-400">Loading artist details...</p>
        </div>
    </div>

    <!-- Error State -->
    <div id="errorState" class="min-h-screen bg-gray-900 flex items-center justify-center hidden">
        <div class="text-center">
            <i class="fas fa-exclamation-triangle text-6xl text-red-500 mb-4"></i>
            <h2 class="text-2xl font-bold text-white mb-2">Artist Not Found</h2>
            <p class="text-gray-400 mb-6">The artist you're looking for doesn't exist or is no longer available.</p>
            <a href="index.html" class="bg-neon-red hover:bg-red-700 text-white px-6 py-3 rounded-md transition-colors">
                Back to Home
            </a>
        </div>
    </div>

    <!-- Main Content -->
    <div id="mainContent" class="min-h-screen bg-gray-900 hidden">
        <!-- Hero Section -->
        <section class="relative pt-32 pb-20 bg-gradient-to-br from-darker-gray to-dark-gray">
            <div class="container mx-auto px-6">
                <div class="grid md:grid-cols-2 gap-12 items-center">
                    <!-- Artist Image -->
                    <div class="reveal">
                        <div class="relative">
                            <div id="artistImageContainer" class="w-full h-96 bg-gray-800 rounded-lg overflow-hidden">
                                <div class="loading-skeleton w-full h-full"></div>
                            </div>
                            <div class="absolute inset-0 bg-gradient-to-t from-black/50 to-transparent rounded-lg"></div>
                        </div>
                    </div>
                    
                    <!-- Artist Info -->
                    <div class="reveal">
                        <div class="space-y-6">
                            <div>
                                <h1 id="artistName" class="text-4xl md:text-5xl font-bold text-white mb-2">
                                    <div class="loading-skeleton h-12 w-64 rounded"></div>
                                </h1>
                                <div id="artistSpecialties" class="flex flex-wrap gap-2 mb-4">
                                    <div class="loading-skeleton h-6 w-24 rounded"></div>
                                    <div class="loading-skeleton h-6 w-20 rounded"></div>
                                </div>
                                <p id="artistExperience" class="text-xl text-gray-400">
                                    <div class="loading-skeleton h-6 w-32 rounded"></div>
                                </p>
                            </div>
                            
                            <div id="artistBio" class="text-gray-300 leading-relaxed">
                                <div class="loading-skeleton h-4 w-full rounded mb-2"></div>
                                <div class="loading-skeleton h-4 w-full rounded mb-2"></div>
                                <div class="loading-skeleton h-4 w-3/4 rounded"></div>
                            </div>
                            
                            <div class="flex flex-wrap gap-4 text-sm text-gray-400">
                                <div class="flex items-center">
                                    <i class="fas fa-calendar-check mr-2 text-neon-red"></i>
                                    <span id="bookingCount">0</span> bookings completed
                                </div>
                                <div class="flex items-center">
                                    <i class="fas fa-star mr-2 text-yellow-400"></i>
                                    <span id="experienceYears">0</span>+ years experience
                                </div>
                            </div>
                            
                            <div class="flex flex-wrap gap-4">
                                <button onclick="scrollToBooking()" class="bg-neon-red hover:bg-red-700 text-white px-8 py-3 rounded-md font-medium transition-colors">
                                    <i class="fas fa-calendar-plus mr-2"></i>
                                    Book with this Artist
                                </button>
                                <button onclick="callNow()" class="glass-morphism text-white px-8 py-3 rounded-md font-medium hover:bg-white/10 transition-colors">
                                    <i class="fas fa-phone mr-2"></i>
                                    Call Now
                                </button>
                            </div>
                            
                            <!-- Style Selection Buttons -->
                            <div class="mt-6">
                                <h3 class="text-lg font-semibold text-white mb-4">Choose Your Style</h3>
                                <div class="flex flex-wrap gap-4">
                                    <button onclick="selectStyle('classic')" id="classicBtn" class="style-btn bg-gradient-to-r from-yellow-600 to-yellow-500 hover:from-yellow-500 hover:to-yellow-400 text-black px-8 py-3 rounded-full font-semibold transition-all duration-300 transform hover:scale-105 border-2 border-yellow-500/50 hover:border-yellow-400 shadow-lg hover:shadow-xl">
                                        <i class="fas fa-crown mr-2"></i>
                                        Classic Style
                                        <i class="fas fa-arrow-right ml-2"></i>
                                    </button>
                                    <button onclick="selectStyle('modern')" id="modernBtn" class="style-btn bg-gradient-to-r from-electric-blue to-cyan-500 hover:from-cyan-500 hover:to-electric-blue text-white px-8 py-3 rounded-full font-semibold transition-all duration-300 transform hover:scale-105 border-2 border-electric-blue/50 hover:border-cyan-400 shadow-lg hover:shadow-xl">
                                        <i class="fas fa-rocket mr-2"></i>
                                        Modern Style
                                        <i class="fas fa-arrow-right ml-2"></i>
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- Portfolio Section -->
        <section id="portfolio" class="py-20 bg-dark-gray">
            <div class="container mx-auto px-6">
                <div class="text-center mb-16 reveal">
                    <h2 class="text-4xl font-bold text-white mb-4">Portfolio</h2>
                    <p class="text-xl text-gray-400">Recent work and recommended services</p>
                </div>
                
                <!-- Recommended Services -->
                <div class="mb-16 reveal">
                    <h3 class="text-2xl font-bold text-white mb-8">Recommended Services</h3>
                    <div id="recommendedServices" class="grid md:grid-cols-2 lg:grid-cols-3 gap-6">
                        <!-- Services will be loaded here -->
                    </div>
                </div>
                
                <!-- Recent Bookings (if any) -->
                <div id="recentBookingsSection" class="reveal hidden">
                    <h3 class="text-2xl font-bold text-white mb-8">Recent Work</h3>
                    <div id="recentBookings" class="space-y-4">
                        <!-- Recent bookings will be loaded here -->
                    </div>
                </div>
            </div>
        </section>

        <!-- Booking Section -->
        <section id="booking" class="py-20 bg-darker-gray">
            <div class="container mx-auto px-6">
                <div class="max-w-4xl mx-auto">
                    <div class="text-center mb-12 reveal">
                        <h2 class="text-4xl font-bold text-white mb-4">Book with <span id="bookingArtistName" class="gradient-text">this Artist</span></h2>
                        <p class="text-xl text-gray-400">Ready to get inked? Let's make it happen!</p>
                    </div>
                    
                    <div class="glass-morphism rounded-lg p-8 reveal">
                        <form id="bookingForm" class="space-y-6">
                            <input type="hidden" id="selectedArtistId" value="<?php echo $artist_id; ?>">
                            
                            <div class="grid md:grid-cols-2 gap-6">
                                <div>
                                    <label class="block text-sm font-medium text-gray-300 mb-2">First Name *</label>
                                    <input type="text" id="firstName" required 
                                           class="w-full bg-darker-gray border border-gray-700 text-white rounded-md px-4 py-3 focus:border-neon-red focus:outline-none">
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-300 mb-2">Last Name *</label>
                                    <input type="text" id="lastName" required 
                                           class="w-full bg-darker-gray border border-gray-700 text-white rounded-md px-4 py-3 focus:border-neon-red focus:outline-none">
                                </div>
                            </div>
                            
                            <div class="grid md:grid-cols-2 gap-6">
                                <div>
                                    <label class="block text-sm font-medium text-gray-300 mb-2">Email *</label>
                                    <input type="email" id="email" required 
                                           class="w-full bg-darker-gray border border-gray-700 text-white rounded-md px-4 py-3 focus:border-neon-red focus:outline-none">
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-300 mb-2">Phone *</label>
                                    <input type="tel" id="phone" required 
                                           class="w-full bg-darker-gray border border-gray-700 text-white rounded-md px-4 py-3 focus:border-neon-red focus:outline-none">
                                </div>
                            </div>
                            
                            <div>
                                <label class="block text-sm font-medium text-gray-300 mb-2">Tattoo Description *</label>
                                <textarea id="description" rows="4" required 
                                          placeholder="Describe your tattoo idea, size, placement, and any specific details..."
                                          class="w-full bg-darker-gray border border-gray-700 text-white rounded-md px-4 py-3 focus:border-neon-red focus:outline-none"></textarea>
                            </div>
                            
                            <div class="grid md:grid-cols-2 gap-6">
                                <div>
                                    <label class="block text-sm font-medium text-gray-300 mb-2">Preferred Date</label>
                                    <input type="date" id="preferredDate" 
                                           class="w-full bg-darker-gray border border-gray-700 text-white rounded-md px-4 py-3 focus:border-neon-red focus:outline-none">
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-300 mb-2">Preferred Time</label>
                                    <select id="preferredTime" 
                                            class="w-full bg-darker-gray border border-gray-700 text-white rounded-md px-4 py-3 focus:border-neon-red focus:outline-none">
                                        <option value="">Select time</option>
                                        <option value="09:00">9:00 AM</option>
                                        <option value="10:00">10:00 AM</option>
                                        <option value="11:00">11:00 AM</option>
                                        <option value="12:00">12:00 PM</option>
                                        <option value="13:00">1:00 PM</option>
                                        <option value="14:00">2:00 PM</option>
                                        <option value="15:00">3:00 PM</option>
                                        <option value="16:00">4:00 PM</option>
                                        <option value="17:00">5:00 PM</option>
                                    </select>
                                </div>
                            </div>
                            
                            <div>
                                <label class="block text-sm font-medium text-gray-300 mb-2">Tattoo Style</label>
                                <select id="tattooStyle" 
                                        class="w-full bg-darker-gray border border-gray-700 text-white rounded-md px-4 py-3 focus:border-neon-red focus:outline-none">
                                    <option value="">Select style</option>
                                    <option value="Blackwork">Blackwork</option>
                                    <option value="Realism">Realism</option>
                                    <option value="Traditional">Traditional</option>
                                    <option value="Minimalist">Minimalist</option>
                                    <option value="Watercolor">Watercolor</option>
                                    <option value="Custom">Custom Design</option>
                                </select>
                            </div>
                            
                            <div class="text-center">
                                <button type="submit" class="bg-neon-red hover:bg-red-700 text-white px-12 py-4 rounded-md font-medium text-lg transition-colors">
                                    <i class="fas fa-calendar-check mr-2"></i>
                                    Submit Booking Request
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </section>

        <!-- Footer -->
        <footer class="bg-gradient-to-b from-darker-gray to-black relative overflow-hidden">
            <!-- Decorative Elements -->
            <div class="absolute top-0 left-0 w-full h-1 bg-gradient-to-r from-neon-red via-electric-blue to-purple-glow"></div>
            <div class="absolute top-8 left-1/4 w-32 h-32 bg-neon-red/5 rounded-full blur-3xl"></div>
            <div class="absolute bottom-8 right-1/4 w-40 h-40 bg-electric-blue/5 rounded-full blur-3xl"></div>
            
            <div class="container mx-auto px-6 py-16 relative z-10">
                <!-- Main Footer Content -->
                <div class="grid lg:grid-cols-4 md:grid-cols-2 gap-12 mb-12">
                    <!-- Brand Section -->
                    <div class="lg:col-span-2">
                        <div class="flex items-center mb-6">
                            <img src="img/main-logo/narshimha-head.png" alt="Narshimha Head" class="h-16 w-auto rounded-lg mr-4">
                            <div>
                                <h3 class="text-2xl font-bold text-white font-serif">Narshimha</h3>
                                <p class="text-sm text-gray-400 uppercase tracking-wider">TATTOO STUDIO</p>
                            </div>
                        </div>
                        
                        <p class="text-gray-300 text-lg leading-relaxed mb-8 max-w-md">
                            Where artistry meets precision. We create timeless tattoos that tell your unique story with exceptional craftsmanship and care.
                        </p>
                        
                        <!-- Social Links -->
                        <div class="flex space-x-6">
                            <a href="#" class="w-12 h-12 bg-gradient-to-br from-pink-500 to-purple-600 rounded-xl flex items-center justify-center text-white hover:scale-110 hover:shadow-lg transition-all duration-300">
                                <i class="fab fa-instagram text-lg"></i>
                            </a>
                            <a href="#" class="w-12 h-12 bg-gradient-to-br from-blue-500 to-blue-600 rounded-xl flex items-center justify-center text-white hover:scale-110 hover:shadow-lg transition-all duration-300">
                                <i class="fab fa-facebook text-lg"></i>
                            </a>
                            <a href="#" class="w-12 h-12 bg-gradient-to-br from-red-500 to-red-600 rounded-xl flex items-center justify-center text-white hover:scale-110 hover:shadow-lg transition-all duration-300">
                                <i class="fab fa-youtube text-lg"></i>
                            </a>
                            <a href="#" class="w-12 h-12 bg-gradient-to-br from-gray-600 to-gray-700 rounded-xl flex items-center justify-center text-white hover:scale-110 hover:shadow-lg transition-all duration-300">
                                <i class="fab fa-tiktok text-lg"></i>
                            </a>
                        </div>
                    </div>
                    
                    <!-- Quick Links -->
                    <div>
                        <h4 class="text-xl font-bold text-white mb-6">Quick Links</h4>
                        <ul class="space-y-4">
                            <li><a href="index.html#booking" class="text-gray-300 hover:text-neon-red transition-colors duration-300 flex items-center group">
                                <i class="fas fa-calendar-alt w-5 h-5 mr-3 group-hover:scale-110 transition-transform"></i>
                                Book Appointment
                            </a></li>
                            <li><a href="index.html#gallery" class="text-gray-300 hover:text-neon-red transition-colors duration-300 flex items-center group">
                                <i class="fas fa-images w-5 h-5 mr-3 group-hover:scale-110 transition-transform"></i>
                                View Gallery
                            </a></li>
                            <li><a href="index.html#artists" class="text-gray-300 hover:text-neon-red transition-colors duration-300 flex items-center group">
                                <i class="fas fa-users w-5 h-5 mr-3 group-hover:scale-110 transition-transform"></i>
                                Meet Artists
                            </a></li>
                            <li><a href="index.html#services" class="text-gray-300 hover:text-neon-red transition-colors duration-300 flex items-center group">
                                <i class="fas fa-cog w-5 h-5 mr-3 group-hover:scale-110 transition-transform"></i>
                                Our Services
                            </a></li>
                        </ul>
                    </div>
                    
                    <!-- Contact Info -->
                    <div>
                        <h4 class="text-xl font-bold text-white mb-6">Contact Info</h4>
                        <div class="space-y-4">
                            <div class="flex items-start space-x-3">
                                <i class="fas fa-map-marker-alt text-neon-red mt-1"></i>
                                <div>
                                    <p class="text-gray-300">123 Ink Street</p>
                                    <p class="text-gray-300">Art District, City 12345</p>
                                </div>
                            </div>
                            <div class="flex items-center space-x-3">
                                <i class="fas fa-phone text-neon-red"></i>
                                <p class="text-gray-300">(555) 123-TATT</p>
                            </div>
                            <div class="flex items-center space-x-3">
                                <i class="fas fa-envelope text-neon-red"></i>
                                <p class="text-gray-300">info@narshimhatattoo.com</p>
                            </div>
                            <div class="flex items-start space-x-3">
                                <i class="fas fa-clock text-neon-red mt-1"></i>
                                <div>
                                    <p class="text-gray-300">Tue - Sat: 12PM - 8PM</p>
                                    <p class="text-gray-300">Sun - Mon: Closed</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Bottom Bar -->
                <div class="border-t border-gray-800 pt-8">
                    <div class="flex flex-col md:flex-row justify-between items-center space-y-4 md:space-y-0">
                        <div class="text-gray-400 text-center md:text-left">
                            <p>&copy; 2024 Narshimha Tattoo Studio. All rights reserved.</p>
                            <p class="text-sm mt-1">Crafted with passion for exceptional artistry</p>
                        </div>
                        <div class="flex space-x-6 text-sm">
                            <a href="privacy-policy.html" class="text-gray-400 hover:text-neon-red transition-colors">Privacy Policy</a>
                            <a href="terms-service.html" class="text-gray-400 hover:text-neon-red transition-colors">Terms of Service</a>
                            <a href="health-safety.html" class="text-gray-400 hover:text-neon-red transition-colors">Health & Safety</a>
                        </div>
                    </div>
                </div>
            </div>
        </footer>
    </div>

    <script>
        const artistId = <?php echo $artist_id; ?>;
        let artistData = null;

        // Load artist data
        async function loadArtistData() {
            try {
                const response = await fetch(`api/artist-details.php?id=${artistId}`);
                const result = await response.json();
                
                if (result.success) {
                    artistData = result.data;
                    displayArtistData();
                    hideLoading();
                } else {
                    showError();
                }
            } catch (error) {
                console.error('Error loading artist data:', error);
                showError();
            }
        }

        function displayArtistData() {
            // Update artist name
            document.getElementById('artistName').textContent = artistData.name;
            document.getElementById('bookingArtistName').textContent = artistData.name;
            
            // Update artist image
            const imageContainer = document.getElementById('artistImageContainer');
            if (artistData.image_url) {
                imageContainer.innerHTML = `<img src="${artistData.image_url}" alt="${artistData.name}" class="w-full h-full object-cover">`;
            } else {
                imageContainer.innerHTML = `
                    <div class="w-full h-full bg-gradient-to-br from-gray-800 to-gray-900 flex items-center justify-center">
                        <i class="fas fa-user-circle text-8xl text-gray-600"></i>
                    </div>
                `;
            }
            
            // Update specialties
            const specialtiesContainer = document.getElementById('artistSpecialties');
            specialtiesContainer.innerHTML = artistData.specialties.map(specialty => 
                `<span class="bg-neon-red/20 text-neon-red px-3 py-1 rounded-full text-sm font-medium">${specialty}</span>`
            ).join('');
            
            // Update experience
            document.getElementById('artistExperience').textContent = `${artistData.experience_years}+ years experience`;
            document.getElementById('experienceYears').textContent = artistData.experience_years;
            
            // Update bio
            document.getElementById('artistBio').textContent = artistData.bio || 'No bio available.';
            
            // Update booking count
            document.getElementById('bookingCount').textContent = artistData.booking_count;
            
            // Display recommended services
            displayRecommendedServices();
            
            // Display recent bookings if any
            if (artistData.recent_bookings && artistData.recent_bookings.length > 0) {
                displayRecentBookings();
            }
        }

        function displayRecommendedServices() {
            const container = document.getElementById('recommendedServices');
            
            if (artistData.recommended_services && artistData.recommended_services.length > 0) {
                container.innerHTML = artistData.recommended_services.map(service => `
                    <div class="group relative bg-darker-gray rounded-xl overflow-hidden hover:scale-105 transition-all duration-500 hover:shadow-2xl hover:shadow-neon-red/20">
                        <!-- Image Container with Overlay -->
                        <div class="relative w-full h-56 overflow-hidden">
                            ${service.image_url ? 
                                `<img src="${service.image_url}" alt="${service.name}" class="w-full h-full object-cover group-hover:scale-110 transition-transform duration-700">` :
                                `<div class="w-full h-full bg-gradient-to-br from-gray-800 to-gray-900 flex items-center justify-center">
                                    <i class="fas fa-image text-6xl text-gray-600 group-hover:text-neon-red transition-colors duration-300"></i>
                                </div>`
                            }
                            
                            <!-- Gradient Overlay -->
                            <div class="absolute inset-0 bg-gradient-to-t from-black/80 via-transparent to-transparent opacity-0 group-hover:opacity-100 transition-opacity duration-300"></div>
                            
                            <!-- Price Badge -->
                            <div class="absolute top-4 right-4 bg-neon-red text-white px-3 py-1 rounded-full text-sm font-bold shadow-lg">
                                $${service.price}
                            </div>
                            
                            <!-- Duration Badge -->
                            <div class="absolute top-4 left-4 bg-black/60 backdrop-blur-sm text-white px-3 py-1 rounded-full text-xs font-medium">
                                <i class="fas fa-clock mr-1"></i>
                                ${service.estimated_duration} min
                            </div>
                            
                            <!-- Hover Action Button -->
                            <div class="absolute bottom-4 left-1/2 transform -translate-x-1/2 translate-y-4 opacity-0 group-hover:translate-y-0 group-hover:opacity-100 transition-all duration-300">
                                <button onclick="bookService('${service.name}', ${service.price}, '${service.estimated_duration}')" class="bg-neon-red hover:bg-red-700 text-white px-6 py-2 rounded-full font-medium text-sm shadow-lg hover:shadow-xl transition-all duration-300">
                                    <i class="fas fa-calendar-plus mr-2"></i>
                                    Book This Service
                                </button>
                            </div>
                        </div>
                        
                        <!-- Content -->
                        <div class="p-6">
                            <div class="flex items-start justify-between mb-3">
                                <h4 class="text-xl font-bold text-white group-hover:text-neon-red transition-colors duration-300">${service.name}</h4>
                                <div class="flex items-center text-yellow-400 text-sm">
                                    <i class="fas fa-star"></i>
                                    <span class="ml-1 text-gray-400">4.9</span>
                                </div>
                            </div>
                            
                            <p class="text-gray-400 text-sm mb-4 leading-relaxed line-clamp-2">${service.description || 'Professional tattoo service with exceptional quality and attention to detail.'}</p>
                            
                            <!-- Service Category -->
                            <div class="flex items-center justify-between">
                                <div class="flex items-center text-gray-500 text-xs">
                                    <i class="fas fa-tag mr-2"></i>
                                    <span>${service.category_name || 'Tattoo Service'}</span>
                                </div>
                                
                                <!-- View Details Button -->
                                <button class="text-neon-red hover:text-white text-sm font-medium flex items-center group-hover:translate-x-1 transition-transform duration-300">
                                    View Details
                                    <i class="fas fa-arrow-right ml-1 text-xs"></i>
                                </button>
                            </div>
                        </div>
                        
                        <!-- Decorative Border -->
                        <div class="absolute inset-0 rounded-xl border border-transparent group-hover:border-neon-red/30 transition-colors duration-300 pointer-events-none"></div>
                    </div>
                `).join('');
            } else {
                container.innerHTML = `
                    <div class="col-span-full text-center py-16">
                        <div class="max-w-md mx-auto">
                            <div class="w-24 h-24 bg-gradient-to-br from-neon-red/20 to-purple-glow/20 rounded-full flex items-center justify-center mx-auto mb-6">
                                <i class="fas fa-palette text-4xl text-neon-red"></i>
                            </div>
                            <h3 class="text-2xl font-bold text-white mb-4">Portfolio Coming Soon</h3>
                            <p class="text-gray-400 mb-6">This artist is working on their portfolio. Check back soon for amazing work samples!</p>
                            <button onclick="scrollToBooking()" class="bg-neon-red hover:bg-red-700 text-white px-8 py-3 rounded-lg font-medium transition-colors">
                                <i class="fas fa-calendar-plus mr-2"></i>
                                Book a Consultation
                            </button>
                        </div>
                    </div>
                `;
            }
        }

        function displayRecentBookings() {
            const section = document.getElementById('recentBookingsSection');
            const container = document.getElementById('recentBookings');
            
            section.classList.remove('hidden');
            container.innerHTML = artistData.recent_bookings.map(booking => `
                <div class="group bg-darker-gray rounded-xl p-6 hover:bg-gray-800/50 transition-all duration-300 border border-gray-800 hover:border-neon-red/30">
                    <div class="flex items-start justify-between mb-4">
                        <div class="flex items-center space-x-3">
                            <div class="w-12 h-12 bg-gradient-to-br from-neon-red/20 to-purple-glow/20 rounded-full flex items-center justify-center">
                                <i class="fas fa-user text-neon-red"></i>
                            </div>
                            <div>
                                <h4 class="text-white font-semibold text-lg">${booking.first_name} ${booking.last_name}</h4>
                                <p class="text-gray-400 text-sm">${booking.tattoo_style || 'Custom Design'}</p>
                            </div>
                        </div>
                        
                        <div class="text-right">
                            <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium ${
                                booking.status === 'completed' ? 'bg-green-900/30 text-green-300 border border-green-700' :
                                booking.status === 'confirmed' ? 'bg-blue-900/30 text-blue-300 border border-blue-700' :
                                booking.status === 'in_progress' ? 'bg-yellow-900/30 text-yellow-300 border border-yellow-700' :
                                'bg-gray-900/30 text-gray-300 border border-gray-700'
                            }">
                                <i class="fas fa-circle text-xs mr-2 ${
                                    booking.status === 'completed' ? 'text-green-400' :
                                    booking.status === 'confirmed' ? 'text-blue-400' :
                                    booking.status === 'in_progress' ? 'text-yellow-400' :
                                    'text-gray-400'
                                }"></i>
                                ${booking.status.charAt(0).toUpperCase() + booking.status.slice(1)}
                            </span>
                        </div>
                    </div>
                    
                    <div class="mb-4">
                        <p class="text-gray-300 text-sm leading-relaxed">${booking.description}</p>
                    </div>
                    
                    <div class="flex items-center justify-between text-sm">
                        <div class="flex items-center text-gray-500">
                            <i class="fas fa-calendar-alt mr-2 text-neon-red"></i>
                            <span>${booking.preferred_date || 'Date TBD'}</span>
                        </div>
                        
                        <div class="flex items-center text-gray-500">
                            <i class="fas fa-hashtag mr-2 text-neon-red"></i>
                            <span>Ref: ${booking.booking_reference}</span>
                        </div>
                    </div>
                </div>
            `).join('');
        }

        function hideLoading() {
            document.getElementById('loadingState').classList.add('hidden');
            document.getElementById('mainContent').classList.remove('hidden');
            
            // Trigger reveal animations
            setTimeout(() => {
                document.querySelectorAll('.reveal').forEach(el => {
                    el.classList.add('active');
                });
            }, 100);
        }

        function showError() {
            document.getElementById('loadingState').classList.add('hidden');
            document.getElementById('errorState').classList.remove('hidden');
        }

        // Smooth scrolling functions
        function scrollToBooking() {
            document.getElementById('booking').scrollIntoView({ behavior: 'smooth' });
        }

        function scrollToPortfolio() {
            document.getElementById('portfolio').scrollIntoView({ behavior: 'smooth' });
        }

        // Call now function
        function callNow() {
            // You can customize the phone number here
            const phoneNumber = '+1234567890'; // Replace with actual phone number
            
            // Create a notification
            const notification = document.createElement('div');
            notification.className = 'fixed top-20 right-4 bg-green-600 text-white px-6 py-4 rounded-lg shadow-xl z-50 transform translate-x-full transition-all duration-500';
            notification.innerHTML = `
                <div class="flex items-start">
                    <i class="fas fa-phone text-2xl mr-3 mt-1"></i>
                    <div>
                        <h4 class="font-bold text-lg">Call Now</h4>
                        <p class="text-sm opacity-90">Calling ${phoneNumber}</p>
                        <p class="text-xs opacity-75 mt-1">Click to initiate call</p>
                    </div>
                    <button onclick="this.parentElement.parentElement.remove()" class="ml-4 text-white/70 hover:text-white">
                        <i class="fas fa-times"></i>
                    </button>
                </div>
            `;
            document.body.appendChild(notification);
            
            // Animate in
            setTimeout(() => notification.classList.remove('translate-x-full'), 100);
            
            // Auto remove after 5 seconds
            setTimeout(() => {
                notification.classList.add('translate-x-full');
                setTimeout(() => {
                    if (notification.parentNode) {
                        notification.parentNode.removeChild(notification);
                    }
                }, 500);
            }, 5000);
            
            // Initiate phone call
            window.location.href = `tel:${phoneNumber}`;
        }

        // Book service from portfolio card
        function bookService(serviceName, price, duration) {
            // Pre-fill the booking form with service details
            const descriptionField = document.getElementById('description');
            const tattooStyleField = document.getElementById('tattooStyle');
            
            if (descriptionField) {
                descriptionField.value = `I'm interested in booking: ${serviceName} ($${price}, ${duration} min)`;
            }
            
            // Try to match service name to tattoo style
            const styleMapping = {
                'blackwork': 'Blackwork',
                'realism': 'Realism', 
                'traditional': 'Traditional',
                'minimal': 'Minimalist',
                'watercolor': 'Watercolor',
                'custom': 'Custom'
            };
            
            if (tattooStyleField) {
                const serviceLower = serviceName.toLowerCase();
                for (const [key, value] of Object.entries(styleMapping)) {
                    if (serviceLower.includes(key)) {
                        tattooStyleField.value = value;
                        break;
                    }
                }
            }
            
            // Scroll to booking section
            scrollToBooking();
            
            // Show notification
            const notification = document.createElement('div');
            notification.className = 'fixed top-20 right-4 bg-green-600 text-white px-6 py-3 rounded-lg shadow-lg z-50 transform translate-x-full transition-transform duration-300';
            notification.innerHTML = `
                <div class="flex items-center">
                    <i class="fas fa-check-circle mr-3"></i>
                    <div>
                        <p class="font-bold">Service Added!</p>
                        <p class="text-sm">${serviceName} added to booking form</p>
                    </div>
                </div>
            `;
            document.body.appendChild(notification);
            
            setTimeout(() => notification.classList.remove('translate-x-full'), 100);
            setTimeout(() => {
                notification.classList.add('translate-x-full');
                setTimeout(() => document.body.removeChild(notification), 300);
            }, 3000);
        }

        // Booking form handling
        document.getElementById('bookingForm').addEventListener('submit', async function(e) {
            e.preventDefault();
            
            const formData = {
                first_name: document.getElementById('firstName').value,
                last_name: document.getElementById('lastName').value,
                email: document.getElementById('email').value,
                phone: document.getElementById('phone').value,
                description: document.getElementById('description').value,
                preferred_artist_id: artistId,
                tattoo_style: document.getElementById('tattooStyle').value,
                preferred_date: document.getElementById('preferredDate').value,
                preferred_time: document.getElementById('preferredTime').value
            };
            
            try {
                const response = await fetch('api/bookings.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                    },
                    body: JSON.stringify(formData)
                });
                
                const result = await response.json();
                
                if (result.success) {
                    alert('Booking request submitted successfully! We\'ll contact you soon to confirm your appointment.');
                    document.getElementById('bookingForm').reset();
                } else {
                    alert('Error submitting booking request: ' + result.message);
                }
            } catch (error) {
                console.error('Error submitting booking:', error);
                alert('Error submitting booking request. Please try again.');
            }
        });

        // Set minimum date to today
        document.getElementById('preferredDate').min = new Date().toISOString().split('T')[0];

        // Style selection functionality
        function selectStyle(style) {
            const classicBtn = document.getElementById('classicBtn');
            const modernBtn = document.getElementById('modernBtn');
            const tattooStyleField = document.getElementById('tattooStyle');
            
            // Remove active state from both buttons
            classicBtn.classList.remove('ring-4', 'ring-yellow-400/50', 'scale-110');
            modernBtn.classList.remove('ring-4', 'ring-cyan-400/50', 'scale-110');
            
            // Add active state to selected button
            if (style === 'classic') {
                classicBtn.classList.add('ring-4', 'ring-yellow-400/50', 'scale-110');
                tattooStyleField.value = 'traditional';
                
                // Show classic style notification
                showStyleNotification('Classic Style Selected!', 'Traditional tattoos with bold lines and vibrant colors.', 'yellow');
            } else if (style === 'modern') {
                modernBtn.classList.add('ring-4', 'ring-cyan-400/50', 'scale-110');
                tattooStyleField.value = 'modern';
                
                // Show modern style notification
                showStyleNotification('Modern Style Selected!', 'Contemporary designs with clean lines and modern aesthetics.', 'cyan');
            }
            
            // Auto-scroll to booking form after selection
            setTimeout(() => {
                scrollToBooking();
            }, 1500);
        }

        function showStyleNotification(title, message, color) {
            const notification = document.createElement('div');
            notification.className = `fixed top-20 right-4 bg-${color}-600 text-white px-6 py-4 rounded-lg shadow-xl z-50 transform translate-x-full transition-all duration-500`;
            notification.innerHTML = `
                <div class="flex items-start">
                    <i class="fas fa-${color === 'yellow' ? 'crown' : 'rocket'} text-2xl mr-3 mt-1"></i>
                    <div>
                        <h4 class="font-bold text-lg">${title}</h4>
                        <p class="text-sm opacity-90">${message}</p>
                    </div>
                    <button onclick="this.parentElement.parentElement.remove()" class="ml-4 text-white/70 hover:text-white">
                        <i class="fas fa-times"></i>
                    </button>
                </div>
            `;
            document.body.appendChild(notification);
            
            // Animate in
            setTimeout(() => notification.classList.remove('translate-x-full'), 100);
            
            // Auto remove after 4 seconds
            setTimeout(() => {
                notification.classList.add('translate-x-full');
                setTimeout(() => {
                    if (notification.parentNode) {
                        notification.parentNode.removeChild(notification);
                    }
                }, 500);
            }, 4000);
        }

        // Mobile menu functionality
        document.addEventListener('DOMContentLoaded', function() {
            const mobileMenuBtn = document.getElementById('mobile-menu-btn');
            const mobileMenu = document.getElementById('mobile-menu');
            const navbar = document.getElementById('navbar');
            
            if (mobileMenuBtn && mobileMenu) {
                mobileMenuBtn.addEventListener('click', function() {
                    mobileMenu.classList.toggle('hidden');
                });
                
                // Close mobile menu when clicking outside
                document.addEventListener('click', function(e) {
                    if (!mobileMenuBtn.contains(e.target) && !mobileMenu.contains(e.target)) {
                        mobileMenu.classList.add('hidden');
                    }
                });
            }
            
            // Navbar scroll effect
            if (navbar) {
                window.addEventListener('scroll', function() {
                    if (window.scrollY > 50) {
                        navbar.classList.add('scrolled');
                    } else {
                        navbar.classList.remove('scrolled');
                    }
                });
            }
            
            // Load artist data
            loadArtistData();
        });
    </script>
</body>
</html>
