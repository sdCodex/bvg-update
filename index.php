<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Database connection and base URL setup
// on server url paste below
// $base_url = 'https://bhaktivedantagurukul.com';

$base_url = "https://localhost/Gurukul-website";

// Include database connection
include 'includes/db.php';

// Set current page for header navigation
$current_page = 'index.php';
$current_directory = '';
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />

    <!-- Title -->
    <title>Bhaktivedanta Gurukul - School of Excellence</title>

    <!-- SEO -->
    <meta name="description" content="Bhaktivedanta Gurukul - School of Excellence blends modern education with traditional Vedic values for holistic student development. Enroll now for spiritual and academic excellence." />
    <meta name="keywords" content="Bhaktivedanta Gurukul, Gurukul School, Vedic Education, Spiritual Learning, Best School in India, Holistic Development, Education with Values" />
    <meta name="author" content="Bhaktivedanta Gurukul School of Excellence" />
    <meta name="robots" content="index, follow" />
    <meta name="language" content="English" />
    <meta name="revisit-after" content="7 days" />
    <link rel="canonical" href="https://bhaktivedantagurukul.com/" />

    <!-- Social Sharing -->
    <meta property="og:title" content="Bhaktivedanta Gurukul - School of Excellence | Modern & Vedic Education" />
    <meta property="og:description" content="Empowering students through modern education combined with ancient Vedic wisdom." />
    <meta property="og:url" content="https://bhaktivedantagurukul.com/" />
    <meta property="og:type" content="website" />
    <meta property="og:site_name" content="Bhaktivedanta Gurukul" />
    <meta property="og:image" content="https://bhaktivedantagurukul.com/images/bvgLogo.png" />
    <meta property="og:image:width" content="512" />
    <meta property="og:image:height" content="512" />

    <!-- Twitter -->
    <meta name="twitter:card" content="summary_large_image" />
    <meta name="twitter:title" content="Bhaktivedanta Gurukul - School of Excellence" />
    <meta name="twitter:description" content="A unique blend of modern academics and spiritual learning." />
    <meta name="twitter:image" content="https://bhaktivedantagurukul.com/images/bvgLogo.png" />

    <!-- Theme Color -->
    <meta name="theme-color" content="#DC143C" />

    <!-- Favicon -->
    <link rel="icon" type="image/png" href="https://bhaktivedantagurukul.com/images/bvgLogo.png" />

    <!-- Swiper CSS -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.css" />

    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com" />
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@400;600;700&family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet" />

    <!-- Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com"></script>

    <!-- Custom CSS (After Tailwind) -->
    <link rel="stylesheet" href="https://bhaktivedantagurukul.com/css/index.css" />


    <style>
        .text-accent {
            color: #DC143C;
        }

        .bg-accent {
            background-color: #DC143C;
        }
    </style>

</head>

