<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>🚧 Maintenance Mode | Bhaktivedanta Gurukul - School of Excellence</title>
    
    <!-- Tailwind CSS CDN -->
    <script src="https://cdn.tailwindcss.com"></script>
    
    <!-- Font Awesome -->
    <script src="https://kit.fontawesome.com/a076d05399.js"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800;900&family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    
    <!-- Custom Styles -->
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Inter', sans-serif;
        }
        
        body {
            overflow: hidden;
            width: 100vw;
            height: 100vh;
        }
        
        /* Custom Scrollbar */
        ::-webkit-scrollbar {
            width: 8px;
        }
        ::-webkit-scrollbar-track {
            background: rgba(255,255,255,0.1);
        }
        ::-webkit-scrollbar-thumb {
            background: linear-gradient(45deg, #f59e0b, #f97316);
            border-radius: 4px;
        }
        
        /* Animations */
        @keyframes float {
            0%, 100% { transform: translateY(0px) rotate(0deg); }
            50% { transform: translateY(-20px) rotate(5deg); }
        }
        
        @keyframes pulse {
            0%, 100% { opacity: 1; }
            50% { opacity: 0.5; }
        }
        
        @keyframes spin-slow {
            from { transform: rotate(0deg); }
            to { transform: rotate(360deg); }
        }
        
        @keyframes gradient {
            0% { background-position: 0% 50%; }
            50% { background-position: 100% 50%; }
            100% { background-position: 0% 50%; }
        }
        
        @keyframes shine {
            0% { left: -100%; }
            100% { left: 200%; }
        }
        
        @keyframes bounce {
            0%, 100% { transform: translateY(0); }
            50% { transform: translateY(-10px); }
        }
        
        .animate-float {
            animation: float 6s ease-in-out infinite;
        }
        
        .animate-float-2 {
            animation: float 8s ease-in-out infinite;
            animation-delay: 1s;
        }
        
        .animate-pulse-slow {
            animation: pulse 3s ease-in-out infinite;
        }
        
        .animate-spin-slow {
            animation: spin-slow 20s linear infinite;
        }
        
        .animate-gradient {
            background-size: 400% 400%;
            animation: gradient 15s ease infinite;
        }
        
        .animate-bounce-slow {
            animation: bounce 2s ease-in-out infinite;
        }
        
        /* Glass Effect */
        .glass {
            background: rgba(255, 255, 255, 0.1);
            backdrop-filter: blur(10px);
            border: 1px solid rgba(255, 255, 255, 0.2);
            box-shadow: 0 8px 32px rgba(0, 0, 0, 0.1);
        }
        
        .glass-dark {
            background: rgba(0, 0, 0, 0.2);
            backdrop-filter: blur(10px);
            border: 1px solid rgba(255, 255, 255, 0.1);
        }
        
        /* Text Gradient */
        .text-gradient {
            background: linear-gradient(90deg, #f59e0b, #f97316, #dc2626);
            -webkit-background-clip: text;
            background-clip: text;
            color: transparent;
            background-size: 200% auto;
            animation: gradient 3s ease infinite;
        }
        
        /* Shine Effect */
        .shine-effect {
            position: relative;
            overflow: hidden;
        }
        
        .shine-effect::after {
            content: '';
            position: absolute;
            top: -50%;
            left: -60%;
            width: 20%;
            height: 200%;
            opacity: 0;
            transform: rotate(30deg);
            background: rgba(255, 255, 255, 0.13);
            background: linear-gradient(
                to right,
                rgba(255, 255, 255, 0.13) 0%,
                rgba(255, 255, 255, 0.13) 77%,
                rgba(255, 255, 255, 0.5) 92%,
                rgba(255, 255, 255, 0.0) 100%
            );
        }
        
        .shine-effect:hover::after {
            opacity: 1;
            left: 130%;
            transition-property: left, top, opacity;
            transition-duration: 0.7s, 0.7s, 0.15s;
            transition-timing-function: ease;
        }
        
        /* Loader */
        .loader {
            border: 3px solid rgba(255, 255, 255, 0.3);
            border-top: 3px solid #f59e0b;
            border-radius: 50%;
            width: 40px;
            height: 40px;
            animation: spin 1s linear infinite;
        }
        
        @keyframes spin {
            0% { transform: rotate(0deg); }
            100% { transform: rotate(360deg); }
        }
    </style>
    
    <!-- Tailwind Config -->
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        'primary': '#f59e0b',
                        'primary-dark': '#d97706',
                        'secondary': '#1e40af',
                        'dark': '#0f172a',
                        'light': '#f8fafc'
                    },
                    animation: {
                        'ping-slow': 'ping 3s cubic-bezier(0, 0, 0.2, 1) infinite',
                        'bounce-slow': 'bounce 3s infinite'
                    }
                }
            }
        }
    </script>
