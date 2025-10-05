
<?php

require_once 'auth.php';

// Redirect if already logged in
if (AdminAuth::isLoggedIn()) {
    header('Location: dashboard.php');
    exit();
}
// Prevent browser caching (important!)
header("Cache-Control: no-cache, must-revalidate, max-age=0");
header("Pragma: no-cache");
header("Expires: Fri, 01 Jan 1990 00:00:00 GMT");


// Initialize variables
$login_error = null;
$logged_out = isset($_GET['logged_out']);
$timeout = isset($_GET['timeout']);

// Handle login form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {// && isset($_POST['login'])
    $username = sanitizeInput($_POST['username'] ?? '');
    $password = $_POST['password'] ?? '';
    
    // Debug: Log the attempt
    error_log("Login attempt - Username: $username, Password length: " . strlen($password));
    
    if (AdminAuth::login($username, $password)) {
        error_log("Login successful for user: $username");
        header('Location: dashboard.php');
        
        exit();
    } else {
        error_log("Login failed for user: $username");
        $login_error = 'Invalid username or password. Please try again.';
    }
}
// exit;
?>
<!DOCTYPE html>
<html lang="en" class="scroll-smooth">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Login - Narshimha Tattoo</title>
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
    <style>
        :root {
            --neon-red: #ff073a;
            --blood-red: #8b0000;
            --dark-gray: #1a1a1a;
            --darker-gray: #0d0d0d;
        }
        
        * {
            scrollbar-width: thin;
            scrollbar-color: var(--neon-red) var(--darker-gray);
        }
        
        *::-webkit-scrollbar {
            width: 8px;
        }
        
        *::-webkit-scrollbar-track {
            background: var(--darker-gray);
        }
        
        *::-webkit-scrollbar-thumb {
            background: var(--neon-red);
            border-radius: 4px;
        }
        
        *::-webkit-scrollbar-thumb:hover {
            background: #ff2555;
        }
        
        .hero-bg {
            background: linear-gradient(135deg, 
                rgba(13, 13, 13, 0.95) 0%, 
                rgba(26, 26, 26, 0.9) 50%, 
                rgba(13, 13, 13, 0.95) 100%),
                url('../img/hero-bg.png') center/cover;
            filter: brightness(0.7);
        }
        
        .neon-glow {
            box-shadow: 0 0 20px rgba(255, 7, 58, 0.5), 0 0 40px rgba(255, 7, 58, 0.3), 0 0 60px rgba(255, 7, 58, 0.1);
            animation: pulse-glow 2s ease-in-out infinite alternate;
        }
        
        @keyframes pulse-glow {
            from { box-shadow: 0 0 20px rgba(255, 7, 58, 0.5), 0 0 40px rgba(255, 7, 58, 0.3), 0 0 60px rgba(255, 7, 58, 0.1); }
            to { box-shadow: 0 0 30px rgba(255, 7, 58, 0.8), 0 0 60px rgba(255, 7, 58, 0.5), 0 0 90px rgba(255, 7, 58, 0.2); }
        }
        
        .glass-morphism {
            background: rgba(26, 26, 26, 0.25);
            backdrop-filter: blur(20px);
            border: 1px solid rgba(255, 255, 255, 0.1);
        }
        
        .gradient-text {
            background: linear-gradient(45deg, var(--neon-red), var(--blood-red));
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
        
        .floating {
            animation: float 6s ease-in-out infinite;
        }
        
        @keyframes float {
            0%, 100% { transform: translateY(0px); }
            50% { transform: translateY(-20px); }
        }
        
        .modern-card {
            background: rgba(26, 26, 26, 0.8);
            backdrop-filter: blur(20px);
            border: 1px solid rgba(255, 255, 255, 0.1);
        }
        
        .particle {
            position: absolute;
            width: 2px;
            height: 2px;
            background: var(--neon-red);
            border-radius: 50%;
            animation: particle-float 15s linear infinite;
        }
        
        @keyframes particle-float {
            0% {
                transform: translateY(100vh) translateX(0);
                opacity: 0;
            }
            10% {
                opacity: 1;
            }
            90% {
                opacity: 1;
            }
            100% {
                transform: translateY(-100px) translateX(100px);
                opacity: 0;
            }
        }
        
        /* Navigation Link Styles */
        .nav-link {
            position: relative;
            overflow: hidden;
            transform: translateY(0);
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        }
        
        .nav-link:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(255, 51, 51, 0.2);
        }
        
        .nav-link::before {
            content: '';
            position: absolute;
            top: 0;
            left: -100%;
            width: 100%;
            height: 100%;
            background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.2), transparent);
            transition: left 0.5s;
        }
        
        .nav-link:hover::before {
            left: 100%;
        }
        
        .nav-link-bg {
            transform-origin: left center;
            transition: transform 0.4s cubic-bezier(0.4, 0, 0.2, 1);
        }
        
        .nav-link-border {
            transform-origin: left center;
            transition: transform 0.4s cubic-bezier(0.4, 0, 0.2, 1);
        }
    </style>
