<?php include '../../includes/header.php'; 
include '../../includes/db.php';

// Database se data fetch karna
try {
    // Admission requirements
    $stmt = $pdo->query("SELECT * FROM admission_requirements WHERE active = TRUE ORDER BY grade_level");
    $requirements = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    // Fee structure
    $stmt = $pdo->query("SELECT * FROM fee_structure WHERE academic_year = '2026-27' AND active = TRUE ORDER BY grade_level");
    $fee_structure = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    // Admission timeline
    $stmt = $pdo->query("SELECT * FROM admission_timeline WHERE academic_year = '2026-27' AND active = TRUE ORDER BY event_date");
    $timeline = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    // Hostel fees
    // $stmt = $pdo->query("SELECT * FROM hostel_fees WHERE academic_year = '2026-27' AND active = TRUE");
    // $hostel_fees = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
} catch(PDOException $e) {
    // Error handling
    $requirements = [];
    $fee_structure = [];
    $timeline = [];
    $hostel_fees = [];
}
?>

<!-- 🧩 SEO Optimization -->
<meta name="description" content="Bhaktivedanta Gurukul School of Excellence blends modern education with traditional Vedic values for holistic student development. Enroll now for spiritual and academic excellence.">
<meta name="keywords" content="Bhaktivedanta Gurukul, Gurukul School, Vedic Education, Spiritual Learning, Best School in India, Holistic Development, Education with Values">
<meta name="author" content="Bhaktivedanta Gurukul School of Excellence">
<meta name="robots" content="index, follow">
<meta name="language" content="English">
<meta name="revisit-after" content="7 days">

<!-- 🔗 Canonical (Avoid Duplicate URLs in Google) -->
<link rel="canonical" href="https://bhaktivedantagurukul.com/">

<!-- 🧠 Open Graph for Social Media -->
<meta property="og:title" content="Bhaktivedanta Gurukul School of Excellence | Modern & Vedic Education">
<meta property="og:description" content="Empowering students through modern education combined with ancient Vedic wisdom.">
<meta property="og:image" content="<?php echo $base_url; ?>/images/bvgBanner.jpg">
<meta property="og:url" content="https://bhaktivedantagurukul.com/">
<meta property="og:type" content="website">
<meta property="og:site_name" content="Bhaktivedanta Gurukul">

<!-- 🐦 Twitter Card -->
<meta name="twitter:card" content="summary_large_image">
<meta name="twitter:title" content="Bhaktivedanta Gurukul School of Excellence">
<meta name="twitter:description" content="A unique blend of modern academics and spiritual learning.">
<meta name="twitter:image" content="<?php echo $base_url; ?>/images/bvgBanner.jpg">

<!-- 🎨 Theme Color (Mobile Tab Color) -->
<meta name="theme-color" content="#DC143C">

<!-- ⚡ PERFORMANCE OPTIMIZATION -->
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@400;600;700&family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">

<!-- 🖼️ Favicon -->
<link rel="icon" type="image/png" href="<?php echo $base_url; ?>/images/bvgLogo.png">