</head>
<body class="w-full h-screen bg-gradient-to-br from-gray-900 via-gray-800 to-slate-900 text-white overflow-hidden">
    
    <!-- Animated Background -->
    <div class="absolute inset-0 overflow-hidden">
        <!-- Grid Pattern -->
        <div class="absolute inset-0" style="
            background-image: 
                radial-gradient(circle at 25% 25%, rgba(120, 119, 198, 0.1) 0%, transparent 55%),
                radial-gradient(circle at 75% 75%, rgba(255, 119, 198, 0.1) 0%, transparent 55%);
        "></div>
        
        <!-- Floating Elements -->
        <div class="absolute top-1/4 left-1/4 w-72 h-72 rounded-full bg-gradient-to-r from-orange-500/10 to-amber-500/5 animate-float"></div>
        <div class="absolute bottom-1/3 right-1/4 w-64 h-64 rounded-full bg-gradient-to-r from-blue-500/10 to-cyan-500/5 animate-float-2"></div>
        <div class="absolute top-2/3 left-1/3 w-48 h-48 rounded-full bg-gradient-to-r from-purple-500/10 to-pink-500/5 animate-float" style="animation-delay: 2s;"></div>
        
        <!-- Animated Lines -->
        <div class="absolute top-0 left-0 w-full h-1 bg-gradient-to-r from-transparent via-orange-500/30 to-transparent animate-pulse"></div>
        <div class="absolute bottom-0 left-0 w-full h-1 bg-gradient-to-r from-transparent via-blue-500/30 to-transparent animate-pulse" style="animation-delay: 1s;"></div>
    </div>
    
    <!-- Main Container -->
    <div class="relative w-full h-full flex flex-col">
        
        <!-- Top Bar -->
        <div class="w-full py-4 px-6 flex justify-between items-center glass-dark">
            <div class="flex items-center space-x-3">
                <div class="w-12 h-12 rounded-xl bg-gradient-to-br from-orange-500 to-amber-500 flex items-center justify-center shadow-lg">
                    <i class="fas fa-graduation-cap text-white text-xl"></i>
                </div>
                <div>
                    <h1 class="text-xl font-bold">Bhaktivedanta Gurukul </h1>
                    <p class="text-xs text-gray-400">School of Excellence</p>
                </div>
            </div>
            
            <div class="flex items-center space-x-4">
                <div class="hidden md:flex items-center space-x-2">
                    <span class="w-2 h-2 rounded-full bg-green-500 animate-pulse"></span>
                    <span class="text-sm">Maintenance Mode Active</span>
                </div>
                <div class="px-4 py-2 rounded-lg bg-gradient-to-r from-orange-500/20 to-amber-500/20 border border-orange-500/30">
                    <span class="text-sm font-medium">ID: MNT-001</span>
                </div>
            </div>
        </div>
        
        <!-- Main Content Area -->
        <div class="flex-1 w-full flex flex-col lg:flex-row p-4 md:p-8 overflow-hidden">
            
            <!-- Left Panel -->
            <div class="w-full lg:w-2/5 flex flex-col p-4 md:p-6">
                
                <!-- Maintenance Icon -->
                <div class="flex-1 flex flex-col items-center justify-center mb-8">
                    <div class="relative mb-10">
                        <!-- Outer Ring -->
                        <div class="absolute -inset-4 border-4 border-orange-500/20 rounded-full animate-spin-slow"></div>
                        
                        <!-- Middle Ring -->
                        <div class="absolute -inset-2 border-4 border-amber-500/30 rounded-full animate-spin-slow" style="animation-direction: reverse; animation-duration: 15s;"></div>
                        
                        <!-- Main Icon -->
                        <div class="relative w-48 h-48 md:w-56 md:h-56 rounded-full bg-gradient-to-br from-orange-500 via-amber-500 to-yellow-500 flex items-center justify-center shadow-2xl animate-bounce-slow">
                            <i class="fas fa-tools text-white text-5xl md:text-6xl"></i>
                            
                            <!-- Small Icons Around -->
                            <div class="absolute -top-2 left-1/2 transform -translate-x-1/2 w-12 h-12 glass rounded-full flex items-center justify-center">
                                <i class="fas fa-server text-amber-300"></i>
                            </div>
                            <div class="absolute -bottom-2 left-1/2 transform -translate-x-1/2 w-12 h-12 glass rounded-full flex items-center justify-center">
                                <i class="fas fa-database text-green-300"></i>
                            </div>
                            <div class="absolute top-1/2 -left-2 transform -translate-y-1/2 w-12 h-12 glass rounded-full flex items-center justify-center">
                                <i class="fas fa-shield-alt text-blue-300"></i>
                            </div>
                            <div class="absolute top-1/2 -right-2 transform -translate-y-1/2 w-12 h-12 glass rounded-full flex items-center justify-center">
                                <i class="fas fa-bolt text-yellow-300"></i>
                            </div>
                        </div>
                    </div>
                    
                    <h2 class="text-4xl md:text-5xl font-bold mb-4 text-center">
                        <span class="text-gradient">We'll Be Back</span>
                    </h2>
                    <p class="text-gray-300 text-center max-w-md">
                        Our site is temporarily unavailable due to scheduled maintenance
                    </p>
                </div>
                
                <!-- Progress Section -->
                <div class="glass rounded-2xl p-6 mb-6">
                    <div class="flex justify-between items-center mb-4">
                        <h3 class="text-xl font-bold">Maintenance Progress</h3>
                        <span class="text-2xl font-bold text-gradient" id="progressPercent">65%</span>
                    </div>
                    
                    <!-- Progress Bar -->
                    <div class="w-full h-3 bg-gray-700 rounded-full overflow-hidden mb-2">
                        <div id="progressBar" class="h-full bg-gradient-to-r from-orange-500 to-amber-500 rounded-full transition-all duration-1000" style="width: 65%"></div>
                    </div>
                    
                    <div class="flex justify-between text-sm text-gray-400">
                        <span>Started: 1:00 PM</span>
                        <span>Est. Complete: 3:00 PM</span>
                    </div>
                    
                    <!-- Progress Steps -->
                    <div class="mt-6 grid grid-cols-2 gap-3">
                        <div class="flex items-center">
                            <div class="w-8 h-8 rounded-full bg-green-500/20 flex items-center justify-center mr-3">
                                <i class="fas fa-check text-green-400 text-sm"></i>
                            </div>
                            <div>
                                <p class="text-sm font-medium">Backup Complete</p>
                                <p class="text-xs text-gray-500">12:45 PM</p>
                            </div>
                        </div>
                        
                        <div class="flex items-center">
                            <div class="w-8 h-8 rounded-full bg-blue-500/20 flex items-center justify-center mr-3">
                                <i class="fas fa-sync-alt text-blue-400 text-sm animate-spin"></i>
                            </div>
                            <div>
                                <p class="text-sm font-medium">Server Update</p>
                                <p class="text-xs text-gray-500">In Progress</p>
                            </div>
                        </div>
                        
                        <div class="flex items-center">
                            <div class="w-8 h-8 rounded-full bg-amber-500/20 flex items-center justify-center mr-3">
                                <div class="loader-small w-6 h-6"></div>
                            </div>
                            <div>
                                <p class="text-sm font-medium">DB Optimization</p>
                                <p class="text-xs text-gray-500">65% Complete</p>
                            </div>
                        </div>
                        
                        <div class="flex items-center">
                            <div class="w-8 h-8 rounded-full bg-gray-700 flex items-center justify-center mr-3">
                                <i class="fas fa-clock text-gray-400 text-sm"></i>
                            </div>
                            <div>
                                <p class="text-sm font-medium">Testing</p>
                                <p class="text-xs text-gray-500">Pending</p>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Countdown Timer -->
                <div class="glass rounded-2xl p-6">
                    <h3 class="text-xl font-bold mb-4 flex items-center">
                        <i class="fas fa-clock text-amber-400 mr-3"></i>
                        Time Remaining
                    </h3>
                    <div class="flex justify-center space-x-4 mb-4">
                        <div class="text-center">
                            <div class="text-4xl font-bold text-gradient" id="hours">02</div>
                            <div class="text-sm text-gray-400">Hours</div>
                        </div>
                        <div class="text-4xl font-bold text-gray-600">:</div>
                        <div class="text-center">
                            <div class="text-4xl font-bold text-gradient" id="minutes">15</div>
                            <div class="text-sm text-gray-400">Minutes</div>
                        </div>
                        <div class="text-4xl font-bold text-gray-600">:</div>
                        <div class="text-center">
                            <div class="text-4xl font-bold text-gradient" id="seconds">30</div>
                            <div class="text-sm text-gray-400">Seconds</div>
                        </div>
                    </div>
                    <p class="text-center text-gray-400 text-sm">
                        Estimated completion: <span class="text-amber-300 font-bold">3:00 PM IST</span>
                    </p>
                </div>
            </div>
            
            <!-- Right Panel -->
            <div class="w-full lg:w-3/5 flex flex-col p-4 md:p-6 overflow-y-auto">
                
                <!-- Status Message -->
                <div class="glass rounded-2xl p-6 mb-6">
                    <div class="flex items-start">
                        <div class="w-12 h-12 rounded-full bg-gradient-to-r from-orange-500/20 to-amber-500/20 flex items-center justify-center mr-4">
                            <i class="fas fa-exclamation-triangle text-amber-400 text-xl"></i>
                        </div>
                        <div class="flex-1">
                            <h3 class="text-2xl font-bold mb-2">Scheduled Maintenance</h3>
                            <p class="text-gray-300 mb-4">
                                We're currently performing <span class="text-amber-300 font-bold">essential system upgrades and optimizations</span> to enhance your learning experience. This maintenance includes server migration, database optimization, security enhancements, and performance improvements.
                            </p>
                            <div class="flex items-center text-sm text-gray-400">
                                <i class="fas fa-info-circle mr-2"></i>
                                <span>We apologize for the inconvenience and appreciate your patience.</span>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Live Updates -->
                <div class="glass rounded-2xl p-6 mb-6">
                    <div class="flex justify-between items-center mb-4">
                        <h3 class="text-xl font-bold flex items-center">
                            <i class="fas fa-broadcast-tower text-green-400 mr-3"></i>
                            Live Updates
                        </h3>
                        <span class="text-xs px-3 py-1 rounded-full bg-green-500/20 text-green-400 flex items-center">
                            <span class="w-2 h-2 rounded-full bg-green-400 animate-pulse mr-2"></span>
                            LIVE
                        </span>
                    </div>
                    
                    <div class="space-y-4 max-h-60 overflow-y-auto pr-2">
                        <!-- Update Items -->
                        <div class="update-item border-l-4 border-green-500 pl-4 py-3">
                            <div class="flex justify-between items-center">
                                <h4 class="font-medium">Database Optimization Phase 2 Complete</h4>
                                <span class="text-xs text-gray-500">2:45 PM</span>
                            </div>
                            <p class="text-sm text-gray-400 mt-1">Successfully completed optimization of user databases.</p>
                        </div>
                        
                        <div class="update-item border-l-4 border-blue-500 pl-4 py-3">
                            <div class="flex justify-between items-center">
                                <h4 class="font-medium">Server Migration In Progress</h4>
                                <span class="text-xs text-gray-500">2:30 PM</span>
                            </div>
                            <p class="text-sm text-gray-400 mt-1">Migrating to new cloud servers (85% complete).</p>
                        </div>
                        
                        <div class="update-item border-l-4 border-amber-500 pl-4 py-3">
                            <div class="flex justify-between items-center">
                                <h4 class="font-medium">Security Updates Applied</h4>
                                <span class="text-xs text-gray-500">2:15 PM</span>
                            </div>
                            <p class="text-sm text-gray-400 mt-1">Latest security patches and encryption protocols installed.</p>
                        </div>
                        
                        <div class="update-item border-l-4 border-purple-500 pl-4 py-3">
                            <div class="flex justify-between items-center">
                                <h4 class="font-medium">Performance Testing Started</h4>
                                <span class="text-xs text-gray-500">2:00 PM</span>
                            </div>
                            <p class="text-sm text-gray-400 mt-1">Running performance benchmarks on updated systems.</p>
                        </div>
                        
                        <div class="update-item border-l-4 border-gray-500 pl-4 py-3">
                            <div class="flex justify-between items-center">
                                <h4 class="font-medium">Maintenance Window Started</h4>
                                <span class="text-xs text-gray-500">1:00 PM</span>
                            </div>
                            <p class="text-sm text-gray-400 mt-1">Scheduled maintenance has begun. All systems offline.</p>
                        </div>
                    </div>
                    
                    <div class="mt-4 pt-4 border-t border-gray-700">
                        <div class="flex items-center text-sm text-gray-400">
                            <i class="fas fa-sync-alt animate-spin mr-2"></i>
                            <span>Updates refresh automatically every 2 minutes</span>
                        </div>
                    </div>
                </div>
                
                <!-- Contact & Actions -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <!-- Contact Card -->
                    <div class="glass rounded-2xl p-6">
                        <h3 class="text-xl font-bold mb-4 flex items-center">
                            <i class="fas fa-headset text-blue-400 mr-3"></i>
                            Need Help?
                        </h3>
                        <p class="text-gray-300 mb-4">For urgent inquiries during maintenance:</p>
                        
                        <div class="space-y-3">
                            <a href="mailto:info@ourgurukul.org" 
                               class="flex items-center p-3 rounded-lg bg-gradient-to-r from-blue-500/10 to-blue-600/10 border border-blue-500/30 hover:from-blue-500/20 hover:to-blue-600/20 transition-all duration-300 shine-effect">
                                <div class="w-10 h-10 rounded-full bg-blue-500/20 flex items-center justify-center mr-3">
                                    <i class="fas fa-envelope text-blue-400"></i>
                                </div>
                                <div>
                                    <div class="font-medium">Email Support</div>
                                    <div class="text-xs text-gray-400">info@ourgurukul.org</div>
                                </div>
                            </a>
                            
                            <a href="#" 
                               class="flex items-center p-3 rounded-lg bg-gradient-to-r from-green-500/10 to-green-600/10 border border-green-500/30 hover:from-green-500/20 hover:to-green-600/20 transition-all duration-300 shine-effect">
                                <div class="w-10 h-10 rounded-full bg-green-500/20 flex items-center justify-center mr-3">
                                    <i class="fab fa-whatsapp text-green-400"></i>
                                </div>
                                <div>
                                    <div class="font-medium">WhatsApp Support</div>
                                    <div class="text-xs text-gray-400">+91 7618040040</div>
                                </div>
                            </a>
                        </div>
                    </div>
                    
                    <!-- Social & Notifications -->
                    <div class="glass rounded-2xl p-6">
                        <h3 class="text-xl font-bold mb-4 flex items-center">
                            <i class="fas fa-bell text-amber-400 mr-3"></i>
                            Stay Updated
                        </h3>
                        <p class="text-gray-300 mb-4">Get notified when we're back online:</p>
                        
                        <!-- Notification Form -->
                        <div class="mb-6">
                            <div class="flex">
                                <input type="email" 
                                       placeholder="Enter your email" 
                                       class="flex-1 bg-gray-800/50 border border-gray-700 rounded-l-lg px-4 py-3 focus:outline-none focus:border-amber-500">
                                <button class="bg-gradient-to-r from-amber-500 to-orange-500 px-4 rounded-r-lg font-medium hover:from-amber-600 hover:to-orange-600 transition-all duration-300">
                                    Notify Me
                                </button>
                            </div>
                        </div>
                        
                        <!-- Social Links -->
                        <div>
                            <p class="text-gray-300 mb-3">Follow us for updates:</p>
                            <div class="flex space-x-3">
                                <a href="https://whatsapp.com/channel/0029VbB2O3F8KMqcp0Eu4A21" class="bg-secondary w-10 h-10 rounded-full flex items-center justify-center text-gray-300 hover:text-white hover:bg-accent transition-all duration-300">
                                    <i class="fab fa-whatsapp"></i>
                                </a>
                                <a href="https://www.instagram.com/bhaktivedanta.gurukul" class="bg-secondary w-10 h-10 rounded-full flex items-center justify-center text-gray-300 hover:text-white hover:bg-accent transition-all duration-300">
                                    <i class="fab fa-instagram"></i>
                                </a>
                                <a href="https://www.facebook.com/share/1Bd7wGt7PP/" class="bg-secondary w-10 h-10 rounded-full flex items-center justify-center text-gray-300 hover:text-white hover:bg-accent transition-all duration-300">
                                    <i class="fab fa-facebook-f"></i>
                                </a>
                                <a href="https://x.com/BVGurukulOffice" class="bg-secondary w-10 h-10 rounded-full flex items-center justify-center text-gray-300 hover:text-white hover:bg-accent transition-all duration-300">
                                    <i class="fab fa-x"></i>
                                </a>

                                <a href="https://www.youtube.com/@BhaktivedantaGurukul" class="bg-secondary w-10 h-10 rounded-full flex items-center justify-center text-gray-300 hover:text-white hover:bg-accent transition-all duration-300">
                                    <i class="fab fa-youtube"></i>
                                </a>
                                <a href="https://www.linkedin.com/in/bhaktivedanta-gurukul/" class="bg-secondary w-10 h-10 rounded-full flex items-center justify-center text-gray-300 hover:text-white hover:bg-accent transition-all duration-300">
                                    <i class="fab fa-linkedin-in"></i>
                                </a>

                                <a href="https://t.me/BhaktivedantaGurukul" class="bg-secondary w-10 h-10 rounded-full flex items-center justify-center text-gray-300 hover:text-white hover:bg-accent transition-all duration-300">
                                    <i class="fab fa-telegram"></i>
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Footer -->
        <div class="w-full py-4 px-6 glass-dark text-center">
            <p class="text-gray-400">
                © 2025 Bhaktivedanta Gurukul - School of Excellence. All rights reserved. 
                <span class="text-amber-300 mx-2">•</span> 
                Maintenance Window: 1:00 PM - 3:00 PM IST
                <span class="text-amber-300 mx-2">•</span> 
                <span id="visitorCount" class="text-blue-300">1,247 visitors waiting</span>
            </p>
        </div>
        
        <!-- Live Notification -->
        <div id="liveNotification" class="fixed top-6 right-6 glass rounded-xl p-4 max-w-sm transform translate-x-full transition-transform duration-500">
            <div class="flex items-start">
                <div class="w-10 h-10 rounded-full bg-green-500/20 flex items-center justify-center mr-3">
                    <i class="fas fa-sync-alt text-green-400 animate-spin"></i>
                </div>
                <div>
                    <h4 class="font-bold">Live Update</h4>
                    <p class="text-sm text-gray-300" id="notificationText">Database optimization progressing well</p>
                    <p class="text-xs text-gray-500 mt-1">Just now</p>
                </div>
                <button onclick="hideNotification()" class="ml-4 text-gray-500 hover:text-white">
                    <i class="fas fa-times"></i>
                </button>
            </div>
        </div>
    </div>

    <script>
        // Initialize variables
        let progress = 65;
        let hours = 2;
        let minutes = 15;
        let seconds = 30;
        let visitorCount = 1247;
        
        // DOM Elements
        const progressBar = document.getElementById('progressBar');
        const progressPercent = document.getElementById('progressPercent');
        const hoursElement = document.getElementById('hours');
        const minutesElement = document.getElementById('minutes');
        const secondsElement = document.getElementById('seconds');
        const visitorCountElement = document.getElementById('visitorCount');
        const liveNotification = document.getElementById('liveNotification');
        const notificationText = document.getElementById('notificationText');
        
        // Update Countdown Timer
        function updateCountdown() {
            seconds--;
            
            if (seconds < 0) {
                seconds = 59;
                minutes--;
                
                if (minutes < 0) {
                    minutes = 59;
                    hours--;
                    
                    if (hours < 0) {
                        hours = 0;
                        minutes = 0;
                        seconds = 0;
                        // Maintenance complete
                        showNotification("Maintenance completed successfully! Site will be live shortly.");
                    }
                }
            }
            
            // Update display
            hoursElement.textContent = hours.toString().padStart(2, '0');
            minutesElement.textContent = minutes.toString().padStart(2, '0');
            secondsElement.textContent = seconds.toString().padStart(2, '0');
            
            // Update progress based on time
            const totalSeconds = 2 * 60 * 60; // 2 hours in seconds
            const elapsedSeconds = (2 * 60 * 60) - (hours * 60 * 60 + minutes * 60 + seconds);
            progress = Math.min(95, 65 + (elapsedSeconds / totalSeconds) * 35);
            
            // Update progress bar
            progressBar.style.width = `${progress}%`;
            progressPercent.textContent = `${Math.round(progress)}%`;
        }
        
        // Update visitor count randomly
        function updateVisitorCount() {
            // Simulate visitor count changes
            const change = Math.floor(Math.random() * 10) - 3; // -3 to +6
            visitorCount += change;
            if (visitorCount < 1200) visitorCount = 1200;
            
            visitorCountElement.textContent = `${visitorCount.toLocaleString()} visitors waiting`;
        }
        
        // Show notification
        function showNotification(message) {
            notificationText.textContent = message;
            liveNotification.style.transform = 'translateX(0)';
            
            // Auto hide after 5 seconds
            setTimeout(hideNotification, 5000);
        }
        
        // Hide notification
        function hideNotification() {
            liveNotification.style.transform = 'translateX(100%)';
        }
        
        // Simulate live updates
        function simulateUpdates() {
            const updates = [
                "Server migration: 85% complete",
                "Database optimization phase 3 started",
                "Security patches applied successfully",
                "Performance testing in progress",
                "Cache systems being optimized",
                "User data verification completed",
                "Load balancer configuration updated",
                "Backup systems verified and tested"
            ];
            
            // Show initial notification after 3 seconds
            setTimeout(() => {
                showNotification("Maintenance is progressing as scheduled");
            }, 3000);
            
            // Show random updates every 30-90 seconds
            setInterval(() => {
                const randomUpdate = updates[Math.floor(Math.random() * updates.length)];
                showNotification(randomUpdate);
            }, 30000 + Math.random() * 60000);
        }
        
        // Add shine effect to glass cards on hover
        function initShineEffects() {
            const shineElements = document.querySelectorAll('.shine-effect');
            shineElements.forEach(element => {
                element.addEventListener('mouseenter', function() {
                    this.style.transform = 'translateY(-2px)';
                });
                
                element.addEventListener('mouseleave', function() {
                    this.style.transform = 'translateY(0)';
                });
            });
        }
        
        // Initialize everything when page loads
        document.addEventListener('DOMContentLoaded', function() {
            // Start countdown
            setInterval(updateCountdown, 1000);
            
            // Update visitor count every 10 seconds
            setInterval(updateVisitorCount, 10000);
            
            // Simulate live updates
            simulateUpdates();
            
            // Initialize effects
            initShineEffects();
            
            // Update time immediately
            updateCountdown();
            
            // Add click effect to buttons
            const buttons = document.querySelectorAll('button, .shine-effect');
            buttons.forEach(button => {
                button.addEventListener('mousedown', function() {
                    this.style.transform = 'scale(0.98)';
                });
                
                button.addEventListener('mouseup', function() {
                    this.style.transform = 'scale(1)';
                });
                
                button.addEventListener('mouseleave', function() {
                    this.style.transform = 'scale(1)';
                });
            });
            
            // Form submission
            const notifyForm = document.querySelector('input[type="email"]');
            const notifyButton = document.querySelector('button.bg-gradient-to-r');
            
            notifyButton.addEventListener('click', function() {
                if (notifyForm.value && notifyForm.value.includes('@')) {
                    showNotification("You'll be notified when we're back online!");
                    notifyForm.value = '';
                } else {
                    showNotification("Please enter a valid email address");
                }
            });
            
            notifyForm.addEventListener('keypress', function(e) {
                if (e.key === 'Enter') {
                    notifyButton.click();
                }
            });
        });
    </script>
</body>
</html>