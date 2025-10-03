<?php
require_once 'auth.php';

// Redirect if already logged in
if (AdminAuth::isLoggedIn()) {
    header('Location: dashboard.php');
    exit();
}

$login_error = $login_error ?? null;
$logged_out = isset($_GET['logged_out']);
$timeout = isset($_GET['timeout']);
?>
<!DOCTYPE html>
<html lang="en" class="h-full bg-gray-900">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Login - Narshimha Tattoo</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;600;700;900&family=Dancing+Script:wght@400;600;700&family=Orbitron:wght@400;700;900&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
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
        .cyber-grid {
            background-image: 
                linear-gradient(rgba(255, 7, 58, 0.1) 1px, transparent 1px),
                linear-gradient(90deg, rgba(255, 7, 58, 0.1) 1px, transparent 1px);
            background-size: 50px 50px;
            animation: grid-move 20s linear infinite;
        }
        
        @keyframes grid-move {
            0% { background-position: 0 0; }
            100% { background-position: 50px 50px; }
        }
        
        .neon-glow {
            box-shadow: 0 0 20px rgba(255, 7, 58, 0.5), 0 0 40px rgba(255, 7, 58, 0.3);
        }
        
        .glass-morphism {
            background: rgba(26, 26, 26, 0.25);
            backdrop-filter: blur(20px);
            border: 1px solid rgba(255, 255, 255, 0.1);
        }
        
        .holographic-border {
            position: relative;
            overflow: hidden;
        }
        
        .holographic-border::before {
            content: '';
            position: absolute;
            top: -2px;
            left: -2px;
            right: -2px;
            bottom: -2px;
            background: linear-gradient(45deg, #ff073a, #00d4ff, #8a2be2, #ff073a);
            background-size: 400% 400%;
            z-index: -1;
            border-radius: inherit;
            animation: holographic 3s linear infinite;
        }
        
        @keyframes holographic {
            0% { background-position: 0% 50%; }
            100% { background-position: 400% 50%; }
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
        
        .floating {
            animation: float 6s ease-in-out infinite;
        }
        
        @keyframes float {
            0%, 100% { transform: translateY(0px); }
            50% { transform: translateY(-20px); }
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
    <!-- Particle Background -->
    <div class="fixed inset-0 overflow-hidden pointer-events-none">
        <div id="particles"></div>
    </div>
    
    <div class="min-h-full flex items-center justify-center py-12 px-4 sm:px-6 lg:px-8 relative z-10">
        <div class="max-w-md w-full space-y-8">
            <div class="text-center">
                <div class="mx-auto h-20 w-20 flex items-center justify-center rounded-full bg-gradient-to-r from-neon-red to-electric-blue neon-glow floating">
                    <i class="fas fa-user-shield text-white text-2xl"></i>
                </div>
                <h2 class="mt-6 text-center text-4xl font-extrabold gradient-text font-orbitron">
                    ADMIN PANEL
                </h2>
                <p class="mt-2 text-center text-lg text-gray-300 font-script">
                    Narshimha Tattoo Studio
                </p>
                <div class="mt-4 flex justify-center space-x-4">
                    <div class="h-1 w-8 bg-neon-red rounded"></div>
                    <div class="h-1 w-8 bg-electric-blue rounded"></div>
                    <div class="h-1 w-8 bg-purple-glow rounded"></div>
                </div>
            </div>
            
            <?php if ($logged_out): ?>
                <div class="bg-green-900 border border-green-700 text-green-100 px-4 py-3 rounded">
                    <i class="fas fa-check-circle mr-2"></i>
                    You have been logged out successfully.
                </div>
            <?php endif; ?>
            
            <?php if ($timeout): ?>
                <div class="bg-yellow-900 border border-yellow-700 text-yellow-100 px-4 py-3 rounded">
                    <i class="fas fa-clock mr-2"></i>
                    Your session has expired. Please log in again.
                </div>
            <?php endif; ?>
            
            <?php if ($login_error): ?>
                <div class="bg-red-900 border border-red-700 text-red-100 px-4 py-3 rounded">
                    <i class="fas fa-exclamation-triangle mr-2"></i>
                    <?php echo htmlspecialchars($login_error); ?>
                </div>
            <?php endif; ?>
            
            <form class="mt-8 space-y-6 glass-morphism p-8 rounded-2xl holographic-border" method="POST">
                <div class="space-y-4">
                    <div class="relative">
                        <label for="username" class="block text-sm font-medium text-gray-300 mb-2">
                            <i class="fas fa-user mr-2 text-neon-red"></i>Username
                        </label>
                        <input id="username" name="username" type="text" required 
                               class="appearance-none relative block w-full px-4 py-3 border border-gray-600 placeholder-gray-400 text-white bg-gray-800/50 rounded-lg focus:outline-none focus:ring-2 focus:ring-neon-red focus:border-transparent transition-all duration-300 backdrop-blur-sm" 
                               placeholder="Enter your username">
                        <div class="absolute inset-0 rounded-lg bg-gradient-to-r from-neon-red/20 to-electric-blue/20 opacity-0 transition-opacity duration-300 pointer-events-none focus-within:opacity-100"></div>
                    </div>
                    <div class="relative">
                        <label for="password" class="block text-sm font-medium text-gray-300 mb-2">
                            <i class="fas fa-lock mr-2 text-neon-red"></i>Password
                        </label>
                        <input id="password" name="password" type="password" required 
                               class="appearance-none relative block w-full px-4 py-3 border border-gray-600 placeholder-gray-400 text-white bg-gray-800/50 rounded-lg focus:outline-none focus:ring-2 focus:ring-neon-red focus:border-transparent transition-all duration-300 backdrop-blur-sm" 
                               placeholder="Enter your password">
                        <div class="absolute inset-0 rounded-lg bg-gradient-to-r from-neon-red/20 to-electric-blue/20 opacity-0 transition-opacity duration-300 pointer-events-none focus-within:opacity-100"></div>
                    </div>
                </div>

                <div>
                    <button type="submit" name="login" 
                            class="group relative w-full flex justify-center py-4 px-6 border border-transparent text-lg font-bold rounded-xl text-white bg-gradient-to-r from-neon-red to-red-600 hover:from-red-600 hover:to-neon-red focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-neon-red transition-all duration-300 transform hover:scale-105 neon-glow">
                        <span class="absolute left-0 inset-y-0 flex items-center pl-4">
                            <i class="fas fa-sign-in-alt text-red-200 group-hover:text-white transition-colors"></i>
                        </span>
                        <span class="font-orbitron">ACCESS SYSTEM</span>
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
            
            <div class="text-center">
                <a href="../index.html" class="inline-flex items-center text-neon-red hover:text-electric-blue transition-colors duration-300 group">
                    <i class="fas fa-arrow-left mr-2 group-hover:-translate-x-1 transition-transform"></i>
                    <span class="font-medium">Back to Website</span>
                </a>
            </div>
            
            <!-- System Status Indicators -->
            <div class="flex justify-center space-x-4 mt-8">
                <div class="flex items-center space-x-2">
                    <div class="w-2 h-2 bg-green-500 rounded-full animate-pulse"></div>
                    <span class="text-xs text-gray-400">System Online</span>
                </div>
                <div class="flex items-center space-x-2">
                    <div class="w-2 h-2 bg-blue-500 rounded-full animate-pulse"></div>
                    <span class="text-xs text-gray-400">Database Connected</span>
                </div>
            </div>
        </div>
    </div>
    
    <script>
        // Particle System for Login Page
        function createParticles() {
            const container = document.getElementById('particles');
            for (let i = 0; i < 30; i++) {
                const particle = document.createElement('div');
                particle.style.cssText = `
                    position: absolute;
                    width: 2px;
                    height: 2px;
                    background: #ff073a;
                    border-radius: 50%;
                    left: ${Math.random() * 100}%;
                    animation: particle-float ${Math.random() * 10 + 10}s linear infinite;
                    animation-delay: ${Math.random() * 5}s;
                `;
                container.appendChild(particle);
            }
        }
        
        // CSS for particle animation
        const style = document.createElement('style');
        style.textContent = `
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
        `;
        document.head.appendChild(style);
        
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
            
            button.innerHTML = `
                <i class="fas fa-spinner fa-spin mr-2"></i>
                <span class="font-orbitron">AUTHENTICATING...</span>
            `;
            button.disabled = true;
            
            // Re-enable after form processes (this is just for UX)
            setTimeout(() => {
                if (!window.location.href.includes('dashboard')) {
                    button.innerHTML = originalText;
                    button.disabled = false;
                }
            }, 2000);
        });
        
        // Keyboard shortcuts
        document.addEventListener('keydown', function(e) {
            if (e.ctrlKey && e.key === 'Enter') {
                document.querySelector('form').submit();
            }
        });
    </script>
</body>
</html>