<body>

    <?php include 'includes/header.php'; ?>
    <!-- index.php में -->

    <!-- Hero Section with Swiper Carousel -->
    <section class="relative overflow-hidden">
        <!-- Swiper Container -->
        <div class="swiper hero-swiper">
            <div class="swiper-wrapper">
                <!-- Slide 1 -->
                <div class="swiper-slide">
                    <div class="absolute inset-0 bg-gradient-to-r from-primary/45 to-primary/35 z-10"></div>
                    <div class="absolute inset-0 bg-cover bg-center" style="background-image: url('<?php echo $base_url; ?>/images/hero-bg-1.png')"></div>
                    <div class="swiper-slide-content">
                        <h1 class="font-serif text-4xl md:text-6xl lg:text-7xl font-bold mb-6 text-shadow">
                            Bhaktivedanta <span class="text-accent">Gurukul</span>
                        </h1>
                        <p class="text-xl md:text-2xl mb-8 text-gray-200 text-shadow">
                            Where Modern Education meets Timeless Vedic Wisdom
                        </p>

                    </div>
                </div>

                <!-- Slide 2 -->
                <div class="swiper-slide">
                    <div class="absolute inset-0 bg-gradient-to-l from-accent/40 to-accent/30 z-10"></div>
                    <div class="absolute inset-0 bg-cover bg-center" style="background-image: url('<?php echo $base_url; ?>/images/background/hero2.jpeg')"></div>
                    <div class="swiper-slide-content">
                        <h1 class="font-serif text-4xl md:text-6xl lg:text-7xl font-bold mb-6 text-shadow">
                            Holistic <span class="text-primary">Education</span>
                        </h1>
                        <p class="text-xl md:text-2xl mb-8 text-gray-200 text-shadow">
                            Body, Mind and Spirit in Perfect Harmony
                        </p>

                    </div>
                </div>

                <!-- Slide 3 -->
                <div class="swiper-slide">
                    <div class="absolute inset-0 bg-gradient-to-br from-secondary/30 to-primary/40 z-10"></div>
                    <div class="absolute inset-0 bg-cover bg-center" style="background-image: url('<?php echo $base_url; ?>/images/icons/home-hero-11.jpeg')"></div>
                    <div class="swiper-slide-content">
                        <h1 class="font-serif text-4xl md:text-6xl lg:text-7xl font-bold mb-6 text-shadow">
                            <span class="text-accent">Vidya</span>
                        </h1>
                        <p class="text-xl md:text-2xl mb-3 text-gray-200 text-shadow">
                            <span class="text-primary bg-white px-2 py-1 rounded-md"> विद्या - Modern Education</span>
                        </p>
                        <p class="text-lg mb-12 text-gray-300 max-w-2xl mx-auto text-shadow ">
                            Expert Faculties for Formal Education
                        </p>


                    </div>
                </div>

                <!-- Slide 4 -->
                <div class="swiper-slide">
                    <div class="absolute inset-0 bg-gradient-to-br from-secondary/30 to-primary/40 z-10"></div>
                    <div class="absolute inset-0 bg-cover bg-center" style="background-image: url('<?php echo $base_url; ?>/images/background/bg-cr-4.jpeg')"></div>
                    <div class="swiper-slide-content">
                        <h1 class="font-serif text-4xl md:text-6xl lg:text-7xl font-bold mb-6 text-shadow">
                            Sad <span class="text-accent">Vidya</span>
                        </h1>
                        <p class="text-xl md:text-2xl mb-3 text-gray-200 text-shadow">
                            <span class="text-primary bg-white px-2 py-1 rounded-md"> सद् विद्या - Value Education</span>
                        </p>
                        <p class="text-lg mb-12 text-gray-300 max-w-2xl mx-auto text-shadow ">
                            Nurturing Future Leaders with Goodness of Character
                        </p>

                    </div>
                </div>

                <!-- Slide 5 -->
                <div class="swiper-slide">
                    <div class="absolute inset-0 bg-gradient-to-br from-secondary/30 to-primary/40 z-10"></div>
                    <div class="absolute inset-0 bg-cover bg-center" style="background-image: url('<?php echo $base_url; ?>/images/background/bg-cr-5.jpeg')"></div>
                    <div class="swiper-slide-content">
                        <h1 class="font-serif text-4xl md:text-6xl lg:text-7xl font-bold mb-6 text-shadow">
                            Brahma <span class="text-accent">Vidya</span>
                        </h1>
                        <p class="text-xl md:text-2xl mb-3 text-gray-200 text-shadow">
                            <span class="text-primary bg-white px-2 py-1 rounded-md"> ब्रह्म विद्या - Spiritual Education </span>
                        </p>
                        <p class="text-lg mb-12 text-gray-300 max-w-2xl mx-auto text-shadow">
                            Preserving and Propagating the Timeless Wisdom in Modern Times
                        </p>

                    </div>
                </div>

                <!-- Slide 6 -->
                <!--<div class="swiper-slide">-->
                <!--    <div class="absolute inset-0 bg-gradient-to-br from-secondary/30 to-primary/40 z-10"></div>-->
                <!--    <div class="absolute inset-0 bg-cover bg-center" style="background-image: url('<?php echo $base_url; ?>/images/background/bg-cr-6.jpg')"></div>-->
                <!--<div class="swiper-slide-content">-->
                <!--    <h1 class="font-serif text-4xl md:text-6xl lg:text-7xl font-bold mb-6 text-shadow">-->
                <!--        Brahma <span class="text-accent">Vidya</span>-->
                <!--    </h1>-->
                <!--    <p class="text-xl md:text-2xl mb-3 text-gray-200 text-shadow">-->
                <!--        <span class="text-primary bg-white px-2 py-1 rounded-md"> ब्रह्म विद्या - Spiritual Education </span>-->
                <!--    </p>-->
                <!--    <p class="text-lg mb-12 text-gray-300 max-w-2xl mx-auto text-shadow">-->
                <!--        Preserving and Propagating the Timeless Wisdom in Modern Times-->
                <!--    </p>-->

                <!--</div>-->
                <!--</div>-->
            </div>

            <!-- Swiper Pagination -->
            <div class="swiper-pagination"></div>

            <!-- Swiper Navigation -->
            <div class="swiper-button-next"></div>
            <div class="swiper-button-prev"></div>
        </div>
    </section>

    <!-- Quick Stats Section -->
    <section class="bg-white py-12 border-b border-gray-200">
        <div class="max-w-7xl mx-auto px-4">
            <div class="grid grid-cols-2 md:grid-cols-4 gap-8">
                <div class="text-center">
                    <div class="text-3xl md:text-4xl font-bold text-accent mb-2">50+</div>
                    <div class="text-secondary font-medium">Students Enrolled</div>
                </div>
                <div class="text-center">
                    <div class="text-3xl md:text-4xl font-bold text-accent mb-2">20+</div>
                    <div class="text-secondary font-medium">Expert Faculty</div>
                </div>
                <div class="text-center">
                    <div class="text-3xl md:text-4xl font-bold text-accent mb-2">8+</div>
                    <div class="text-secondary font-medium">Years Teaching Experience</div>
                </div>
                <div class="text-center">
                    <div class="text-3xl md:text-4xl font-bold text-accent mb-2">100%</div>
                    <div class="text-secondary font-medium">Values Based</div>
                </div>
            </div>
        </div>
    </section>

    <!-- About Preview Section -->
    <section class="section-padding bg-light">
        <div class="max-w-7xl mx-auto px-4">
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-12 items-center">
                <div>
                    <div class="inline-flex items-center px-4 py-2 rounded-full bg-accent/10 text-accent font-semibold text-sm mb-6">
                        <i class="fas fa-star mr-2"></i> Established 2025
                    </div>
                    <h2 class="font-serif text-3xl md:text-4xl font-bold text-primary mb-6">
                        Tradition Meets <span class="text-accent">Innovation</span>
                    </h2>
                    <p class="text-lg text-secondary mb-6 leading-relaxed">
                        Established with the Divine Vision of Srila Prabhupada, Bhaktivedanta Gurukul seamlessly integrates the ancient Gurukul system with contemporary educational methodologies.
                    </p>
                    <p class="text-secondary mb-8 leading-relaxed">
                        Our unique pedagogical approach ensures students receive not only academic excellence but also spiritual wisdom, character development, and practical life skills essential for the modern world.
                    </p>

                    <div class="flex flex-col sm:flex-row gap-4">
                        <a href="<?php echo $base_url; ?>/about.php" class="btn-primary">
                            Discover Our Story <i class="fas fa-book-open ml-2"></i>
                        </a>
                        <a href="<?php echo $base_url; ?>/contact.php" class="btn-secondary">
                            <i class="fas fa-map-marker-alt mr-2"></i> Visit Campus
                        </a>
                    </div>
                </div>
                <div>
                    <div class="grid grid-cols-2 gap-4">
                        <div class="space-y-4">
                            <div class="bg-white rounded-xl p-4 shadow-lg border border-gray-100">
                                <img src="<?php echo $base_url; ?>/images/background/hero-bg-10" alt="Campus Building" class="w-full h-32 object-cover rounded-lg mb-3">
                                <h4 class="font-semibold text-primary">Modern Infrastructure</h4>
                            </div>
                            <div class="bg-white rounded-xl p-4 shadow-lg border border-gray-100">
                                <img src="<?php echo $base_url; ?>/images/background/library.jpg" alt="Library" class="w-full h-32 object-cover rounded-lg mb-3">
                                <h4 class="font-semibold text-primary"> Library</h4>
                            </div>
                        </div>
                        <div class="space-y-4 mt-8">
                            <div class="bg-white rounded-xl p-4 shadow-lg border border-gray-100">
                                <img src="<?php echo $base_url; ?>/images/background/bg-r1-img.jpg" alt="Sports" class="w-full h-32 object-cover rounded-lg mb-3">
                                <h4 class="font-semibold text-primary">Sports Facilities</h4>
                            </div>
                            <div class="bg-white rounded-xl p-4 shadow-lg border border-gray-100">
                                <img src="<?php echo $base_url; ?>/images/background/lab.jpg" alt="Science Lab" class="w-full h-32 object-cover rounded-lg mb-3">
                                <h4 class="font-semibold text-primary">Science Labs</h4>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Why Choose Us Section -->
    <section class="section-padding bg-white">
        <div class="max-w-7xl mx-auto px-4">
            <div class="text-center mb-16">
                <div class="inline-flex items-center px-4 py-2 rounded-full bg-accent/10 text-accent font-semibold text-sm mb-4">
                    <i class="fas fa-award mr-2"></i> Why Choose Us
                </div>
                <h2 class="font-serif text-3xl md:text-4xl font-bold text-primary mb-4">
                    The <span class="text-accent">Gurukul</span> Advantage
                </h2>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
                <div class="text-center p-6 rounded-2xl bg-light hover:bg-white hover:shadow-xl transition-all duration-300">
                    <div class="w-20 h-20 bg-gradient-to-br from-accent to-red-600 rounded-full flex items-center justify-center mx-auto mb-6">
                        <i class="fas fa-brain text-white text-2xl"></i>
                    </div>
                    <h3 class="font-serif text-xl font-bold text-primary mb-4">Academic Excellence</h3>
                    <p class="text-secondary leading-relaxed">
                        CBSE curriculum with innovative teaching methods, regular assessments, and personalized attention for each student.
                    </p>
                </div>
                <div class="text-center p-6 rounded-2xl bg-light hover:bg-white hover:shadow-xl transition-all duration-300">
                    <div class="w-20 h-20 bg-gradient-to-br from-accent to-red-600 rounded-full flex items-center justify-center mx-auto mb-6">
                        <i class="fas fa-users text-white text-2xl"></i>
                    </div>
                    <h3 class="font-serif text-xl font-bold text-primary mb-4">Holistic Development</h3>
                    <p class="text-secondary leading-relaxed">
                        Sports, arts, cultural activities, and life skills training for complete personality development.
                    </p>
                </div>

                <div class="text-center p-6 rounded-2xl bg-light hover:bg-white hover:shadow-xl transition-all duration-300">
                    <div class="w-20 h-20 bg-gradient-to-br from-accent to-red-600 rounded-full flex items-center justify-center mx-auto mb-6">
                        <i class="fas fa-heart text-white text-2xl"></i>
                    </div>
                    <h3 class="font-serif text-xl font-bold text-primary mb-4">Spiritual Growth</h3>
                    <p class="text-secondary leading-relaxed">
                        Daily yoga, meditation, Vedic studies, and moral education for character building and inner peace.
                    </p>
                </div>


            </div>
        </div>
    </section>

    <!--  Programs Section from database -->
    <section class="section-padding program-section">
        <div class="max-w-7xl mx-auto px-4">
            <div class="text-center mb-16">
                <div class="inline-flex items-center px-4 py-2 rounded-full bg-accent/10 text-accent font-semibold text-sm mb-4">
                    <i class="fas fa-graduation-cap mr-2"></i> Our Programs
                </div>
                <h2 class="font-serif text-3xl md:text-4xl font-bold text-primary mb-4">
                    Educational <span class="text-accent">Programs</span>
                </h2>
                <p class="text-xl text-gray-600 max-w-2xl mx-auto">
                    Comprehensive curriculum designed for holistic development and academic excellence
                </p>
            </div>

            <?php
            // Database se programs fetch karna
            try {
                $stmt = $pdo->query("
                SELECT * FROM programs 
                WHERE active = 1
                ORDER BY display_order, title
                LIMIT 3
            ");
                $programs = $stmt->fetchAll(PDO::FETCH_ASSOC);
            } catch (PDOException $e) {
                error_log("Database error: " . $e->getMessage());
                $programs = [];
            }

            // Helper functions
            function getProgramIcon($programTitle)
            {
                $icons = [
                    'Primary Education' => 'fas fa-child',
                    'Middle School' => 'fas fa-school',
                    'High School' => 'fas fa-user-graduate',
                    'Vedic Studies' => 'fas fa-om',
                    'Yoga & Meditation' => 'fas fa-spa',
                    'IIT/JEE' => 'fas fa-atom',
                    'NEET' => 'fas fa-stethoscope'
                ];

                foreach ($icons as $key => $icon) {
                    if (strpos($programTitle, $key) !== false) {
                        return $icon;
                    }
                }

                return 'fas fa-graduation-cap';
            }

            function getProgramImage($program)
            {
                if (!empty($program['image'])) {
                    if (filter_var($program['image'], FILTER_VALIDATE_URL)) {
                        return $program['image'];
                    } else {
                        global $base_url;
                        return $base_url . '/' . ltrim($program['image'], '/');
                    }
                }

                $defaultImages = [
                    'Primary Education' => 'https://images.unsplash.com/photo-1503676260728-1c00da094a0b?ixlib=rb-4.0.3&auto=format&fit=crop&w=500&q=80',
                    'Middle School' => 'https://images.unsplash.com/photo-1498243691581-b145c3f54a5a?ixlib=rb-4.0.3&auto=format&fit=crop&w=500&q=80',
                    'High School' => 'https://images.unsplash.com/photo-1562774053-701939374585?ixlib=rb-4.0.3&auto=format&fit=crop&w=500&q=80',
                    'Vedic Studies' => 'https://images.unsplash.com/photo-1548351514-8b6b13d7176b?ixlib=rb-4.0.3&auto=format&fit=crop&w=500&q=80',
                    'Yoga & Meditation' => 'https://images.unsplash.com/photo-1544367567-0f2fcb009e0b?ixlib=rb-4.0.3&auto=format&fit=crop&w=500&q=80',
                    'IIT/JEE Foundation' => 'https://images.unsplash.com/photo-1635070041078-e363dbe005cb?ixlib=rb-4.0.3&auto=format&fit=crop&w=500&q=80'
                ];

                foreach ($defaultImages as $key => $image) {
                    if (strpos($program['title'], $key) !== false) {
                        return $image;
                    }
                }

                return 'https://images.unsplash.com/photo-1523050854058-8df90110c9f1?ixlib=rb-4.0.3&auto=format&fit=crop&w=500&q=80';
            }

            function getProgramCategory($programTitle)
            {
                $academic = ['CBSE', 'class-1-primary', 'class-2-primary', 'class-3-primary'];
                $spiritual = ['Vedic', 'Middle', 'Spiritual', 'class-4-middle', 'class-5-middle', 'class-6-middle',];
                $coCurricular = ['Sports', 'Arts', 'Music', 'Dance', 'Cultural', 'class-6-high', 'class-7-high', 'class-8-foundation'];

                foreach ($academic as $keyword) {
                    if (stripos($programTitle, $keyword) !== false) {
                        return 'academic';
                    }
                }

                foreach ($spiritual as $keyword) {
                    if (stripos($programTitle, $keyword) !== false) {
                        return 'spiritual';
                    }
                }

                foreach ($coCurricular as $keyword) {
                    if (stripos($programTitle, $keyword) !== false) {
                        return 'co-curricular';
                    }
                }

                return 'academic';
            }

            function getCategoryBadgeColor($category)
            {
                $colors = [
                    'academic' => 'bg-blue-100 text-blue-800',
                    'spiritual' => 'bg-purple-100 text-purple-800',
                    'co-curricular' => 'bg-green-100 text-green-800'
                ];
                return $colors[$category] ?? 'bg-gray-100 text-gray-800';
            }
            ?>

            <!-- Programs Grid -->
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8" id="programs-grid">
                <?php foreach ($programs as $program):
                    $icon = getProgramIcon($program['title']);
                    $is_featured = $program['featured'] ?? false;
                    $image = getProgramImage($program);
                    $category = getProgramCategory($program['title']);
                    $badgeColor = getCategoryBadgeColor($category);
                ?>
                    <div class="program-card bg-white rounded-2xl shadow-lg overflow-hidden border border-gray-100" data-category="<?php echo $category; ?>">
                        <div class="relative">
                            <!-- Program Image -->
                            <div class="program-image-container">
                                <img src="<?php echo $image; ?>"
                                    alt="<?php echo htmlspecialchars($program['title']); ?>"
                                    class="w-full h-full object-cover"
                                    onerror="this.src='https://images.unsplash.com/photo-1523050854058-8df90110c9f1?ixlib=rb-4.0.3&auto=format&fit=crop&w=500&q=80'">
                                <div class="absolute inset-0 bg-gradient-to-t from-black/50 to-transparent"></div>

                                <!-- Category Badge -->
                                <div class="program-category-badge">
                                    <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium <?php echo $badgeColor; ?>">
                                        <?php echo ucfirst($category); ?>
                                    </span>
                                </div>

                                <!-- Featured Badge -->
                                <?php if ($is_featured): ?>
                                    <div class="program-featured-badge">
                                        <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-accent text-white">
                                            <i class="fas fa-star mr-1 text-xs"></i> Featured
                                        </span>
                                    </div>
                                <?php endif; ?>
                            </div>
                        </div>

                        <!-- Program Content -->
                        <div class="program-content p-6">
                            <!-- Program Icon and Title -->
                            <div class="flex items-start mb-4">
                                <div class="flex-shrink-0 w-12 h-12 bg-accent/10 rounded-lg flex items-center justify-center mr-4">
                                    <i class="<?php echo $icon; ?> text-accent text-lg"></i>
                                </div>
                                <div>
                                    <h3 class="font-serif text-xl font-bold text-gray-800 mb-1">
                                        <?php echo htmlspecialchars($program['title']); ?>
                                    </h3>
                                    <p class="text-sm text-gray-500">
                                        <?php echo htmlspecialchars($program['duration']); ?> • <?php echo htmlspecialchars($program['age_group']); ?>
                                    </p>
                                </div>
                            </div>

                            <!-- Program Description -->
                            <div class="program-description text-gray-600 mb-4 text-sm leading-relaxed">
                                <?php
                                $description = $program['short_description'] ?? $program['description'];
                                if (strlen($description) > 120) {
                                    $description = substr($description, 0, 120) . '...';
                                }
                                echo htmlspecialchars($description);
                                ?>
                            </div>

                            <!-- Program Features in all programs -->
                            <div class="program-features">
                                <span class="program-feature-tag">
                                    <i class="fas fa-users mr-1"></i> Small Classes
                                </span>
                                <span class="program-feature-tag">
                                    <i class="fas fa-book mr-1"></i> CBSE Curriculum
                                </span>
                                <span class="program-feature-tag">
                                    <i class="fas fa-heart mr-1"></i> Holistic Approach
                                </span>
                            </div>

                            <!-- Program Details -->
                            <div class="space-y-2 text-sm text-gray-600 mb-6">
                                <div class="flex justify-between items-center py-2 border-b border-gray-100">
                                    <span class="flex items-center font-medium">
                                        <i class="fas fa-clock text-accent mr-2"></i> Duration:
                                    </span>
                                    <span class="font-semibold text-gray-800"><?php echo htmlspecialchars($program['duration']); ?></span>
                                </div>
                                <div class="flex justify-between items-center py-2 border-b border-gray-100">
                                    <span class="flex items-center font-medium">
                                        <i class="fas fa-users text-accent mr-2"></i> Age Group:
                                    </span>
                                    <span class="font-semibold text-gray-800"><?php echo htmlspecialchars($program['age_group']); ?></span>
                                </div>
                                <div class="flex justify-between items-center py-2">
                                    <span class="flex items-center font-medium">
                                        <i class="fas fa-rupee-sign text-accent mr-2"></i> Annual Fees:
                                    </span>
                                    <span class="font-semibold text-accent text-lg"><?php echo htmlspecialchars($program['fees']); ?></span>
                                </div>
                            </div>

                            <!-- Action Buttons -->
                            <div class="program-actions">
                                <a href="<?php echo $base_url; ?>/pages/admissions/apply.php?program=<?php echo urlencode($program['title']); ?>"
                                    class="btn-primary w-full text-center block">
                                    <i class="fas fa-info-circle mr-2"></i>Enquire Now
                                </a>
                                <a href="<?php echo $base_url; ?>/programs/index.php#program-<?php echo $program['id']; ?>"
                                    class="btn-outline w-full text-center block">
                                    <i class="fas fa-book-open mr-2"></i>Learn More
                                </a>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>

            <!-- View All Programs Button -->
            <div class="text-center mt-12">
                <a href="<?php echo $base_url; ?>/programs/index.php"
                    class="btn-secondary inline-flex items-center justify-center px-8 py-3">
                    View All Programs <i class="fas fa-arrow-right ml-2"></i>
                </a>
            </div>
        </div>
    </section>

    <!-- Campus Life Section -->
    <section class="section-padding bg-white">
        <div class="max-w-7xl mx-auto px-4">
            <div class="text-center mb-16">
                <div class="inline-flex items-center px-4 py-2 rounded-full bg-accent/10 text-accent font-semibold text-sm mb-4">
                    <i class="fas fa-school mr-2"></i> Campus Life
                </div>
                <h2 class="font-serif text-3xl md:text-4xl font-bold text-primary mb-4">
                    Life at <span class="text-accent">Bhaktivedanta Gurukul</span>
                </h2>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
                <div class="text-center">
                    <div class="relative overflow-hidden rounded-2xl mb-4">
                        <img src="<?php echo $base_url; ?>/images/daily-routine-1.jpeg" alt="Daily Routine" class="w-full h-48 object-cover">
                    </div>
                    <h4 class="font-semibold text-primary mb-2">Structured Daily Routine</h4>
                    <p class="text-secondary text-sm">Balanced schedule of studies, sports, and spiritual practices</p>
                </div>

                <div class="text-center">
                    <div class="relative overflow-hidden rounded-2xl mb-4">
                        <img src="<?php echo $base_url; ?>/images/festivals.jpeg" alt="Festivals" class="w-full h-48 object-cover">
                    </div>
                    <h4 class="font-semibold text-primary mb-2">Cultural Festivals</h4>
                    <p class="text-secondary text-sm">Celebration of traditional Indian festivals and values</p>
                </div>

                <div class="text-center">
                    <div class="relative overflow-hidden rounded-2xl mb-4">
                        <img src="<?php echo $base_url; ?>/images/Hawan-day.jpeg" alt="Hawan Ceremony Day" class="w-full h-48 object-cover">
                    </div>
                    <h4 class="font-semibold text-primary mb-2">Vidya Aarambh Sanskar</h4>
                    <p class="text-secondary text-sm">Celebration of the traditional fire ritual for purification and blessings</p>
                </div>

                <div class="text-center">
                    <div class="relative overflow-hidden rounded-2xl mb-4">
                        <img src="<?php echo $base_url; ?>/images/Lunch.jpeg" alt="Lunch Break" class="w-full h-48 object-cover">
                    </div>
                    <h4 class="font-semibold text-primary mb-2">Lunch & Fun</h4>
                    <p class="text-secondary text-sm">Engagement in social service and environmental activities</p>
                </div>
            </div>
        </div>
    </section>

    <!-- Testimonials Section -->
    <section class="section-padding bg-light">
        <div class="max-w-7xl mx-auto px-4">
            <div class="text-center mb-16">
                <div class="inline-flex items-center px-4 py-2 rounded-full bg-accent/10 text-accent font-semibold text-sm mb-4">
                    <i class="fas fa-heart mr-2"></i> Success Stories
                </div>
                <h2 class="font-serif text-3xl md:text-4xl font-bold text-primary mb-4">
                    What Parents <span class="text-accent">Say</span>
                </h2>
                <p class="text-xl text-secondary max-w-2xl mx-auto leading-relaxed">
                    Hear from our satisfied parents and students about their transformative Gurukul experience
                </p>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
                <div class="bg-white rounded-2xl shadow-lg p-6">
                    <div class="flex items-center mb-4">
                        <p class="w-12 h-12 bg-accent rounded-full flex items-center justify-center text-white font-bold mr-4">PK
                        </p>
                        <div>
                            <div class="font-semibold text-primary">Pragati Kesharwani </div>
                            <div class="text-sm text-secondary">Parent of Aayu Kesharwani</div>
                        </div>
                    </div>
                    <div class="flex text-yellow-400 mb-3">
                        <i class="fas fa-star"></i>
                        <i class="fas fa-star"></i>
                        <i class="fas fa-star"></i>
                        <i class="fas fa-star"></i>
                        <i class="fas fa-star"></i>
                    </div>
                    <p class="text-secondary italic">
                        "My son Aayu Kesharwani is a student of Class IV at Bhaktivedanta Gurukul
                        I have noticed many positive changes in my son .
                        He has become more confident and happy. He wants to learn more and more in this safe ,calm and spiritual environment
                        My gratitude to the Gurukul for creating such an academically spiritual environment for the holistic growth of my child."
                    </p>
                </div>

                <div class="bg-white rounded-2xl shadow-lg p-6">
                    <div class="flex items-center mb-4">
                        <p class="w-12 h-12 bg-accent rounded-full flex items-center justify-center text-white font-bold mr-4">MS
                        </p>
                        <div>
                            <div class="font-semibold text-primary">Meena Sahu </div>
                            <div class="text-sm text-secondary">Parent of Rudra Sahu</div>
                        </div>
                    </div>
                    <div class="flex text-yellow-400 mb-3">
                        <i class="fas fa-star"></i>
                        <i class="fas fa-star"></i>
                        <i class="fas fa-star"></i>
                        <i class="fas fa-star"></i>
                        <i class="fas fa-star"></i>
                    </div>
                    <p class="text-secondary italic">
                        "My son Rudra Sahu is a student of Class II at Bhaktivedanta Gurukul and I 've seen a remarkable transformation in my child since joining this school - from shy and hesitant to confident and expressive.

                        The perfect blend of academic, spiritual, creative and care brings out the best in every child!

                        We are thankful to the Gurukul for the efforts and it not just focuses on marks, but on values, discipline, emotional and spiritual growth.
                        "
                    </p>
                </div>

                <div class="bg-white rounded-2xl shadow-lg p-6">
                    <div class="flex items-center mb-4">
                        <div>
                            <p class="w-12 h-12 bg-accent rounded-full flex items-center justify-center text-white font-bold mr-4">RK
                            </p>
                        </div>
                        <div>
                            <div class="font-semibold text-primary">Ruchi Kesarwani.</div>
                            <div class="text-sm text-secondary">Parent of Vastvik Kesarwani</div>
                        </div>
                    </div>
                    <div class="flex text-yellow-400 mb-3">
                        <i class="fas fa-star"></i>
                        <i class="fas fa-star"></i>
                        <i class="fas fa-star"></i>
                        <i class="fas fa-star"></i>
                        <i class="fas fa-star"></i>
                    </div>
                    <p class="text-secondary italic">
                        "As a parent of Vastvik Kesarwani of Class VII, I am extremely pleased with the holistic development of my child. Beyond the commendable academic improvement in areas like Science, Mathematics, we deeply appreciate the strong emphasis on values, discipline, and spiritual education.
                        "
                    </p>
                </div>
            </div>
        </div>
    </section>

    <!-- CTA Section -->
    <section class="bg-gradient-to-br from-primary to-accent text-white section-padding">
        <div class="max-w-4xl mx-auto text-center px-4">
            <h2 class="font-serif text-3xl md:text-4xl font-bold mb-6">
                Begin Your Child's Spiritual & Academic Journey
            </h2>
            <p class="text-xl mb-8 opacity-90 leading-relaxed">
                Join our community dedicated to nurturing young minds with Vedic wisdom and modern education
            </p>
            <div class="flex flex-col sm:flex-row gap-4 justify-center">
                <a href="<?php echo $base_url; ?>/pages/admissions/index.php" class="bg-white text-primary hover:bg-gray-100 font-semibold py-4 px-8 rounded-lg transition-all duration-300 inline-flex items-center justify-center">
                    <i class="fas fa-user-graduate mr-3"></i> Admission Inquiry
                </a>
                <a href="<?php echo $base_url; ?>/contact.php" class="border-2 border-white text-white hover:bg-white hover:text-primary font-semibold py-4 px-8 rounded-lg transition-all duration-300 inline-flex items-center justify-center">
                    <i class="fas fa-calendar-alt mr-3"></i> Schedule Visit
                </a>
            </div>
            <p class="text-white/70 text-sm mt-8">
                Limited seats available for the upcoming academic session 2026-27
            </p>
        </div>
    </section>

    <!-- FAQ Section -->
    <section class="section-padding bg-white">
        <div class="max-w-4xl mx-auto px-4">
            <div class="text-center mb-16">
                <div class="inline-flex items-center px-4 py-2 rounded-full bg-accent/10 text-accent font-semibold text-sm mb-4">
                    <i class="fas fa-question-circle mr-2"></i> FAQs
                </div>
                <h2 class="font-serif text-3xl md:text-4xl font-bold text-primary mb-4">
                    Frequently Asked <span class="text-accent">Questions</span>
                </h2>
                <p class="text-xl text-secondary max-w-2xl mx-auto leading-relaxed">
                    Find answers to common questions about admissions, programs, and campus life at Bhaktivedanta Gurukul
                </p>
            </div>

            <div class="space-y-4">
                <!-- FAQ Item 1 -->
                <div class="faq-item bg-white rounded-2xl shadow-lg border border-gray-100 overflow-hidden">
                    <button class="faq-question w-full text-left p-6 flex justify-between items-center hover:bg-gray-50 transition-colors duration-200">
                        <span class="font-semibold text-lg text-primary">
                            What is the admission process at Bhaktivedanta Gurukul?
                        </span>
                        <i class="fas fa-chevron-down text-accent transition-transform duration-300"></i>
                    </button>
                    <div class="faq-answer overflow-hidden transition-all duration-300">
                        <div class="p-6 pt-0 border-t border-gray-100">
                            <p class="text-secondary leading-relaxed">
                                Our admission process involves these steps:
                            </p>
                            <ol class="list-decimal list-inside mt-3 space-y-2 text-secondary">
                                <li><strong>Inquiry Form:</strong> Submit the online inquiry form or visit campus</li>
                                <li><strong>Entrance Test:</strong> Age-appropriate assessment for academic level</li>
                                <li><strong>Parent Interview:</strong> Discussion about educational philosophy alignment</li>
                                <li><strong>Student Interaction:</strong> Informal meeting with faculty</li>
                                <li><strong>Documentation:</strong> Submission of required documents</li>
                                <li><strong>Fee Payment:</strong> Complete admission formalities</li>
                            </ol>
                            <p class="mt-4 text-sm text-gray-600">
                                <i class="fas fa-clock text-accent mr-1"></i> Complete process typically takes 7-10 working days
                            </p>
                        </div>
                    </div>
                </div>

                <!-- FAQ Item 2 -->
                <div class="faq-item bg-white rounded-2xl shadow-lg border border-gray-100 overflow-hidden">
                    <button class="faq-question w-full text-left p-6 flex justify-between items-center hover:bg-gray-50 transition-colors duration-200">
                        <span class="font-semibold text-lg text-primary">
                            How do you balance modern education with Vedic values?
                        </span>
                        <i class="fas fa-chevron-down text-accent transition-transform duration-300"></i>
                    </button>
                    <div class="faq-answer overflow-hidden transition-all duration-300">
                        <div class="p-6 pt-0 border-t border-gray-100">
                            <p class="text-secondary leading-relaxed">
                                We follow a unique integrated approach:
                            </p>
                            <div class="grid md:grid-cols-2 gap-4 mt-4">
                                <div class="space-y-2">
                                    <h4 class="font-semibold text-accent">Modern Education</h4>
                                    <ul class="list-disc list-inside text-sm text-secondary space-y-1">
                                        <li>CBSE curriculum with digital learning</li>
                                        <li>STEM labs and computer education</li>
                                        <li>Sports and co-curricular activities</li>
                                        <li>Career counseling and guidance</li>
                                    </ul>
                                </div>
                                <div class="space-y-2">
                                    <h4 class="font-semibold text-accent">Vedic Values</h4>
                                    <ul class="list-disc list-inside text-sm text-secondary space-y-1">
                                        <li>Daily yoga and meditation</li>
                                        <li>Vedic scriptures and philosophy</li>
                                        <li>Moral science and character building</li>
                                        <li>Cultural festivals and traditions</li>
                                    </ul>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- FAQ Item 3 -->
                <div class="faq-item bg-white rounded-2xl shadow-lg border border-gray-100 overflow-hidden">
                    <button class="faq-question w-full text-left p-6 flex justify-between items-center hover:bg-gray-50 transition-colors duration-200">
                        <span class="font-semibold text-lg text-primary">
                            What are the hostel facilities and accommodation like?
                        </span>
                        <i class="fas fa-chevron-down text-accent transition-transform duration-300"></i>
                    </button>
                    <div class="faq-answer overflow-hidden transition-all duration-300">
                        <div class="p-6 pt-0 border-t border-gray-100">
                            <div class="grid md:grid-cols-2 gap-6">
                                <div>
                                    <h4 class="font-semibold text-accent mb-3">Facilities Include:</h4>
                                    <ul class="space-y-2 text-secondary">
                                        <li class="flex items-center">
                                            <i class="fas fa-home text-accent mr-3 w-5"></i>
                                            Spacious, well-ventilated rooms
                                        </li>
                                        <li class="flex items-center">
                                            <i class="fas fa-utensils text-accent mr-3 w-5"></i>
                                            Nutritious vegetarian meals
                                        </li>
                                        <li class="flex items-center">
                                            <i class="fas fa-user-md text-accent mr-3 w-5"></i>
                                            24/7 medical facilities
                                        </li>
                                        <li class="flex items-center">
                                            <i class="fas fa-wifi text-accent mr-3 w-5"></i>
                                            Supervised internet access
                                        </li>
                                    </ul>
                                </div>
                                <div>
                                    <h4 class="font-semibold text-accent mb-3">Daily Schedule:</h4>
                                    <ul class="space-y-2 text-secondary text-sm">
                                        <li>5:30 AM - Wake up & meditation</li>
                                        <li>7:00 AM - Breakfast</li>
                                        <li>8:00 AM - Academic classes</li>
                                        <li>3:00 PM - Sports & activities</li>
                                        <li>7:00 PM - Study hours</li>
                                        <li>9:30 PM - Lights out</li>
                                    </ul>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- FAQ Item 4 -->
                <div class="faq-item bg-white rounded-2xl shadow-lg border border-gray-100 overflow-hidden">
                    <button class="faq-question w-full text-left p-6 flex justify-between items-center hover:bg-gray-50 transition-colors duration-200">
                        <span class="font-semibold text-lg text-primary">
                            Are scholarships available for deserving students?
                        </span>
                        <i class="fas fa-chevron-down text-accent transition-transform duration-300"></i>
                    </button>
                    <div class="faq-answer overflow-hidden transition-all duration-300">
                        <div class="p-6 pt-0 border-t border-gray-100">
                            <p class="text-secondary leading-relaxed mb-4">
                                Yes, we offer various scholarship programs to support meritorious and deserving students:
                            </p>
                            <div class="space-y-3">
                                <div class="flex items-start">
                                    <i class="fas fa-award text-accent mt-1 mr-3"></i>
                                    <div>
                                        <strong class="text-primary">Merit Scholarship:</strong>
                                        <p class="text-secondary text-sm">For students with outstanding academic performance</p>
                                    </div>
                                </div>
                                <div class="flex items-start">
                                    <i class="fas fa-hands-helping text-accent mt-1 mr-3"></i>
                                    <div>
                                        <strong class="text-primary">Need-based Scholarship:</strong>
                                        <p class="text-secondary text-sm">For economically weaker sections</p>
                                    </div>
                                </div>
                                <div class="flex items-start">
                                    <i class="fas fa-music text-accent mt-1 mr-3"></i>
                                    <div>
                                        <strong class="text-primary">Talent Scholarship:</strong>
                                        <p class="text-secondary text-sm">For excellence in sports, arts, or cultural activities</p>
                                    </div>
                                </div>
                            </div>
                            <div class="mt-4 p-4 bg-accent/10 rounded-lg">
                                <p class="text-sm text-secondary">
                                    <i class="fas fa-info-circle text-accent mr-2"></i>
                                    Scholarship applications are accepted during admission period. Contact our admission office for details.
                                </p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- FAQ Item 5 -->
                <div class="faq-item bg-white rounded-2xl shadow-lg border border-gray-100 overflow-hidden">
                    <button class="faq-question w-full text-left p-6 flex justify-between items-center hover:bg-gray-50 transition-colors duration-200">
                        <span class="font-semibold text-lg text-primary">
                            What safety and security measures are in place?
                        </span>
                        <i class="fas fa-chevron-down text-accent transition-transform duration-300"></i>
                    </button>
                    <div class="faq-answer overflow-hidden transition-all duration-300">
                        <div class="p-6 pt-0 border-t border-gray-100">
                            <div class="grid md:grid-cols-2 gap-6">
                                <div>
                                    <h4 class="font-semibold text-accent mb-3">Security Measures:</h4>
                                    <ul class="space-y-2 text-secondary">
                                        <li class="flex items-center">
                                            <i class="fas fa-shield-alt text-accent mr-3 w-5"></i>
                                            24/7 CCTV surveillance
                                        </li>
                                        <li class="flex items-center">
                                            <i class="fas fa-user-shield text-accent mr-3 w-5"></i>
                                            Trained security personnel
                                        </li>
                                        <li class="flex items-center">
                                            <i class="fas fa-fingerprint text-accent mr-3 w-5"></i>
                                            Biometric attendance system
                                        </li>
                                        <li class="flex items-center">
                                            <i class="fas fa-map-marker-alt text-accent mr-3 w-5"></i>
                                            GPS-enabled transport
                                        </li>
                                    </ul>
                                </div>
                                <div>
                                    <h4 class="font-semibold text-accent mb-3">Health & Safety:</h4>
                                    <ul class="space-y-2 text-secondary">
                                        <li class="flex items-center">
                                            <i class="fas fa-first-aid text-accent mr-3 w-5"></i>
                                            On-campus medical room
                                        </li>
                                        <li class="flex items-center">
                                            <i class="fas fa-hand-sparkles text-accent mr-3 w-5"></i>
                                            Regular health check-ups
                                        </li>
                                        <li class="flex items-center">
                                            <i class="fas fa-fire-extinguisher text-accent mr-3 w-5"></i>
                                            Fire safety compliance
                                        </li>
                                        <li class="flex items-center">
                                            <i class="fas fa-phone-alt text-accent mr-3 w-5"></i>
                                            Emergency response system
                                        </li>
                                    </ul>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- FAQ Item 6 -->
                <div class="faq-item bg-white rounded-2xl shadow-lg border border-gray-100 overflow-hidden">
                    <button class="faq-question w-full text-left p-6 flex justify-between items-center hover:bg-gray-50 transition-colors duration-200">
                        <span class="font-semibold text-lg text-primary">
                            What co-curricular activities are available?
                        </span>
                        <i class="fas fa-chevron-down text-accent transition-transform duration-300"></i>
                    </button>
                    <div class="faq-answer overflow-hidden transition-all duration-300">
                        <div class="p-6 pt-0 border-t border-gray-100">
                            <div class="grid md:grid-cols-3 gap-4">
                                <div class="text-center">
                                    <div class="w-12 h-12 bg-accent/10 rounded-full flex items-center justify-center mx-auto mb-2">
                                        <i class="fas fa-music text-accent"></i>
                                    </div>
                                    <h5 class="font-semibold text-primary">Arts & Culture</h5>
                                    <p class="text-secondary text-sm">Music, Dance, Drama, Painting</p>
                                </div>
                                <div class="text-center">
                                    <div class="w-12 h-12 bg-accent/10 rounded-full flex items-center justify-center mx-auto mb-2">
                                        <i class="fas fa-running text-accent"></i>
                                    </div>
                                    <h5 class="font-semibold text-primary">Sports</h5>
                                    <p class="text-secondary text-sm">Cricket, Basketball, Yoga, Athletics</p>
                                </div>
                                <div class="text-center">
                                    <div class="w-12 h-12 bg-accent/10 rounded-full flex items-center justify-center mx-auto mb-2">
                                        <i class="fas fa-robot text-accent"></i>
                                    </div>
                                    <h5 class="font-semibold text-primary">Clubs</h5>
                                    <p class="text-secondary text-sm">Robotics, Debate, Eco, Science</p>
                                </div>
                            </div>
                            <div class="mt-4 text-center">
                                <p class="text-secondary text-sm">
                                    <i class="fas fa-calendar-alt text-accent mr-1"></i>
                                    Regular inter-school competitions and annual cultural fest
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Still Have Questions -->
            <div class="text-center mt-12 p-8 bg-gradient-to-r from-accent/5 to-primary/5 rounded-2xl">
                <h3 class="font-serif text-2xl font-bold text-primary mb-4">
                    Still have questions?
                </h3>
                <p class="text-secondary mb-6 max-w-md mx-auto">
                    Our admission team is here to help you with any questions about our programs and admission process.
                </p>
                <div class="flex flex-col sm:flex-row gap-4 justify-center">
                    <a href="<?php echo $base_url; ?>/contact.php"
                        class="btn-primary inline-flex items-center justify-center">
                        <i class="fa-regular fa-address-card text-white mr-2"></i> Contact Us
                    </a>
                    <a href="tel:+917618040040"
                        class="btn-secondary inline-flex items-center justify-center">
                        <i class="fas fa-phone mr-2"></i> Call Now
                    </a>
                </div>
            </div>
        </div>
    </section>





    <?php include 'includes/footer.php'; ?>

    <!-- Swiper JS -->
    <script src="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.js"></script>

    <script src="<?php echo $base_url; ?>/js/index.js"></script>
</body>

</html>