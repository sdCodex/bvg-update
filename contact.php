<?php
// Include database connection
include './includes/db.php';

// Handle form submission
$success_message = '';
$error_message = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        // Validate required fields
        $required_fields = ['name', 'email', 'subject', 'message'];
        $missing_fields = [];
        
        foreach ($required_fields as $field) {
            if (empty(trim($_POST[$field] ?? ''))) {
                $missing_fields[] = $field;
            }
        }
        
        if (!empty($missing_fields)) {
            $error_message = "Please fill in all required fields: " . implode(', ', $missing_fields);
        } else {
            // Sanitize inputs
            $name = htmlspecialchars(trim($_POST['name']));
            $email = htmlspecialchars(trim($_POST['email']));
            $phone = htmlspecialchars(trim($_POST['phone'] ?? ''));
            $subject = htmlspecialchars(trim($_POST['subject']));
            $message = htmlspecialchars(trim($_POST['message']));
            
            // First, ensure the contact_messages table exists with correct structure
            $pdo->exec("CREATE TABLE IF NOT EXISTS contact_messages (
                id INT PRIMARY KEY AUTO_INCREMENT,
                name VARCHAR(255) NOT NULL,
                email VARCHAR(255) NOT NULL,
                phone VARCHAR(20),
                subject VARCHAR(500) NOT NULL,
                message TEXT NOT NULL,
                created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
            )");
            
            // Prepare and execute insert statement - EXACT field names matching your table
            $stmt = $pdo->prepare("INSERT INTO contact_messages 
                (name, email, phone, subject, message) 
                VALUES (?, ?, ?, ?, ?)");
            
            $result = $stmt->execute([$name, $email, $phone, $subject, $message]);
            
            if ($result && $stmt->rowCount() > 0) {
                $success_message = "Thank you for your message! We will get back to you within 24 hours.";
                
                // Clear form fields
                $_POST = [];
            } else {
                $error_message = "Failed to save your message. Please try again.";
            }
        }
    } catch (PDOException $e) {
        error_log("Contact form error: " . $e->getMessage());
        $error_message = "There was an error sending your message. Please try again or contact us directly.";
    }
}
?>
<title>Bhaktivedanta Gurukul - School of Excellence</title>

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
<!--<meta property="og:image" content="<?php echo $base_url; ?>/images/bvgBanner.jpg">-->
<meta property="og:url" content="https://bhaktivedantagurukul.com/">
<meta property="og:type" content="website">
<meta property="og:site_name" content="Bhaktivedanta Gurukul">

<!-- 🐦 Twitter Card -->
<meta name="twitter:card" content="summary_large_image">
<meta name="twitter:title" content="Bhaktivedanta Gurukul School of Excellence">
<meta name="twitter:description" content="A unique blend of modern academics and spiritual learning.">
<!--<meta name="twitter:image" content="<?php echo $base_url; ?>/images/bvgBanner.jpg">-->

<!-- 🎨 Theme Color (Mobile Tab Color) -->
<meta name="theme-color" content="#DC143C">

<!-- ⚡ PERFORMANCE OPTIMIZATION -->
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@400;600;700&family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">


<!-- 🖼️ Favicon -->
<link rel="icon" type="image/png" href="<?php echo $base_url; ?>/images/bvgLogo.png">

<?php include './includes/header.php'; ?>