<!-- Hero Section -->
<section class="relative bg-gradient-to-br from-primary via-primary to-accent text-white overflow-hidden min-h-[80vh] flex items-center">
    <!-- Background Pattern -->
    <div class="absolute inset-0 opacity-10">
        <div class="absolute top-10 left-10 w-20 h-20 bg-white rounded-full"></div>
        <div class="absolute bottom-10 right-10 w-32 h-32 bg-white rounded-full"></div>
        <div class="absolute top-1/2 left-1/4 w-16 h-16 bg-white rounded-full"></div>
    </div>
    
    <div class="max-w-7xl mx-auto px-4 text-center relative z-10 w-full">
        <!-- Breadcrumb -->
        <div class="flex justify-center mb-8">
            <nav class="flex items-center space-x-2 text-white/80 text-sm">
                <a href="<?php echo $base_url; ?>/index.php" class="hover:text-white transition-colors">Home</a>
                <span class="text-white/60">/</span>
                <span class="text-white font-medium">Admissions</span>
            </nav>
        </div>
        
        <h1 class="font-serif text-3xl md:text-5xl lg:text-6xl font-bold mb-6 leading-tight">
            Admissions <span class="text-yellow-300">2026-27</span>
        </h1>
        <p class="text-xl md:text-2xl mb-8 text-gray-200 max-w-3xl mx-auto leading-relaxed">
            Begin Your Child's Transformational Journey at Bhaktivedanta Gurukul
        </p>
        <p class="text-lg text-gray-300 max-w-2xl mx-auto mb-12">
            Where Ancient Wisdom Meets Modern Education for Holistic Excellence
        </p>
        
        <div class="flex flex-col sm:flex-row gap-4 justify-center items-center">
            <a href="apply.php" class="bg-white text-primary hover:bg-gray-100 font-semibold py-4 px-8 rounded-xl transition-all duration-300 transform hover:scale-105 shadow-lg inline-flex items-center group">
                <i class="fas fa-edit mr-3 group-hover:scale-110 transition-transform"></i> 
                Apply Now
            </a>
            <a href="#process" class="border-2 border-white text-white hover:bg-white hover:text-primary font-semibold py-4 px-8 rounded-xl transition-all duration-300 transform hover:scale-105 inline-flex items-center group">
                <i class="fas fa-info-circle mr-3 group-hover:scale-110 transition-transform"></i>
                Learn More
            </a>
        </div>
        
        <!-- Quick Stats -->
        <div class="grid grid-cols-2 md:grid-cols-4 gap-8 mt-16 max-w-4xl mx-auto">
            <div class="text-center">
                <div class="text-2xl md:text-3xl font-bold text-yellow-300 mb-2">50+</div>
                <div class="text-gray-300 text-sm">Students Enrolled</div>
            </div>
            <div class="text-center">
                <div class="text-2xl md:text-3xl font-bold text-yellow-300 mb-2">20:1</div>
                <div class="text-gray-300 text-sm">Student-Teacher Ratio</div>
            </div>
            <div class="text-center">
                <div class="text-2xl md:text-3xl font-bold text-yellow-300 mb-2">95%</div>
                <div class="text-gray-300 text-sm">Success Rate</div>
            </div>
            <div class="text-center">
                <div class="text-2xl md:text-3xl font-bold text-yellow-300 mb-2">20+</div>
                <div class="text-gray-300 text-sm">Expert Faculty</div>
            </div>
        </div>
    </div>
</section>

<!-- Quick Navigation -->
<section class="bg-white border-b border-gray-200 sticky top-0 z-40 shadow-sm">
    <div class="max-w-7xl mx-auto px-4">
        <div class="flex overflow-x-auto py-4 space-x-8">
            <a href="#process" class="flex items-center space-x-2 text-gray-600 hover:text-accent transition-colors whitespace-nowrap">
                <i class="fas fa-list-ol text-sm"></i>
                <span>Process</span>
            </a>
            <a href="#requirements" class="flex items-center space-x-2 text-gray-600 hover:text-accent transition-colors whitespace-nowrap">
                <i class="fas fa-file-alt text-sm"></i>
                <span>Requirements</span>
            </a>
            <a href="#fees" class="flex items-center space-x-2 text-gray-600 hover:text-accent transition-colors whitespace-nowrap">
                <i class="fas fa-rupee-sign text-sm"></i>
                <span>Fee Structure</span>
            </a>
            <a href="#dates" class="flex items-center space-x-2 text-gray-600 hover:text-accent transition-colors whitespace-nowrap">
                <i class="fas fa-calendar-alt text-sm"></i>
                <span>Important Dates</span>
            </a>
            <a href="#faq" class="flex items-center space-x-2 text-gray-600 hover:text-accent transition-colors whitespace-nowrap">
                <i class="fas fa-question-circle text-sm"></i>
                <span>FAQ</span>
            </a>
        </div>
    </div>
</section>

