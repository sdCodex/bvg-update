<?php
// Smart Base URL - Auto detect for deployment
$is_localhost = ($_SERVER['SERVER_NAME'] == 'localhost' || $_SERVER['SERVER_NAME'] == '127.0.0.1');
$base_url = $is_localhost ? '/work/Gurkul_Project' : '';

// Set current page for active navigation
$current_page = 'index.php';
$current_directory = 'programs';

// Include header
include '../includes/header.php';

// Database connection
try {
    include '../includes/db.php';

    // Fetch programs from database
    $stmt = $pdo->query("
        SELECT * FROM programs 
        WHERE active = 1 
        ORDER BY display_order, title
    ");
    $programs = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
} catch(PDOException $e) {
    error_log("Database error: " . $e->getMessage());
    $programs = [];
}

// Helper function to get appropriate icon for each program
function getProgramIcon($programTitle) {
    $icons = [
        'Primary Education' => 'fas fa-child',
        'Middle School' => 'fas fa-school',
        'High School' => 'fas fa-user-graduate',
        'Vedic Studies' => 'fas fa-om',
        'Yoga & Meditation' => 'fas fa-spa',
        'IIT/JEE Foundation' => 'fas fa-atom'
    ];
    
    foreach ($icons as $key => $icon) {
        if (strpos($programTitle, $key) !== false) {
            return $icon;
        }
    }
    
    return 'fas fa-graduation-cap';
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Educational Programs - Bhaktivedanta Gurukul</title>
    <meta name="description" content="Explore our comprehensive educational programs at Bhaktivedanta Gurukul - Primary, Middle, High School with CBSE curriculum and Vedic studies">
    <meta name="keywords" content="educational programs, CBSE school, Vedic education, gurukul, holistic education">
    
    <!-- Additional CSS for this page -->
    <style>
        .program-nav-link {
            padding: 12px 20px;
            border-radius: 8px;
            font-weight: 600;
            color: #4B5563;
            transition: all 0.3s ease;
            white-space: nowrap;
            border: 2px solid transparent;
            text-decoration: none;
            display: inline-block;
        }

        .program-nav-link:hover {
            color: #DC2626;
            background-color: rgba(220, 38, 38, 0.1);
            border-color: rgba(220, 38, 38, 0.2);
        }

        .program-nav-link.active {
            background-color: #DC2626;
            color: white;
            border-color: #DC2626;
        }

        .hide-scrollbar {
            -ms-overflow-style: none;
            scrollbar-width: none;
        }

        .hide-scrollbar::-webkit-scrollbar {
            display: none;
        }

        /* Fix for mobile sidebar overflow */
        .mobile-sidebar {
            max-height: 100vh;
            overflow-y: auto;
        }

        /* Ensure programs dropdown doesn't overflow */
        @media (max-width: 1024px) {
            .mobile-accordion-content {
                max-height: 300px;
                overflow-y: auto;
            }
        }

        /* Modern Card Styles */
        .program-card {
            background: white;
            border-radius: 20px;
            overflow: hidden;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.05);
            transition: all 0.4s cubic-bezier(0.175, 0.885, 0.32, 1.275);
            position: relative;
            height: 100%;
            display: flex;
            flex-direction: column;
            border: 1px solid rgba(0, 0, 0, 0.05);
        }

        .program-card:hover {
            transform: translateY(-10px);
            box-shadow: 0 20px 40px rgba(0, 0, 0, 0.1);
        }

        .card-image-container {
            position: relative;
            height: 200px;
            overflow: hidden;
        }

        .card-image {
            width: 100%;
            height: 100%;
            object-fit: cover;
            transition: transform 0.8s ease;
        }

        .program-card:hover .card-image {
            transform: scale(1.1);
        }

        .card-overlay {
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: linear-gradient(to bottom, transparent 0%, rgba(0, 0, 0, 0.7) 100%);
            display: flex;
            align-items: flex-end;
            padding: 20px;
        }

        .card-icon {
            width: 60px;
            height: 60px;
            background: white;
            border-radius: 16px;
            display: flex;
            align-items: center;
            justify-content: center;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
        }

        .card-icon i {
            font-size: 24px;
            color: #DC2626;
        }

        .card-content {
            padding: 24px;
            flex-grow: 1;
            display: flex;
            flex-direction: column;
        }

        .card-title {
            font-family: 'Playfair Display', serif;
            font-size: 1.5rem;
            font-weight: 700;
            color: #1F2937;
            margin-bottom: 12px;
            line-height: 1.3;
        }

        .card-description {
            color: #6B7280;
            font-size: 0.95rem;
            line-height: 1.6;
            margin-bottom: 20px;
            flex-grow: 1;
        }

        .card-details {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 12px;
            margin-bottom: 24px;
        }

        .detail-item {
            display: flex;
            flex-direction: column;
            align-items: center;
            text-align: center;
            padding: 12px;
            background: #F9FAFB;
            border-radius: 12px;
        }

        .detail-icon {
            color: #DC2626;
            margin-bottom: 6px;
            font-size: 14px;
        }

        .detail-label {
            font-size: 0.75rem;
            color: #6B7280;
            margin-bottom: 4px;
        }

        .detail-value {
            font-size: 0.9rem;
            font-weight: 600;
            color: #1F2937;
        }

        .card-actions {
            display: flex;
            flex-direction: column;
            gap: 12px;
        }

        .btn-primary {
            background: #DC2626;
            color: white;
            border: none;
            padding: 12px 16px;
            border-radius: 12px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s ease;
            display: flex;
            align-items: center;
            justify-content: center;
            text-decoration: none;
        }

        .btn-primary:hover {
            background: #B91C1C;
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(220, 38, 38, 0.3);
        }

        .btn-secondary {
            background: transparent;
            color: #DC2626;
            border: 2px solid #DC2626;
            padding: 10px 16px;
            border-radius: 12px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s ease;
            display: flex;
            align-items: center;
            justify-content: center;
            text-decoration: none;
        }

        .btn-secondary:hover {
            background: #DC2626;
            color: white;
            transform: translateY(-2px);
        }

        .featured-badge {
            position: absolute;
            top: 16px;
            right: 16px;
            background: linear-gradient(135deg, #DC2626, #B91C1C);
            color: white;
            padding: 6px 12px;
            border-radius: 20px;
            font-size: 0.75rem;
            font-weight: 600;
            z-index: 10;
            box-shadow: 0 4px 10px rgba(220, 38, 38, 0.3);
        }

        .btn-icon {
            margin-right: 8px;
        }

        /* Grid layout improvements */
        .programs-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(350px, 1fr));
            gap: 30px;
        }

        @media (max-width: 768px) {
            .programs-grid {
                grid-template-columns: 1fr;
            }
            
            .card-details {
                grid-template-columns: 1fr;
            }
        }
    </style>