<!-- Hero Section -->
<section class="relative bg-gradient-to-br from-[#800000] via-[#1E3A5F] to-[#3E2723] text-white overflow-hidden">
    <!-- Background Elements -->
    <div class="absolute inset-0 opacity-10">
        <div class="absolute top-20 left-20 w-32 h-32 bg-white rounded-full animate-pulse"></div>
        <div class="absolute bottom-20 right-20 w-24 h-24 bg-white rounded-full animate-bounce"></div>
        <div class="absolute top-1/2 left-1/3 w-20 h-20 bg-white rounded-full animate-ping"></div>
    </div>
    
    <div class="max-w-6xl mx-auto px-4 py-20 text-center relative z-10">
        <!-- Breadcrumb -->
        <div class="flex justify-center mb-8">
            <nav class="flex items-center space-x-2 text-white/80 text-sm">
                <a href="<?php echo $base_url; ?>/index.php" class="hover:text-yellow-300 transition-colors duration-300">Home</a>
                <span class="text-white/60">/</span>
                <span class="text-yellow-300 font-semibold">Contact Us</span>
            </nav>
        </div>
        
        <h1 class="text-4xl md:text-5xl font-bold mb-6 leading-tight">
            Get In <span class="text-yellow-300">Touch</span> With Us
        </h1>
        <p class="text-xl text-gray-200 max-w-2xl mx-auto leading-relaxed">
            Ready to begin your educational journey? We're here to answer all your questions about admissions and campus life.
        </p>
        
        <!-- Quick Stats -->
        <div class="grid grid-cols-2 md:grid-cols-4 gap-6 mt-12 max-w-xl mx-auto">
            <div class="text-center p-4 bg-white/10 rounded-2xl backdrop-blur-sm border border-white/20">
                <div class="text-2xl font-bold text-yellow-300 mb-2">24hrs</div>
                <div class="text-gray-300 text-sm font-medium">Response Time</div>
            </div>
            <div class="text-center p-4 bg-white/10 rounded-2xl backdrop-blur-sm border border-white/20">
                <div class="text-2xl font-bold text-[#FFD700] mb-2">100%</div>
                <div class="text-gray-300 text-sm font-medium">Secure</div>
            </div>
            <div class="text-center p-4 bg-white/10 rounded-2xl backdrop-blur-sm border border-white/20">
                <div class="text-2xl font-bold text-[#87CEEB] mb-2">3</div>
                <div class="text-gray-300 text-sm font-medium">Ways to Contact</div>
            </div>
            <div class="text-center p-4 bg-white/10 rounded-2xl backdrop-blur-sm border border-white/20">
                <div class="text-2xl font-bold text-[#90EE90] mb-2">Free</div>
                <div class="text-gray-300 text-sm font-medium">Consultation</div>
            </div>
        </div>
    </div>
</section>