<!-- Admissions Process Section -->
<section id="process" class="py-20 bg-white">
    <div class="max-w-7xl mx-auto px-4">
        <div class="text-center mb-16">
            <span class="inline-flex items-center px-4 py-2 rounded-full bg-accent/10 text-accent font-semibold text-sm mb-4">
                <i class="fas fa-list-ol mr-2"></i> Admission Process
            </span>
            <h2 class="font-serif text-3xl md:text-4xl font-bold text-primary mb-4">
                Simple & Transparent <span class="text-accent">Process</span>
            </h2>
            <p class="text-xl text-gray-600 max-w-2xl mx-auto">
                Four easy steps to join our Gurukul family
            </p>
        </div>

        <!-- Process Steps with Icons -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-8 mb-16">
            <?php
            $process_steps = [
                [
                    'icon' => 'fas fa-file-invoice',
                    'title' => 'Submit Application',
                    'description' => 'Complete the online application form with required details',
                    'color' => 'from-blue-500 to-blue-600'
                ],
                [
                    'icon' => 'fas fa-user-check',
                    'title' => 'Student Assessment',
                    'description' => 'Interactive session and academic evaluation',
                    'color' => 'from-green-500 to-green-600'
                ],
                [
                    'icon' => 'fas fa-file-upload',
                    'title' => 'Document Submission',
                    'description' => 'Upload required documents and certificates',
                    'color' => 'from-purple-500 to-purple-600'
                ],
                [
                    'icon' => 'fas fa-check-circle',
                    'title' => 'Admission Confirmation',
                    'description' => 'Fee payment and final admission approval',
                    'color' => 'from-accent to-red-600'
                ]
            ];
            
            foreach ($process_steps as $index => $step):
            ?>
            <div class="text-center group">
                <div class="relative mb-6">
                    <div class="w-20 h-20 bg-gradient-to-br <?php echo $step['color']; ?> rounded-2xl flex items-center justify-center mx-auto mb-4 shadow-lg transform group-hover:scale-110 transition-all duration-300">
                        <i class="<?php echo $step['icon']; ?> text-white text-2xl"></i>
                    </div>
                    <div class="absolute -top-2 -right-2 w-8 h-8 bg-accent rounded-full flex items-center justify-center text-white text-sm font-bold shadow-lg">
                        <?php echo $index + 1; ?>
                    </div>
                </div>
                <h3 class="font-serif text-xl font-bold text-primary mb-3 group-hover:text-accent transition-colors">
                    <?php echo $step['title']; ?>
                </h3>
                <p class="text-gray-600 leading-relaxed">
                    <?php echo $step['description']; ?>
                </p>
            </div>
            <?php endforeach; ?>
        </div>

        <!-- Process CTA -->
        <div class="text-center">
            <div class="bg-gradient-to-r from-primary/5 to-accent/5 rounded-2xl p-8 border border-gray-200">
                <h3 class="font-serif text-2xl font-bold text-primary mb-4">Ready to Start Your Journey?</h3>
                <p class="text-gray-600 mb-6 max-w-2xl mx-auto">
                    Begin the admission process today and take the first step towards holistic education for your child.
                </p>
                <div class="flex flex-col sm:flex-row gap-4 justify-center">
                    <a href="<?php echo $base_url; ?>/pages/admissions/apply.php" class="bg-accent hover:bg-red-700 text-white font-semibold py-3 px-8 rounded-xl transition-all duration-300 transform hover:scale-105 shadow-lg inline-flex items-center justify-center group">
                        <i class="fas fa-edit mr-3 group-hover:scale-110 transition-transform"></i> 
                        Start Application
                    </a>
                    <a href="<?php echo $base_url; ?>/contact.php" class="border-2 border-accent text-accent hover:bg-accent hover:text-white font-semibold py-3 px-8 rounded-xl transition-all duration-300 transform hover:scale-105 inline-flex items-center justify-center group">
                        <i class="fas fa-calendar-alt mr-3 group-hover:scale-110 transition-transform"></i>
                        Schedule Campus Visit
                    </a>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Requirements Section -->