</head>
<body class="font-sans bg-white">
    <!-- Programs Page Content -->
    <section class="min-h-screen bg-gray-50">
        <!-- Hero Section -->
        <section class="relative bg-gradient-to-br from-primary to-accent text-white py-20 lg:py-28 overflow-hidden">
            <div class="absolute inset-0 opacity-10">
                <div class="absolute top-10 left-10 w-20 h-20 bg-white rounded-full"></div>
                <div class="absolute bottom-10 right-10 w-32 h-32 bg-white rounded-full"></div>
                <div class="absolute top-1/2 left-1/4 w-16 h-16 bg-white rounded-full"></div>
            </div>
            
            <div class="max-w-7xl mx-auto px-4 text-center relative z-10">
                <h1 class="font-serif text-4xl md:text-5xl lg:text-6xl font-bold mb-6">
                    Our <span class="text-yellow-300">Educational</span> Programs
                </h1>
                <p class="text-xl md:text-2xl text-gray-200 max-w-3xl mx-auto mb-8">
                    Comprehensive curriculum designed for academic excellence and spiritual growth
                </p>
                <p class="text-lg text-gray-300 max-w-2xl mx-auto">
                    Blending modern education with ancient Vedic wisdom for holistic development
                </p>
                
                <!-- Stats -->
                <div class="grid grid-cols-2 md:grid-cols-4 gap-8 mt-12 max-w-2xl mx-auto">
                    <div class="text-center">
                        <div class="text-3xl font-bold text-yellow-300"><?php echo count($programs); ?>+</div>
                        <div class="text-gray-300 text-sm">Programs</div>
                    </div>
                    <div class="text-center">
                        <div class="text-3xl font-bold text-yellow-300">8+</div>
                        <div class="text-gray-300 text-sm">Years Teaching Experience</div>
                    </div>
                    <div class="text-center">
                        <div class="text-3xl font-bold text-yellow-300">50+</div>
                        <div class="text-gray-300 text-sm">Students</div>
                    </div>
                    <div class="text-center">
                        <div class="text-3xl font-bold text-yellow-300">20+</div>
                        <div class="text-gray-300 text-sm">Expert Faculty</div>
                    </div>
                </div>
            </div>
        </section>

        <!-- Programs Navigation -->
        <section class="bg-white shadow-sm sticky top-[76px] z-40 border-b border-gray-200">
            <div class="max-w-7xl mx-auto px-4">
                <div class="flex overflow-x-auto space-x-1 py-4 hide-scrollbar">
                    <a href="#all-programs" class="program-nav-link active whitespace-nowrap">
                        <i class="fas fa-th-large mr-2"></i>All Programs
                    </a>
                    <?php foreach ($programs as $program): 
                        $program_id = 'program-' . $program['id'];
                    ?>
                    <a href="#<?php echo $program_id; ?>" class="program-nav-link whitespace-nowrap">
                        <i class="<?php echo getProgramIcon($program['title']); ?> mr-2"></i>
                        <?php 
                            $shortTitle = $program['title'];
                            if (strlen($shortTitle) > 20) {
                                $shortTitle = substr($shortTitle, 0, 20) . '...';
                            }
                            echo htmlspecialchars($shortTitle); 
                        ?>
                    </a>
                    <?php endforeach; ?>
                </div>
            </div>
        </section>

        <!-- Programs Grid -->
        <section id="all-programs" class="py-16 lg:py-24 bg-gray-50">
            <div class="max-w-7xl mx-auto px-4">
                <div class="text-center mb-16">
                    <h2 class="font-serif text-3xl md:text-4xl font-bold text-primary mb-4">
                        Explore Our <span class="text-accent">Programs</span>
                    </h2>
                    <p class="text-xl text-gray-600 max-w-2xl mx-auto">
                        Choose from our diverse range of educational programs designed for holistic development
                    </p>
                </div>

                <?php if (!empty($programs)): ?>
                <div class="programs-grid">
                    <?php foreach ($programs as $program): 
                        $program_id = 'program-' . $program['id'];
                        $icon = getProgramIcon($program['title']);
                        $is_featured = $program['featured'] ?? false;
                        $image = $program['image'] ?? 'https://images.unsplash.com/photo-1523050854058-8df90110c9f1?ixlib=rb-4.0.3&auto=format&fit=crop&w=500&q=80';
                    ?>
                    <div class="program-card">
                        <?php if ($is_featured): ?>
                        <div class="featured-badge">
                            <i class="fas fa-star mr-1"></i>Featured
                        </div>
                        <?php endif; ?>
                        
                        <!-- Program Image -->
                        <div class="card-image-container">
                            <img src="<?php echo $image; ?>" 
                                 alt="<?php echo htmlspecialchars($program['title']); ?>" 
                                 class="card-image">
                            <div class="card-overlay">
                                <div class="card-icon">
                                    <i class="<?php echo $icon; ?>"></i>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Card Content -->
                        <div class="card-content">
                            <!-- Program Title -->
                            <h3 class="card-title">
                                <?php echo htmlspecialchars($program['title']); ?>
                            </h3>
                            
                            <!-- Program Description -->
                            <p class="card-description">
                                <?php echo htmlspecialchars($program['short_description'] ?? $program['description']); ?>
                            </p>
                            
                            <!-- Program Details -->
                            <div class="card-details">
                                <div class="detail-item">
                                    <i class="fas fa-clock detail-icon"></i>
                                    <span class="detail-label">Duration</span>
                                    <span class="detail-value"><?php echo htmlspecialchars($program['duration']); ?></span>
                                </div>
                                <div class="detail-item">
                                    <i class="fas fa-users detail-icon"></i>
                                    <span class="detail-label">Age Group</span>
                                    <span class="detail-value"><?php echo htmlspecialchars($program['age_group']); ?></span>
                                </div>
                                <div class="detail-item">
                                    <i class="fas fa-rupee-sign detail-icon"></i>
                                    <span class="detail-label">Annual Fees</span>
                                    <span class="detail-value"><?php echo htmlspecialchars($program['fees']); ?></span>
                                </div>
                                <div class="detail-item">
                                    <i class="fas fa-book detail-icon"></i>
                                    <span class="detail-label">Curriculum</span>
                                    <span class="detail-value">CBSE + Vedic</span>
                                </div>
                            </div>
                            
                            <!-- Action Buttons -->
                            <div class="card-actions">
                                <a href="<?php echo $base_url ?>/pages/admissions/apply.php"
                                   class="btn-primary">
                                    <i class="fas fa-info-circle btn-icon"></i>Enquire Now
                                </a>
                                <a href="#<?php echo $program_id; ?>" 
                                   class="btn-secondary program-detail-btn">
                                    <i class="fas fa-book-open btn-icon"></i>Learn More
                                </a>
                            </div>
                        </div>
                    </div>
                    <?php endforeach; ?>
                </div>
                <?php else: ?>
                <div class="text-center py-12">
                    <div class="text-gray-500 text-lg">
                        <i class="fas fa-exclamation-circle text-4xl mb-4"></i>
                        <p>No programs available at the moment.</p>
                    </div>
                </div>
                <?php endif; ?>
            </div>
        </section>

        <!-- Rest of the page content remains the same -->
        <!-- Program Details Section -->
        <section class="py-16 lg:py-24 bg-white">
            <div class="max-w-7xl mx-auto px-4">
                <div class="text-center mb-16">
                    <h2 class="font-serif text-3xl md:text-4xl font-bold text-primary mb-4">
                        Program <span class="text-accent">Details</span>
                    </h2>
                    <p class="text-xl text-gray-600 max-w-2xl mx-auto">
                        In-depth information about our educational offerings
                    </p>
                </div>

                <?php if (!empty($programs)): ?>
                <?php foreach ($programs as $index => $program): 
                    $program_id = 'program-' . $program['id'];
                    $image = $program['image'] ?? 'https://images.unsplash.com/photo-1523050854058-8df90110c9f1?ixlib=rb-4.0.3&auto=format&fit=crop&w=500&q=80';
                ?>
                <div id="<?php echo $program_id; ?>" class="mb-20 scroll-mt-24">
                    <div class="bg-gradient-to-br from-gray-50 to-white rounded-2xl p-8 border border-gray-200">
                        <div class="grid grid-cols-1 lg:grid-cols-2 gap-8 items-start">
                            <!-- Left Content -->
                            <div>
                                <div class="inline-flex items-center px-4 py-2 rounded-full bg-accent/10 text-accent font-semibold text-sm mb-6">
                                    <i class="<?php echo getProgramIcon($program['title']); ?> mr-2"></i> 
                                    <?php echo htmlspecialchars($program['age_group']); ?>
                                </div>
                                <h3 class="font-serif text-2xl md:text-3xl font-bold text-gray-800 mb-4">
                                    <?php echo htmlspecialchars($program['title']); ?>
                                </h3>
                                <p class="text-lg text-gray-600 mb-6 leading-relaxed">
                                    <?php echo htmlspecialchars($program['description']); ?>
                                </p>
                                
                                <!-- Features -->
                                <div class="space-y-4 mb-8">
                                    <div class="flex items-start space-x-4">
                                        <div class="w-6 h-6 bg-green-100 rounded-full flex items-center justify-center flex-shrink-0 mt-1">
                                            <i class="fas fa-check text-green-600 text-xs"></i>
                                        </div>
                                        <span class="text-gray-600">CBSE curriculum with Vedic values integration</span>
                                    </div>
                                    <div class="flex items-start space-x-4">
                                        <div class="w-6 h-6 bg-green-100 rounded-full flex items-center justify-center flex-shrink-0 mt-1">
                                            <i class="fas fa-check text-green-600 text-xs"></i>
                                        </div>
                                        <span class="text-gray-600">Holistic development approach</span>
                                    </div>
                                    <div class="flex items-start space-x-4">
                                        <div class="w-6 h-6 bg-green-100 rounded-full flex items-center justify-center flex-shrink-0 mt-1">
                                            <i class="fas fa-check text-green-600 text-xs"></i>
                                        </div>
                                        <span class="text-gray-600">Experienced and dedicated faculty</span>
                                    </div>
                                    <div class="flex items-start space-x-4">
                                        <div class="w-6 h-6 bg-green-100 rounded-full flex items-center justify-center flex-shrink-0 mt-1">
                                            <i class="fas fa-check text-green-600 text-xs"></i>
                                        </div>
                                        <span class="text-gray-600">Modern infrastructure with traditional values</span>
                                    </div>
                                </div>
                                
                                <!-- CTA Button -->
                                <a href="<?php echo $base_url ?>/pages/admissions/apply.php" 
                                       class="bg-accent hover:bg-red-700 text-white font-semibold py-3 px-6 rounded-lg transition-colors duration-300 inline-flex items-center">
                                    <i class="fas fa-user-graduate mr-3"></i> Enroll Now
                                </a>
                            </div>
                            
                            <!-- Right Content -->
                            <div class="space-y-6">
                                <!-- Program Image -->
                                <div class="rounded-xl overflow-hidden shadow-lg">
                                    <img src="<?php echo $image; ?>" 
                                         alt="<?php echo htmlspecialchars($program['title']); ?>" 
                                         class="w-full h-56 object-cover">
                                </div>
                                
                                <!-- Program Summary -->
                                <div class="bg-white rounded-xl p-6 shadow-lg border border-gray-200">
                                    <h4 class="font-serif text-xl font-bold text-primary mb-4 text-center">Program Summary</h4>
                                    <div class="space-y-4 text-sm">
                                        <div class="flex justify-between items-center py-2 border-b border-gray-100">
                                            <span class="font-semibold text-gray-700">Duration:</span>
                                            <span class="text-gray-600"><?php echo htmlspecialchars($program['duration']); ?></span>
                                        </div>
                                        <div class="flex justify-between items-center py-2 border-b border-gray-100">
                                            <span class="font-semibold text-gray-700">Age Group:</span>
                                            <span class="text-gray-600"><?php echo htmlspecialchars($program['age_group']); ?></span>
                                        </div>
                                        <div class="flex justify-between items-center py-2 border-b border-gray-100">
                                            <span class="font-semibold text-gray-700">Annual Fees:</span>
                                            <span class="font-semibold text-accent"><?php echo htmlspecialchars($program['fees']); ?></span>
                                        </div>
                                        <div class="flex justify-between items-center py-2">
                                            <span class="font-semibold text-gray-700">Curriculum:</span>
                                            <span class="text-gray-600"><?php echo htmlspecialchars($program['curriculum_type'] ?? 'CBSE + Vedic Studies'); ?></span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <?php endforeach; ?>
                <?php else: ?>
                <div class="text-center py-12">
                    <div class="text-gray-500 text-lg">
                        <i class="fas fa-exclamation-circle text-4xl mb-4"></i>
                        <p>No program details available at the moment.</p>
                    </div>
                </div>
                <?php endif; ?>
            </div>
        </section>

        <!-- CTA Section -->
        <section class="py-16 lg:py-24 bg-gray-50">
            <div class="max-w-4xl mx-auto text-center px-4">
                <h2 class="font-serif text-3xl md:text-4xl font-bold text-primary mb-6">
                    Ready to Begin the Journey?
                </h2>
                <p class="text-xl text-gray-600 mb-8">
                    Join Bhaktivedanta Gurukul for holistic education
                </p>
                <div class="flex flex-col sm:flex-row gap-4 justify-center">
                    <a href="<?php echo $base_url ?>/contact.php" 
                           class="bg-accent hover:bg-red-700 text-white font-semibold py-3 px-8 rounded-lg transition-colors duration-300 inline-flex items-center justify-center">
                        <i class="fas fa-calendar-alt mr-3"></i> Schedule Visit
                    </button>
                    <a href="<?php echo $base_url ?> /pages/admissions/apply.php" 
                           class="border-2 border-accent text-accent hover:bg-accent hover:text-white font-semibold py-3 px-8 rounded-lg transition-all duration-300 inline-flex items-center justify-center">
                        <i class="fas fa-edit mr-3"></i> Apply Now
                    </a>
                </div>
            </div>
        </section>
    </section>

    <!-- Enrollment Modal -->
    <div id="enrollmentModal" class="fixed inset-0 bg-black bg-opacity-50 z-50 hidden flex items-center justify-center p-4">
        <div class="bg-white rounded-2xl max-w-md w-full max-h-[90vh] overflow-y-auto">
            <div class="p-6">
                <!-- Header -->
                <div class="flex justify-between items-center mb-6">
                    <h3 class="font-serif text-2xl font-bold text-primary" id="modalTitle">Enroll Now</h3>
                    <button onclick="closeModal()" class="text-gray-500 hover:text-gray-700">
                        <i class="fas fa-times text-xl"></i>
                    </button>
                </div>
                
                <!-- Form -->
                <form id="enrollmentForm" class="space-y-4">
                    <input type="hidden" id="selectedProgram" name="program">
                    
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Student Name *</label>
                        <input type="text" name="student_name" required 
                               class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-accent focus:border-transparent">
                    </div>
                    
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Parent Name *</label>
                        <input type="text" name="parent_name" required 
                               class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-accent focus:border-transparent">
                    </div>
                    
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Email *</label>
                        <input type="email" name="email" required 
                               class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-accent focus:border-transparent">
                    </div>
                    
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Phone *</label>
                        <input type="tel" name="phone" required 
                               class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-accent focus:border-transparent">
                    </div>
                    
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Current Grade</label>
                        <select name="grade" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-accent focus:border-transparent">
                            <option value="">Select Grade</option>
                            <option value="Nursery">Nursery</option>
                            <option value="KG">KG</option>
                            <option value="1">Grade 1</option>
                            <option value="2">Grade 2</option>
                            <option value="3">Grade 3</option>
                            <option value="4">Grade 4</option>
                            <option value="5">Grade 5</option>
                            <option value="6">Grade 6</option>
                            <option value="7">Grade 7</option>
                            <option value="8">Grade 8</option>
                            <option value="9">Grade 9</option>
                            <option value="10">Grade 10</option>
                            <option value="11">Grade 11</option>
                            <option value="12">Grade 12</option>
                        </select>
                    </div>
                    
                    <div class="pt-4">
                        <button type="submit" 
                                class="w-full bg-accent hover:bg-red-700 text-white font-semibold py-3 px-4 rounded-lg transition-colors">
                            Submit Enrollment Request
                        </button>
                    </div>
                </form>
                
                <div class="mt-6 p-4 bg-blue-50 rounded-lg">
                    <p class="text-sm text-blue-700 text-center">
                        <i class="fas fa-info-circle mr-1"></i>
                        Our team will contact you within 24 hours
                    </p>
                </div>
            </div>
        </div>
    </div>

    <script>
    // Enrollment Modal Functions
    function openEnrollmentModal(programTitle) {
        document.getElementById('selectedProgram').value = programTitle;
        document.getElementById('modalTitle').textContent = 'Enroll in ' + programTitle;
        document.getElementById('enrollmentModal').classList.remove('hidden');
        document.body.style.overflow = 'hidden';
    }

    function closeModal() {
        document.getElementById('enrollmentModal').classList.add('hidden');
        document.body.style.overflow = 'auto';
    }

    // Form Submission
    document.getElementById('enrollmentForm').addEventListener('submit', function(e) {
        e.preventDefault();
        
        const formData = new FormData(this);
        const program = formData.get('program');
        const studentName = formData.get('student_name');
        
        // Show success message
        alert('Thank you ' + studentName + '! Your enrollment request for "' + program + '" has been submitted successfully. Our team will contact you within 24 hours.');
        
        // Close modal and reset form
        closeModal();
        this.reset();
    });

    // Program Navigation and Smooth Scrolling
    document.addEventListener('DOMContentLoaded', function() {
        const programNavLinks = document.querySelectorAll('.program-nav-link');
        const programDetailBtns = document.querySelectorAll('.program-detail-btn');
        
        // Smooth scrolling for navigation links
        programNavLinks.forEach(link => {
            link.addEventListener('click', function(e) {
                e.preventDefault();
                const targetId = this.getAttribute('href').substring(1);
                const targetSection = document.getElementById(targetId);
                
                if (targetSection) {
                    const offsetTop = targetSection.offsetTop - 120;
                    window.scrollTo({
                        top: offsetTop,
                        behavior: 'smooth'
                    });
                    
                    // Update active state
                    programNavLinks.forEach(l => l.classList.remove('active'));
                    this.classList.add('active');
                }
            });
        });

        // Smooth scrolling for learn more buttons
        programDetailBtns.forEach(button => {
            button.addEventListener('click', function(e) {
                e.preventDefault();
                const targetId = this.getAttribute('href').substring(1);
                const targetSection = document.getElementById(targetId);
                
                if (targetSection) {
                    const offsetTop = targetSection.offsetTop - 120;
                    window.scrollTo({
                        top: offsetTop,
                        behavior: 'smooth'
                    });
                    
                    // Update active state in navigation
                    programNavLinks.forEach(link => {
                        link.classList.remove('active');
                        if (link.getAttribute('href') === `#${targetId}`) {
                            link.classList.add('active');
                        }
                    });
                }
            });
        });

        // Update active nav link on scroll
        function updateActiveNavLink() {
            const programDetailSections = document.querySelectorAll('[id^="program-"]');
            let current = '';
            const scrollPosition = window.scrollY + 150;
            
            programDetailSections.forEach(section => {
                const sectionTop = section.offsetTop;
                const sectionHeight = section.clientHeight;
                
                if (scrollPosition >= sectionTop && scrollPosition < sectionTop + sectionHeight) {
                    current = section.getAttribute('id');
                }
            });

            programNavLinks.forEach(link => {
                link.classList.remove('active');
                if (link.getAttribute('href') === `#${current}`) {
                    link.classList.add('active');
                }
            });

            // If no section is active and we're at top, make "All Programs" active
            if (!current && window.scrollY < 500) {
                programNavLinks[0].classList.add('active');
            }
        }

        let scrollTimeout;
        window.addEventListener('scroll', function() {
            if (!scrollTimeout) {
                scrollTimeout = setTimeout(function() {
                    updateActiveNavLink();
                    scrollTimeout = null;
                }, 100);
            }
        });

        // Initial update
        updateActiveNavLink();
    });

    // Close modal when clicking outside
    document.getElementById('enrollmentModal').addEventListener('click', function(e) {
        if (e.target === this) {
            closeModal();
        }
    });

    // Close modal with Escape key
    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape') {
            closeModal();
        }
    });
    </script>

    <?php
    // Include footer
    include '../includes/footer.php';
    ?>
</body>
</html>