<!-- Contact Information & Form -->
<section class="py-16 bg-gradient-to-br from-gray-50 to-blue-50">
    <div class="max-w-6xl mx-auto px-4">
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
            <!-- Contact Information -->
            <div class="lg:col-span-1 space-y-6">
                <div>
                    <span class="inline-flex items-center px-3 py-1 rounded-full bg-[#800000]/10 text-[#800000] font-semibold text-sm mb-3">
                        <i class="fas fa-info-circle mr-2"></i> Contact Info
                    </span>
                    <h2 class="text-2xl font-bold text-gray-900 mb-4">How to Reach Us</h2>
                    <p class="text-gray-600 leading-relaxed">
                        Multiple ways to get in touch with our admission team. We're always happy to help!
                    </p>
                </div>
                
                <!-- Contact Cards -->
                <div class="space-y-4">
                    <!-- Address -->
                    <div class="bg-white rounded-xl p-5 border border-gray-200 hover:shadow-lg transition-all duration-300 hover:border-[#1E3A5F]">
                        <div class="flex items-start space-x-3">
                            <div class="w-10 h-10 bg-gradient-to-br from-[#800000] to-[#1E3A5F] rounded-lg flex items-center justify-center flex-shrink-0">
                                <i class="fas fa-map-marker-alt text-white text-sm"></i>
                            </div>
                            <div class="flex-1">
                                <h3 class="font-semibold text-gray-900 mb-1">Our Campus</h3>
                                <p class="text-gray-600 text-sm leading-relaxed">
                                    Bhaktivedanta Gurukul<br>
                                    Near ISKCON Prayagraj<br>
                                    Mutthi Ganj, Prayagraj<br>
                                    Uttar Pradesh - 211003
                                </p>
                                <a href="https://maps.app.goo.gl/vsJxdLrZ6XBZ2PLM8" 
                                   class="inline-flex items-center text-[#1E3A5F] hover:text-[#800000] font-medium mt-2 text-xs transition-colors">
                                    <i class="fas fa-directions mr-1"></i> Get Directions
                                </a>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Phone -->
                    <div class="bg-white rounded-xl p-5 border border-gray-200 hover:shadow-lg transition-all duration-300 hover:border-[#3E2723]">
                        <div class="flex items-start space-x-3">
                            <div class="w-10 h-10 bg-gradient-to-br from-[#3E2723] to-[#800000] rounded-lg flex items-center justify-center flex-shrink-0">
                                <i class="fas fa-phone text-white text-sm"></i>
                            </div>
                            <div class="flex-1">
                                <h3 class="font-semibold text-gray-900 mb-2">Call Us</h3>
                                <div class="space-y-1">
                                    <a href="tel:+917618040040" class="block text-[#3E2723] hover:text-[#800000] font-semibold text-sm transition-colors">
                                        +91 7618040040
                                    </a>
                                    <a href="tel:+915912591091" class="block text-[#3E2723] hover:text-[#800000] font-semibold text-sm transition-colors">
                                        +91 91295 91091
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Email -->
                    <div class="bg-white rounded-xl p-5 border border-gray-200 hover:shadow-lg transition-all duration-300 hover:border-[#1E3A5F]">
                        <div class="flex items-start space-x-3">
                            <div class="w-10 h-10 bg-gradient-to-br from-[#1E3A5F] to-[#3E2723] rounded-lg flex items-center justify-center flex-shrink-0">
                                <i class="fas fa-envelope text-white text-sm"></i>
                            </div>
                            <div class="flex-1">
                                <h3 class="font-semibold text-gray-900 mb-2">Email Us</h3>
                                <div class="space-y-1">
                                    <a href="mailto:info@ourgurukul.org" class="block text-[#1E3A5F] hover:text-[#800000] font-semibold text-xs transition-colors">
                                        info@ourgurukul.org
                                    </a>
                                    <a href="mailto:principal@ourgurukul.org" class="block text-[#1E3A5F] hover:text-[#800000] font-semibold text-xs transition-colors">
                                        principal@ourgurukul.org
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Hours -->
                    <div class="bg-white rounded-xl p-5 border border-gray-200 hover:shadow-lg transition-all duration-300 hover:border-[#800000]">
                        <div class="flex items-start space-x-3">
                            <div class="w-10 h-10 bg-gradient-to-br from-[#800000] to-[#3E2723] rounded-lg flex items-center justify-center flex-shrink-0">
                                <i class="fas fa-clock text-white text-sm"></i>
                            </div>
                            <div class="flex-1">
                                <h3 class="font-semibold text-gray-900 mb-2">Office Hours</h3>
                                <div class="space-y-1 text-gray-600 text-sm">
                                    <div class="flex justify-between">
                                        <span>Mon - Sat:</span>
                                        <span class="font-semibold text-[#800000]">8:00 AM - 2:00 PM</span>
                                    </div>
                                    <div class="flex justify-between">
                                        <span>Sunday:</span>
                                        <span class="font-semibold text-[#1E3A5F]">9:00 AM - 1:00 PM</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Contact Form -->
            <div class="lg:col-span-2">
                <div class="bg-white rounded-2xl shadow-xl border border-gray-200 overflow-hidden">
                    <!-- Form Header -->
                    <div class="bg-gradient-to-r from-[#800000] via-[#1E3A5F] to-[#3E2723] text-white p-6">
                        <div class="flex items-center justify-between">
                            <div>
                                <h2 class="text-2xl font-bold mb-2">
                                    Send Us a Message
                                </h2>
                                <p class="text-[#FFD700] text-sm">
                                    Fill out the form below and we'll get back to you soon
                                </p>
                            </div>
                            <div class="hidden md:block">
                                <div class="bg-white/20 rounded-lg p-2 text-center">
                                    <i class="fas fa-paper-plane text-lg mb-1"></i>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Form Content -->
                    <div class="p-6">
                        <?php if ($success_message): ?>
                        <div class="bg-gradient-to-r from-green-50 to-emerald-50 border border-green-200 rounded-xl p-4 mb-6">
                            <div class="flex items-center">
                                <div class="w-8 h-8 bg-green-100 rounded-lg flex items-center justify-center mr-3">
                                    <i class="fas fa-check text-green-600"></i>
                                </div>
                                <div>
                                    <h3 class="font-semibold text-green-800">Success!</h3>
                                    <p class="text-green-700 text-sm"><?php echo $success_message; ?></p>
                                </div>
                            </div>
                        </div>
                        <?php endif; ?>

                        <?php if ($error_message): ?>
                        <div class="bg-gradient-to-r from-red-50 to-pink-50 border border-red-200 rounded-xl p-4 mb-6">
                            <div class="flex items-center">
                                <div class="w-8 h-8 bg-red-100 rounded-lg flex items-center justify-center mr-3">
                                    <i class="fas fa-exclamation-triangle text-red-600"></i>
                                </div>
                                <div>
                                    <h3 class="font-semibold text-red-800">Error</h3>
                                    <p class="text-red-700 text-sm"><?php echo $error_message; ?></p>
                                </div>
                            </div>
                        </div>
                        <?php endif; ?>

                        <form method="POST" class="space-y-6" id="contact-form">
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <!-- Name Field -->
                                <div class="form-group">
                                    <label for="name" class="block text-sm font-semibold text-gray-700 mb-2">
                                        Full Name <span class="text-red-500">*</span>
                                    </label>
                                    <div class="relative">
                                        <input type="text" id="name" name="name" required 
                                            value="<?php echo htmlspecialchars($_POST['name'] ?? ''); ?>"
                                            class="w-full px-4 py-3 pl-10 border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#800000] focus:border-[#800000] transition-all duration-200"
                                            placeholder="Enter your full name">
                                        <div class="absolute left-3 top-1/2 transform -translate-y-1/2 text-gray-400">
                                            <i class="fas fa-user"></i>
                                        </div>
                                    </div>
                                </div>
                                
                                <!-- Email Field -->
                                <div class="form-group">
                                    <label for="email" class="block text-sm font-semibold text-gray-700 mb-2">
                                        Email Address <span class="text-red-500">*</span>
                                    </label>
                                    <div class="relative">
                                        <input type="email" id="email" name="email" required 
                                            value="<?php echo htmlspecialchars($_POST['email'] ?? ''); ?>"
                                            class="w-full px-4 py-3 pl-10 border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#1E3A5F] focus:border-[#1E3A5F] transition-all duration-200"
                                            placeholder="Enter your email">
                                        <div class="absolute left-3 top-1/2 transform -translate-y-1/2 text-gray-400">
                                            <i class="fas fa-envelope"></i>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <!-- Phone Field -->
                                <!-- Phone Field -->
                                <div class="form-group">
                                    <label for="phone" required class="block text-sm font-semibold text-gray-700 mb-2">
                                        Phone Number<span class="text-red-500">*</span>
                                    </label>
                                    <div class="relative">
                                        <input type="tel" id="phone" name="phone" 
                                            value="<?php echo htmlspecialchars($_POST['phone'] ?? ''); ?>"
                                            class="w-full px-4 py-3 pl-10 border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#3E2723] focus:border-[#3E2723] transition-all duration-200"
                                            placeholder="Enter your phone number">
                                        <div class="absolute left-3 top-1/2 transform -translate-y-1/2 text-gray-400">
                                            <i class="fas fa-phone"></i>
                                        </div>
                                    </div>
                                </div>
                                
                                <!-- Subject Field -->
                                <div class="form-group">
                                    <label for="subject" class="block text-sm font-semibold text-gray-700 mb-2">
                                        Subject <span class="text-red-500">*</span>
                                    </label>
                                    <div class="relative">
                                        <select id="subject" name="subject" required 
                                            class="w-full px-4 py-3 pl-10 border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#800000] focus:border-[#800000] transition-all duration-200 appearance-none bg-white">
                                            <option value="">Select a subject</option>
                                            <option value="Admission Inquiry" <?php echo (isset($_POST['subject']) && $_POST['subject'] == 'Admission Inquiry') ? 'selected' : ''; ?>>Admission Inquiry</option>
                                            <option value="Scholarship Information" <?php echo (isset($_POST['subject']) && $_POST['subject'] == 'Scholarship Information') ? 'selected' : ''; ?>>Scholarship Information</option>
                                            <option value="Campus Visit" <?php echo (isset($_POST['subject']) && $_POST['subject'] == 'Campus Visit') ? 'selected' : ''; ?>>Schedule Campus Visit</option>
                                            <option value="Academic Programs" <?php echo (isset($_POST['subject']) && $_POST['subject'] == 'Academic Programs') ? 'selected' : ''; ?>>Academic Programs</option>
                                            <option value="Fee Structure" <?php echo (isset($_POST['subject']) && $_POST['subject'] == 'Fee Structure') ? 'selected' : ''; ?>>Fee Structure</option>
                                            <option value="Other" <?php echo (isset($_POST['subject']) && $_POST['subject'] == 'Other') ? 'selected' : ''; ?>>Other Inquiry</option>
                                        </select>
                                        <div class="absolute left-3 top-1/2 transform -translate-y-1/2 text-gray-400">
                                            <i class="fas fa-tag"></i>
                                        </div>
                                        <div class="absolute right-3 top-1/2 transform -translate-y-1/2 text-gray-400 pointer-events-none">
                                            <i class="fas fa-chevron-down"></i>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            
                            <!-- Message Field -->
                            <div class="form-group">
                                <label for="message" class="block text-sm font-semibold text-gray-700 mb-2">
                                    Message <span class="text-red-500">*</span>
                                </label>
                                <div class="relative">
                                    <textarea id="message" name="message" required rows="5"
                                        class="w-full px-4 py-3 pl-10 border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#1E3A5F] focus:border-[#1E3A5F] transition-all duration-200 resize-none"
                                        placeholder="Tell us about your inquiry..."><?php echo htmlspecialchars($_POST['message'] ?? ''); ?></textarea>
                                    <div class="absolute left-3 top-3 transform text-gray-400">
                                        <i class="fas fa-comment"></i>
                                    </div>
                                </div>
                            </div>
                            
                            <!-- Submit Button -->
                            <div class="pt-4">
                                <button type="submit" 
                                        class="w-full bg-gradient-to-r from-[#800000] via-[#1E3A5F] to-[#3E2723] hover:from-[#600000] hover:via-[#152A47] hover:to-[#2D1B1A] text-white font-semibold py-3 px-6 rounded-lg transition-all duration-200 transform hover:scale-105 shadow-lg hover:shadow-xl inline-flex items-center justify-center submit-button">
                                    <i class="fas fa-paper-plane mr-2"></i> 
                                    <span class="submit-text">Send Message</span>
                                    <i class="fas fa-spinner fa-spin ml-2 hidden loading-icon"></i>
                                </button>
                            </div>
                            
                            <!-- Form Footer -->
                            <div class="text-center pt-4 border-t border-gray-200">
                                <p class="text-xs text-gray-500">
                                    <i class="fas fa-lock text-green-500 mr-1"></i>
                                    Your information is secure and confidential
                                </p>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Simple FAQ Section -->