<section id="requirements" class="py-20 bg-gray-50">
    <div class="max-w-7xl mx-auto px-4">
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-12 items-start">
            <div>
                <div class="mb-8">
                    <span class="inline-flex items-center px-4 py-2 rounded-full bg-accent/10 text-accent font-semibold text-sm mb-4">
                        <i class="fas fa-file-alt mr-2"></i> Requirements
                    </span>
                    <h2 class="font-serif text-3xl md:text-4xl font-bold text-primary mb-4">
                        Admission <span class="text-accent">Requirements</span>
                    </h2>
                    <p class="text-lg text-gray-600">
                        To ensure the best educational experience for your child, we have certain requirements for admission.
                    </p>
                </div>

                <div class="space-y-6">
                    <?php if(!empty($requirements)): ?>
                        <?php foreach($requirements as $req): 
                            $documents = json_decode($req['required_documents'], true);
                        ?>
                        <div class="bg-white rounded-2xl p-6 shadow-lg border border-gray-100 hover-lift transition-all duration-300">
                            <h3 class="font-serif text-xl font-bold text-primary mb-4 flex items-center">
                                <i class="fas fa-graduation-cap text-accent mr-3"></i> 
                                <?php echo htmlspecialchars($req['grade_level']); ?>
                            </h3>
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <div>
                                    <h4 class="font-semibold text-primary mb-3 flex items-center">
                                        <i class="fas fa-check-circle text-green-500 mr-2"></i>
                                        Requirements
                                    </h4>
                                    <ul class="space-y-2 text-gray-600">
                                        <li class="flex items-start">
                                            <i class="fas fa-child text-blue-500 mr-2 mt-1"></i>
                                            <span>Minimum Age: <strong><?php echo $req['min_age_years']; ?> years</strong></span>
                                        </li>
                                        <li class="flex items-start">
                                            <i class="fas fa-book text-blue-500 mr-2 mt-1"></i>
                                            <span><?php echo htmlspecialchars($req['academic_requirements']); ?></span>
                                        </li>
                                    </ul>
                                </div>
                                <div>
                                    <h4 class="font-semibold text-primary mb-3 flex items-center">
                                        <i class="fas fa-file text-purple-500 mr-2"></i>
                                        Required Documents
                                    </h4>
                                    <ul class="space-y-2 text-gray-600">
                                        <?php if(is_array($documents)): ?>
                                            <?php foreach($documents as $doc): ?>
                                            <li class="flex items-start">
                                                <i class="fas fa-file-pdf text-red-500 mr-2 mt-1"></i>
                                                <span><?php echo ucwords(str_replace('_', ' ', $doc)); ?></span>
                                            </li>
                                            <?php endforeach; ?>
                                        <?php else: ?>
                                            <li class="text-gray-500">No specific documents listed</li>
                                        <?php endif; ?>
                                    </ul>
                                </div>
                            </div>
                        </div>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <div class="bg-white rounded-2xl p-8 text-center">
                            <i class="fas fa-info-circle text-gray-400 text-4xl mb-4"></i>
                            <h3 class="text-xl font-bold text-gray-600 mb-2">Admission Requirements</h3>
                            <p class="text-gray-500">Detailed requirements will be available soon. Please contact our admissions office for more information.</p>
                        </div>
                    <?php endif; ?>
                </div>
            </div>

            <!-- Sidebar Information -->
            <div class="space-y-6">
                <!-- Important Notice -->
                <div class="bg-gradient-to-br from-primary to-accent rounded-2xl p-6 text-white shadow-lg">
                    <h3 class="font-serif text-xl font-bold mb-4 flex items-center">
                        <i class="fas fa-exclamation-circle mr-3"></i>
                        Important Notice
                    </h3>
                    <ul class="space-y-3 text-white/90">
                        <li class="flex items-start">
                            <i class="fas fa-clock mt-1 mr-3 text-yellow-300"></i>
                            <span>Early applications encouraged - limited seats available</span>
                        </li>
                        <li class="flex items-start">
                            <i class="fas fa-star mt-1 mr-3 text-yellow-300"></i>
                            <span>Priority given to applications before April 19st</span>
                        </li>
                        <li class="flex items-start">
                            <i class="fas fa-award mt-1 mr-3 text-yellow-300"></i>
                            <span>Scholarship applications processed separately</span>
                        </li>
                    </ul>
                </div>

                <!-- Contact Card -->
                <div class="bg-white rounded-2xl p-6 shadow-lg border border-gray-100">
                    <h3 class="font-serif text-xl font-bold text-primary mb-4 flex items-center">
                        <i class="fas fa-headset text-accent mr-3"></i>
                        Admissions Office
                    </h3>
                    <div class="space-y-4">
                        <div class="flex items-center p-3 bg-red-50 rounded-lg">
                            <i class="fas fa-phone text-accent mr-3"></i>
                            <div>
                                <div class="font-semibold text-primary">+91-7618040040</div>
                                <div class="text-sm text-gray-600">Admissions Helpline</div>
                            </div>
                        </div>
                        <div class="flex items-center p-3 bg-blue-50 rounded-lg">
                            <i class="fas fa-envelope text-accent mr-3"></i>
                            <div>
                                <div class="font-semibold text-primary">info@ourgurukul.org</div>
                                <div class="text-sm text-gray-600">Email Support</div>
                            </div>
                        </div>
                        <div class="flex items-center p-3 bg-green-50 rounded-lg">
                            <i class="fas fa-clock text-accent mr-3"></i>
                            <div>
                                <div class="font-semibold text-primary">Mon - Sat: 9:00 AM - 5:00 PM</div>
                                <div class="text-sm text-gray-600">Office Hours</div>
                            </div>
                        </div>
                    </div>
                    <div class="mt-4 pt-4 border-t border-gray-200">
                        <a href="<?php echo $base_url; ?>/contact.php" class="w-full bg-accent hover:bg-red-700 text-white font-semibold py-3 px-4 rounded-lg transition-all duration-300 inline-flex items-center justify-center">
                            <i class="fas fa-map-marker-alt mr-2"></i>
                            Visit Campus
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Fee Structure Section -->
<section id="fees" class="py-20 bg-white">
    <div class="max-w-7xl mx-auto px-4">
        <div class="text-center mb-16">
            <span class="inline-flex items-center px-4 py-2 rounded-full bg-accent/10 text-accent font-semibold text-sm mb-4">
                Fee Structure
            </span>
            <h2 class="font-serif text-3xl md:text-4xl font-bold text-primary mb-4">
                Transparent <span class="text-accent">Fee Structure</span>
            </h2>
            <p class="text-xl text-gray-600 max-w-2xl mx-auto">
                Affordable education with multiple payment options and scholarship opportunities
            </p>
        </div>

        <!-- Fee Cards -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8 mb-12">
            <?php if(!empty($fee_structure)): ?>
                <?php foreach($fee_structure as $index => $fee): ?>
                <div class="bg-white rounded-2xl shadow-lg border border-gray-100 hover-lift transition-all duration-300 group">
                    <div class="bg-gradient-to-r from-primary to-accent text-white rounded-t-2xl py-6 text-center relative overflow-hidden">
                        <div class="absolute inset-0 opacity-10">
                            <div class="absolute top-2 left-2 w-8 h-8 bg-white rounded-full"></div>
                            <div class="absolute bottom-2 right-2 w-12 h-12 bg-white rounded-full"></div>
                        </div>
                        <h3 class="font-serif text-xl font-bold relative z-10"><?php echo htmlspecialchars($fee['grade_level']); ?></h3>
                        <div class="text-sm opacity-90 mt-1 relative z-10">Annual Fee</div>
                    </div>
                    <div class="p-6">
                        <div class="text-center mb-6">
                            <span class="text-3xl font-bold text-primary">₹<?php echo number_format($fee['total_fee']); ?></span>
                            <span class="text-gray-600 text-lg">/ year</span>
                        </div>
                        <ul class="space-y-3 text-gray-600 mb-6">
                            <li class="flex justify-between items-center py-2 border-b border-gray-100">
                                <span class="flex items-center">
                                    <i class="fas fa-file-signature text-green-500 mr-2"></i>
                                    Admission Fee (one time)
                                </span>
                                <span class="font-semibold text-primary">₹<?php echo number_format($fee['admission_fee']); ?></span>
                            </li>
                            <li class="flex justify-between items-center py-2 border-b border-gray-100">
                                <span class="flex items-center">
                                    <i class="fas fa-book text-blue-500 mr-2"></i>
                                    Quarterly
                                </span>
                                <span class="font-semibold text-primary">₹<?php echo number_format($fee['tuition_fee']); ?></span>
                            </li>
                            <li class="flex justify-between items-center py-2 border-b border-gray-100">
                                <span class="flex items-center">
                                    <i class="fas fa-building text-purple-500 mr-2"></i>
                                    Annual Fee
                                </span>
                                <span class="font-semibold text-primary">₹<?php echo number_format($fee['development_fee']); ?></span>
                            </li>
                        </ul>
                        <a href="apply.php?grade=<?php echo urlencode($fee['grade_level']); ?>" 
                           class="bg-accent hover:bg-red-700 text-white font-semibold py-3 px-6 rounded-xl transition-all duration-300 w-full text-center block transform group-hover:scale-105 shadow-lg">
                            Apply for <?php echo htmlspecialchars($fee['grade_level']); ?>
                        </a>
                    </div>
                </div>
                <?php endforeach; ?>
            <?php else: ?>
                <div class="col-span-3 bg-white rounded-2xl p-8 text-center">
                    <i class="fas fa-info-circle text-gray-400 text-4xl mb-4"></i>
                    <h3 class="text-xl font-bold text-gray-600 mb-2">Fee Structure</h3>
                    <p class="text-gray-500">Detailed fee structure will be available soon. Please contact our admissions office for more information.</p>
                </div>
            <?php endif; ?>
        </div>

        <!-- Hostel Fees -->
        <!-- <?php if(!empty($hostel_fees)): ?>
        <div class="bg-gradient-to-r from-primary/5 to-accent/5 rounded-2xl p-8 border border-gray-200">
            <h3 class="font-serif text-2xl font-bold text-primary mb-6 text-center flex items-center justify-center">
                <i class="fas fa-home text-accent mr-3"></i>
                Hostel Accommodation
            </h3>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                <?php foreach($hostel_fees as $hostel): ?>
                <div class="bg-white rounded-xl p-6 shadow-lg border border-gray-100 hover-lift transition-all duration-300">
                    <h4 class="font-bold text-primary mb-4 text-lg flex items-center">
                        <i class="fas fa-bed text-accent mr-2"></i>
                        <?php echo htmlspecialchars($hostel['hostel_type']); ?>
                    </h4>
                    <div class="text-center mb-4">
                        <span class="text-2xl font-bold text-accent">₹<?php echo number_format($hostel['total_fee']); ?></span>
                        <span class="text-gray-600">/ year</span>
                    </div>
                    <ul class="space-y-3 text-gray-600">
                        <li class="flex justify-between items-center py-2 border-b border-gray-100">
                            <span class="flex items-center">
                                <i class="fas fa-door-open text-blue-500 mr-2"></i>
                                Room Charges
                            </span>
                            <span class="font-semibold text-primary">₹<?php echo number_format($hostel['room_charges']); ?></span>
                        </li>
                        <li class="flex justify-between items-center py-2 border-b border-gray-100">
                            <span class="flex items-center">
                                <i class="fas fa-utensils text-green-500 mr-2"></i>
                                Food Charges
                            </span>
                            <span class="font-semibold text-primary">₹<?php echo number_format($hostel['food_charges']); ?></span>
                        </li>
                        <?php if($hostel['other_charges'] > 0): ?>
                        <li class="flex justify-between items-center py-2">
                            <span class="flex items-center">
                                <i class="fas fa-cogs text-orange-500 mr-2"></i>
                                Other Charges
                            </span>
                            <span class="font-semibold text-primary">₹<?php echo number_format($hostel['other_charges']); ?></span>
                        </li>
                        <?php endif; ?>
                    </ul>
                </div>
                <?php endforeach; ?>
            </div>
            <div class="mt-6 text-center">
                <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-4 inline-flex items-center">
                    <i class="fas fa-info-circle text-yellow-600 mr-2"></i>
                    <span class="text-yellow-700 text-sm">
                        Hostel fees include accommodation, meals, and basic medical facilities. Separate application required.
                    </span>
                </div>
            </div>
        </div>
        <?php endif; ?> -->
    </div>
