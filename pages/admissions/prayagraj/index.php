<?php include '../../../includes/header.php';
include '../../../includes/db.php';

// Database se data fetch karna
try {
    // Admission requirements
    $stmt = $pdo->query("SELECT * FROM admission_requirements WHERE active = TRUE ORDER BY id");
    $requirements = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Fee structure
    $stmt = $pdo->query("SELECT * FROM fee_structure WHERE academic_year = '2026-27' AND active = TRUE ORDER BY id");
    $fee_structure = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Admission timeline
    $stmt = $pdo->query("SELECT * FROM admission_timeline WHERE academic_year = '2026-27' AND active = TRUE ORDER BY event_date");
    $timeline = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Hostel fees
    // $stmt = $pdo->query("SELECT * FROM hostel_fees WHERE academic_year = '2026-27' AND active = TRUE");
    // $hostel_fees = $stmt->fetchAll(PDO::FETCH_ASSOC);

} catch (PDOException $e) {
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


<!-- Admissions Process Section -->

<!-- Process Steps with Icons -->



<!-- Requirements Section -->
<section id="requirements" class="py-20 bg-gray-50">
    <div class="max-w-7xl mx-auto px-4">

        <!-- Header -->
        <div class="text-center mb-14">
            <span class="inline-flex items-center px-5 py-2 rounded-full bg-accent/10 text-accent font-semibold text-sm">
                <i class="fas fa-file-alt mr-2"></i> Admission Requirements
            </span>
            <h2 class="font-serif text-4xl font-bold text-primary mt-4">
                Eligibility & <span class="text-accent">Documents</span>
            </h2>
            <p class="text-lg text-gray-600 max-w-2xl mx-auto mt-3">
                Please review the grade-wise admission requirements before applying.
            </p>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-10">

            <!-- Cards Section (Now Responsive 2 Per Row) -->
            <div class="lg:col-span-2">

                <div class="grid grid-cols-1 md:grid-cols-2 gap-8">

                    <?php if (!empty($requirements)): ?>
                        <?php foreach ($requirements as $req):
                            $documents = json_decode($req['required_documents'], true);
                        ?>

                            <!-- Requirement Card -->
                            <div class="bg-white rounded-2xl p-7 shadow-lg border border-gray-100 hover:shadow-xl hover:-translate-y-1 transition-all duration-300">

                                <h3 class="font-serif text-xl font-bold text-primary flex items-center mb-5">
                                    <span class="w-11 h-11 flex items-center justify-center rounded-full bg-accent/10 text-accent mr-3">
                                        <i class="fas fa-graduation-cap"></i>
                                    </span>
                                    <?php echo htmlspecialchars($req['grade_level']); ?>
                                </h3>

                                <div class="border-t pt-4 space-y-5">

                                    <!-- Eligibility -->
                                    <div>
                                        <h4 class="font-semibold text-primary mb-2 flex items-center">
                                            <i class="fas fa-check-circle text-green-500 mr-2"></i>
                                            Eligibility
                                        </h4>
                                        <ul class="space-y-1 text-gray-600 text-sm">
                                            <li class="flex items-center">
                                                <i class="fas fa-child text-blue-500 mr-2"></i>
                                                Minimum Age: <strong class="ml-1"><?php echo $req['min_age_years']; ?> years</strong>
                                            </li>
                                            <li class="flex items-center">
                                                <i class="fas fa-book text-blue-500 mr-2"></i>
                                                <?php echo htmlspecialchars($req['academic_requirements']); ?>
                                            </li>
                                        </ul>
                                    </div>

                                    <!-- Required Docs -->
                                    <div>
                                        <h4 class="font-semibold text-primary mb-2 flex items-center">
                                            <i class="fas fa-file-alt text-purple-500 mr-2"></i>
                                            Documents Needed
                                        </h4>

                                        <ul class="space-y-1 text-gray-600 text-sm">
                                            <?php if (is_array($documents)): ?>
                                                <?php foreach ($documents as $doc): ?>
                                                    <li class="flex items-center">
                                                        <i class="fas fa-file-pdf text-red-500 mr-2"></i>
                                                        <?php echo ucwords(str_replace('_', ' ', $doc)); ?>
                                                    </li>
                                                <?php endforeach; ?>
                                            <?php endif; ?>
                                        </ul>
                                    </div>

                                </div>
                            </div>

                        <?php endforeach; ?>

                    <?php else: ?>
                        <div class="bg-white p-10 rounded-xl shadow text-center">
                            <i class="fas fa-info-circle text-4xl text-gray-400 mb-4"></i>
                            <h3 class="text-lg font-semibold text-gray-600">Coming Soon</h3>
                            <p class="text-sm text-gray-500">Admission details will be updated soon.</p>
                        </div>
                    <?php endif; ?>

                </div>

            </div>

            <!-- Sidebar -->
            <div class="space-y-8">

                <!-- Notice Box -->
                <div class="bg-gradient-to-br from-primary to-accent text-white rounded-2xl p-7 shadow-lg">
                    <h3 class="font-serif text-xl font-bold mb-5 flex items-center">
                        <i class="fas fa-exclamation-circle mr-3"></i> Important Notice
                    </h3>
                    <ul class="space-y-4 text-white/90 text-sm">
                        <li class="flex items-start">
                            <i class="fas fa-clock text-yellow-300 mr-2 mt-1"></i>
                            Early applications encouraged — limited seats available
                        </li>
                        <li class="flex items-start">
                            <i class="fas fa-star text-yellow-300 mr-2 mt-1"></i>
                            Priority for applications before <strong>April 19</strong>
                        </li>
                        <li class="flex items-start">
                            <i class="fas fa-award text-yellow-300 mr-2 mt-1"></i>
                            Scholarships processed separately
                        </li>
                    </ul>
                </div>

                <!-- Contact -->
                <div class="bg-white p-7 rounded-2xl shadow-lg border border-gray-100">
                    <h3 class="font-serif text-xl font-bold text-primary mb-5 flex items-center">
                        <i class="fas fa-headset text-accent mr-3"></i> Admissions Office
                    </h3>

                    <div class="space-y-4 text-sm">
                        <div class="p-3 bg-red-50 rounded-xl flex items-center">
                            <i class="fas fa-phone text-accent mr-3"></i>
                            +91-7618040040
                        </div>
                        <div class="p-3 bg-blue-50 rounded-xl flex items-center">
                            <i class="fas fa-envelope text-accent mr-3"></i>
                            info@ourgurukul.org
                        </div>
                        <div class="p-3 bg-green-50 rounded-xl flex items-center">
                            <i class="fas fa-clock text-accent mr-3"></i>
                            Mon - Sat: 9:00 AM - 5:00 PM
                        </div>
                    </div>

                    <a href="<?php echo $base_url; ?>/contact.php" class="mt-6 block text-center bg-accent hover:bg-red-700 text-white py-3 rounded-xl font-semibold transition">
                        <i class="fas fa-map-marker-alt mr-2"></i> Visit Campus
                    </a>
                </div>

            </div>

        </div>
    </div>
</section>


<!-- Fee Structure Section -->
<section id="fees" class="py-16 md:py-20 bg-gradient-to-b from-white to-gray-50">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <!-- Section Header -->
        <div class="text-center mb-12 md:mb-16">
            <div class="inline-flex items-center justify-center w-20 h-20 rounded-2xl bg-gradient-to-br from-accent/10 to-accent/5 mb-6">
                <i class="fas fa-indian-rupee-sign text-accent text-2xl"></i>
            </div>
            <h2 class="font-serif text-3xl md:text-4xl lg:text-5xl font-bold text-primary mb-4">
                Transparent & <span class="text-accent">Affordable Fees</span>
            </h2>
            <p class="text-lg md:text-xl text-gray-600 max-w-3xl mx-auto">
                Clear pricing with flexible payment options, scholarships, and financial assistance programs available.
            </p>
        </div>

        <!-- Fee Cards Grid - 2 per row on desktop -->
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 md:gap-8 mb-12 md:mb-16">
            <?php if (!empty($fee_structure)): ?>
                <?php
                $cardColors = [
                    'from-blue-500 to-blue-600',
                    'from-emerald-500 to-emerald-600',
                    'from-purple-500 to-purple-600',
                    'from-amber-500 to-amber-600'
                ];
                $iconColors = ['text-blue-100', 'text-emerald-100', 'text-purple-100', 'text-amber-100'];
                ?>

                <?php foreach ($fee_structure as $index => $fee):
                    $colorClass = $cardColors[$index % count($cardColors)];
                    $iconClass = $iconColors[$index % count($iconColors)];
                ?>
                    <div class="group relative">
                        <!-- Floating Badge -->
                        <div class="absolute -top-3 left-1/2 transform -translate-x-1/2 z-10">
                            <span class="inline-flex items-center px-4 py-1.5 rounded-full bg-white border border-gray-200 shadow-lg">
                                <span class="w-2 h-2 rounded-full bg-green-500 mr-2 animate-pulse"></span>
                                <span class="text-sm font-semibold text-primary">Admission Open</span>
                            </span>
                        </div>

                        <!-- Fee Card -->
                        <div class="bg-white rounded-2xl shadow-xl hover:shadow-2xl transition-all duration-300 overflow-hidden border border-gray-200 h-full">
                            <!-- Card Header -->
                            <div class="bg-gradient-to-r <?php echo $colorClass; ?> text-white p-6 md:p-8 relative overflow-hidden">
                                <!-- Pattern Background -->
                                <div class="absolute inset-0 opacity-10">
                                    <div class="absolute top-4 left-4 w-12 h-12 bg-white rounded-full"></div>
                                    <div class="absolute bottom-4 right-4 w-16 h-16 bg-white rounded-full"></div>
                                    <div class="absolute top-1/2 left-1/4 w-8 h-8 bg-white rounded-full"></div>
                                </div>

                                <div class="relative z-10">
                                    <div class="flex items-center justify-between mb-4">
                                        <div class="w-14 h-14 rounded-xl bg-white/20 backdrop-blur-sm flex items-center justify-center">
                                            <i class="fas fa-graduation-cap text-2xl <?php echo $iconClass; ?>"></i>
                                        </div>
                                        <span class="px-3 py-1 bg-white/20 backdrop-blur-sm rounded-full text-sm font-semibold">
                                            <!-- Grade <?php echo $index + 1; ?> -->
                                        </span>
                                    </div>

                                    <h3 class="font-serif text-xl md:text-2xl font-bold mb-2"><?php echo htmlspecialchars($fee['grade_level']); ?></h3>
                                    <div class="flex items-center text-white/90">
                                        <i class="fas fa-calendar-alt mr-2"></i>
                                        <span>Annual Fee Structure</span>
                                    </div>
                                </div>
                            </div>

                            <!-- Card Body -->
                            <div class="p-6 md:p-8">
                                <!-- Total Fee Display -->
                                <div class="text-center mb-6">
                                    <div class="inline-flex items-baseline bg-gradient-to-r from-gray-50 to-white rounded-2xl p-4 md:p-6 shadow-inner">
                                        <span class="text-4xl md:text-5xl font-bold text-primary">₹<?php echo number_format($fee['total_fee']); ?></span>
                                        <span class="text-gray-500 ml-2">/ year</span>
                                    </div>
                                    <p class="text-sm text-gray-500 mt-2">
                                        <i class="fas fa-info-circle mr-1"></i>
                                        Inclusive of all academic charges
                                    </p>
                                </div>

                                <!-- Fee Breakdown -->
                                <div class="mb-6">
                                    <h4 class="font-bold text-gray-800 mb-4 flex items-center">
                                        <i class="fas fa-receipt text-accent mr-2"></i>
                                        Fee Breakdown
                                    </h4>

                                    <div class="space-y-3">
                                        <!-- Admission Fee -->
                                        <div class="flex items-center justify-between p-3 bg-blue-50 rounded-xl hover:bg-blue-100 transition-colors">
                                            <div class="flex items-center">
                                                <div class="w-8 h-8 rounded-lg bg-blue-100 flex items-center justify-center mr-3">
                                                    <i class="fas fa-file-signature text-blue-600 text-sm"></i>
                                                </div>
                                                <div>
                                                    <div class="font-semibold text-gray-800 text-sm">Admission Fee</div>
                                                    <div class="text-xs text-gray-500">One-time</div>
                                                </div>
                                            </div>
                                            <div class="text-right">
                                                <div class="font-bold text-primary">₹<?php echo number_format($fee['admission_fee']); ?></div>
                                            </div>
                                        </div>

                                        <!-- Tuition Fee -->
                                        <div class="flex items-center justify-between p-3 bg-emerald-50 rounded-xl hover:bg-emerald-100 transition-colors">
                                            <div class="flex items-center">
                                                <div class="w-8 h-8 rounded-lg bg-emerald-100 flex items-center justify-center mr-3">
                                                    <i class="fas fa-book-open text-emerald-600 text-sm"></i>
                                                </div>
                                                <div>
                                                    <div class="font-semibold text-gray-800 text-sm">Tuition Fee</div>
                                                    <div class="text-xs text-gray-500">Quarterly</div>
                                                </div>
                                            </div>
                                            <div class="text-right">
                                                <div class="font-bold text-primary">₹<?php echo number_format($fee['tuition_fee']); ?></div>
                                            </div>
                                        </div>

                                        <!-- Development Fee -->
                                        <div class="flex items-center justify-between p-3 bg-purple-50 rounded-xl hover:bg-purple-100 transition-colors">
                                            <div class="flex items-center">
                                                <div class="w-8 h-8 rounded-lg bg-purple-100 flex items-center justify-center mr-3">
                                                    <i class="fas fa-building text-purple-600 text-sm"></i>
                                                </div>
                                                <div>
                                                    <div class="font-semibold text-gray-800 text-sm">Development Fee</div>
                                                    <div class="text-xs text-gray-500">Annual</div>
                                                </div>
                                            </div>
                                            <div class="text-right">
                                                <div class="font-bold text-primary">₹<?php echo number_format($fee['development_fee']); ?></div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- CTA Button -->
                                <a href="apply.php?grade=<?php echo urlencode($fee['grade_level']); ?>"
                                    class="block w-full bg-gradient-to-r from-primary to-accent text-white font-semibold py-3 px-4 rounded-xl hover:shadow-lg transition-all duration-300 text-center group relative overflow-hidden">
                                    <div class="absolute inset-0 bg-white/20 transform -translate-x-full group-hover:translate-x-full transition-transform duration-1000"></div>
                                    <span class="relative flex items-center justify-center text-sm md:text-base">
                                        <i class="fas fa-file-import mr-2"></i>
                                        Apply Now
                                    </span>
                                </a>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php else: ?>
                <!-- Empty State -->
                <div class="lg:col-span-2">
                    <div class="bg-gradient-to-br from-gray-50 to-white rounded-2xl p-12 text-center border-2 border-dashed border-gray-300">
                        <div class="w-24 h-24 rounded-full bg-gray-100 flex items-center justify-center mx-auto mb-6">
                            <i class="fas fa-file-invoice-dollar text-gray-400 text-4xl"></i>
                        </div>
                        <h3 class="text-2xl font-bold text-gray-700 mb-4">Fee Structure Coming Soon</h3>
                        <p class="text-gray-600 max-w-md mx-auto mb-8">
                            We are finalizing our fee structure for the upcoming academic year. Please contact our admissions office for current fee details.
                        </p>
                        <a href="contact.php" class="inline-flex items-center bg-accent hover:bg-red-700 text-white font-semibold py-3 px-6 rounded-lg transition-colors duration-300">
                            <i class="fas fa-phone-alt mr-2"></i>
                            Contact Admissions
                        </a>
                    </div>
                </div>
            <?php endif; ?>
        </div>

        <!-- Additional Information - Now 2 per row -->
       

            <!-- Payment Plans -->
         
        </div>

        <!-- Contact & Disclaimer - Full width -->
        <div class="grid grid-cols-1 gap-6 md:gap-8 mt-6 md:mt-8">
            <!-- Contact Support -->
            <div class="bg-gradient-to-br from-purple-50 to-violet-100 rounded-2xl p-6 border border-purple-200">
                <div class="flex items-start gap-4 mb-6">
                    <div class="w-12 h-12 rounded-xl bg-purple-100 flex items-center justify-center flex-shrink-0">
                        <i class="fas fa-headset text-purple-600 text-xl"></i>
                    </div>
                    <div class="flex-1">
                        <h3 class="font-bold text-purple-800 text-lg mb-2">Need Assistance?</h3>
                        <p class="text-purple-700 text-sm mb-4">Our finance team is here to help with all fee-related queries</p>
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                            <div class="flex items-center gap-3 bg-white/50 rounded-lg p-3">
                                <div class="w-10 h-10 rounded-lg bg-purple-100 flex items-center justify-center">
                                    <i class="fas fa-phone text-purple-600"></i>
                                </div>
                                <div>
                                    <div class="font-semibold text-purple-800 text-sm">+91-7618040040</div>
                                    <div class="text-xs text-purple-600">Fee Queries</div>
                                </div>
                            </div>
                            <div class="flex items-center gap-3 bg-white/50 rounded-lg p-3">
                                <div class="w-10 h-10 rounded-lg bg-purple-100 flex items-center justify-center">
                                    <i class="fas fa-envelope text-purple-600"></i>
                                </div>
                                <div>
                                    <div class="font-semibold text-purple-800 text-sm">fees@ourgurukul.org</div>
                                    <div class="text-xs text-purple-600">Email Support</div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                
            </div>

            <!-- Disclaimer -->
           
        </div>
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
                <?php if (!empty($timeline)): ?>
                    <?php foreach ($timeline as $index => $event):
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
                                    <?php if (!$is_past): ?>
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

<?php include '../../../includes/footer.php'; ?>

<style>
    .hover-lift {
        transition: transform 0.3s ease, box-shadow 0.3s ease;
    }

    .hover-lift:hover {
        transform: translateY(-5px);
        box-shadow: 0 20px 40px rgba(0, 0, 0, 0.1);
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

        .flex.items-start.group>div:first-child {
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