<section class="py-16 bg-white">
    <div class="max-w-4xl mx-auto px-4">
        <div class="text-center mb-12">
            <h2 class="text-3xl font-bold text-gray-900 mb-4">Common Questions</h2>
            <p class="text-gray-600 max-w-2xl mx-auto">Quick answers to frequently asked questions</p>
        </div>
        
        <div class="space-y-4">
            <?php
            $faqs = [
                [
                    'question' => 'What is the admission process?',
                    'answer' => 'Our admission process includes online application, student assessment, document verification, and final confirmation.'
                ],
                [
                    'question' => 'Do you offer hostel facilities?',
                    'answer' => 'Yes, we provide safe and comfortable hostel facilities for both boys and girls with proper supervision.'
                ],
                [
                    'question' => 'What is the student-teacher ratio?',
                    'answer' => 'We maintain a healthy 1:20 ratio to ensure personalized attention for every student.'
                ]
            ];
            
            foreach ($faqs as $index => $faq):
            ?>
            <div class="bg-gray-50 rounded-lg p-4 border border-gray-200 hover:border-[#7E0001] transition-all duration-300">
                <button class="faq-question w-full text-left flex justify-between items-center group">
                    <h3 class="font-semibold text-gray-900 text-lg"><?php echo $faq['question']; ?></h3>
                    <i class="fas fa-chevron-down text-[#7E0001] transition-transform duration-300 group-hover:rotate-180"></i>
                </button>
                <div class="faq-answer overflow-hidden transition-all duration-300 max-h-0">
                    <div class="pt-3 text-gray-600 leading-relaxed border-t border-gray-200 mt-3">
                        <?php echo $faq['answer']; ?>
                    </div>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
    </div>