</section>

<!-- Important Dates Section -->
<section id="dates" class="py-20 bg-gray-50">
    <div class="max-w-7xl mx-auto px-4">
        <div class="text-center mb-16">
            <span class="inline-flex items-center px-4 py-2 rounded-full bg-accent/10 text-accent font-semibold text-sm mb-4">
                <i class="fas fa-calendar-alt mr-2"></i> Important Dates
            </span>
            <h2 class="font-serif text-3xl md:text-4xl font-bold text-primary mb-4">
                Academic Year <span class="text-accent">2026-27</span>
            </h2>
            <p class="text-xl text-gray-600 max-w-2xl mx-auto">
                Mark your calendar with these important admission dates and deadlines
            </p>
        </div>

        <div class="max-w-4xl mx-auto">
            <!-- Timeline -->
            <div class="space-y-8">
                <?php if(!empty($timeline)): ?>
                    <?php foreach($timeline as $index => $event): 
                        $event_date = new DateTime($event['event_date']);
                        $is_past = $event_date < new DateTime();
                    ?>
                    <div class="flex items-start group">
                        <div class="bg-<?php echo $is_past ? 'gray-400' : 'accent'; ?> text-white rounded-xl px-5 py-3 min-w-32 text-center mr-6 shadow-lg transform group-hover:scale-105 transition-all duration-300">
                            <div class="font-bold text-lg"><?php echo $event_date->format('M j'); ?></div>
                            <div class="text-sm opacity-90"><?php echo $event_date->format('Y'); ?></div>
                        </div>
                        <div class="bg-white rounded-xl p-6 shadow-lg border border-gray-100 flex-1 hover-lift transition-all duration-300">
                            <div class="flex items-center justify-between mb-3">
                                <h3 class="font-bold text-primary text-lg"><?php echo htmlspecialchars($event['event_name']); ?></h3>
                                <?php if(!$is_past): ?>
                                <span class="bg-green-100 text-green-800 text-xs font-semibold px-3 py-1 rounded-full">
                                    Upcoming
                                </span>
                                <?php else: ?>
                                <span class="bg-gray-100 text-gray-600 text-xs font-semibold px-3 py-1 rounded-full">
                                    Completed
                                </span>
                                <?php endif; ?>
                            </div>
                            <p class="text-gray-600 leading-relaxed"><?php echo htmlspecialchars($event['description']); ?></p>
                        </div>
                    </div>
                    <?php endforeach; ?>
                <?php else: ?>
                    <div class="bg-white rounded-2xl p-8 text-center">
                        <i class="fas fa-info-circle text-gray-400 text-4xl mb-4"></i>
                        <h3 class="text-xl font-bold text-gray-600 mb-2">Admission Timeline</h3>
                        <p class="text-gray-500">Important dates for the academic year 2026-27 will be announced soon. Please check back later or contact our admissions office.</p>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</section>