</head>
<body class="min-h-screen hero-bg">
    <!-- Particle Background -->
    <div class="fixed inset-0 overflow-hidden pointer-events-none">
        <div id="particles"></div>
    </div>
    
    <!-- Navigation Bar -->
    <nav class="fixed top-0 w-full z-50 bg-black/20 backdrop-blur-sm border-b border-neon-red/20">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between items-center py-4">
                <!-- Logo - Exact match to main website -->
                <a href="../index.html" class="transition-all duration-300 flex items-center flex-shrink-0 group">
                    <!-- Head Photo -->
                    <div class="relative mr-2">
                        <img src="../img/main-logo/narshimha-head.png" alt="Narshimha Head" class="h-16 w-auto rounded-lg group-hover:shadow-[inset_0_0_25px_rgba(255,7,58,0.4)] transition-all duration-300">
                    </div>
                    
                    <!-- Text Logo -->
                    <div class="flex flex-col">
                        <!-- Main Name -->
                        <h1 class="text-lg font-bold text-white group-hover:text-neon-red transition-colors duration-300 font-serif">
                            Narshimha
                        </h1>
                        
                        <!-- Studio Text with Decorative Lines -->
                        <div class="flex items-center justify-center mt-0.5">
                            <!-- Left decorative line -->
                            <div class="w-4 h-0.5 bg-neon-red transition-all duration-300"></div>
                            
                            <!-- Studio Text -->
                            <span class="text-xs text-gray-400 group-hover:text-gray-300 transition-colors duration-300 font-sans uppercase tracking-wider mx-1">
                                ADMIN PANEL
                            </span>
                            
                            <!-- Right decorative line -->
                            <div class="w-4 h-0.5 bg-neon-red transition-all duration-300"></div>
                        </div>
                        
                        <!-- Bottom Left Accent -->
                        <div class="flex items-center mt-0.5">
                            <div class="w-1 h-1 bg-neon-red rounded-full transition-transform duration-300"></div>
                            <div class="w-2 h-0.5 bg-neon-red ml-1 group-hover:w-12 transition-all duration-700 ease-out"></div>
                        </div>
                    </div>
                </a>
                
                <a href="../index.html" class="nav-link text-white hover:text-neon-red transition-all duration-300 font-medium px-3 py-2 rounded-md relative overflow-hidden group">
                    <span class="relative z-10">Back to Website</span>
                    <div class="nav-link-bg absolute inset-0 bg-gradient-to-r from-neon-red/10 to-red-500/10 scale-x-0 group-hover:scale-x-100 transition-transform duration-300 ease-out"></div>
                    <div class="nav-link-border absolute bottom-0 left-0 w-full h-0.5 bg-neon-red scale-x-0 group-hover:scale-x-100 transition-transform duration-300 ease-out"></div>
                </a>
            </div>
        </div>
    </nav>
    
    <div class="min-h-screen flex items-center justify-center py-20 px-4 sm:px-6 lg:px-8 relative z-10">
        <div class="max-w-md w-full space-y-8">
            <div class="text-center">
                <div class="mx-auto h-24 w-24 flex items-center justify-center rounded-full bg-gradient-to-r from-neon-red to-red-600 neon-glow floating">
                    <i class="fas fa-user-shield text-white text-3xl"></i>
                </div>
                <h2 class="mt-6 text-center text-5xl font-extrabold gradient-text">
                    ADMIN ACCESS
                </h2>
                <p class="mt-2 text-center text-xl text-gray-300 font-script">
                    Narshimha Tattoo Studio
                </p>
                <div class="mt-6 flex justify-center space-x-2">
                    <div class="h-1 w-12 bg-neon-red rounded"></div>
                    <div class="h-1 w-12 bg-red-600 rounded"></div>
                    <div class="h-1 w-12 bg-blood-red rounded"></div>
                </div>
            </div>
            
            <?php if ($logged_out): ?>
                <div class="bg-green-900 border border-green-700 text-green-100 px-4 py-3 rounded-lg">
                    <i class="fas fa-check-circle mr-2"></i>
                    You have been logged out successfully.
                </div>
            <?php endif; ?>
            
            <?php if ($timeout): ?>
                <div class="bg-yellow-900 border border-yellow-700 text-yellow-100 px-4 py-3 rounded-lg">
                    <i class="fas fa-clock mr-2"></i>
                    Your session has expired. Please log in again.
                </div>
            <?php endif; ?>
            
            <?php if ($login_error): ?>
                <div class="bg-red-900 border border-red-700 text-red-100 px-4 py-3 rounded-lg">
                    <i class="fas fa-exclamation-triangle mr-2"></i>
                    <?php echo htmlspecialchars($login_error); ?>
                </div>
            <?php endif; ?>
            
            <form action="login.php" class="mt-8 space-y-6 modern-card p-8 rounded-2xl" method="POST">
                <div class="space-y-6">
                    <div class="relative">
                        <label for="username" class="block text-sm font-medium text-gray-300 mb-3">
                            <i class="fas fa-user mr-2 text-neon-red"></i>Username
                        </label>
                        <input id="username" name="username" type="text" required 
                               class="appearance-none relative block w-full px-4 py-4 border border-gray-600 placeholder-gray-400 text-white bg-gray-800/50 rounded-lg focus:outline-none focus:ring-2 focus:ring-neon-red focus:border-transparent transition-all duration-300 backdrop-blur-sm" 
                               placeholder="Enter your username">
                    </div>
                    <div class="relative">
                        <label for="password" class="block text-sm font-medium text-gray-300 mb-3">
                            <i class="fas fa-lock mr-2 text-neon-red"></i>Password
                        </label>
                        <input id="password" name="password" type="password" required 
                               class="appearance-none relative block w-full px-4 py-4 border border-gray-600 placeholder-gray-400 text-white bg-gray-800/50 rounded-lg focus:outline-none focus:ring-2 focus:ring-neon-red focus:border-transparent transition-all duration-300 backdrop-blur-sm" 
                               placeholder="Enter your password">
                    </div>
                </div>

                <div>
                    <button type="submit" name="login" 
                            class="group relative w-full flex justify-center py-4 px-6 border border-transparent text-lg font-bold rounded-xl text-white bg-gradient-to-r from-neon-red to-red-600 hover:from-red-600 hover:to-neon-red focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-neon-red transition-all duration-300 transform hover:scale-105 neon-glow">
                        <span class="absolute left-0 inset-y-0 flex items-center pl-4">
                            <i class="fas fa-sign-in-alt text-red-200 group-hover:text-white transition-colors"></i>
                        </span>
                        <span>ACCESS SYSTEM</span>
                    </button>
                </div>
                
                <div class="text-center">
                    <div class="bg-yellow-900/20 border border-yellow-600/30 rounded-lg p-4 mb-4">
                        <p class="text-sm text-yellow-300 font-medium">
                            <i class="fas fa-key mr-2"></i>Default Credentials
                        </p>
                        <p class="text-xs text-yellow-200 mt-1">
                            Username: <span class="font-mono bg-gray-800 px-2 py-1 rounded">admin</span><br>
                            Password: <span class="font-mono bg-gray-800 px-2 py-1 rounded">narshimha2024</span>
                        </p>
                        <p class="text-xs text-red-400 mt-2">
                            <i class="fas fa-exclamation-triangle mr-1"></i>
                            Change these in production!
                        </p>
                    </div>
                </div>
            </form>
            
            <!-- System Status Indicators -->
            <div class="flex justify-center space-x-6 mt-8">
                <div class="flex items-center space-x-2">
                    <div class="w-3 h-3 bg-green-500 rounded-full animate-pulse"></div>
                    <span class="text-sm text-gray-300">System Online</span>
                </div>
                <div class="flex items-center space-x-2">
                    <div class="w-3 h-3 bg-blue-500 rounded-full animate-pulse"></div>
                    <span class="text-sm text-gray-300">Database Connected</span>
                </div>
                <div class="flex items-center space-x-2">
                    <div class="w-3 h-3 bg-neon-red rounded-full animate-pulse"></div>
                    <span class="text-sm text-gray-300">Secure Login</span>
                </div>
            </div>
        </div>
    </div>
    
    <script>
        // Particle System for Login Page
        function createParticles() {
            const container = document.getElementById('particles');
            for (let i = 0; i < 20; i++) {
                const particle = document.createElement('div');
                particle.className = 'particle';
                particle.style.cssText = `
                    left: ${Math.random() * 100}%;
                    animation-delay: ${Math.random() * 5}s;
                    animation-duration: ${Math.random() * 10 + 15}s;
                `;
                container.appendChild(particle);
            }
        }
        
        // Initialize particles
        createParticles();
        
        // Enhanced form interactions
        document.querySelectorAll('input').forEach(input => {
            input.addEventListener('focus', function() {
                this.parentElement.classList.add('focused');
            });
            
            input.addEventListener('blur', function() {
                this.parentElement.classList.remove('focused');
            });
        });
        
        // Login form enhancement
        document.querySelector('form').addEventListener('submit', function(e) {
            const button = this.querySelector('button[type="submit"]');
            const originalText = button.innerHTML;
            
            // Show loading state
            button.innerHTML = `
                <i class="fas fa-spinner fa-spin mr-2"></i>
                <span>AUTHENTICATING...</span>
            `;
            button.disabled = true;
            
            // Don't prevent default - let the form submit normally
            // The form will handle the submission to PHP
        });
        
        // Keyboard shortcuts
        document.addEventListener('keydown', function(e) {
            if (e.ctrlKey && e.key === 'Enter') {
                document.querySelector('form').submit();
            }
        });
        
        // Smooth scrolling for navigation
        document.querySelectorAll('a[href^="#"]').forEach(anchor => {
            anchor.addEventListener('click', function (e) {
                e.preventDefault();
                const target = document.querySelector(this.getAttribute('href'));
                if (target) {
                    target.scrollIntoView({
                        behavior: 'smooth'
                    });
                }
            });
        });
    </script>
</body>
</html>