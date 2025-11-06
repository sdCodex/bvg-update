<?php
// Error reporting on karein
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Check header file
$header_file = './includes/header.php';
if(file_exists($header_file)) {
    include $header_file;
} else {
    // Fallback header
    echo '<!DOCTYPE html>
    <html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>About - Bhaktivedanta Gurukul</title>
        <!-- Tailwind CSS -->
        <script src="https://cdn.tailwindcss.com"></script>
        <!-- Font Awesome -->
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
        <!-- Google Fonts -->
        <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@400;500;600;700&family=Inter:wght@300;400;500;600&display=swap" rel="stylesheet">
        <style>
            .font-serif { font-family: \'Playfair Display\', serif; }
            .bg-primary { background-color: #1e3a5f; }
            .bg-accent { background-color: #dc2626; }
            .text-primary { color: #1e3a5f; }
            .text-accent { color: #dc2626; }
            .bg-light { background-color: #f8fafc; }
            .section-padding { padding: 80px 0; }
            @media (max-width: 768px) {
                .section-padding { padding: 60px 0; }
            }
            
            /* Custom Animations */
            .fade-in-up {
                opacity: 0;
                transform: translateY(30px);
                transition: all 0.6s ease;
            }
            
            .fade-in-up.visible {
                opacity: 1;
                transform: translateY(0);
            }
            
            .hover-lift {
                transition: transform 0.3s ease, box-shadow 0.3s ease;
            }
            
            .hover-lift:hover {
                transform: translateY(-5px);
                box-shadow: 0 20px 40px rgba(0,0,0,0.1);
            }
        </style>
    </head>
    <body class="font-sans">';
}
?>

<!-- Hero Section -->
<section class="relative min-h-[80vh] flex items-center justify-center overflow-hidden bg-gradient-to-br from-primary via-primary to-accent">
    <!-- Animated Background -->
    <div class="absolute inset-0 opacity-10">
        <div class="absolute top-1/4 left-1/4 w-32 h-32 bg-white rounded-full animate-pulse"></div>
        <div class="absolute bottom-1/3 right-1/3 w-24 h-24 bg-white rounded-full animate-pulse" style="animation-delay: 1s;"></div>
        <div class="absolute top-1/3 right-1/4 w-20 h-20 bg-white rounded-full animate-pulse" style="animation-delay: 2s;"></div>
    </div>
    
    <!-- Decorative Elements -->
    <div class="absolute top-0 left-0 w-full h-16 bg-gradient-to-b from-white/10 to-transparent"></div>
    
    <div class="max-w-7xl mx-auto px-4 text-center relative z-10">
        <!-- Breadcrumb -->
        <div class="flex justify-center mb-8">
            <nav class="flex items-center space-x-2 text-white/80 text-sm">
                <a href="<?php echo $base_url; ?>/index.php" class="hover:text-white transition-colors">Home</a>
                <span class="text-white/60">/</span>
                <span class="text-white font-medium">About Us</span>
            </nav>
        </div>
        
        <h1 class="font-serif text-4xl md:text-5xl lg:text-6xl font-bold text-white mb-6 leading-tight">
            About <span class="text-yellow-300">Bhaktivedanta</span><br>
            <span class="text-3xl md:text-4xl lg:text-5xl">Gurukul & School</span>
        </h1>
        
        <p class="text-xl md:text-2xl text-gray-200 max-w-3xl mx-auto mb-8 leading-relaxed">
            Where Ancient Vedic Wisdom Meets Modern Education for Holistic Excellence
        </p>
        
        <div class="flex flex-col sm:flex-row gap-4 justify-center items-center mt-12">
            <a href="#mission" class="bg-white text-primary hover:bg-gray-100 font-semibold py-4 px-8 rounded-lg transition-all duration-300 transform hover:scale-105 inline-flex items-center shadow-lg">
                <i class="fas fa-bullseye mr-3"></i> Our Mission
            </a>
            <a href="#campus" class="border-2 border-white text-white hover:bg-white hover:text-primary font-semibold py-4 px-8 rounded-lg transition-all duration-300 transform hover:scale-105 inline-flex items-center">
                <i class="fas fa-school mr-3"></i> Virtual Tour
            </a>
        </div>
        
        <!-- Scroll Indicator -->
        <div class="absolute bottom-8 left-1/2 transform -translate-x-1/2 animate-bounce">
            <a href="#mission" class="text-white/80 hover:text-white transition-colors">
                <i class="fas fa-chevron-down text-2xl"></i>
            </a>
        </div>
    </div>
</section>

<!-- Quick Stats -->
<section class="bg-white py-12 border-b border-gray-200">
    <div class="max-w-7xl mx-auto px-4">
        <div class="grid grid-cols-2 md:grid-cols-4 gap-8">
            <div class="text-center fade-in-up">
                <div class="text-3xl md:text-4xl font-bold text-accent mb-2">50+</div>
                <div class="text-secondary font-medium">Students Enrolled</div>
            </div>
            <div class="text-center fade-in-up">
                <div class="text-3xl md:text-4xl font-bold text-accent mb-2">20+</div>
                <div class="text-secondary font-medium">Expert Faculty</div>
            </div>
            <div class="text-center fade-in-up">
                <div class="text-3xl md:text-4xl font-bold text-accent mb-2">5+</div>
                <div class="text-secondary font-medium">Years Experience</div>
            </div>
            <div class="text-center fade-in-up">
                <div class="text-3xl md:text-4xl font-bold text-accent mb-2">100%</div>
                <div class="text-secondary font-medium">Values Based</div>
            </div>
        </div>
    </div>
</section>

<!-- Mission & Vision Section -->
<section id="mission" class="section-padding bg-white">
    <div class="max-w-7xl mx-auto px-4">
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-12 items-start">
            <!-- Mission & Vision Content -->
            <div class="space-y-8">
                <div class="fade-in-up">
                    <span class="inline-flex items-center px-4 py-2 mt-5 rounded-full bg-accent/10 text-accent font-semibold text-sm mb-4">
                        <i class="fas fa-star mr-2"></i> Our Philosophy
                    </span>
                    <h2 class="font-serif text-3xl md:text-4xl font-bold text-primary mb-6">
                        Mission & <span class="text-accent">Vision</span>
                    </h2>
                </div>

                <!-- Mission Card -->
                <div class="fade-in-up hover-lift bg-gradient-to-br from-blue-50 to-indigo-50 rounded-2xl p-6 border-l-4 border-accent">
                    <div class="flex items-start space-x-4">
                        <div class="w-12 h-12 bg-accent rounded-xl flex items-center justify-center flex-shrink-0">
                            <i class="fas fa-bullseye text-white text-lg"></i>
                        </div>
                        <div>
                            <h3 class="font-serif text-xl font-bold text-primary mb-3">Our Mission</h3>
                            <p class="text-gray-700 leading-relaxed">
                                To provide value-based education that combines academic excellence with spiritual wisdom, 
                                nurturing students into responsible global citizens with strong character and moral values. 
                                We create an environment where modern education and ancient Vedic wisdom coexist harmoniously.
                            </p>
                        </div>
                    </div>
                </div>

                <!-- Vision Card -->
                <div class="fade-in-up hover-lift bg-gradient-to-br from-orange-50 to-red-50 rounded-2xl p-6 border-l-4 border-primary">
                    <div class="flex items-start space-x-4">
                        <div class="w-12 h-12 bg-primary rounded-xl flex items-center justify-center flex-shrink-0">
                            <i class="fas fa-eye text-white text-lg"></i>
                        </div>
                        <div>
                            <h3 class="font-serif text-xl font-bold text-primary mb-3">Our Vision</h3>
                            <p class="text-gray-700 leading-relaxed">
                                To revive the ancient Gurukul system in a modern context, creating leaders who contribute 
                                positively to society while maintaining their cultural and spiritual roots. We envision 
                                a world where education empowers individuals to achieve both material success and spiritual fulfillment.
                            </p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Core Values -->
            <div class="fade-in-up mt-5">
                <div class="bg-gradient-to-br from-primary to-accent rounded-2xl p-8 text-white relative overflow-hidden shadow-xl">
                    <!-- Background Pattern -->
                    <div class="absolute inset-0 opacity-10">
                        <div class="absolute top-4 left-4 w-12 h-12 bg-white rounded-full"></div>
                        <div class="absolute bottom-4 right-4 w-16 h-16 bg-white rounded-full"></div>
                        <div class="absolute top-1/2 left-1/2 transform -translate-x-1/2 -translate-y-1/2 w-24 h-24 bg-white rounded-full"></div>
                    </div>
                    
                    <h3 class="font-serif text-2xl font-bold mb-8 text-center relative z-10">Our Core Values</h3>
                    <div class="space-y-6 relative z-10">
                        <?php
                        $core_values = [
                            ['fas fa-hands-helping', 'Service & Compassion', 'Developing empathy and willingness to serve society selflessly'],
                            ['fas fa-brain', 'Wisdom & Knowledge', 'Pursuing both worldly knowledge and spiritual wisdom in harmony'],
                            ['fas fa-shield-alt', 'Integrity & Character', 'Building strong moral character and unwavering ethical values'],
                            ['fas fa-globe-asia', 'Cultural Preservation', 'Honoring and preserving Vedic culture and timeless traditions'],
                            ['fas fa-users', 'Community & Unity', 'Fostering a sense of belonging and collective growth'],
                            ['fas fa-heart', 'Love & Respect', 'Cultivating unconditional love and respect for all beings']
                        ];
                        
                        foreach ($core_values as $value):
                        ?>
                        <div class="flex items-start space-x-4 group hover:bg-white/10 p-3 rounded-xl transition-all duration-300">
                            <div class="w-12 h-12 bg-white/20 rounded-xl flex items-center justify-center flex-shrink-0 mt-1 group-hover:bg-white group-hover:text-accent transition-all duration-300">
                                <i class="<?php echo $value[0]; ?> text-white text-lg group-hover:text-accent"></i>
                            </div>
                            <div>
                                <h4 class="font-semibold text-lg mb-2"><?php echo $value[1]; ?></h4>
                                <p class="text-white/80 text-sm"><?php echo $value[2]; ?></p>
                            </div>
                        </div>
                        <?php endforeach; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- History Timeline -->
<section class="section-padding bg-light mt-5">
    <div class="max-w-7xl mx-auto px-4">
        <div class="text-center mb-16 fade-in-up">
            <span class="inline-flex items-center px-4 py-2 mt-4 rounded-full bg-accent/10 text-accent font-semibold text-sm mb-4">
                <i class="fas fa-history mr-2"></i> Our Journey
            </span>
            <h2 class="font-serif text-3xl md:text-4xl font-bold text-primary mb-4">
                Our <span class="text-accent">History</span>
            </h2>
            <p class="text-xl text-gray-600 max-w-2xl mx-auto">
                A legacy of spiritual and educational excellence spanning decades
            </p>
        </div>

        <!-- Timeline -->
        <div class="max-w-4xl mx-auto">
            <div class="relative">
                <!-- Timeline Line - Hidden on mobile -->
                <div class="hidden md:block absolute left-1/2 transform -translate-x-1/2 w-1 bg-accent/20 h-full"></div>
                
                <?php
                $timeline_events = [
                    [
                        'year' => '2021',
                        'title' => 'Conceived',
                        'description' => 'On the 125th Appearance (Birth) Anniversary of Srila Prabhupada, a dire need of authentic Vedic-value-based chain of schools was thought of.',
                        'icon' => 'fas fa-seedling',
                        'side' => 'left'
                    ],
                    [
                        'year' => '2025',
                        'title' => 'Execution Started',
                        'description' => 'To impart Prabhupada\'s teachings and Vedic sanskaras along with academic excellence, planning was started by the IITians ISKCON devotees.',
                        'icon' => 'fas fa-graduation-cap',
                        'side' => 'right'
                    ],
                    [
                        'year' => '2025',
                        'title' => 'Established',
                        'description' => 'As Prayagraj is the divine land of the first creation of the universe by Sri Mahamabi Bhardwaj, so just after Mahakumbh 2025, Prayagraj thought to be the 1st centre of Bhaktivedanta Gurukul.',
                        'icon' => 'fas fa-building',
                        'side' => 'left'
                    ],
                    [
                        'year' => '2026',
                        'title' => 'GURUKUL FORTUNATE 50',
                        'description' => 'We\'re going to launch a nationwide scholarship exam for select students, providing free education & free accommodation for 50–50 students of classes 6, 7, 8.',
                        'icon' => 'fas fa-award',
                        'side' => 'right'
                    ],
                    [
                        'year' => '2030',
                        'title' => 'Upcoming 5-Year Plan',
                        'description' => 'Implemented digital learning platforms and smart classrooms',
                        'icon' => 'fas fa-laptop',
                        'side' => 'left'
                    ],
                    [
                        'year' => '2050',
                        'title' => 'Upcoming 25-Year Plan',
                        'description' => 'To have presence of Bhaktivedanta Gurukul in every state of Bharat.',
                        'icon' => 'fas fa-trophy',
                        'side' => 'right'
                    ],
                    [
                        'year' => '2096',
                        'title' => 'AIM Offering to Srila Prabhupada on his 200th Birth Anniversary (Vyasa Puja)',
                        'description' => 'To have a branch of Bhaktivedanta Gurukul in every district of Bharat (India).',
                        'icon' => 'fas fa-trophy',
                        'side' => 'left'
                    ]
                ];
                
                foreach ($timeline_events as $index => $event):
                ?>
                <!-- Timeline Item -->
                <div class="relative mb-8 fade-in-up">
                    <!-- Desktop Layout -->
                    <div class="hidden md:flex items-center w-full pb-5 <?php echo $event['side'] === 'left' ? 'flex-row' : 'flex-row-reverse'; ?>">
                        <div class="w-1/2 <?php echo $event['side'] === 'left' ? 'pr-8 text-right' : 'pl-8'; ?>">
                            <div class="bg-white rounded-2xl shadow-lg p-6 border border-gray-100 hover-lift">
                                <div class="w-12 h-12 bg-accent rounded-full flex items-center justify-center mb-4 mx-<?php echo $event['side'] === 'left' ? 'auto ml-0' : 'auto mr-0'; ?>">
                                    <i class="<?php echo $event['icon']; ?> text-white"></i>
                                </div>
                                <div class="text-2xl font-bold text-accent mb-2"><?php echo $event['year']; ?></div>
                                <h3 class="font-serif text-xl font-bold text-primary mb-2"><?php echo $event['title']; ?></h3>
                                <p class="text-gray-600"><?php echo $event['description']; ?></p>
                            </div>
                        </div>
                        <div class="w-8 h-8 bg-accent rounded-full border-4 border-white shadow-lg z-10"></div>
                        <div class="w-1/2"></div>
                    </div>
                    
                    <!-- Mobile Layout - Simple and Clean -->
                    <div class="md:hidden w-full">
                        <div class="flex items-start">
                            <!-- Timeline Dot and Line -->
                            <div class="flex flex-col items-center w-8 mr-4 flex-shrink-0">
                                <div class="w-6 h-6 bg-accent rounded-full border-2 border-white shadow-md z-10"></div>
                                <?php if($index < count($timeline_events) - 1): ?>
                                <div class="w-1 bg-accent/20 flex-grow mt-2"></div>
                                <?php endif; ?>
                            </div>
                            
                            <!-- Content -->
                            <div class="flex-1 bg-white rounded-xl shadow-md p-4 border border-gray-100 mb-4">
                                <div class="flex items-start mb-3">
                                    <div class="w-8 h-8 bg-accent rounded-full flex items-center justify-center mr-3 flex-shrink-0 mt-1">
                                        <i class="<?php echo $event['icon']; ?> text-white text-xs"></i>
                                    </div>
                                    <div class="flex-1">
                                        <div class="text-lg font-bold text-accent mb-1"><?php echo $event['year']; ?></div>
                                        <h3 class="font-serif text-base font-bold text-primary mb-2"><?php echo $event['title']; ?></h3>
                                        <p class="text-gray-600 text-sm leading-relaxed"><?php echo $event['description']; ?></p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
        </div>
    </div>
</section>

<!-- Faculty Section -->
<section class="section-padding bg-white">
    <div class="max-w-7xl mx-auto px-4">
        <div class="text-center mb-16 fade-in-up">
            <span class="inline-flex items-center px-4 py-2 rounded-full bg-accent/10 text-accent font-semibold text-sm mb-4">
                <i class="fas fa-users mr-2"></i> Meet Our Team
            </span>
            <h2 class="font-serif text-3xl md:text-4xl font-bold text-primary mb-4">
                Our <span class="text-accent">Faculty</span>
            </h2>
            <p class="text-xl text-gray-600 max-w-2xl mx-auto">
                Dedicated educators committed to holistic development and academic excellence
            </p>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
            <?php
            $faculty_members = [
                [
                    'name' => 'Dr. Rajesh Sharma',
                    'position' => 'Principal & Academic Head',
                    'qualification' => 'Ph.D. in Education, M.A. Sanskrit',
                    'experience' => '25+ years in education',
                    'specialization' => 'Educational Leadership & Vedic Studies',
                    'icon' => 'fas fa-user-tie',
                    'image' => 'https://images.unsplash.com/photo-1560250097-0b93528c311a?ixlib=rb-4.0.3&auto=format&fit=crop&w=400&q=80'
                ],
                [
                    'name' => 'Shri Gopal Das',
                    'position' => 'Head of Spiritual Education',
                    'qualification' => 'M.A. Philosophy, Bhakti Shastri',
                    'experience' => '20+ years in spiritual teaching',
                    'specialization' => 'Vedic Philosophy & Meditation',
                    'icon' => 'fas fa-om',
                    'image' => 'https://images.unsplash.com/photo-1587132137056-d4d5e19e9209?ixlib=rb-4.0.3&auto=format&fit=crop&w=400&q=80'
                ],
                [
                    'name' => 'Mrs. Anjali Patel',
                    'position' => 'Science Department Head',
                    'qualification' => 'M.Sc. Physics, B.Ed.',
                    'experience' => '15+ years teaching experience',
                    'specialization' => 'Physics & Environmental Science',
                    'icon' => 'fas fa-atom',
                    'image' => 'https://images.unsplash.com/photo-1580894894513-541e068a3e2b?ixlib=rb-4.0.3&auto=format&fit=crop&w=400&q=80'
                ]
            ];
            
            foreach ($faculty_members as $index => $faculty):
            ?>
            <div class="fade-in-up hover-lift bg-white rounded-2xl shadow-lg p-6 text-center border border-gray-100 group h-full flex flex-col">
                <div class="relative mb-6">
                    <div class="w-32 h-32 rounded-full mx-auto overflow-hidden border-4 border-white shadow-lg group-hover:border-accent transition-colors duration-300">
                        <img src="<?php echo $faculty['image']; ?>" alt="<?php echo $faculty['name']; ?>" 
                             class="w-full h-full object-cover group-hover:scale-110 transition-transform duration-300">
                    </div>
                    <div class="absolute bottom-0 right-1/2 translate-x-12 w-10 h-10 bg-gradient-to-br from-accent to-red-700 rounded-full flex items-center justify-center border-2 border-white shadow-lg">
                        <i class="<?php echo $faculty['icon']; ?> text-white text-sm"></i>
                    </div>
                </div>
                
                <h3 class="font-serif text-xl font-bold text-gray-800 mb-2 group-hover:text-accent transition-colors duration-300">
                    <?php echo $faculty['name']; ?>
                </h3>
                <p class="text-accent font-semibold mb-4"><?php echo $faculty['position']; ?></p>
                
                <div class="space-y-2 text-sm text-gray-600 flex-grow mb-4">
                    <div class="flex items-center justify-center">
                        <i class="fas fa-graduation-cap text-accent mr-2"></i>
                        <span><?php echo $faculty['qualification']; ?></span>
                    </div>
                    <div class="flex items-center justify-center">
                        <i class="fas fa-clock text-accent mr-2"></i>
                        <span><?php echo $faculty['experience']; ?></span>
                    </div>
                    <div class="flex items-center justify-center">
                        <i class="fas fa-star text-accent mr-2"></i>
                        <span class="text-center"><?php echo $faculty['specialization']; ?></span>
                    </div>
                </div>
                
                <div class="pt-4 border-t border-gray-100">
                    <button class="text-primary hover:text-accent text-sm font-medium transition-colors inline-flex items-center">
                        <i class="fas fa-envelope mr-2"></i> Contact Faculty
                    </button>
                </div>
            </div>
            <?php endforeach; ?>
        </div>

        <!-- Faculty CTA -->
        <div class="text-center mt-12 fade-in-up">
            <div class="bg-gradient-to-r from-primary/5 to-accent/5 rounded-2xl p-8 border border-gray-200">
                <h3 class="font-serif text-2xl font-bold text-primary mb-4">Join Our Esteemed Faculty</h3>
                <p class="text-gray-600 mb-6 max-w-2xl mx-auto">
                    We're always looking for passionate educators who share our vision for holistic education. 
                    If you're dedicated to nurturing young minds with both academic excellence and spiritual values, 
                    we'd love to hear from you.
                </p>
                <a href="<?php echo $base_url; ?>/pages/career/index.php" class="bg-accent hover:bg-red-700 text-white font-semibold py-3 px-8 rounded-lg transition-all duration-300 transform hover:scale-105 inline-flex items-center shadow-lg">
                    <i class="fas fa-user-plus mr-3"></i> Explore Teaching Opportunities
                </a>
            </div>
        </div>
    </div>
</section>

<!-- Campus Section -->
<section id="campus" class="section-padding bg-light">
    <div class="max-w-7xl mx-auto px-4 mt-4">
        <div class="text-center mb-16 fade-in-up">
            <span class="inline-flex items-center px-4 py-2 rounded-full bg-accent/10 text-accent font-semibold text-sm mb-4">
                <i class="fas fa-school mr-2"></i> Our Campus
            </span>
            <h2 class="font-serif text-3xl md:text-4xl font-bold text-primary mb-4">
                Campus <span class="text-accent">Tour</span>
            </h2>
            <p class="text-xl text-gray-600 max-w-2xl mx-auto">
                Experience our serene and inspiring learning environment designed for holistic growth
            </p>
        </div>

        <!-- Campus Gallery -->
        <div class="mb-12 fade-in-up">
            <div class="grid grid-cols-2 md:grid-cols-3 gap-4">
                <div class="col-span-2 row-span-2 rounded-2xl overflow-hidden shadow-lg">
                    <img src="https://images.unsplash.com/photo-1562774053-701939374585?ixlib=rb-4.0.3&auto=format&fit=crop&w=1000&q=80" 
                         alt="Gurukul Main Building" 
                         class="w-full h-full object-cover hover:scale-105 transition-transform duration-500">
                </div>
                <div class="rounded-2xl overflow-hidden shadow-lg">
                    <img src="<?php echo $base_url; ?>/images/icons/about-page1.jpeg" 
                         alt="Modern Classroom" 
                         class="w-full h-42 object-cover hover:scale-105 transition-transform duration-500">
                </div>
                <div class="rounded-2xl overflow-hidden shadow-lg">
                    <img src="<?php echo $base_url; ?>/images/icons/class1.jpeg" 
                         alt="School Library" 
                         class="w-full h-42 object-cover hover:scale-105 transition-transform duration-500">
                </div>
                <div class="rounded-2xl overflow-hidden shadow-lg">
                    <img src="<?php echo $base_url; ?>/images/icons/class2.jpeg" 
                         alt="Campus Temple" 
                         class="w-full h-48 object-cover hover:scale-105 transition-transform duration-500">
                </div>
                <div class="rounded-2xl overflow-hidden shadow-lg">
                    <img src="<?php echo $base_url; ?>/images/icons/class3.jpeg" 
                         alt="Sports Ground" 
                         class="w-full h-48 object-cover hover:scale-105 transition-transform duration-500">
                </div>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
            <!-- Facilities -->
            <div class="fade-in-up">
                <div class="bg-white rounded-2xl shadow-lg p-6 h-full hover-lift">
                    <h3 class="font-serif text-2xl font-bold text-primary mb-6 flex items-center">
                        <i class="fas fa-building text-accent mr-3"></i> Campus Facilities
                    </h3>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <?php
                        $facilities = [
                            ['fas fa-school', 'Spacious Classrooms', 'green'],
                            ['fas fa-flask', 'Science & Computer Labs', 'blue'],
                            ['fas fa-book', 'Extensive Library', 'purple'],
                            ['fas fa-meditation', 'Meditation Hall', 'orange'],
                            ['fas fa-running', 'Sports Ground', 'red'],
                            ['fas fa-home', 'Hostel Facilities', 'indigo'],
                            ['fas fa-seedling', 'Organic Garden', 'teal'],
                            ['fas fa-heartbeat', 'Health Center', 'pink']
                        ];
                        
                        foreach ($facilities as $facility):
                        ?>
                        <div class="flex items-center space-x-3 p-3 rounded-lg hover:bg-gray-50 transition-colors group">
                            <div class="w-10 h-10 bg-<?php echo $facility[2]; ?>-100 rounded-lg flex items-center justify-center group-hover:scale-110 transition-transform">
                                <i class="<?php echo $facility[0]; ?> text-<?php echo $facility[2]; ?>-600"></i>
                            </div>
                            <span class="text-gray-700 font-medium"><?php echo $facility[1]; ?></span>
                        </div>
                        <?php endforeach; ?>
                    </div>
                </div>
            </div>

            <!-- Daily Schedule -->
            <div class="fade-in-up">
                <div class="bg-white rounded-2xl shadow-lg p-6 h-full hover-lift">
                    <h3 class="font-serif text-2xl font-bold text-primary mb-6 flex items-center">
                        <i class="fas fa-clock text-accent mr-3"></i> Daily Schedule
                    </h3>
                    <div class="space-y-4">
                        <?php
                        $schedule = [
                            ['4:30 AM', 'Morning Prayers & Meditation', 'fas fa-pray'],
                            ['6:00 AM', 'Yoga & Physical Exercise', 'fas fa-spa'],
                            ['8:00 AM', 'Academic Classes Begin', 'fas fa-graduation-cap'],
                            ['12:30 PM', 'Lunch & Rest Period', 'fas fa-utensils'],
                            ['2:00 PM', 'Practical & Creative Sessions', 'fas fa-paint-brush'],
                            ['4:30 PM', 'Sports & Extracurricular', 'fas fa-running'],
                            ['6:30 PM', 'Evening Prayers & Culture', 'fas fa-music'],
                            ['8:00 PM', 'Self Study & Reflection', 'fas fa-book-reader']
                        ];
                        
                        foreach ($schedule as $item):
                        ?>
                        <div class="flex items-center justify-between p-3 rounded-lg hover:bg-gray-50 transition-colors group">
                            <div class="flex items-center space-x-3">
                                <div class="w-8 h-8 bg-accent/10 rounded-lg flex items-center justify-center group-hover:bg-accent group-hover:text-white transition-colors">
                                    <i class="<?php echo $item[2]; ?> text-accent text-sm group-hover:text-white"></i>
                                </div>
                                <span class="text-gray-700 font-medium"><?php echo $item[1]; ?></span>
                            </div>
                            <span class="text-primary font-semibold bg-gray-100 px-3 py-1 rounded-full text-sm group-hover:bg-accent group-hover:text-white transition-colors">
                                <?php echo $item[0]; ?>
                            </span>
                        </div>
                        <?php endforeach; ?>
                    </div>
                </div>
            </div>
        </div>

        <!-- CTA Section -->
        <div class="text-center mt-12 fade-in-up pb-8">
            <div class="bg-gradient-to-r from-primary to-accent rounded-2xl p-8 text-white relative overflow-hidden shadow-xl">
                <!-- Background Pattern -->
                <div class="absolute inset-0 opacity-10">
                    <div class="absolute top-4 left-10 w-16 h-16 bg-white rounded-full"></div>
                    <div class="absolute bottom-4 right-10 w-20 h-20 bg-white rounded-full"></div>
                </div>
                
                <h3 class="font-serif text-2xl md:text-3xl font-bold mb-4 relative z-10">Experience the Gurukul Difference</h3>
                <p class="text-xl mb-6 opacity-90 relative z-10">Schedule a visit and see our campus in person</p>
                <div class="flex flex-col sm:flex-row gap-4 justify-center relative z-10">
                    <a href="<?php echo $base_url; ?>/contact.php" class="bg-white text-primary hover:bg-gray-100 font-semibold py-3 px-8 rounded-lg transition-all duration-300 transform hover:scale-105 inline-flex items-center">
                        <i class="fas fa-calendar-alt mr-3"></i> Schedule Visit
                    </a>
                    <a href="<?php echo $base_url; ?>/admissions/index.php" class="border-2 border-white text-white hover:bg-white hover:text-primary font-semibold py-3 px-8 rounded-lg transition-all duration-300 transform hover:scale-105 inline-flex items-center">
                        <i class="fas fa-user-graduate mr-3"></i> Apply Now
                    </a>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- JavaScript for Animations -->
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Fade in animation on scroll
    const fadeElements = document.querySelectorAll('.fade-in-up');
    
    const fadeInOnScroll = () => {
        fadeElements.forEach(element => {
            const elementTop = element.getBoundingClientRect().top;
            const elementVisible = 150;
            
            if (elementTop < window.innerHeight - elementVisible) {
                element.classList.add('visible');
            }
        });
    };
    
    // Initial check
    fadeInOnScroll();
    
    // Check on scroll
    window.addEventListener('scroll', fadeInOnScroll);
    
    // Smooth scrolling for anchor links
    document.querySelectorAll('a[href^="#"]').forEach(anchor => {
        anchor.addEventListener('click', function (e) {
            e.preventDefault();
            const target = document.querySelector(this.getAttribute('href'));
            if (target) {
                target.scrollIntoView({
                    behavior: 'smooth',
                    block: 'start'
                });
            }
        });
    });
});
</script>

<?php
// Check footer file
$footer_file = './includes/footer.php';
if(file_exists($footer_file)) {
    include $footer_file;
} else {
    echo '</body></html>';
}
?>