<!-- FAQ Section -->
<section id="faq" class="py-20 bg-white">
    <div class="max-w-4xl mx-auto px-4">
        <div class="text-center mb-16">
            <span class="inline-flex items-center px-4 py-2 rounded-full bg-accent/10 text-accent font-semibold text-sm mb-4">
                <i class="fas fa-question-circle mr-2"></i> FAQ
            </span>
            <h2 class="font-serif text-3xl md:text-4xl font-bold text-primary mb-4">
                Frequently Asked <span class="text-accent">Questions</span>
            </h2>
            <p class="text-xl text-gray-600 max-w-2xl mx-auto">
                Find answers to common questions about our admission process
            </p>
        </div>

        <div class="space-y-4">
            <?php
            $faqs = [
                [
                    'question' => 'What is the admission criteria?',
                    'answer' => 'Admission is based on previous academic performance, student interaction, and availability of seats. We focus on holistic development rather than just academic scores.'
                ],
                [
                    'question' => 'Is hostel accommodation compulsory?',
                    'answer' => 'Hostel accommodation is optional for local students but recommended for outstation students to fully experience the Gurukul system and spiritual environment.'
                ],
                [
                    'question' => 'What is the student-teacher ratio?',
                    'answer' => 'We maintain an excellent student-teacher ratio of 20:1 to ensure personalized attention and quality education for every student.'
                ],
                [
                    'question' => 'Are scholarships available?',
                    'answer' => 'Yes, we offer merit-based and need-based scholarships. Students can apply separately for scholarships after admission confirmation.'
                ],
                [
                    'question' => 'What is the medium of instruction?',
                    'answer' => 'English is the primary medium of instruction, with Hindi and Sanskrit as additional languages in the curriculum.'
                ]
            ];
            
            foreach ($faqs as $index => $faq):
            ?>
            <div class="bg-white rounded-xl shadow-lg border border-gray-100 faq-item overflow-hidden">
                <button class="faq-question w-full text-left p-6 font-semibold text-primary text-lg flex justify-between items-center hover:bg-gray-50 transition-colors duration-300">
                    <span><?php echo $faq['question']; ?></span>
                    <i class="fas fa-chevron-down text-accent transition-transform duration-300"></i>
                </button>
                <div class="faq-answer overflow-hidden transition-all duration-300 max-h-0">
                    <div class="px-6 pb-6 text-gray-600 leading-relaxed">
                        <?php echo $faq['answer']; ?>
                    </div>
                </div>
            </div>
            <?php endforeach; ?>
        </div>

        <!-- Contact CTA -->
        <div class="text-center mt-12">
            <div class="bg-gradient-to-r from-primary/5 to-accent/5 rounded-2xl p-8 border border-gray-200">
                <h3 class="font-serif text-2xl font-bold text-primary mb-4">Still have questions?</h3>
                <p class="text-gray-600 mb-6 max-w-md mx-auto">Our admissions team is here to help you with any queries about the admission process.</p>
                <div class="flex flex-col sm:flex-row gap-4 justify-center">
                    <a href="<?php echo $base_url; ?>/contact.php" class="bg-accent hover:bg-red-700 text-white font-semibold py-3 px-8 rounded-xl transition-all duration-300 transform hover:scale-105 shadow-lg inline-flex items-center">
                        <i class="fas fa-phone mr-3"></i> Contact Admissions
                    </a>
                    <a href="tel:+919876543210" class="border-2 border-accent text-accent hover:bg-accent hover:text-white font-semibold py-3 px-8 rounded-xl transition-all duration-300 transform hover:scale-105 inline-flex items-center">
                        <i class="fas fa-mobile-alt mr-3"></i> Call Now
                    </a>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Final CTA Section -->
