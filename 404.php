<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>404 - Page Not Found | Bhaktivedanta Gurukul</title>
    
    <!-- Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com"></script>
    
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&family=Inter:wght@300;400;500;600&family=Playfair+Display:wght@400;500;600;700&display=swap" rel="stylesheet">
    
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Inter', sans-serif;
        }
        
        html, body {
            width: 100vw;
            height: 100vh;
            overflow: hidden;
        }
        
        h1, h2, h3, .brand-font {
            font-family: 'Playfair Display', serif;
        }
        
        /* Animations */
        @keyframes float {
            0%, 100% { transform: translateY(0) translateX(0); }
            50% { transform: translateY(-20px) translateX(10px); }
        }
        
        @keyframes pulse {
            0%, 100% { opacity: 1; }
            50% { opacity: 0.5; }
        }
        
        @keyframes glow {
            0%, 100% { 
                box-shadow: 0 0 20px rgba(220, 38, 38, 0.3);
                filter: brightness(1);
            }
            50% { 
                box-shadow: 0 0 40px rgba(220, 38, 38, 0.5);
                filter: brightness(1.1);
            }
        }
        
        @keyframes spin {
            from { transform: rotate(0deg); }
            to { transform: rotate(360deg); }
        }
        
        @keyframes textShine {
            0% { background-position: 0% 50%; }
            100% { background-position: 100% 50%; }
        }
        
        @keyframes gradientShift {
            0% { background-position: 0% 0%; }
            50% { background-position: 100% 100%; }
            100% { background-position: 0% 0%; }
        }
        
        @keyframes slideIn {
            from { transform: translateY(50px); opacity: 0; }
            to { transform: translateY(0); opacity: 1; }
        }
        
        .animate-float {
            animation: float 6s ease-in-out infinite;
        }
        
        .animate-float-delay {
            animation: float 7s ease-in-out infinite;
            animation-delay: 1s;
        }
        
        .animate-pulse {
            animation: pulse 2s ease-in-out infinite;
        }
        
        .animate-glow {
            animation: glow 3s ease-in-out infinite;
        }
        
        .animate-spin {
            animation: spin 20s linear infinite;
        }
        
        .animate-gradient {
            background-size: 200% 200%;
            animation: gradientShift 15s ease infinite;
        }
        
        .animate-slide-in {
            animation: slideIn 0.8s ease-out forwards;
        }
        
        /* Text Gradient */
        .text-gradient {
            background: linear-gradient(135deg, #B91C1C 0%, #DC2626 25%, #F59E0B 50%, #DC2626 75%, #B91C1C 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
            background-size: 200% auto;
        }
        
        .text-gradient-alt {
            background: linear-gradient(135deg, #0C4A6E 0%, #1D4ED8 50%, #3B82F6 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }
        
        .shine-text {
            background: linear-gradient(
                90deg,
                #B91C1C 0%,
                #DC2626 25%,
                #F59E0B 50%,
                #DC2626 75%,
                #B91C1C 100%
            );
            background-size: 200% auto;
            -webkit-background-clip: text;
            background-clip: text;
            -webkit-text-fill-color: transparent;
            animation: textShine 5s linear infinite;
        }
        
        /* Glass Effect */
        .glass {
            background: rgba(255, 255, 255, 0.08);
            backdrop-filter: blur(12px);
            border: 1px solid rgba(255, 255, 255, 0.15);
            box-shadow: 0 20px 40px rgba(0, 0, 0, 0.2);
        }
        
        .glass-dark {
            background: rgba(0, 0, 0, 0.25);
            backdrop-filter: blur(12px);
            border: 1px solid rgba(255, 255, 255, 0.1);
        }
        
        /* Custom Scrollbar */
        ::-webkit-scrollbar {
            width: 8px;
        }
        
        ::-webkit-scrollbar-track {
            background: rgba(255, 255, 255, 0.05);
        }
        
        ::-webkit-scrollbar-thumb {
            background: linear-gradient(45deg, #DC2626, #F59E0B);
            border-radius: 4px;
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
            background: linear-gradient(
                to right,
                rgba(255, 255, 255, 0) 0%,
                rgba(255, 255, 255, 0.3) 50%,
                rgba(255, 255, 255, 0) 100%
            );
            transition: all 0.7s ease;
        }
        
        .shine-effect:hover::after {
            opacity: 1;
            left: 130%;
        }
        
        /* 404 Number Styles */
        .error-404 {
            text-shadow: 0 10px 30px rgba(220, 38, 38, 0.3);
        }
        
        /* Responsive adjustments */
        @media (max-height: 768px) {
            .responsive-container {
                padding-top: 2rem !important;
                padding-bottom: 2rem !important;
            }
        }
    </style>
</head>
<body class="w-screen h-screen bg-gradient-to-br from-slate-900 via-gray-900 to-slate-900 animate-gradient overflow-hidden">
    
    <!-- Full Screen Background Pattern -->
    <div class="absolute inset-0 overflow-hidden">
        <!-- Animated Gradient Orbs -->
        <div class="absolute top-0 left-0 w-[40vw] h-[40vw] rounded-full bg-gradient-to-br from-red-600/5 to-amber-500/5 animate-float"></div>
        <div class="absolute bottom-0 right-0 w-[35vw] h-[35vw] rounded-full bg-gradient-to-br from-blue-600/5 to-cyan-500/5 animate-float-delay"></div>
        
        <!-- Sanskrit Symbols -->
        <div class="absolute top-1/4 left-1/4 text-[10vw] opacity-[0.03] animate-float">ॐ</div>
        <div class="absolute top-1/3 right-1/4 text-[8vw] opacity-[0.03] animate-float-delay">卐</div>
        <div class="absolute bottom-1/4 left-1/2 text-[12vw] opacity-[0.03] animate-float" style="animation-delay: 2s;">🕉️</div>
        
        <!-- Grid Pattern -->
        <div class="absolute inset-0" style="
            background-image: 
                linear-gradient(rgba(255,255,255,0.05) 1px, transparent 1px),
                linear-gradient(90deg, rgba(255,255,255,0.05) 1px, transparent 1px);
            background-size: 50px 50px;
        "></div>
    </div>
    
    <!-- Main Container - Full Screen -->
    <div class="relative w-full h-full flex flex-col">
        
        <!-- Header -->
        <div class="w-full px-6 md:px-10 py-5 md:py-6 flex justify-between items-center glass-dark border-b border-slate-800/50">
            <!-- Logo -->
            <div class="flex items-center space-x-4">
                <div class="w-12 h-12 md:w-14 md:h-14 rounded-xl bg-gradient-to-br from-red-600 to-amber-500 flex items-center justify-center shadow-xl">
                    <i class="fas fa-school text-white text-lg md:text-xl"></i>
                </div>
                <div>
                    <h1 class="text-lg md:text-xl font-bold text-white brand-font">Bhaktivedanta Gurukul</h1>
                    <p class="text-xs md:text-sm text-gray-300">School of Excellence</p>
                </div>
            </div>
            
            <!-- Navigation removed -->
        </div>
        
        <!-- Main Content Area - Full Height -->
        <div class="flex-1 w-full overflow-auto">
            <div class="w-full h-full flex flex-col lg:flex-row items-center justify-center px-4 md:px-8 lg:px-16 responsive-container">
                
                <!-- Left Column - 404 Visual -->
                <div class="w-full lg:w-1/2 h-full flex flex-col items-center justify-center py-8 lg:py-0">
                    <div class="relative w-full max-w-2xl">
                        <!-- Large 404 Number -->
                        <div class="text-center">
                            <h1 class="text-[25vw] lg:text-[20vw] xl:text-[18vw] font-bold leading-none error-404">
                                <span class="shine-text">404</span>
                            </h1>
                            
                            <!-- Animated Elements Around 404 -->
                            <div class="absolute top-1/4 left-1/4 -translate-x-1/2 -translate-y-1/2">
                                <div class="w-16 h-16 md:w-20 md:h-20 rounded-full glass animate-glow flex items-center justify-center">
                                    <i class="fas fa-book-open text-2xl md:text-3xl text-red-400 animate-pulse"></i>
                                </div>
                            </div>
                            
                            <div class="absolute top-1/4 right-1/4 translate-x-1/2 -translate-y-1/2">
                                <div class="w-12 h-12 md:w-16 md:h-16 rounded-full glass-dark animate-spin flex items-center justify-center">
                                    <i class="fas fa-compass text-xl md:text-2xl text-amber-400"></i>
                                </div>
                            </div>
                            
                            <div class="absolute bottom-1/4 left-1/3 -translate-x-1/2 translate-y-1/2">
                                <div class="w-14 h-14 md:w-18 md:h-18 rounded-full glass animate-float flex items-center justify-center">
                                    <i class="fas fa-map-signs text-xl md:text-2xl text-blue-400"></i>
                                </div>
                            </div>
                            
                            <div class="absolute bottom-1/3 right-1/3 translate-x-1/2 translate-y-1/2">
                                <div class="w-10 h-10 md:w-14 md:h-14 rounded-full glass-dark animate-float-delay flex items-center justify-center">
                                    <i class="fas fa-search text-lg md:text-xl text-green-400"></i>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Error Message -->
                        <div class="mt-8 md:mt-12 text-center animate-slide-in">
                            <h2 class="text-3xl md:text-4xl lg:text-5xl font-bold text-white mb-4">
                                Page <span class="text-gradient">Not Found</span>
                            </h2>
                            <p class="text-gray-300 text-lg md:text-xl max-w-xl mx-auto">
                                The knowledge you seek has wandered into the cosmic library
                            </p>
                            
                            <!-- Animated Dots -->
                            <div class="flex justify-center space-x-3 mt-6">
                                <div class="w-3 h-3 rounded-full bg-red-500 animate-pulse"></div>
                                <div class="w-3 h-3 rounded-full bg-amber-500 animate-pulse" style="animation-delay: 0.2s;"></div>
                                <div class="w-3 h-3 rounded-full bg-blue-500 animate-pulse" style="animation-delay: 0.4s;"></div>
                                <div class="w-3 h-3 rounded-full bg-green-500 animate-pulse" style="animation-delay: 0.6s;"></div>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Right Column - Actions & Info -->
                <div class="w-full lg:w-1/2 h-full flex items-center justify-center py-8 lg:py-0 lg:pl-10 xl:pl-16">
                    <div class="w-full max-w-lg glass-dark rounded-3xl p-6 md:p-8 lg:p-10 shadow-2xl animate-slide-in" style="animation-delay: 0.2s;">
                        
                        <!-- Message Card -->
                        <div class="mb-8">
                            <div class="flex items-center mb-6">
                                <div class="w-14 h-14 rounded-full bg-gradient-to-r from-red-500/20 to-amber-500/20 flex items-center justify-center mr-4">
                                    <i class="fas fa-exclamation-triangle text-red-400 text-2xl"></i>
                                </div>
                                <div>
                                    <h3 class="text-2xl md:text-3xl font-bold">Lost in Wisdom?</h3>
                                    <p class="text-gray-400 text-sm md:text-base">The path to knowledge continues elsewhere</p>
                                </div>
                            </div>
                                <!-- Wisdom block removed -->
                        </div>
                        
                        <!-- Quick Actions Grid -->
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-8">
                            <a href="https://bhaktivedantagurukul.com/" class="group shine-effect">
                                <div class="glass rounded-xl p-5 hover:bg-white/5 transition-all duration-300 h-full">
                                    <div class="flex items-center mb-3">
                                        <div class="w-10 h-10 rounded-full bg-blue-500/20 flex items-center justify-center mr-3 group-hover:bg-blue-500/30 transition-colors">
                                            <i class="fas fa-home text-blue-400"></i>
                                        </div>
                                        <div class="font-medium">Home</div>
                                    </div>
                                    <p class="text-gray-400 text-sm">Return to the main entrance of our gurukul</p>
                                </div>
                            </a>
                            
                            <a href="https://bhaktivedantagurukul.com/</a>" class="group shine-effect">
                                <div class="glass rounded-xl p-5 hover:bg-white/5 transition-all duration-300 h-full">
                                    <div class="flex items-center mb-3">
                                        <div class="w-10 h-10 rounded-full bg-green-500/20 flex items-center justify-center mr-3 group-hover:bg-green-500/30 transition-colors">
                                            <i class="fas fa-graduation-cap text-green-400"></i>
                                        </div>
                                        <div class="font-medium">Academics</div>
                                    </div>
                                    <p class="text-gray-400 text-sm">Explore our curriculum and courses</p>
                                </div>
                            </a>
                            
                            <a href="https://bhaktivedantagurukul.com/" class="group shine-effect">
                                <div class="glass rounded-xl p-5 hover:bg-white/5 transition-all duration-300 h-full">
                                    <div class="flex items-center mb-3">
                                        <div class="w-10 h-10 rounded-full bg-purple-500/20 flex items-center justify-center mr-3 group-hover:bg-purple-500/30 transition-colors">
                                            <i class="fas fa-calendar-alt text-purple-400"></i>
                                        </div>
                                        <div class="font-medium">Events</div>
                                    </div>
                                    <p class="text-gray-400 text-sm">View upcoming spiritual and academic events</p>
                                </div>
                            </a>
                            
                            <a href="https://bhaktivedantagurukul.com/" class="group shine-effect"></a>
                                <div class="glass rounded-xl p-5 hover:bg-white/5 transition-all duration-300 h-full">
                                    <div class="flex items-center mb-3">
                                        <div class="w-10 h-10 rounded-full bg-amber-500/20 flex items-center justify-center mr-3 group-hover:bg-amber-500/30 transition-colors">
                                            <i class="fas fa-users text-amber-400"></i>
                                        </div>
                                        <div class="font-medium">Faculty</div>
                                    </div>
                                    <p class="text-gray-400 text-sm">Meet our enlightened teachers and guides</p>
                                </div>
                            </a>
                        </div>
                            <!-- Quick actions removed -->
                        
                        <!-- Search Section -->
                        <div class="mb-8">
                            <h4 class="text-lg md:text-xl font-bold mb-4 flex items-center">
                                <i class="fas fa-search text-amber-400 mr-3"></i>
                                Search Our Wisdom
                            </h4>
                            <div class="flex"> 
                                <input type="text" 
                                       placeholder="What knowledge do you seek?" 
                                       class="flex-1 bg-slate-800/50 border border-slate-700 rounded-l-xl px-5 py-3 md:py-4 focus:outline-none focus:border-amber-500 focus:ring-2 focus:ring-amber-500/20 text-sm md:text-base">
                                <button class="bg-gradient-to-r from-amber-500 to-red-500 hover:from-amber-600 hover:to-red-600 px-5 md:px-6 rounded-r-xl font-medium transition-all duration-300 group">
                                    <i class="fas fa-search group-hover:scale-110 transition-transform"></i>
                                </button>
                            </div>
                        </div>
                        
                        <!-- Contact & Help -->
                        <div class="border-t border-slate-700 pt-6 md:pt-8">
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <div class="flex items-center">
                                    <div class="w-12 h-12 rounded-full bg-gradient-to-r from-red-500/20 to-amber-500/20 flex items-center justify-center mr-4">
                                        <i class="fas fa-headset text-red-400"></i>
                                    </div>
                                    <div>
                                        <div class="font-medium">Need Guidance?</div>
                                        <a href="mailto:info@bhaktivedantagurukul.edu" class="text-amber-400 hover:text-amber-300 transition-colors text-sm">
                                            Contact Us
                                        </a>
                                    </div>
                                </div>
                                
                                <div class="flex items-center">
                                    <div class="w-12 h-12 rounded-full bg-gradient-to-r from-blue-500/20 to-cyan-500/20 flex items-center justify-center mr-4">
                                        <i class="fas fa-phone text-blue-400"></i>
                                    </div>
                                    <div>
                                        <div class="font-medium">Call Us</div>
                                        <div class="text-gray-400 text-sm">+91 98765 43210</div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Footer removed -->
        
        <!-- Floating Help Button -->
        <button id="helpButton" class="fixed bottom-6 right-6 w-14 h-14 md:w-16 md:h-16 rounded-full bg-gradient-to-r from-red-500 to-amber-500 flex items-center justify-center shadow-2xl hover:shadow-amber-500/30 transition-all duration-300 animate-pulse z-40">
            <i class="fas fa-question text-white text-xl"></i>
        </button>
        
        <!-- Lost Student Animation -->
        <div class="fixed bottom-24 left-6 md:left-10 animate-float z-30">
            <div class="relative">
                <div class="w-16 h-16 md:w-20 md:h-20 rounded-full glass flex items-center justify-center">
                    <i class="fas fa-user-graduate text-2xl md:text-3xl text-amber-400"></i>
                </div>
                <div class="absolute -top-2 -right-2 w-6 h-6 md:w-8 md:h-8 rounded-full bg-red-500/30 flex items-center justify-center animate-pulse">
                    <i class="fas fa-question text-red-400 text-xs"></i>
                </div>
            </div>
        </div>
        
        <!-- Mobile menu removed -->
        
        <!-- Help Modal -->
        <div id="helpModal" class="fixed inset-0 bg-black/80 flex items-center justify-center p-4 z-50 hidden">
            <div class="glass rounded-3xl max-w-md w-full p-8 animate-slide-in">
                <div class="flex justify-between items-center mb-8">
                    <h3 class="text-2xl md:text-3xl font-bold">Need Assistance?</h3>
                    <button onclick="closeHelp()" class="text-gray-500 hover:text-white text-2xl md:text-3xl">
                        <i class="fas fa-times"></i>
                    </button>
                </div>
                
                <div class="space-y-4 mb-8">
                    <div class="flex items-center p-4 rounded-xl bg-red-500/10">
                        <i class="fas fa-phone text-red-400 text-xl mr-4"></i>
                        <div>
                            <p class="font-medium">Call Us</p>
                            <p class="text-gray-300">+91 98765 43210</p>
                        </div>
                    </div>
                    
                    <div class="flex items-center p-4 rounded-xl bg-blue-500/10">
                        <i class="fas fa-envelope text-blue-400 text-xl mr-4"></i>
                        <div>
                            <p class="font-medium">Email</p>
                            <p class="text-gray-300">info@bhaktivedantagurukul.edu</p>
                        </div>
                    </div>
                    
                    <div class="flex items-center p-4 rounded-xl bg-green-500/10">
                        <i class="fab fa-whatsapp text-green-400 text-xl mr-4"></i>
                        <div>
                            <p class="font-medium">WhatsApp</p>
                            <p class="text-gray-300">+91 98765 43210</p>
                        </div>
                    </div>
                </div>
                
                <p class="text-gray-400 text-sm mb-6">
                    <i class="fas fa-info-circle mr-2"></i>
                    Our administrative office is open Monday to Saturday, 9 AM to 5 PM.
                </p>
                
                <button onclick="closeHelp()" class="w-full bg-gradient-to-r from-amber-500 to-red-500 hover:from-amber-600 hover:to-red-600 py-3 md:py-4 rounded-xl font-medium transition-all duration-300">
                    Return to 404 Page
                </button>
            </div>
        </div>
    </div>

    <script>
        // DOM Elements
        const helpButton = document.getElementById('helpButton');
        const helpModal = document.getElementById('helpModal');
        const mobileMenuBtn = document.getElementById('mobileMenuBtn');
        const mobileMenu = document.getElementById('mobileMenu');
        const closeMobileMenu = document.getElementById('closeMobileMenu');
        
        // Show/Hide Help Modal
        helpButton.addEventListener('click', function() {
            helpModal.classList.remove('hidden');
        });
        
        function closeHelp() {
            helpModal.classList.add('hidden');
        }
        
        // Mobile Menu Toggle
        mobileMenuBtn.addEventListener('click', function() {
            mobileMenu.classList.remove('hidden');
        });
        
        closeMobileMenu.addEventListener('click', function() {
            mobileMenu.classList.add('hidden');
        });
        
        // Close modals when clicking outside
        helpModal.addEventListener('click', function(e) {
            if (e.target === this) {
                closeHelp();
            }
        });
        
        mobileMenu.addEventListener('click', function(e) {
            if (e.target === this) {
                mobileMenu.classList.add('hidden');
            }
        });
        
        // Search functionality
        const searchInput = document.querySelector('input[type="text"]');
        const searchButton = document.querySelector('button.bg-gradient-to-r');
        
        searchButton.addEventListener('click', function() {
            if (searchInput.value.trim()) {
                // Show searching animation
                const originalHTML = this.innerHTML;
                this.innerHTML = '<i class="fas fa-spinner fa-spin"></i>';
                this.disabled = true;
                
                // Simulate search
                setTimeout(() => {
                    this.innerHTML = originalHTML;
                    this.disabled = false;
                    
                    // Show search results message
                    const message = document.createElement('div');
                    message.className = 'fixed top-6 right-6 glass rounded-xl p-4 max-w-sm z-50 animate-slide-in';
                    message.innerHTML = `
                        <div class="flex items-center">
                            <div class="w-10 h-10 rounded-full bg-amber-500/20 flex items-center justify-center mr-3">
                                <i class="fas fa-search text-amber-400"></i>
                            </div>
                            <div>
                                <h4 class="font-bold">Search Results</h4>
                                <p class="text-gray-300 text-sm">Found 0 results for "${searchInput.value}"</p>
                            </div>
                            <button onclick="this.parentElement.parentElement.remove()" class="ml-4 text-gray-500 hover:text-white">
                                <i class="fas fa-times"></i>
                            </button>
                        </div>
                    `;
                    document.body.appendChild(message);
                    
                    // Auto remove after 5 seconds
                    setTimeout(() => {
                        if (message.parentElement) {
                            message.remove();
                        }
                    }, 5000);
                }, 1500);
            } else {
                // Shake animation for empty search
                searchInput.classList.add('animate-pulse');
                setTimeout(() => {
                    searchInput.classList.remove('animate-pulse');
                }, 500);
                searchInput.focus();
            }
        });
        
        // Add enter key support for search
        searchInput.addEventListener('keypress', function(e) {
            if (e.key === 'Enter') {
                searchButton.click();
            }
        });
        
        // Add shine effect to links
        const shineLinks = document.querySelectorAll('.shine-effect');
        shineLinks.forEach(link => {
            link.addEventListener('mouseenter', function() {
                this.style.transform = 'translateY(-4px) scale(1.02)';
            });
            
            link.addEventListener('mouseleave', function() {
                this.style.transform = 'translateY(0) scale(1)';
            });
        });
        
        // Animate 404 number periodically
        const errorNumber = document.querySelector('.shine-text');
        setInterval(() => {
            errorNumber.style.animation = 'none';
            setTimeout(() => {
                errorNumber.style.animation = 'textShine 5s linear infinite';
            }, 10);
        }, 5000);
        
        // Make help button bounce occasionally
        setInterval(() => {
            helpButton.classList.add('animate-bounce');
            setTimeout(() => {
                helpButton.classList.remove('animate-bounce');
            }, 1000);
        }, 15000);
        
        // Add animation styles
        const style = document.createElement('style');
        style.textContent = `
            .animate-bounce {
                animation: bounce 1s;
            }
            
            @keyframes bounce {
                0%, 100% { transform: translateY(0); }
                50% { transform: translateY(-10px); }
            }
            
            .fa-spin {
                animation: spin 1s linear infinite;
            }
        `;
        document.head.appendChild(style);
        
        // Page load animations
        document.addEventListener('DOMContentLoaded', function() {
            // Animate elements sequentially
            const elements = document.querySelectorAll('.animate-slide-in');
            elements.forEach((el, index) => {
                el.style.animationDelay = `${index * 0.1}s`;
            });
            
            // Add floating animation to lost student
            const lostStudent = document.querySelector('.animate-float');
            setInterval(() => {
                lostStudent.style.animation = 'none';
                setTimeout(() => {
                    lostStudent.style.animation = 'float 6s ease-in-out infinite';
                }, 10);
            }, 6000);
        });
        
        // Prevent scrolling on body when modal is open
        function preventBodyScroll(prevent) {
            if (prevent) {
                document.body.style.overflow = 'hidden';
            } else {
                document.body.style.overflow = 'auto';
            }
        }
        
        // Update modals to prevent body scroll
        helpButton.addEventListener('click', () => preventBodyScroll(true));
        mobileMenuBtn.addEventListener('click', () => preventBodyScroll(true));
        
        const closeButtons = document.querySelectorAll('[onclick*="close"], #closeMobileMenu');
        closeButtons.forEach(btn => {
            btn.addEventListener('click', () => preventBodyScroll(false));
        });
    </script>
</body>
</html>