</section>

<!-- Final CTA -->
<section class="py-16 bg-gradient-to-r from-[#023264] to-[#7E0001] text-white">
    <div class="max-w-4xl mx-auto px-4 text-center">
        <h2 class="text-3xl font-bold mb-4">Ready to Get Started?</h2>
        <p class="text-[#FFD700] text-lg mb-8 max-w-2xl mx-auto">
            Contact us today to begin your educational journey at Bhaktivedanta Gurukul
        </p>
        <div class="flex flex-col sm:flex-row gap-4 justify-center">
            <a href="tel:+917618040040" 
               class="bg-white text-[#7E0001] hover:bg-gray-100 font-semibold py-3 px-6 rounded-lg transition-all duration-200 inline-flex items-center hover:scale-105">
                <i class="fas fa-phone mr-2"></i> Call Now
            </a>
            <a href="mailto:info@ourgurukul.org" 
               class="border-2 border-white text-white hover:bg-white hover:text-[#023264] font-semibold py-3 px-6 rounded-lg transition-all duration-200 inline-flex items-center hover:scale-105">
                <i class="fas fa-envelope mr-2"></i> Send Email
            </a>
        </div>
    </div>
</section>

<?php include './includes/footer.php'; ?>

<style>
.form-input:focus, .form-select:focus, .form-textarea:focus {
    box-shadow: 0 0 0 3px rgba(128, 0, 0, 0.1);
}