<section class="bg-gradient-to-r from-primary to-accent text-white py-20">
    <div class="max-w-4xl mx-auto text-center px-4">
        <h2 class="font-serif text-3xl md:text-4xl font-bold mb-6">
            Ready to Join Our Gurukul Family?
        </h2>
        <p class="text-xl mb-8 opacity-90 leading-relaxed">
            Take the first step towards your child's holistic development and spiritual growth
        </p>
        <div class="flex flex-col sm:flex-row gap-4 justify-center">
            <a href="apply.php" class="bg-white text-primary hover:bg-gray-100 font-semibold py-4 px-8 rounded-xl transition-all duration-300 transform hover:scale-105 shadow-lg inline-flex items-center justify-center group">
                <i class="fas fa-edit mr-3 group-hover:scale-110 transition-transform"></i>
                Start Application Process
            </a>
            <a href="../contact.php" class="border-2 border-white text-white hover:bg-white hover:text-primary font-semibold py-4 px-8 rounded-xl transition-all duration-300 transform hover:scale-105 inline-flex items-center justify-center group">
                <i class="fas fa-info-circle mr-3 group-hover:scale-110 transition-transform"></i>
                Request More Information
            </a>
        </div>
        <p class="text-white/70 text-sm mt-8">
            Limited seats available for Academic Year 2026-27. Apply early to secure your spot.
        </p>
    </div>
