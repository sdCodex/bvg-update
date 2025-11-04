<?php
require_once '../../includes/db.php';
include_once '../../includes/header.php';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Our Inspiration - Bhaktivedanta Gurukul</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        .gradient-bg {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        }
        
        .inspiration-card {
            transition: all 0.3s ease;
            background: white;
            border-radius: 16px;
            overflow: hidden;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.05);
        }
        
        .inspiration-card:hover {
            transform: translateY(-8px);
            box-shadow: 0 20px 40px rgba(0, 0, 0, 0.1);
        }
        
        .philosophy-item {
            transition: all 0.3s ease;
            background: white;
            border-radius: 16px;
            padding: 2rem;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.05);
        }
        
        .philosophy-item:hover {
            transform: translateY(-5px);
            box-shadow: 0 15px 30px rgba(0, 0, 0, 0.1);
        }
        
        .icon-container {
            width: 80px;
            height: 80px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 2rem;
            margin: 0 auto 1.5rem;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        }
        
        .fade-in {
            animation: fadeIn 0.8s ease-in-out;
        }
        
        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(30px); }
            to { opacity: 1; transform: translateY(0); }
        }
        
        .stagger-animation > * {
            opacity: 0;
            animation: fadeIn 0.8s ease-in-out forwards;
        }
        
        .stagger-animation > *:nth-child(1) { animation-delay: 0.1s; }
        .stagger-animation > *:nth-child(2) { animation-delay: 0.2s; }
        .stagger-animation > *:nth-child(3) { animation-delay: 0.3s; }
        .stagger-animation > *:nth-child(4) { animation-delay: 0.4s; }
    </style>
