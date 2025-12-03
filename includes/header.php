<?php
// Smart Base URL - Auto detect for deployment
$is_localhost = ($_SERVER['SERVER_NAME'] == 'localhost' || $_SERVER['SERVER_NAME'] == '127.0.0.1');
$base_url = $is_localhost ? '/Gurukul_website' : 'https://bhaktivedantagurukul.com';

// Check if session is already started - FIX FOR REDIRECT LOOP
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$is_admin = isset($_SESSION['admin_logged_in']) && $_SESSION['admin_logged_in'] === true;

// Set default values if not set
$current_page = $current_page ?? basename($_SERVER['PHP_SELF']);
$current_directory = $current_directory ?? '';
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Bhaktivedanta Gurukul - School of Excellence</title>
    <meta name="description" content="Bhaktivedanta Gurukul - Combining modern education with traditional Vedic values for holistic development">
    <meta name="keywords" content="gurukul, vedic education, school, bhaktivedanta, spiritual education">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@400;600;700&family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link rel="stylesheet" href="<?php echo $base_url; ?>/css/header.css">
    <link rel="icon" type="image/x-icon" href="<?php echo $base_url; ?>/images/bvgLogo.png">

    
  <!-- New Meta Tags -->
  <meta property="og:title" content="Bhaktivedanta Gurukul - School of Excellence">
  <meta property="og:description" content="Empowering students through modern education blended with traditional Vedic values. Join Bhaktivedanta Gurukul for holistic learning.">
  <meta property="og:image" content="<?php echo $base_url; ?>/images/bvgLogo.png">
  <meta property="og:url" content="https://bhaktivedantagurukul.com/">
  <meta property="og:type" content="website">
  <meta property="og:site_name" content="Bhaktivedanta Gurukul">
  <meta name="theme-color" content="#DC143C">
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        primary: '#003366',
                        accent: '#800000',
                        secondary: '#3e2723',
                        light: '#F8FAFC',
                        admin: '#059669'
                    },
                    fontFamily: {
                        'serif': ['Playfair Display', 'serif'],
                        'sans': ['Inter', 'sans-serif'],
                    }
                }
            }
        }
    </script>
    <style>
        .fixed-header {
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            z-index: 1000;
            background: white;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
        }

        body {
            padding-top: 80px; 
        }

        @media (max-width: 1024px) {
            body {
                padding-top: 75px;
            }
          
            .logo-container img {
                height: 75px !important; 
            }
        }

        @media (max-width: 768px) {
            body {
                padding-top: 65px; 
            }
            
            .fixed-header {
                height: 65px; 
            }
            
            .logo-container img {
                height: 60px !important; 
            }
        }

        @media (max-width: 480px) {
            body {
                padding-top: 60px; 
            }
            
            .fixed-header {
                height: 60px;
            }
        }

        /* Mobile Sidebar - Fixed Width */
        .mobile-sidebar {
            position: fixed;
            top: 0;
            right: -100%;
            width: 280px; /* Reduced from 320px to 280px */
            height: 100vh;
            background: white;
            box-shadow: -5px 0 15px rgba(0,0,0,0.1);
            transition: right 0.3s ease;
            z-index: 1001;
            overflow-y: auto;
        }

        /* For very small screens */
        @media (max-width: 360px) {
            .mobile-sidebar {
                width: 260px; /* Even smaller for very small phones */
            }
        }

        .mobile-sidebar.active {
            right: 0;
        }

        .mobile-sidebar-overlay {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0,0,0,0.5);
            opacity: 0;
            visibility: hidden;
            transition: all 0.3s ease;
            z-index: 1000; 
        }

        .mobile-sidebar-overlay.active {
            opacity: 1;
            visibility: visible;
        }

        .mobile-nav-item {
            display: flex;
            align-items: center;
            padding: 12px 16px;
            border-bottom: 1px solid #f3f4f6;
            transition: all 0.3s ease;
            font-size: 14px; /* Slightly smaller font */
        }

        .mobile-accordion-btn {
            display: flex;
            align-items: center;
            justify-content: space-between;
            width: 100%;
            padding: 12px 16px;
            border-bottom: 1px solid #f3f4f6;
            text-align: left;
            transition: all 0.3s ease;
            font-size: 14px; /* Slightly smaller font */
        }

        .mobile-accordion-content {
            background: #f9fafb;
            border-bottom: 1px solid #f3f4f6;
            max-height: 0;
            overflow: hidden;
            transition: max-height 0.4s ease;
        }

        .mobile-accordion-content.active {
            max-height: 500px;
        }

        .mobile-subnav-item {
            display: block;
            padding: 10px 16px 10px 32px; /* Reduced left padding */
            border-bottom: 1px solid #e5e7eb;
            transition: all 0.3s ease;
            font-size: 13px; /* Smaller font for sub-items */
        }

        .mobile-subnav-item:hover {
            background: #edf2f7;
        }

        .logo-container img {
            height: 75px;
            width: auto;
            transition: height 0.3s ease;
        }

        .mobile-sidebar .logo-container img {
            height: 60px !important; /* Smaller logo in sidebar */
            width: auto;
        }

        .mobile-accordion-content:not(.hidden) {
            display: block;
        }

        html {
            scroll-behavior: smooth;
        }

        .relative.group .absolute {
            z-index: 1002;
        }

        .mobile-header-content {
            display: flex;
            align-items: center;
            justify-content: space-between;
            width: 100%;
            height: 100%;
        }

        .mobile-cta-buttons {
            align-items: center;
            gap: 8px;
        }

        .compact-button {
            padding: 6px 12px !important;
            font-size: 0.8rem !important;
            white-space: nowrap;
        }

        .mobile-sidebar-header {
            padding: 15px 16px; /* Reduced padding */
            border-bottom: 2px solid #e5e7eb;
            background: white;
            text-align: center;
        }

        .mobile-sidebar-logo {
            display: flex;
            justify-content: center;
            align-items: center;
            margin-bottom: 8px; /* Reduced margin */
        }
        
        /* Blinking Animation for Apply Now Button */
        .blink-apply {
            animation: blink 2s infinite;
            background: linear-gradient(45deg, #800000, #a00000, #800000);
            background-size: 200% 200%;
            transition: all 0.3s ease;
        }
        
        @keyframes blink {
            0%, 100% {
                opacity: 1;
                box-shadow: 0 4px 12px rgba(128, 0, 0, 0.3);
            }
            50% {
                opacity: 0.8;
                box-shadow: 0 6px 20px rgba(128, 0, 0, 0.5);
            }
        }
        
        .blink-apply:hover {
            animation: none;
            background: #a00000;
            transform: translateY(-2px);
            box-shadow: 0 8px 25px rgba(128, 0, 0, 0.4);
        }
        
        .admin-login-hidden {
            display: none !important;
        }

        /* Mobile sidebar text adjustments */
        .mobile-sidebar h2 {
            font-size: 18px !important; /* Smaller heading */
        }
        
        .mobile-sidebar p {
            font-size: 12px !important; /* Smaller subheading */
        }
    </style>
</head>

<body class="font-sans bg-white">
    <!-- Mobile Sidebar Overlay -->
    <div id="mobile-sidebar-overlay" class="mobile-sidebar-overlay"></div>

    <!-- Mobile Sidebar -->
    <div id="mobile-sidebar" class="mobile-sidebar">
        <div class="mobile-sidebar-header">
            <div class="mobile-sidebar-logo">
                <div class="logo-container">
                    <img src="<?php echo $base_url; ?>/images/BVG-Header.png" alt="Bhaktivedanta Gurukul Logo">
                </div>
            </div>
            <div class="text-center mb-2">
<h2 class="text-lg font-bold font-serif" style="color:#800000;">
    Bhaktivedanta Gurukul
</h2>
                <p class="text-xs text-primary">School of Excellence</p>
            </div>
            <button id="mobile-sidebar-close" class="absolute top-4 right-4 text-primary hover:text-accent p-2 rounded-lg hover:bg-gray-100 transition-colors">
                <i class="fas fa-times text-lg"></i>
            </button>
        </div>
      
        <!-- Navigation Content -->
        <div class="py-2"> <!-- Reduced padding -->
            <!-- Home -->
            <a href="<?php echo $base_url; ?>/index.php" class="mobile-nav-item <?php echo $current_page == 'index.php' ? 'bg-[#800000]/90 text-white' : 'text-primary'; ?>">
                <i class="fas fa-home mr-3 text-base"></i> <!-- Smaller icon -->
                <span class="text-sm font-medium">Home</span> <!-- Smaller text -->
            </a>

            <!-- About Accordion -->
            <div class="mobile-accordion text-primary">
                <button class="mobile-accordion-btn <?php echo $current_directory == 'about' ? 'bg-gray-50' : ''; ?>">
                    <div class="flex items-center">
                        <i class="fas fa-info-circle mr-3 text-base"></i> <!-- Smaller icon -->
                        <span class="text-sm font-medium">About</span> <!-- Smaller text -->
                    </div>
                    <i class="fas fa-chevron-down text-xs transition-transform duration-300"></i> <!-- Smaller icon -->
                </button>
                <div class="mobile-accordion-content">
                    <a href="<?php echo $base_url; ?>/about.php" class="mobile-subnav-item <?php echo $current_page == 'about.php' ? 'text-accent font-semibold' : ''; ?>">
                        <i class="fas fa-circle text-xs mr-2"></i> About Us
                    </a>
                    <a href="<?php echo $base_url; ?>/about.php#history" class="mobile-subnav-item hover:bg-gray-100">
                        <i class="fas fa-circle text-xs mr-2"></i> Our History
                    </a>
                    <a href="<?php echo $base_url; ?>/about.php#mission" class="mobile-subnav-item hover:bg-gray-100">
                        <i class="fas fa-circle text-xs mr-2"></i> Mission & Vision
                    </a>
                </div>
            </div>

            <!-- Our Inspiration -->
            <a href="<?php echo $base_url; ?>/pages/inspiration/index.php" class="mobile-nav-item <?php echo $current_directory == 'inspiration' ? 'bg-gray-50 text-accent' : 'text-primary'; ?>">
                <i class="fas fa-heart mr-3 text-base"></i> <!-- Smaller icon -->
                <span class="text-sm font-medium">Our Inspiration</span> <!-- Smaller text -->
            </a>

            <!-- Admissions -->
            <a href="<?php echo $base_url; ?>/apply-now.php" class="mobile-nav-item <?php echo $current_directory == 'admissions' ? 'bg-gray-50 text-accent' : 'text-primary'; ?>">
                <i class="fas fa-user-graduate mr-3 text-base"></i> <!-- Smaller icon -->
                <span class="text-sm font-medium">Admissions</span> <!-- Smaller text -->
            </a>

            <!-- Career Accordion -->
            <div class="mobile-accordion text-primary">
                <button class="mobile-accordion-btn <?php echo $current_directory == 'career' ? 'bg-gray-50' : ''; ?>">
                    <div class="flex items-center">
                        <i class="fas fa-briefcase mr-3 text-base"></i> <!-- Smaller icon -->
                        <span class="text-sm font-medium">Career</span> <!-- Smaller text -->
                    </div>
                    <i class="fas fa-chevron-down text-xs transition-transform duration-300"></i> <!-- Smaller icon -->
                </button>
                <div class="mobile-accordion-content">
                    <a href="<?php echo $base_url; ?>/pages/career/index.php" class="mobile-subnav-item <?php echo $current_page == 'index.php' && $current_directory == 'career' ? 'text-accent font-semibold' : ''; ?>">
                        <i class="fas fa-circle text-xs mr-2"></i> Career Opportunities
                    </a>
                    <a href="<?php echo $base_url; ?>/pages/career/application.php" class="mobile-subnav-item">
                        <i class="fas fa-circle text-xs mr-2"></i> Teaching Positions
                    </a>
                    <a href="<?php echo $base_url; ?>/pages/career/application.php" class="mobile-subnav-item">
                        <i class="fas fa-circle text-xs mr-2"></i> Non-Teaching Positions
                    </a>
                </div>
            </div>

            <!-- Blog Accordion -->
            <div class="mobile-accordion text-primary">
                <button class="mobile-accordion-btn <?php echo $current_directory == 'blog' ? 'bg-gray-50' : ''; ?>">
                    <div class="flex items-center">
                        <i class="fas fa-blog mr-3 text-base"></i> <!-- Smaller icon -->
                        <span class="text-sm font-medium">Blog</span> <!-- Smaller text -->
                    </div>
                    <i class="fas fa-chevron-down text-xs transition-transform duration-300"></i> <!-- Smaller icon -->
                </button>
                <div class="mobile-accordion-content">
                    <a href="<?php echo $base_url; ?>/pages/blog/index.php" class="mobile-subnav-item <?php echo $current_page == 'index.php' && $current_directory == 'blog' ? 'text-accent font-semibold' : ''; ?>">
                        <i class="fas fa-circle text-xs mr-2"></i> Latest News & Posts
                    </a>
                    <a href="<?php echo $base_url; ?>/pages/blog/downloads.php" class="mobile-subnav-item">
                        <i class="fas fa-circle text-xs mr-2"></i> Downloads
                    </a>
                    <a href="<?php echo $base_url; ?>/pages/blog/question-papers.php" class="mobile-subnav-item">
                        <i class="fas fa-circle text-xs mr-2"></i> Question Papers
                    </a>
                </div>
            </div>

            <!-- Contact -->
            <a href="<?php echo $base_url; ?>/contact.php" class="mobile-nav-item <?php echo $current_page == 'contact.php' ? 'bg-gray-50 text-accent' : 'text-primary'; ?>">
                <i class="fas fa-phone mr-3 text-base"></i> <!-- Smaller icon -->
                <span class="text-sm font-medium">Contact</span> <!-- Smaller text -->
            </a>

            <!-- Apply Now Button (Mobile) - Direct Link -->
            <a href="<?php echo $base_url; ?>/apply-now.php" class="mobile-nav-item blink-apply text-white">
                <i class="fas fa-edit mr-3 text-base"></i> <!-- Smaller icon -->
                <span class="text-sm font-medium">Apply Now</span> <!-- Smaller text -->
            </a>

            <!-- Admin Dashboard (Mobile) - ONLY SHOW WHEN LOGGED IN -->
            <?php if ($is_admin): ?>
            <div class="mobile-accordion">
                <button class="mobile-accordion-btn <?php echo $current_directory == 'admin' ? 'bg-gray-50' : ''; ?>">
                    <div class="flex items-center">
                        <i class="fas fa-cog mr-3 text-base text-admin"></i> <!-- Smaller icon -->
                        <span class="text-sm font-medium text-admin">Admin</span> <!-- Smaller text -->
                    </div>
                    <i class="fas fa-chevron-down text-xs transition-transform duration-300"></i> <!-- Smaller icon -->
                </button>
                <div class="mobile-accordion-content">
                    <a href="<?php echo $base_url; ?>/admin/dashboard.php" class="mobile-subnav-item <?php echo $current_page == 'dashboard.php' && $current_directory == 'admin' ? 'text-admin font-semibold' : ''; ?>">
                        <i class="fas fa-circle text-xs mr-2"></i> Dashboard
                    </a>
                    <a href="<?php echo $base_url; ?>/admin/students.php" class="mobile-subnav-item">
                        <i class="fas fa-circle text-xs mr-2"></i> Manage Students
                    </a>
                    <a href="<?php echo $base_url; ?>/admin/teachers.php" class="mobile-subnav-item">
                        <i class="fas fa-circle text-xs mr-2"></i> Manage Teachers
                    </a>
                    <a href="<?php echo $base_url; ?>/admin/applications.php" class="mobile-subnav-item">
                        <i class="fas fa-circle text-xs mr-2"></i> Applications
                    </a>
                    <a href="<?php echo $base_url; ?>/admin/settings.php" class="mobile-subnav-item">
                        <i class="fas fa-circle text-xs mr-2"></i> Settings
                    </a>
                </div>
            </div>
            <?php endif; ?>

            <!-- Admin Login/Logout (Mobile) - HIDDEN FROM NORMAL USERS -->
            <div class="px-4 mt-4 admin-login-hidden">
                <?php if ($is_admin): ?>
                    <a href="<?php echo $base_url; ?>/admin/logout.php" class="w-full bg-admin text-white py-2 px-4 rounded-lg font-semibold hover:bg-green-700 transition-colors duration-300 text-center block text-sm">
                        <i class="fas fa-sign-out-alt mr-2"></i> Logout
                    </a>
                <?php else: ?>
                    <a href="<?php echo $base_url; ?>/admin/login.php" class="w-full bg-gray-600 text-white py-2 px-4 rounded-lg font-semibold hover:bg-gray-700 transition-colors duration-300 text-center block text-sm">
                        <i class="fas fa-lock mr-2"></i> Admin Login
                    </a>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <!-- Fixed Header -->
    <header class="fixed-header">
        <nav class="bg-white shadow-lg h-full">
            <div class="max-w-7xl mx-auto px-4 h-full">
                <div class="flex justify-between items-center h-full py-2 lg:py-4">
                    <!-- Logo -->
                    <div class="flex items-center space-x-3">
                        <a href="<?php echo $base_url; ?>/index.php" class="logo-container">
                            <img src="<?php echo $base_url; ?>/images/BVG-Header.png" alt="Bhaktivedanta Gurukul Logo">
                        </a>
                    </div>

                    <!-- Desktop Menu -->
                    <div class="hidden lg:flex items-center space-x-8">
                        <a href="<?php echo $base_url; ?>/index.php" class="nav-link <?php echo $current_page == 'index.php' ? 'text-accent font-semibold' : 'text-primary hover:text-accent'; ?> transition-colors duration-300 py-2">Home</a>

                        <!-- About Dropdown -->
                        <div class="relative group">
                            <button class="nav-link <?php echo $current_directory == 'about' ? 'text-accent font-bold' : 'text-primary hover:text-accent'; ?> flex items-center transition-colors duration-300 py-2">
                                About <i class="fas fa-chevron-down ml-1 text-xs transition-transform duration-300 group-hover:rotate-180"></i>
                            </button>
                            <div class="absolute left-0 mt-2 w-64 bg-white shadow-xl rounded-lg opacity-0 invisible group-hover:opacity-100 group-hover:visible transition-all duration-300 border border-gray-100 z-50">
                                <div class="py-2">
                                    <a href="<?php echo $base_url; ?>/about.php" class="block px-4 py-3 hover:bg-gray-50 text-primary transition-colors duration-200">About Us</a>
                                    <a href="<?php echo $base_url; ?>/about.php#history" class="block px-4 py-3 hover:bg-gray-50 text-primary transition-colors duration-200">Our History</a>
                                    <a href="<?php echo $base_url; ?>/about.php#mission" class="block px-4 py-3 hover:bg-gray-50 text-primary transition-colors duration-200">Mission & Vision</a>
                                </div>
                            </div>
                        </div>

                        <!-- Our Inspiration -->
                        <a href="<?php echo $base_url; ?>/pages/inspiration/index.php" class="nav-link <?php echo $current_directory == 'inspiration' ? 'text-accent font-bold' : 'text-primary hover:text-accent'; ?> transition-colors duration-300 py-2">Our Inspiration</a>

                        <!-- Admissions -->
                        <a href="<?php echo $base_url; ?>/apply-now.php" class="nav-link <?php echo $current_directory == 'admissions' ? 'text-accent font-bold' : 'text-primary hover:text-accent'; ?> transition-colors duration-300 py-2">Admissions</a>

                        <!-- Career Dropdown -->
                        <div class="relative group">
                            <button class="nav-link <?php echo $current_directory == 'career' ? 'text-accent font-bold' : 'text-primary hover:text-accent'; ?> flex items-center transition-colors duration-300 py-2">
                                Career <i class="fas fa-chevron-down ml-1 text-xs transition-transform duration-300 group-hover:rotate-180"></i>
                            </button>
                            <div class="absolute left-0 mt-2 w-64 bg-white shadow-xl rounded-lg opacity-0 invisible group-hover:opacity-100 group-hover:visible transition-all duration-300 border border-gray-100 z-50">
                                <div class="py-2">
                                    <a href="<?php echo $base_url; ?>/pages/career/index.php" class="block px-4 py-3 hover:bg-gray-50 text-primary transition-colors duration-200">Career Opportunities</a>
                                    <a href="<?php echo $base_url; ?>/pages/career/application.php" class="block px-4 py-3 hover:bg-gray-50 text-primary transition-colors duration-200">Teaching Positions</a>
                                    <a href="<?php echo $base_url; ?>/pages/career/application.php" class="block px-4 py-3 hover:bg-gray-50 text-primary transition-colors duration-200">Non-Teaching Positions</a>
                                </div>
                            </div>
                        </div>

                        <!-- Blog Dropdown -->
                        <div class="relative group">
                            <button class="nav-link <?php echo $current_directory == 'blog' ? 'text-accent font-bold' : 'text-primary hover:text-accent'; ?> flex items-center transition-colors duration-300 py-2">
                                Blog <i class="fas fa-chevron-down ml-1 text-xs transition-transform duration-300 group-hover:rotate-180"></i>
                            </button>
                            <div class="absolute left-0 mt-2 w-64 bg-white shadow-xl rounded-lg opacity-0 invisible group-hover:opacity-100 group-hover:visible transition-all duration-300 border border-gray-100 z-50">
                                <div class="py-2">
                                    <a href="<?php echo $base_url; ?>/pages/blog/index.php" class="block px-4 py-3 hover:bg-gray-50 text-primary transition-colors duration-200">Latest News & Posts</a>
                                    <a href="<?php echo $base_url; ?>/pages/blog/downloads.php" class="block px-4 py-3 hover:bg-gray-50 text-primary transition-colors duration-200">Downloads</a>
                                    <a href="<?php echo $base_url; ?>/pages/blog/question-papers.php" class="block px-4 py-3 hover:bg-gray-50 text-primary transition-colors duration-200">Question Papers</a>
                                </div>
                            </div>
                        </div>

                        <a href="<?php echo $base_url; ?>/contact.php" class="nav-link <?php echo $current_page == 'contact.php' ? 'text-accent font-bold' : 'text-primary hover:text-accent'; ?> transition-colors duration-300 py-2">Contact</a>

                        <!-- Admin Dashboard Dropdown (Desktop) - ONLY SHOW WHEN LOGGED IN -->
                        <?php if ($is_admin): ?>
                        <div class="relative group">
                            <button class="nav-link text-admin font-bold flex items-center transition-colors duration-300 py-2">
                                Admin <i class="fas fa-chevron-down ml-1 text-xs transition-transform duration-300 group-hover:rotate-180"></i>
                            </button>
                            <div class="absolute left-0 mt-2 w-64 bg-white shadow-xl rounded-lg opacity-0 invisible group-hover:opacity-100 group-hover:visible transition-all duration-300 border border-gray-100 z-50">
                                <div class="py-2">
                                    <a href="<?php echo $base_url; ?>/admin/dashboard.php" class="block px-4 py-3 hover:bg-gray-50 text-primary transition-colors duration-200 border-l-4 border-admin">Dashboard</a>
                                    <a href="<?php echo $base_url; ?>/admin/students.php" class="block px-4 py-3 hover:bg-gray-50 text-primary transition-colors duration-200">Manage Students</a>
                                    <a href="<?php echo $base_url; ?>/admin/teachers.php" class="block px-4 py-3 hover:bg-gray-50 text-primary transition-colors duration-200">Manage Teachers</a>
                                    <a href="<?php echo $base_url; ?>/admin/applications.php" class="block px-4 py-3 hover:bg-gray-50 text-primary transition-colors duration-200">Applications</a>
                                    <a href="<?php echo $base_url; ?>/admin/settings.php" class="block px-4 py-3 hover:bg-gray-50 text-primary transition-colors duration-200">Settings</a>
                                    <div class="border-t border-gray-200 mt-2 pt-2">
                                        <a href="<?php echo $base_url; ?>/admin/logout.php" class="block px-4 py-3 hover:bg-red-50 text-red-600 transition-colors duration-200">
                                            <i class="fas fa-sign-out-alt mr-2"></i> Logout
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <?php endif; ?>
                    </div>

                    <!-- CTA Buttons - Desktop -->
                    <div class="hidden lg:flex items-center space-x-4">
                        <!-- Admin Login Button - HIDDEN FROM NORMAL USERS -->
                        <div class="admin-login-hidden">
                            <?php if (!$is_admin): ?>
                                <a href="<?php echo $base_url; ?>/admin/login.php" class="bg-gray-600 text-white px-4 py-2 rounded-lg font-medium hover:bg-gray-700 transition-colors duration-300 text-sm">
                                    <i class="fas fa-lock mr-2"></i> Admin
                                </a>
                            <?php endif; ?>
                        </div>
                        
                        <!-- Apply Now Button (Desktop) - Direct Link with Blinking Effect -->
                        <a href="<?php echo $base_url; ?>/apply-now.php" 
                           class="blink-apply text-white px-6 py-2.5 rounded-lg font-medium transition-all duration-300 shadow-md flex items-center">
                            <i class="fas fa-edit mr-2"></i> Apply Now
                        </a>
                    </div>

                    <!-- Mobile menu button -->
                    <div class="lg:hidden flex items-center space-x-0 mobile-cta-buttons">
                        <!-- Admin Badge - ONLY SHOW WHEN LOGGED IN -->
                        <?php if ($is_admin): ?>
                            <span class="bg-admin text-white px-2 py-1 rounded text-xs font-bold compact-button">
                                Admin
                            </span>
                        <?php endif; ?>
                        
                        <a href="<?php echo $base_url; ?>/contact.php" class="text-primary hover:text-accent transition-colors p-2 rounded-lg hover:bg-gray-100 compact-button">
                            <i class="fas fa-phone text-lg"></i>
                        </a>
                        
                        <!-- Mobile Apply Now Button - Direct Link with Blinking Effect -->
                        <a href="<?php echo $base_url; ?>/apply-now.php" 
                           class="blink-apply text-white px-3 py-2 rounded-lg font-medium transition-all duration-300 text-sm compact-button flex items-center">
                            <i class="fas fa-edit mr-1"></i> Apply
                        </a>
                        
                        <button id="mobile-menu-button" class="text-primary hover:text-accent transition-colors p-2 rounded-lg hover:bg-gray-100 compact-button">
                            <i class="fas fa-bars text-xl"></i>
                        </button>
                    </div>
                </div>
            </div>
        </nav>
    </header>

    <script>
        // Mobile sidebar functionality
        document.addEventListener('DOMContentLoaded', function() {
            const mobileMenuButton = document.getElementById('mobile-menu-button');
            const mobileSidebar = document.getElementById('mobile-sidebar');
            const mobileSidebarClose = document.getElementById('mobile-sidebar-close');
            const mobileSidebarOverlay = document.getElementById('mobile-sidebar-overlay');
            
            // Open mobile sidebar
            mobileMenuButton.addEventListener('click', function() {
                mobileSidebar.classList.add('active');
                mobileSidebarOverlay.classList.add('active');
                document.body.style.overflow = 'hidden';
            });
            
            // Close mobile sidebar
            function closeMobileSidebar() {
                mobileSidebar.classList.remove('active');
                mobileSidebarOverlay.classList.remove('active');
                document.body.style.overflow = '';
                
                // Close all accordions when sidebar closes
                const accordionContents = document.querySelectorAll('.mobile-accordion-content');
                const accordionIcons = document.querySelectorAll('.mobile-accordion-btn i.fa-chevron-down');
                
                accordionContents.forEach(content => {
                    content.classList.remove('active');
                });
                
                accordionIcons.forEach(icon => {
                    icon.classList.remove('rotate-180');
                });
            }
            
            mobileSidebarClose.addEventListener('click', closeMobileSidebar);
            mobileSidebarOverlay.addEventListener('click', closeMobileSidebar);
            
            // Mobile accordion functionality
            const accordionButtons = document.querySelectorAll('.mobile-accordion-btn');
            
            accordionButtons.forEach(button => {
                button.addEventListener('click', function(e) {
                    e.preventDefault();
                    e.stopPropagation();
                    
                    const content = this.nextElementSibling;
                    const icon = this.querySelector('i.fa-chevron-down');
                    
                    // Toggle current accordion
                    if (content.classList.contains('active')) {
                        content.classList.remove('active');
                        icon.classList.remove('rotate-180');
                    } else {
                        content.classList.add('active');
                        icon.classList.add('rotate-180');
                    }
                    
                    // Close other accordions
                    accordionButtons.forEach(otherButton => {
                        if (otherButton !== button) {
                            const otherContent = otherButton.nextElementSibling;
                            const otherIcon = otherButton.querySelector('i.fa-chevron-down');
                            
                            otherContent.classList.remove('active');
                            otherIcon.classList.remove('rotate-180');
                        }
                    });
                });
            });
            
            // Close sidebar when clicking on a link
            const mobileNavLinks = document.querySelectorAll('.mobile-nav-item, .mobile-subnav-item');
            mobileNavLinks.forEach(link => {
                link.addEventListener('click', function() {
                    closeMobileSidebar();
                });
            });

            // Handle anchor links with fixed header
            document.querySelectorAll('a[href^="#"]').forEach(anchor => {
                anchor.addEventListener('click', function (e) {
                    e.preventDefault();
                    const targetId = this.getAttribute('href');
                    if (targetId === '#') return;
                    
                    const targetElement = document.querySelector(targetId);
                    if (targetElement) {
                        const headerHeight = document.querySelector('.fixed-header').offsetHeight;
                        const targetPosition = targetElement.getBoundingClientRect().top + window.pageYOffset - headerHeight;
                        
                        window.scrollTo({
                            top: targetPosition,
                            behavior: 'smooth'
                        });
                    }
                });
            });

            // Adjust body padding based on header height
            function adjustBodyPadding() {
                const header = document.querySelector('.fixed-header');
                const headerHeight = header.offsetHeight;
                document.body.style.paddingTop = headerHeight + 'px';
            }

            // Adjust on load and resize
            window.addEventListener('load', adjustBodyPadding);
            window.addEventListener('resize', adjustBodyPadding);
        });
    </script>
</body>
</html>