</section>

<?php include '../../includes/footer.php'; ?>

<style>
.hover-lift {
    transition: transform 0.3s ease, box-shadow 0.3s ease;
}

.hover-lift:hover {
    transform: translateY(-5px);
    box-shadow: 0 20px 40px rgba(0,0,0,0.1);
}

.faq-question.active {
    background-color: #f8fafc;
}

.faq-question.active i {
    transform: rotate(180deg);
}

.faq-answer.open {
    max-height: 500px !important;
}

/* Smooth scrolling */
html {
    scroll-behavior: smooth;
}

/* Sticky navigation highlight */
.sticky-nav {
    position: sticky;
    top: 0;
    z-index: 40;
    backdrop-filter: blur(10px);
}

/* Responsive improvements */
@media (max-width: 768px) {
    .flex.items-start.group {
        flex-direction: column;
        text-align: center;
    }
    
    .flex.items-start.group > div:first-child {
        margin-right: 0;
        margin-bottom: 1rem;
    }
}
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // FAQ Accordion
    const faqQuestions = document.querySelectorAll('.faq-question');
    
    faqQuestions.forEach(question => {
        question.addEventListener('click', function() {
            const answer = this.nextElementSibling;
            const isOpen = answer.classList.contains('open');
            
            // Close all FAQs
            document.querySelectorAll('.faq-answer').forEach(ans => {
                ans.classList.remove('open');
                ans.style.maxHeight = '0';
            });
            document.querySelectorAll('.faq-question').forEach(q => {
                q.classList.remove('active');
            });
            
            // Toggle current FAQ if it wasn't open
            if (!isOpen) {
                this.classList.add('active');
                answer.classList.add('open');
                answer.style.maxHeight = answer.scrollHeight + 'px';
            }
        });
    });
    
    // Open first FAQ by default
    if (faqQuestions.length > 0) {
        faqQuestions[0].click();
    }
    
    // Add scroll animation for elements
    const observerOptions = {
        threshold: 0.1,
        rootMargin: '0px 0px -50px 0px'
    };
    
    const observer = new IntersectionObserver(function(entries) {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                entry.target.style.opacity = '1';
                entry.target.style.transform = 'translateY(0)';
            }
        });
    }, observerOptions);
    
    // Observe all elements with hover-lift class
    document.querySelectorAll('.hover-lift').forEach(el => {
        el.style.opacity = '0';
        el.style.transform = 'translateY(20px)';
        el.style.transition = 'opacity 0.6s ease, transform 0.6s ease';
        observer.observe(el);
    });
});
</script>