</head>
<body class="bg-gray-50 font-sans">
    <!-- Hero Section -->
    <section class="gradient-bg text-white py-20 lg:py-28">
        <div class="container mx-auto px-4 text-center fade-in">
            <h1 class="text-4xl lg:text-6xl font-bold font-serif mb-6">Our Inspiration</h1>
            <p class="text-xl lg:text-2xl opacity-90 max-w-3xl mx-auto leading-relaxed">
                Guiding Lights on Our Educational Journey
            </p>
            <div class="mt-8">
                <div class="w-24 h-1 bg-white/30 mx-auto rounded-full"></div>
            </div>
        </div>
    </section>

    <!-- Inspiration Content -->
    <section class="py-16 lg:py-24">
        <div class="container mx-auto px-4">
            <div class="max-w-6xl mx-auto space-y-16 stagger-animation">
                <?php
                $stmt = $pdo->query("SELECT * FROM inspiration WHERE is_active = TRUE ORDER BY display_order");
                $inspirations = $stmt->fetchAll(PDO::FETCH_ASSOC);
                
                foreach($inspirations as $index => $inspiration):
                    $isEven = $index % 2 === 0;
                ?>
                <div class="inspiration-card">
                    <div class="flex flex-col lg:flex-row <?php echo $isEven ? '' : 'lg:flex-row-reverse'; ?>">
                        <!-- Image Section -->
                        <div class="lg:w-1/2">
                            <?php if($inspiration['image_path']): ?>
                                <img 
                                    src="<?php echo $base_url . $inspiration['image_path']; ?>" 
                                    alt="<?php echo htmlspecialchars($inspiration['title']); ?>"
                                    class="w-full h-64 lg:h-full object-cover"
                                >
                            <?php else: ?>
                                <div class="w-full h-64 lg:h-full bg-gradient-to-br from-primary/10 to-accent/10 flex items-center justify-center">
                                    <i class="fas fa-user text-6xl text-primary/40"></i>
                                </div>
                            <?php endif; ?>
                        </div>
                        
                        <!-- Text Content -->
                        <div class="lg:w-1/2 p-8 lg:p-12">
                            <div class="flex items-center mb-6">
                                <div class="w-12 h-12 bg-gradient-to-br from-primary to-accent rounded-full flex items-center justify-center mr-4">
                                    <i class="fas fa-lightbulb text-white text-lg"></i>
                                </div>
                                <h2 class="text-2xl lg:text-3xl font-bold text-primary font-serif">
                                    <?php echo htmlspecialchars($inspiration['title']); ?>
                                </h2>
                            </div>
                            
                            <div class="text-gray-700 leading-relaxed text-lg space-y-4">
                                <?php echo nl2br(htmlspecialchars($inspiration['content'])); ?>
                            </div>
                            
                            <div class="mt-8 pt-6 border-t border-gray-200">
                                <div class="flex items-center text-primary">
                                    <i class="fas fa-quote-left text-2xl opacity-50 mr-4"></i>
                                    <span class="text-sm font-medium">Divine Inspiration</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
        </div>
    </section>

    <!-- Philosophy Section -->
    <section class="py-16 lg:py-24 bg-white">
        <div class="container mx-auto px-4">
            <div class="text-center mb-16 fade-in">
                <h2 class="text-3xl lg:text-4xl font-bold text-primary font-serif mb-4">Our Educational Philosophy</h2>
                <p class="text-gray-600 text-lg max-w-2xl mx-auto">
                    Blending ancient wisdom with modern education for holistic development
                </p>
                <div class="mt-6">
                    <div class="w-20 h-1 bg-accent mx-auto rounded-full"></div>
                </div>
            </div>
            
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-8 max-w-7xl mx-auto stagger-animation">
                <!-- Holistic Development -->
                <div class="philosophy-item text-center group">
                    <div class="icon-container group-hover:scale-110 transition-transform duration-300">
                        <i class="fas fa-brain text-white"></i>
                    </div>
                    <h3 class="text-xl font-bold text-primary mb-4 font-serif">Holistic Development</h3>
                    <p class="text-gray-600 leading-relaxed">
                        Balancing academic excellence with spiritual growth and character building for complete personality development
                    </p>
                </div>
                
                <!-- Vedic Integration -->
                <div class="philosophy-item text-center group">
                    <div class="icon-container group-hover:scale-110 transition-transform duration-300">
                        <i class="fas fa-book-open text-white"></i>
                    </div>
                    <h3 class="text-xl font-bold text-primary mb-4 font-serif">Vedic Integration</h3>
                    <p class="text-gray-600 leading-relaxed">
                        Combining modern education with timeless Vedic wisdom to create well-rounded individuals
                    </p>
                </div>
                
                <!-- Value-Based Education -->
                <div class="philosophy-item text-center group">
                    <div class="icon-container group-hover:scale-110 transition-transform duration-300">
                        <i class="fas fa-heart text-white"></i>
                    </div>
                    <h3 class="text-xl font-bold text-primary mb-4 font-serif">Value-Based Education</h3>
                    <p class="text-gray-600 leading-relaxed">
                        Instilling moral values, ethical principles, and cultural heritage in every student's life
                    </p>
                </div>
                
                <!-- Guru-Shishya Tradition -->
                <div class="philosophy-item text-center group">
                    <div class="icon-container group-hover:scale-110 transition-transform duration-300">
                        <i class="fas fa-hands-helping text-white"></i>
                    </div>
                    <h3 class="text-xl font-bold text-primary mb-4 font-serif">Guru-Shishya Tradition</h3>
                    <p class="text-gray-600 leading-relaxed">
                        Maintaining the sacred teacher-student relationship for personalized guidance and mentorship
                    </p>
                </div>
            </div>
            
            <!-- Additional Philosophy Points -->
            <div class="mt-16 grid grid-cols-1 md:grid-cols-3 gap-6 max-w-5xl mx-auto stagger-animation">
                <div class="bg-primary/5 rounded-2xl p-6 text-center group hover:bg-primary/10 transition-colors duration-300">
                    <i class="fas fa-seedling text-3xl text-primary mb-4 group-hover:scale-110 transition-transform duration-300"></i>
                    <h4 class="font-bold text-primary mb-2">Natural Learning</h4>
                    <p class="text-gray-600 text-sm">Learning in harmony with nature and natural rhythms</p>
                </div>
                
                <div class="bg-accent/5 rounded-2xl p-6 text-center group hover:bg-accent/10 transition-colors duration-300">
                    <i class="fas fa-globe-asia text-3xl text-accent mb-4 group-hover:scale-110 transition-transform duration-300"></i>
                    <h4 class="font-bold text-accent mb-2">Global Citizens</h4>
                    <p class="text-gray-600 text-sm">Preparing students to be responsible global citizens</p>
                </div>
                
                <div class="bg-admin/5 rounded-2xl p-6 text-center group hover:bg-admin/10 transition-colors duration-300">
                    <i class="fas fa-medal text-3xl text-admin mb-4 group-hover:scale-110 transition-transform duration-300"></i>
                    <h4 class="font-bold text-admin mb-2">Excellence</h4>
                    <p class="text-gray-600 text-sm">Striving for excellence in all aspects of life</p>
                </div>
            </div>
        </div>
    </section>

    <!-- Call to Action Section -->
    <section class="py-16 bg-gradient-to-r from-primary to-accent text-white">
        <div class="container mx-auto px-4 text-center">
            <div class="max-w-3xl mx-auto fade-in">
                <h2 class="text-3xl lg:text-4xl font-bold font-serif mb-6">Join Our Inspiring Journey</h2>
                <p class="text-xl opacity-90 mb-8 leading-relaxed">
                    Become part of an educational institution that values tradition, innovation, and holistic growth
                </p>
                <div class="flex flex-col sm:flex-row gap-4 justify-center">
                    <a href="<?php echo $base_url; ?>/pages/admissions/apply.php" 
                       class="bg-white text-primary px-8 py-4 rounded-xl font-semibold hover:bg-gray-100 transition-all duration-300 transform hover:scale-105 shadow-lg">
                        <i class="fas fa-edit mr-2"></i>Apply Now
                    </a>
                    <a href="<?php echo $base_url; ?>/contact.php" 
                       class="border-2 border-white text-white px-8 py-4 rounded-xl font-semibold hover:bg-white hover:text-primary transition-all duration-300 transform hover:scale-105">
                        <i class="fas fa-phone mr-2"></i>Contact Us
                    </a>
                </div>
            </div>
        </div>
    </section>

    <?php include_once '../../includes/footer.php'; ?>

    <script>
        // Add intersection observer for scroll animations
        document.addEventListener('DOMContentLoaded', function() {
            const observerOptions = {
                threshold: 0.1,
                rootMargin: '0px 0px -50px 0px'
            };

            const observer = new IntersectionObserver(function(entries) {
                entries.forEach(entry => {
                    if (entry.isIntersecting) {
                        entry.target.classList.add('fade-in');
                    }
                });
            }, observerOptions);

            // Observe all sections for animation
            document.querySelectorAll('section').forEach(section => {
                observer.observe(section);
            });
        });
    </script>
</body>
</html>