.submit-button:disabled {
    opacity: 0.7;
    cursor: not-allowed;
    transform: none !important;
}

.faq-item.active .faq-answer {
    max-height: 500px !important;
}

.faq-item.active .faq-question i {
    transform: rotate(180deg);
}

/* Custom select arrow */
.form-select {
    background-image: url("data:image/svg+xml,%3csvg xmlns='http://www.w3.org/2000/svg' fill='none' viewBox='0 0 20 20'%3e%3cpath stroke='%236b7280' stroke-linecap='round' stroke-linejoin='round' stroke-width='1.5' d='m6 8 4 4 4-4'/%3e%3c/svg%3e");
    background-position: right 0.75rem center;
    background-repeat: no-repeat;
    background-size: 1em 1em;
    padding-right: 2.5rem;
}

/* Custom scrollbar for textarea */
textarea::-webkit-scrollbar {
    width: 6px;
}

textarea::-webkit-scrollbar-track {
    background: #f1f1f1;
    border-radius: 3px;
}

textarea::-webkit-scrollbar-thumb {
    background: linear-gradient(to bottom, #800000, #1E3A5F);
    border-radius: 3px;
}

textarea::-webkit-scrollbar-thumb:hover {
    background: linear-gradient(to bottom, #600000, #152A47);
}

/* Smooth transitions */
* {
    transition-property: color, background-color, border-color, transform, box-shadow;
    transition-duration: 300ms;
    transition-timing-function: cubic-bezier(0.4, 0, 0.2, 1);
}
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const contactForm = document.getElementById('contact-form');
    
    // Form validation and submission
    if (contactForm) {
        const submitButton = contactForm.querySelector('.submit-button');
        const submitText = contactForm.querySelector('.submit-text');
        const loadingIcon = contactForm.querySelector('.loading-icon');
        
        // Form submission
        contactForm.addEventListener('submit', function(e) {
            let isValid = true;
            
            // Validate required fields
            const requiredFields = contactForm.querySelectorAll('[required]');
            requiredFields.forEach(field => {
                if (!field.value.trim()) {
                    isValid = false;
                    field.classList.add('border-red-500');
                } else {
                    field.classList.remove('border-red-500');
                }
            });
            
            if (!isValid) {
                e.preventDefault();
                alert('Please fill in all required fields.');
            } else {
                // Add loading state
                submitButton.disabled = true;
                submitText.textContent = 'Sending...';
                loadingIcon.classList.remove('hidden');
            }
        });
    }
    
    // FAQ Accordion
    const faqItems = document.querySelectorAll('.faq-question');
    faqItems.forEach(question => {
        question.addEventListener('click', () => {
            const answer = question.nextElementSibling;
            const isOpen = answer.style.maxHeight && answer.style.maxHeight !== '0px';
            
            // Close all FAQs
            faqItems.forEach(q => {
                const a = q.nextElementSibling;
                a.style.maxHeight = '0';
            });
            
            // Open current if it was closed
            if (!isOpen) {
                answer.style.maxHeight = answer.scrollHeight + 'px';
            }
        });
    });
});
</script>