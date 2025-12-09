<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Start session at the very beginning
session_start();

// Database connection and base URL setup
$base_url = 'https://bhaktivedantagurukul.com/';

// Include database connection
include_once '../../../includes/db.php';

// Form submission handling
$success_message = '';
$error_message = '';

// Debug: Check if form is being submitted
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    error_log("Form submitted via POST method");
    
    // Debug: Print all POST data
    error_log("POST data: " . print_r($_POST, true));

    try {
        // Sanitize and validate input
        $student_name = isset($_POST['student_name']) ? htmlspecialchars(trim($_POST['student_name'])) : '';
        $father_name = isset($_POST['father_name']) ? htmlspecialchars(trim($_POST['father_name'])) : '';
        $mother_name = isset($_POST['mother_name']) ? htmlspecialchars(trim($_POST['mother_name'])) : '';
        $email = isset($_POST['email']) ? filter_var(trim($_POST['email']), FILTER_VALIDATE_EMAIL) : '';
        $phone = isset($_POST['phone']) ? htmlspecialchars(trim($_POST['phone'])) : '';
        $dob = isset($_POST['dob']) ? htmlspecialchars(trim($_POST['dob'])) : '';
        $gender = isset($_POST['gender']) ? htmlspecialchars(trim($_POST['gender'])) : '';
        $grade = isset($_POST['grade']) ? htmlspecialchars(trim($_POST['grade'])) : '';
        $academic_year = isset($_POST['academic_year']) ? htmlspecialchars(trim($_POST['academic_year'])) : '';
        $address = isset($_POST['address']) ? htmlspecialchars(trim($_POST['address'])) : '';
        $city = isset($_POST['city']) ? htmlspecialchars(trim($_POST['city'])) : '';
        $state = isset($_POST['state']) ? htmlspecialchars(trim($_POST['state'])) : '';
        $pincode = isset($_POST['pincode']) ? htmlspecialchars(trim($_POST['pincode'])) : '';
        $previous_school = isset($_POST['previous_school']) ? htmlspecialchars(trim($_POST['previous_school'])) : '';
        $message = isset($_POST['message']) ? htmlspecialchars(trim($_POST['message'])) : '';

        // Debug: Check individual fields
        error_log("Student Name: " . $student_name);
        error_log("Father Name: " . $father_name);
        error_log("Mother Name: " . $mother_name);
        error_log("Email: " . $email);
        error_log("Phone: " . $phone);

        // Validate required fields
        $required_fields = ['student_name', 'father_name', 'email', 'phone', 'dob', 'gender', 'grade', 'academic_year'];
        $missing_fields = [];
        
        foreach ($required_fields as $field) {
            if (empty($_POST[$field])) {
                $missing_fields[] = $field;
            }
        }
        
        if (!empty($missing_fields)) {
            $error_message = "Please fill all required fields.";
            error_log("Validation failed: Required fields missing - " . implode(', ', $missing_fields));
        } elseif (!$email) {
            $error_message = "Please enter a valid email address.";
            error_log("Validation failed: Invalid email");
        } elseif (!preg_match('/^[0-9]{10}$/', $phone)) {
            $error_message = "Please enter a valid 10-digit phone number.";
            error_log("Validation failed: Invalid phone");
        } else {
            // All validation passed, insert into database
            error_log("All validations passed, inserting into database");
            
            // Fixed campus type for Khargone
            $campus_type = 'Khargone Campus';
            
            // Generate order ID before insertion
            $timestamp = time();
            $random = rand(1000, 9999);
            $order_id = 'KHARG-' . date('Ymd', $timestamp) . '-' . $random;
            
            $stmt = $pdo->prepare("
                INSERT INTO khargone_admissions 
                (student_name, father_name, mother_name, email, phone, dob, gender, grade, program, campus_type, academic_year, address, city, state, pincode, previous_school, message, status, order_id, created_at) 
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, NOW())
            ");
        
            $result = $stmt->execute([
                $student_name,
                $father_name,
                $mother_name,
                $email,
                $phone,
                $dob,
                $gender,
                $grade,
                $campus_type, // program
                $campus_type, // campus_type
                $academic_year,
                $address,
                $city,
                $state,
                $pincode,
                $previous_school,
                $message,
                'pending', // status
                $order_id  // order_id
            ]);

            // Check if insertion was successful
            if ($result) {
                $last_id = $pdo->lastInsertId();
                error_log("Database insertion successful. Last insert ID: " . $last_id);
                error_log("Order ID generated: " . $order_id);
                
                $success_message = "Thank you! Your admission inquiry has been submitted successfully. Please proceed with the ₹500 application fee payment.";

                // Store success message in session for redirect
                $_SESSION['form_success'] = $success_message;
                $_SESSION['admission_id'] = $last_id;
                $_SESSION['campus_type'] = $campus_type;
                $_SESSION['order_id'] = $order_id;
                
                // Clear POST data
                $_POST = array();
                
                // ✅ Direct redirect to payment page with order_id
                header("Location: request.php?order_id=" . urlencode($order_id));
                exit();                
            } else {
                $error_message = "Failed to save your application. Please try again.";
                error_log("Database insertion failed");
            }
        }
    } catch (PDOException $e) {
        $error_message = "Database error: " . $e->getMessage();
        error_log("Database error: " . $e->getMessage());
    }
} else {
    error_log("Form not submitted via POST method. Method: " . $_SERVER['REQUEST_METHOD']);
}

// Get program from URL parameter if available
$selected_program = isset($_GET['program']) ? htmlspecialchars($_GET['program']) : '';
?>

<?php include_once '../../../includes/header.php' ?>

<style>
    :root {
        --primary-color: #003366;
        --secondary-color: #D4AF37;
        --accent-color: #800000;
        --neutral-color: #f7f7f7;
        --text-dark: #1f2937;
    }
    
    /* Custom styles using the color scheme */
    .bg-primary-custom { background-color: #003366; }
    .bg-secondary-custom { background-color: #D4AF37; }
    .bg-accent-custom { background-color: #800000; }
    .bg-neutral-custom { background-color: #f7f7f7; }
    
    .text-primary-custom { color: #003366; }
    .text-secondary-custom { color: #D4AF37; }
    .text-accent-custom { color: #800000; }
    
    .border-primary-custom { border-color: #003366; }
    .border-secondary-custom { border-color: #D4AF37; }
    .border-accent-custom { border-color: #800000; }
    
    .hover\:bg-primary-dark:hover { background-color: #002244; }
    .hover\:bg-accent-dark:hover { background-color: #660000; }
</style>

<!-- 🧩 SEO Optimization -->
<meta name="description" content="Apply for admission to Khargone Campus - Quality education with modern facilities and experienced faculty. Pay ₹500 application fee online.">
<meta name="keywords" content="Khargone School Admission, School Application, Quality Education, CBSE School, Application Fee ₹500">
<meta name="author" content="Educational Institution">
<meta name="robots" content="index, follow">
<meta name="language" content="English">
<meta name="revisit-after" content="7 days">

<!-- 🔗 Canonical (Avoid Duplicate URLs in Google) -->
<link rel="canonical" href="https://institution.com/">

<!-- 🧠 Open Graph for Social Media -->
<meta property="og:title" content="Khargone Campus Admission | Application Fee ₹500">
<meta property="og:description" content="Apply for admission to our premier Khargone Campus with modern facilities and experienced faculty.">
<meta property="og:image" content="<?php echo $base_url; ?>/images/campus-banner.jpg">
<meta property="og:url" content="https://institution.com/">
<meta property="og:type" content="website">
<meta property="og:site_name" content="Khargone Campus">

<!-- 🐦 Twitter Card -->
<meta name="twitter:card" content="summary_large_image">
<meta name="twitter:title" content="Khargone Campus Admission">
<meta name="twitter:description" content="Begin your educational journey with our quality CBSE program.">
<meta name="twitter:image" content="<?php echo $base_url; ?>/images/campus-banner.jpg">

<!-- 🎨 Theme Color (Mobile Tab Color) -->
<meta name="theme-color" content="#003366">

<!-- ⚡ PERFORMANCE OPTIMIZATION -->
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@400;600;700&family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">

<!-- 🖼️ Favicon -->
<link rel="icon" type="image/png" href="<?php echo $base_url; ?>/images/logo.png">

<!-- Hero Section -->
<section class="relative bg-gradient-to-br from-[#003366] via-[#003366] to-[#800000] text-white overflow-hidden">
    <!-- Background Pattern -->
    <div class="absolute inset-0 opacity-10">
        <div class="absolute top-10 left-10 w-20 h-20 bg-[#D4AF37] rounded-full"></div>
        <div class="absolute bottom-10 right-10 w-32 h-32 bg-[#D4AF37] rounded-full"></div>
        <div class="absolute top-1/2 left-1/4 w-16 h-16 bg-[#D4AF37] rounded-full"></div>
    </div>

    <div class="max-w-7xl mx-auto px-4 py-20 text-center relative z-10">
        <!-- Breadcrumb -->
        <div class="flex justify-center mb-8">
            <nav class="flex items-center space-x-2 text-white/80 text-sm">
                <a href="<?php echo $base_url; ?>/index.php" class="hover:text-[#D4AF37] transition-colors">Home</a>
                <span class="text-white/60">/</span>
                <a href="<?php echo $base_url; ?>/pages/admissions/index.php" class="hover:text-[#D4AF37] transition-colors">Admissions</a>
                <span class="text-white/60">/</span>
                <span class="text-white font-medium">Khargone Campus Application</span>
            </nav>
        </div>

        <h1 class="font-serif text-4xl md:text-5xl font-bold mb-6 leading-tight">
            Khargone Campus <span class="text-[#D4AF37]">Admission</span>
        </h1>
        <p class="text-xl text-gray-200 max-w-2xl mx-auto leading-relaxed">
            Apply for CBSE Education with Modern Facilities & Experienced Faculty
        </p>

        <!-- Quick Stats -->
        <div class="grid grid-cols-2 md:grid-cols-4 gap-8 mt-12 max-w-2xl mx-auto">
            <div class="text-center">
                <div class="text-2xl font-bold text-[#D4AF37] mb-2">₹500</div>
                <div class="text-gray-300 text-sm">Application Fee</div>
            </div>
            <div class="text-center">
                <div class="text-2xl font-bold text-[#D4AF37] mb-2">4</div>
                <div class="text-gray-300 text-sm">Simple Steps</div>
            </div>
            <div class="text-center">
                <div class="text-2xl font-bold text-[#D4AF37] mb-2">24hrs</div>
                <div class="text-gray-300 text-sm">Response Time</div>
            </div>
            <div class="text-center">
                <div class="text-2xl font-bold text-[#D4AF37] mb-2">100%</div>
                <div class="text-gray-300 text-sm">Secure</div>
            </div>
        </div>
    </div>
</section>

<!-- Campus Highlight Section -->
<section class="py-16 bg-[#f7f7f7]">
    <div class="max-w-6xl mx-auto px-4">
        <div class="bg-white rounded-2xl shadow-2xl overflow-hidden border border-[#D4AF37]">
            <div class="grid grid-cols-1 lg:grid-cols-2">
                <!-- Left Content - Campus Image & Info -->
                <div class="relative">
                    <div class="h-full bg-gradient-to-br from-[#003366] to-[#004080] p-8 lg:p-12 text-white">
                        <div class="flex items-center mb-6">
                            <div class="w-12 h-12 bg-[#D4AF37] rounded-xl flex items-center justify-center mr-4">
                                <i class="fas fa-school text-[#003366] text-xl"></i>
                            </div>
                            <h2 class="font-serif text-3xl font-bold">Khargone Campus</h2>
                        </div>
                        
                        <p class="text-blue-100 text-lg mb-6 leading-relaxed">
                            Our premier CBSE campus in Khargone offering quality education with modern infrastructure and experienced faculty.
                        </p>

                        <div class="space-y-4 mb-8">
                            <div class="flex items-center">
                                <i class="fas fa-check-circle text-[#D4AF37] mr-3 text-lg"></i>
                                <span class="text-blue-100">CBSE Affiliated Curriculum</span>
                            </div>
                            <div class="flex items-center">
                                <i class="fas fa-check-circle text-[#D4AF37] mr-3 text-lg"></i>
                                <span class="text-blue-100">Modern Smart Classrooms</span>
                            </div>
                            <div class="flex items-center">
                                <i class="fas fa-check-circle text-[#D4AF37] mr-3 text-lg"></i>
                                <span class="text-blue-100">Sports & Playground Facilities</span>
                            </div>
                            <div class="flex items-center">
                                <i class="fas fa-check-circle text-[#D4AF37] mr-3 text-lg"></i>
                                <span class="text-blue-100">Transportation Available</span>
                            </div>
                            <div class="flex items-center">
                                <i class="fas fa-check-circle text-[#D4AF37] mr-3 text-lg"></i>
                                <span class="text-blue-100">Experienced Teaching Staff</span>
                            </div>
                        </div>

                        <!-- Fee Information -->
                        <div class="bg-white/10 rounded-xl p-6 backdrop-blur-sm">
                            <h3 class="font-semibold text-[#D4AF37] mb-3 text-lg">💰 Application Fee</h3>
                            <div class="flex items-center justify-between">
                                <div>
                                    <p class="text-3xl font-bold text-white">₹500</p>
                                    <p class="text-blue-100 text-sm">Non-refundable application fee</p>
                                </div>
                                <div class="text-right">
                                    <p class="text-sm text-blue-100">Required for</p>
                                    <p class="text-white font-semibold">Form Processing</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Right Content - Features -->
                <div class="p-8 lg:p-12">
                    <h3 class="font-serif text-2xl font-bold text-[#003366] mb-6">Why Choose Khargone Campus?</h3>
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div class="bg-gradient-to-br from-blue-50 to-cyan-100 rounded-xl p-6 hover-lift transition-all duration-300">
                            <div class="w-12 h-12 bg-[#003366] rounded-lg flex items-center justify-center mb-4">
                                <i class="fas fa-graduation-cap text-white text-lg"></i>
                            </div>
                            <h4 class="font-semibold text-[#1f2937] mb-2">Quality Education</h4>
                            <p class="text-gray-600 text-sm">CBSE curriculum with modern teaching methodologies.</p>
                        </div>

                        <div class="bg-gradient-to-br from-amber-50 to-yellow-100 rounded-xl p-6 hover-lift transition-all duration-300">
                            <div class="w-12 h-12 bg-[#D4AF37] rounded-lg flex items-center justify-center mb-4">
                                <i class="fas fa-laptop text-[#003366] text-lg"></i>
                            </div>
                            <h4 class="font-semibold text-[#1f2937] mb-2">Smart Classes</h4>
                            <p class="text-gray-600 text-sm">Digital classrooms with interactive learning tools.</p>
                        </div>

                        <div class="bg-gradient-to-br from-green-50 to-emerald-100 rounded-xl p-6 hover-lift transition-all duration-300">
                            <div class="w-12 h-12 bg-[#800000] rounded-lg flex items-center justify-center mb-4">
                                <i class="fas fa-futbol text-white text-lg"></i>
                            </div>
                            <h4 class="font-semibold text-[#1f2937] mb-2">Sports Facilities</h4>
                            <p class="text-gray-600 text-sm">Indoor & outdoor sports for holistic development.</p>
                        </div>

                        <div class="bg-gradient-to-br from-purple-50 to-violet-100 rounded-xl p-6 hover-lift transition-all duration-300">
                            <div class="w-12 h-12 bg-[#003366] rounded-lg flex items-center justify-center mb-4">
                                <i class="fas fa-bus text-white text-lg"></i>
                            </div>
                            <h4 class="font-semibold text-[#1f2937] mb-2">Transport</h4>
                            <p class="text-gray-600 text-sm">Safe and reliable transportation services.</p>
                        </div>
                    </div>

                    <div class="mt-8 pt-6 border-t border-gray-200">
                        <div class="flex items-center text-sm text-gray-600">
                            <i class="fas fa-info-circle text-[#003366] mr-2"></i>
                            <span>Application fee of ₹500 is mandatory for form processing and is non-refundable.</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Progress Steps - Fixed Layout -->
<section class="bg-white border-b border-gray-200 py-8">
    <div class="max-w-4xl mx-auto px-4">
        <!-- Desktop - Single Line -->
        <div class="hidden md:flex items-center justify-between relative">
            <?php
            $steps = [
                ['icon' => 'fas fa-edit', 'label' => 'Application', 'active' => true],
                ['icon' => 'fas fa-rupee-sign', 'label' => 'Pay ₹500', 'active' => false],
                ['icon' => 'fas fa-file-alt', 'label' => 'Documentation', 'active' => false],
                ['icon' => 'fas fa-check-circle', 'label' => 'Confirmation', 'active' => false]
            ];
            ?>
            
            <!-- Progress Line -->
            <div class="absolute top-6 left-0 right-0 h-1 bg-gray-200 -z-10">
                <div class="h-full bg-[#800000] transition-all duration-500" style="width: 25%"></div>
            </div>

            <?php foreach ($steps as $index => $step): ?>
                <div class="flex flex-col items-center relative z-10">
                    <div class="<?php echo $step['active']
                        ? 'bg-[#800000] text-white shadow-lg scale-110 border-2 border-white'
                        : 'bg-gray-200 text-gray-400 border-2 border-white'; ?>
                        w-12 h-12 rounded-full flex items-center justify-center mb-3 text-lg
                        transition-all duration-300 transform hover:scale-105">
                        <i class="<?php echo $step['icon']; ?>"></i>
                    </div>
                    <span class="text-sm font-semibold <?php echo $step['active'] ? 'text-[#800000]' : 'text-gray-500'; ?> text-center whitespace-nowrap">
                        <?php echo $step['label']; ?>
                    </span>
                    <?php if ($step['active']): ?>
                        <div class="absolute -bottom-2 w-3 h-3 bg-[#800000] rounded-full"></div>
                    <?php endif; ?>
                </div>
            <?php endforeach; ?>
        </div>

        <!-- Mobile - Grid Layout -->
        <div class="md:hidden">
            <div class="grid grid-cols-2 gap-6">
                <?php foreach ($steps as $index => $step): ?>
                    <div class="flex flex-col items-center">
                        <div class="<?php echo $step['active']
                            ? 'bg-[#800000] text-white shadow-lg scale-105'
                            : 'bg-gray-200 text-gray-400'; ?>
                            w-12 h-12 rounded-full flex items-center justify-center mb-2 text-lg
                            border-2 border-white transition-all duration-300">
                            <i class="<?php echo $step['icon']; ?>"></i>
                        </div>
                        <span class="text-xs font-semibold <?php echo $step['active'] ? 'text-[#800000]' : 'text-gray-500'; ?> text-center px-2">
                            <?php echo $step['label']; ?>
                        </span>
                    </div>
                <?php endforeach; ?>
            </div>
            
            <!-- Mobile Progress Bar -->
            <div class="mt-6 bg-gray-200 rounded-full h-2">
                <div class="bg-[#800000] h-2 rounded-full transition-all duration-500" style="width: 25%"></div>
            </div>
        </div>
    </div>
</section>

<!-- Main Form Section -->
<section class="py-16 bg-[#f7f7f7]">
    <div class="max-w-4xl mx-auto px-4">
        <?php if ($error_message): ?>
            <div class="bg-red-50 border border-red-200 rounded-2xl p-6 mb-8 animate-fade-in">
                <div class="flex items-start">
                    <div class="w-12 h-12 bg-red-100 rounded-xl flex items-center justify-center mr-4 flex-shrink-0">
                        <i class="fas fa-exclamation-triangle text-red-600 text-lg"></i>
                    </div>
                    <div>
                        <h3 class="font-semibold text-red-800 text-lg mb-2">Submission Error</h3>
                        <p class="text-red-700"><?php echo $error_message; ?></p>
                        <p class="text-red-600 text-sm mt-2">Please check all required fields and try again.</p>
                    </div>
                </div>
            </div>
        <?php endif; ?>

        <div class="bg-white rounded-2xl shadow-xl overflow-hidden border border-gray-200">
            <!-- Form Header -->
            <div class="bg-gradient-to-r from-[#003366] to-[#800000] text-white p-8">
                <div class="flex items-center justify-between">
                    <div>
                        <h2 class="font-serif text-2xl md:text-3xl font-bold mb-2">
                            Khargone Campus Admission Form
                        </h2>
                        <p class="text-blue-100">
                            Complete the form below to begin your admission process
                        </p>
                    </div>
                    <div class="hidden md:block">
                        <div class="bg-white/20 rounded-xl p-4 text-center">
                            <div class="text-2xl font-bold">Step 1</div>
                            <div class="text-sm opacity-90">of 4</div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Form Content -->
            <form method="POST" class="p-8 space-y-8" id="admissionForm">
                <!-- Academic Year Section (Campus Type Removed) -->
                <div class="bg-gradient-to-br from-blue-50 to-cyan-50 rounded-2xl p-6 transition-all duration-300 hover:shadow-md border border-blue-100">
                    <div class="flex items-center mb-6">
                        <div class="w-10 h-10 bg-[#003366] rounded-xl flex items-center justify-center mr-4">
                            <i class="fas fa-calendar-alt text-white"></i>
                        </div>
                        <h3 class="font-serif text-xl font-bold text-[#003366]">Academic Details</h3>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <!-- Campus Type Hidden Field -->
                        <input type="hidden" name="campus_type" value="Khargone Campus">
                        
                        <!-- Academic Year -->
                        <div class="form-group">
                            <label for="academic_year" class="block text-sm font-semibold text-gray-700 mb-3 flex items-center">
                                <span class="flex items-center">
                                    <i class="fas fa-asterisk text-red-500 text-xs mr-1"></i>
                                    Academic Year
                                </span>
                            </label>
                            <div class="relative">
                                <select id="academic_year" name="academic_year" required
                                    class="w-full px-4 py-3 pl-11 border border-gray-300 rounded-xl focus:ring-2 focus:ring-[#003366] focus:border-transparent transition-all duration-300 form-select appearance-none bg-white">
                                    <option value="">Select Academic Year</option>
                                    <option value="2024-2025" <?php echo (isset($_POST['academic_year']) && $_POST['academic_year'] == '2024-2025') ? 'selected' : ''; ?>>2024-2025</option>
                                    <option value="2025-2026" <?php echo (isset($_POST['academic_year']) && $_POST['academic_year'] == '2025-2026') ? 'selected' : ''; ?>>2025-2026</option>
                                    <option value="2026-2027" <?php echo (isset($_POST['academic_year']) && $_POST['academic_year'] == '2026-2027') ? 'selected' : ''; ?>>2026-2027</option>
                                </select>
                                <div class="absolute left-3 top-1/2 transform -translate-y-1/2 text-gray-400">
                                    <i class="fas fa-calendar-alt"></i>
                                </div>
                                <div class="absolute right-3 top-1/2 transform -translate-y-1/2 text-gray-400 pointer-events-none">
                                    <i class="fas fa-chevron-down"></i>
                                </div>
                            </div>
                            <div class="error-message text-red-500 text-sm mt-1 hidden" id="academic_year_error"></div>
                        </div>
                        
                        <!-- Campus Display (Read-only) -->
                        <div class="form-group">
                            <label class="block text-sm font-semibold text-gray-700 mb-3">
                                Campus
                            </label>
                            <div class="relative">
                                <div class="w-full px-4 py-3 pl-11 border border-gray-300 rounded-xl bg-gray-50 text-gray-700">
                                    Khargone Campus
                                </div>
                                <div class="absolute left-3 top-1/2 transform -translate-y-1/2 text-gray-400">
                                    <i class="fas fa-school"></i>
                                </div>
                            </div>
                            <p class="text-xs text-gray-500 mt-1">This form is specifically for Khargone Campus admission</p>
                        </div>
                    </div>
                    
                    <!-- Fee Notice -->
                    <div class="mt-6 p-4 bg-amber-50 border border-amber-200 rounded-xl">
                        <div class="flex items-start">
                            <i class="fas fa-exclamation-circle text-amber-500 mt-1 mr-3"></i>
                            <div>
                                <p class="text-amber-800 font-semibold text-sm">Application Fee: ₹500</p>
                                <p class="text-amber-600 text-xs mt-1">A non-refundable application fee of ₹500 is required to process your admission form. You will be redirected to payment after form submission.</p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Student Information Section -->
                <div class="bg-gradient-to-br from-blue-50 to-cyan-50 rounded-2xl p-6 transition-all duration-300 hover:shadow-md border border-blue-100">
                    <div class="flex items-center mb-6">
                        <div class="w-10 h-10 bg-[#003366] rounded-xl flex items-center justify-center mr-4">
                            <i class="fas fa-user-graduate text-white"></i>
                        </div>
                        <h3 class="font-serif text-xl font-bold text-[#003366]">Student Information</h3>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <!-- Student Name -->
                        <div class="form-group">
                            <label for="student_name" class="block text-sm font-semibold text-gray-700 mb-3 flex items-center">
                                <span class="flex items-center">
                                    <i class="fas fa-asterisk text-red-500 text-xs mr-1"></i>
                                    Student Full Name
                                </span>
                            </label>
                            <div class="relative">
                                <input type="text" id="student_name" name="student_name" required
                                    value="<?php echo isset($_POST['student_name']) ? htmlspecialchars($_POST['student_name']) : ''; ?>"
                                    class="w-full px-4 py-3 pl-11 border border-gray-300 rounded-xl focus:ring-2 focus:ring-[#003366] focus:border-transparent transition-all duration-300 form-input"
                                    placeholder="Enter student's full name">
                                <div class="absolute left-3 top-1/2 transform -translate-y-1/2 text-gray-400">
                                    <i class="fas fa-user"></i>
                                </div>
                            </div>
                            <div class="error-message text-red-500 text-sm mt-1 hidden" id="student_name_error"></div>
                        </div>

                        <!-- Date of Birth -->
                        <div class="form-group">
                            <label for="dob" class="block text-sm font-semibold text-gray-700 mb-3 flex items-center">
                                <span class="flex items-center">
                                    <i class="fas fa-asterisk text-red-500 text-xs mr-1"></i>
                                    Date of Birth
                                </span>
                            </label>
                            <div class="relative">
                                <input type="date" id="dob" name="dob" required
                                    value="<?php echo isset($_POST['dob']) ? htmlspecialchars($_POST['dob']) : ''; ?>"
                                    class="w-full px-4 py-3 pl-11 border border-gray-300 rounded-xl focus:ring-2 focus:ring-[#003366] focus:border-transparent transition-all duration-300 form-input">
                                <div class="absolute left-3 top-1/2 transform -translate-y-1/2 text-gray-400">
                                    <i class="fas fa-birthday-cake"></i>
                                </div>
                            </div>
                            <div class="error-message text-red-500 text-sm mt-1 hidden" id="dob_error"></div>
                        </div>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mt-6">
                        <!-- Gender -->
                        <div class="form-group">
                            <label for="gender" class="block text-sm font-semibold text-gray-700 mb-3 flex items-center">
                                <span class="flex items-center">
                                    <i class="fas fa-asterisk text-red-500 text-xs mr-1"></i>
                                    Gender
                                </span>
                            </label>
                            <div class="relative">
                                <select id="gender" name="gender" required
                                    class="w-full px-4 py-3 pl-11 border border-gray-300 rounded-xl focus:ring-2 focus:ring-[#003366] focus:border-transparent transition-all duration-300 form-select appearance-none bg-white">
                                    <option value="">Select Gender</option>
                                    <option value="Male" <?php echo (isset($_POST['gender']) && $_POST['gender'] == 'Male') ? 'selected' : ''; ?>>Male</option>
                                    <option value="Female" <?php echo (isset($_POST['gender']) && $_POST['gender'] == 'Female') ? 'selected' : ''; ?>>Female</option>
                                    <option value="Other" <?php echo (isset($_POST['gender']) && $_POST['gender'] == 'Other') ? 'selected' : ''; ?>>Other</option>
                                </select>
                                <div class="absolute left-3 top-1/2 transform -translate-y-1/2 text-gray-400">
                                    <i class="fas fa-venus-mars"></i>
                                </div>
                                <div class="absolute right-3 top-1/2 transform -translate-y-1/2 text-gray-400 pointer-events-none">
                                    <i class="fas fa-chevron-down"></i>
                                </div>
                            </div>
                            <div class="error-message text-red-500 text-sm mt-1 hidden" id="gender_error"></div>
                        </div>

                        <!-- Grade/Class -->
                        <div class="form-group">
                            <label for="grade" class="block text-sm font-semibold text-gray-700 mb-3 flex items-center">
                                <span class="flex items-center">
                                    <i class="fas fa-asterisk text-red-500 text-xs mr-1"></i>
                                    Grade/Class Applying For
                                </span>
                            </label>
                            <div class="relative">
                                <select id="grade" name="grade" required
                                    class="w-full px-4 py-3 pl-11 border border-gray-300 rounded-xl focus:ring-2 focus:ring-[#003366] focus:border-transparent transition-all duration-300 form-select appearance-none bg-white">
                                    <option value="">Select Grade Level</option>
                                    <option value="Nursery" <?php echo (isset($_POST['grade']) && $_POST['grade'] == 'Nursery') ? 'selected' : ''; ?>>Nursery</option>
                                    <option value="LKG" <?php echo (isset($_POST['grade']) && $_POST['grade'] == 'LKG') ? 'selected' : ''; ?>>LKG</option>
                                    <option value="UKG" <?php echo (isset($_POST['grade']) && $_POST['grade'] == 'UKG') ? 'selected' : ''; ?>>UKG</option>
                                    <option value="Grade 1" <?php echo (isset($_POST['grade']) && $_POST['grade'] == 'Grade 1') ? 'selected' : ''; ?>>Grade 1</option>
                                    <option value="Grade 2" <?php echo (isset($_POST['grade']) && $_POST['grade'] == 'Grade 2') ? 'selected' : ''; ?>>Grade 2</option>
                                    <option value="Grade 3" <?php echo (isset($_POST['grade']) && $_POST['grade'] == 'Grade 3') ? 'selected' : ''; ?>>Grade 3</option>
                                    <option value="Grade 4" <?php echo (isset($_POST['grade']) && $_POST['grade'] == 'Grade 4') ? 'selected' : ''; ?>>Grade 4</option>
                                    <option value="Grade 5" <?php echo (isset($_POST['grade']) && $_POST['grade'] == 'Grade 5') ? 'selected' : ''; ?>>Grade 5</option>
                                    <option value="Grade 6" <?php echo (isset($_POST['grade']) && $_POST['grade'] == 'Grade 6') ? 'selected' : ''; ?>>Grade 6</option>
                                    <option value="Grade 7" <?php echo (isset($_POST['grade']) && $_POST['grade'] == 'Grade 7') ? 'selected' : ''; ?>>Grade 7</option>
                                </select>
                                <div class="absolute left-3 top-1/2 transform -translate-y-1/2 text-gray-400">
                                    <i class="fas fa-graduation-cap"></i>
                                </div>
                                <div class="absolute right-3 top-1/2 transform -translate-y-1/2 text-gray-400 pointer-events-none">
                                    <i class="fas fa-chevron-down"></i>
                                </div>
                            </div>
                            <div class="error-message text-red-500 text-sm mt-1 hidden" id="grade_error"></div>
                        </div>
                    </div>

                    <!-- Previous School -->
                    <div class="mt-6 form-group">
                        <label for="previous_school" class="block text-sm font-semibold text-gray-700 mb-3">
                            Previous School (If applicable)
                        </label>
                        <div class="relative">
                            <input type="text" id="previous_school" name="previous_school"
                                value="<?php echo isset($_POST['previous_school']) ? htmlspecialchars($_POST['previous_school']) : ''; ?>"
                                class="w-full px-4 py-3 pl-11 border border-gray-300 rounded-xl focus:ring-2 focus:ring-[#003366] focus:border-transparent transition-all duration-300 form-input"
                                placeholder="Name of previous school attended">
                            <div class="absolute left-3 top-1/2 transform -translate-y-1/2 text-gray-400">
                                <i class="fas fa-school"></i>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Parent/Guardian Information -->
                <div class="bg-gradient-to-br from-emerald-50 to-green-50 rounded-2xl p-6 transition-all duration-300 hover:shadow-md border border-emerald-100">
                    <div class="flex items-center mb-6">
                        <div class="w-10 h-10 bg-[#800000] rounded-xl flex items-center justify-center mr-4">
                            <i class="fas fa-users text-white"></i>
                        </div>
                        <h3 class="font-serif text-xl font-bold text-[#003366]">Parent/Guardian Information</h3>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <!-- Father Name -->
                        <div class="form-group">
                            <label for="father_name" class="block text-sm font-semibold text-gray-700 mb-3 flex items-center">
                                <span class="flex items-center">
                                    <i class="fas fa-asterisk text-red-500 text-xs mr-1"></i>
                                    Father's Name
                                </span>
                            </label>
                            <div class="relative">
                                <input type="text" id="father_name" name="father_name" required
                                    value="<?php echo isset($_POST['father_name']) ? htmlspecialchars($_POST['father_name']) : ''; ?>"
                                    class="w-full px-4 py-3 pl-11 border border-gray-300 rounded-xl focus:ring-2 focus:ring-[#003366] focus:border-transparent transition-all duration-300 form-input"
                                    placeholder="Enter father's full name">
                                <div class="absolute left-3 top-1/2 transform -translate-y-1/2 text-gray-400">
                                    <i class="fas fa-male"></i>
                                </div>
                            </div>
                            <div class="error-message text-red-500 text-sm mt-1 hidden" id="father_name_error"></div>
                        </div>

                        <!-- Mother Name -->
                        <div class="form-group">
                            <label for="mother_name" class="block text-sm font-semibold text-gray-700 mb-3">
                                Mother's Name
                            </label>
                            <div class="relative">
                                <input type="text" id="mother_name" name="mother_name"
                                    value="<?php echo isset($_POST['mother_name']) ? htmlspecialchars($_POST['mother_name']) : ''; ?>"
                                    class="w-full px-4 py-3 pl-11 border border-gray-300 rounded-xl focus:ring-2 focus:ring-[#003366] focus:border-transparent transition-all duration-300 form-input"
                                    placeholder="Enter mother's full name">
                                <div class="absolute left-3 top-1/2 transform -translate-y-1/2 text-gray-400">
                                    <i class="fas fa-female"></i>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mt-6">
                        <!-- Email -->
                        <div class="form-group">
                            <label for="email" class="block text-sm font-semibold text-gray-700 mb-3 flex items-center">
                                <span class="flex items-center">
                                    <i class="fas fa-asterisk text-red-500 text-xs mr-1"></i>
                                    Email Address
                                </span>
                            </label>
                            <div class="relative">
                                <input type="email" id="email" name="email" required
                                    value="<?php echo isset($_POST['email']) ? htmlspecialchars($_POST['email']) : ''; ?>"
                                    class="w-full px-4 py-3 pl-11 border border-gray-300 rounded-xl focus:ring-2 focus:ring-[#003366] focus:border-transparent transition-all duration-300 form-input"
                                    placeholder="Enter active email address">
                                <div class="absolute left-3 top-1/2 transform -translate-y-1/2 text-gray-400">
                                    <i class="fas fa-envelope"></i>
                                </div>
                            </div>
                            <div class="error-message text-red-500 text-sm mt-1 hidden" id="email_error"></div>
                        </div>

                        <!-- Phone -->
                        <div class="form-group">
                            <label for="phone" class="block text-sm font-semibold text-gray-700 mb-3 flex items-center">
                                <span class="flex items-center">
                                    <i class="fas fa-asterisk text-red-500 text-xs mr-1"></i>
                                    Phone Number
                                </span>
                            </label>
                            <div class="relative">
                                <input type="tel" id="phone" name="phone" required
                                    value="<?php echo isset($_POST['phone']) ? htmlspecialchars($_POST['phone']) : ''; ?>"
                                    class="w-full px-4 py-3 pl-11 border border-gray-300 rounded-xl focus:ring-2 focus:ring-[#003366] focus:border-transparent transition-all duration-300 form-input"
                                    placeholder="Enter 10-digit mobile number"
                                    maxlength="10"
                                    pattern="[0-9]{10}">
                                <div class="absolute left-3 top-1/2 transform -translate-y-1/2 text-gray-400">
                                    <i class="fas fa-phone"></i>
                                </div>
                            </div>
                            <div class="text-xs text-gray-500 mt-1">Enter 10-digit number without spaces or special characters</div>
                            <div class="error-message text-red-500 text-sm mt-1 hidden" id="phone_error"></div>
                        </div>
                    </div>
                </div>

                <!-- Address Information -->
                <div class="bg-gradient-to-br from-purple-50 to-violet-50 rounded-2xl p-6 transition-all duration-300 hover:shadow-md border border-purple-100">
                    <div class="flex items-center mb-6">
                        <div class="w-10 h-10 bg-[#D4AF37] rounded-xl flex items-center justify-center mr-4">
                            <i class="fas fa-map-marker-alt text-[#003366]"></i>
                        </div>
                        <h3 class="font-serif text-xl font-bold text-[#003366]">Address Information</h3>
                    </div>

                    <!-- Address -->
                    <div class="form-group mb-6">
                        <label for="address" class="block text-sm font-semibold text-gray-700 mb-3">
                            Complete Address
                        </label>
                        <div class="relative">
                            <textarea id="address" name="address" rows="3"
                                class="w-full px-4 py-3 pl-11 border border-gray-300 rounded-xl focus:ring-2 focus:ring-[#003366] focus:border-transparent transition-all duration-300 form-textarea resize-none"
                                placeholder="Enter complete residential address"><?php echo isset($_POST['address']) ? htmlspecialchars($_POST['address']) : ''; ?></textarea>
                            <div class="absolute left-3 top-4 transform text-gray-400">
                                <i class="fas fa-home"></i>
                            </div>
                        </div>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                        <!-- City -->
                        <div class="form-group">
                            <label for="city" class="block text-sm font-semibold text-gray-700 mb-3">
                                City
                            </label>
                            <div class="relative">
                                <input type="text" id="city" name="city" required
                                    value="<?php echo isset($_POST['city']) ? htmlspecialchars($_POST['city']) : ''; ?>"
                                    class="w-full px-4 py-3 pl-11 border border-gray-300 rounded-xl focus:ring-2 focus:ring-[#003366] focus:border-transparent transition-all duration-300 form-input"
                                    placeholder="Enter city name">
                                <div class="absolute left-3 top-1/2 transform -translate-y-1/2 text-gray-400">
                                    <i class="fas fa-city"></i>
                                </div>
                            </div>
                            <div class="error-message text-red-500 text-sm mt-1 hidden" id="city_error"></div>
                        </div>

                        <!-- State -->
                        <div class="form-group">
                            <label for="state" class="block text-sm font-semibold text-gray-700 mb-3">
                                State
                            </label>
                            <div class="relative">
                                <input type="text" id="state" name="state" required
                                    value="<?php echo isset($_POST['state']) ? htmlspecialchars($_POST['state']) : ''; ?>"
                                    class="w-full px-4 py-3 pl-11 border border-gray-300 rounded-xl focus:ring-2 focus:ring-[#003366] focus:border-transparent transition-all duration-300 form-input"
                                    placeholder="Enter state">
                                <div class="absolute left-3 top-1/2 transform -translate-y-1/2 text-gray-400">
                                    <i class="fas fa-map"></i>
                                </div>
                            </div>
                            <div class="error-message text-red-500 text-sm mt-1 hidden" id="state_error"></div>
                        </div>

                        <!-- Pincode -->
                        <div class="form-group">
                            <label for="pincode" class="block text-sm font-semibold text-gray-700 mb-3">
                                Pincode
                            </label>
                            <div class="relative">
                                <input type="text" id="pincode" name="pincode" required
                                    value="<?php echo isset($_POST['pincode']) ? htmlspecialchars($_POST['pincode']) : ''; ?>"
                                    class="w-full px-4 py-3 pl-11 border border-gray-300 rounded-xl focus:ring-2 focus:ring-[#003366] focus:border-transparent transition-all duration-300 form-input"
                                    placeholder="6-digit pincode"
                                    maxlength="6"
                                    pattern="[0-9]{6}">
                                <div class="absolute left-3 top-1/2 transform -translate-y-1/2 text-gray-400">
                                    <i class="fas fa-map-pin"></i>
                                </div>
                            </div>
                            <div class="error-message text-red-500 text-sm mt-1 hidden" id="pincode_error"></div>
                        </div>
                    </div>
                </div>

                <!-- Additional Information -->
                <div class="bg-gradient-to-br from-orange-50 to-amber-50 rounded-2xl p-6 transition-all duration-300 hover:shadow-md border border-orange-100">
                    <div class="flex items-center mb-6">
                        <div class="w-10 h-10 bg-[#800000] rounded-xl flex items-center justify-center mr-4">
                            <i class="fas fa-edit text-white"></i>
                        </div>
                        <h3 class="font-serif text-xl font-bold text-[#003366]">Additional Information</h3>
                    </div>

                    <div class="form-group">
                        <label for="message" class="block text-sm font-semibold text-gray-700 mb-3">
                            Additional Questions or Information
                        </label>
                        <div class="relative">
                            <textarea id="message" name="message" rows="4"
                                class="w-full px-4 py-3 pl-11 border border-gray-300 rounded-xl focus:ring-2 focus:ring-[#003366] focus:border-transparent transition-all duration-300 form-textarea resize-none"
                                placeholder="Any specific requirements, questions about curriculum, transportation, or additional information you'd like to share..."><?php echo isset($_POST['message']) ? htmlspecialchars($_POST['message']) : ''; ?></textarea>
                            <div class="absolute left-3 top-4 transform text-gray-400">
                                <i class="fas fa-comment-dots"></i>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Form Actions -->
                <div class="bg-[#f7f7f7] rounded-2xl p-6 border border-gray-200">
                    <div class="flex flex-col md:flex-row items-center justify-between space-y-4 md:space-y-0">
                        <div class="text-sm text-gray-600">
                            <p class="flex items-center">
                                <i class="fas fa-shield-alt text-green-500 mr-2"></i>
                                Your information is secure and confidential
                            </p>
                            <p class="flex items-center mt-2 text-amber-600">
                                <i class="fas fa-rupee-sign text-amber-500 mr-2"></i>
                                <span class="font-semibold">Application Fee: ₹500</span>
                            </p>
                        </div>

                        <div class="flex space-x-4">
                            <button type="button" onclick="window.location.href='<?php echo $base_url; ?>/admissions/index.php'"
                                class="px-6 py-3 border border-gray-300 text-gray-700 font-semibold rounded-xl hover:bg-gray-50 transition-all duration-300 inline-flex items-center">
                                <i class="fas fa-arrow-left mr-2"></i> Back
                            </button>
                            <button type="submit"
                                class="bg-[#800000] hover:bg-[#660000] text-white font-semibold py-3 px-8 rounded-xl transition-all duration-300 transform hover:scale-105 shadow-lg hover:shadow-xl inline-flex items-center submit-button">
                                <i class="fas fa-rupee-sign mr-3"></i>
                                <span class="submit-text">Pay ₹500 & Submit</span>
                                <i class="fas fa-spinner fa-spin ml-2 hidden loading-icon"></i>
                            </button>
                        </div>
                    </div>

                    <div class="mt-6 pt-6 border-t border-gray-200">
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-4 text-center">
                            <div class="p-3 bg-blue-50 rounded-lg">
                                <p class="text-xs text-gray-600">Step 1: Fill Form</p>
                            </div>
                            <div class="p-3 bg-amber-50 rounded-lg">
                                <p class="text-xs font-semibold text-amber-700">Step 2: Pay ₹500</p>
                            </div>
                            <div class="p-3 bg-gray-50 rounded-lg">
                                <p class="text-xs text-gray-600">Step 3: Documentation</p>
                            </div>
                        </div>
                    </div>

                    <div class="mt-4 text-center">
                        <p class="text-xs text-gray-500">
                            <i class="fas fa-asterisk text-red-500 mr-1"></i>
                            Required fields. By submitting this form, you agree to pay ₹500 application fee and our
                            <a href="#" class="text-[#003366] hover:underline font-semibold">Privacy Policy</a> and
                            <a href="#" class="text-[#003366] hover:underline font-semibold">Terms of Service</a>.
                        </p>
                    </div>
                </div>
            </form>
        </div>
    </div>
</section>

<!-- Payment Information Section -->
<section class="py-16 bg-white">
    <div class="max-w-4xl mx-auto px-4">
        <div class="text-center mb-12">
            <h2 class="font-serif text-3xl font-bold text-[#003366] mb-4">
                Payment <span class="text-[#800000]">Information</span>
            </h2>
            <p class="text-gray-600">
                Secure payment process for your application fee
            </p>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
            <div class="text-center p-6 bg-gradient-to-br from-blue-50 to-blue-100 rounded-2xl hover-lift transition-all duration-300 border border-blue-200">
                <div class="w-16 h-16 bg-[#003366] rounded-2xl flex items-center justify-center mx-auto mb-4">
                    <i class="fas fa-shield-alt text-white text-xl"></i>
                </div>
                <h3 class="font-semibold text-gray-800 mb-2">Secure Payment</h3>
                <p class="text-gray-600 mb-4 text-sm">SSL encrypted payment gateway</p>
                <div class="text-sm text-blue-600 font-semibold">100% Secure</div>
            </div>

            <div class="text-center p-6 bg-gradient-to-br from-amber-50 to-yellow-100 rounded-2xl hover-lift transition-all duration-300 border border-amber-200">
                <div class="w-16 h-16 bg-[#D4AF37] rounded-2xl flex items-center justify-center mx-auto mb-4">
                    <i class="fas fa-rupee-sign text-[#003366] text-xl"></i>
                </div>
                <h3 class="font-semibold text-gray-800 mb-2">Application Fee</h3>
                <p class="text-gray-600 mb-4 text-sm">Non-refundable processing fee</p>
                <div class="text-2xl font-bold text-[#800000]">₹500</div>
            </div>

            <div class="text-center p-6 bg-gradient-to-br from-green-50 to-emerald-100 rounded-2xl hover-lift transition-all duration-300 border border-green-200">
                <div class="w-16 h-16 bg-[#800000] rounded-2xl flex items-center justify-center mx-auto mb-4">
                    <i class="fas fa-receipt text-white text-xl"></i>
                </div>
                <h3 class="font-semibold text-gray-800 mb-2">Instant Receipt</h3>
                <p class="text-gray-600 mb-4 text-sm">Payment confirmation email</p>
                <div class="text-sm text-green-600 font-semibold">Immediate Confirmation</div>
            </div>
        </div>
    </div>
</section>

<!-- Support Section -->
<section class="py-16 bg-[#f7f7f7]">
    <div class="max-w-4xl mx-auto px-4">
        <div class="text-center mb-12">
            <h2 class="font-serif text-3xl font-bold text-[#003366] mb-4">
                Need <span class="text-[#800000]">Help?</span>
            </h2>
            <p class="text-gray-600">
                Our admissions team is here to assist you every step of the way
            </p>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
            <div class="text-center p-6 bg-white rounded-2xl hover-lift transition-all duration-300 shadow-sm border border-gray-200">
                <div class="w-16 h-16 bg-[#003366] rounded-2xl flex items-center justify-center mx-auto mb-4">
                    <i class="fas fa-phone text-white text-xl"></i>
                </div>
                <h3 class="font-semibold text-gray-800 mb-2">Call Admissions</h3>
                <p class="text-gray-600 mb-4 text-sm">Speak directly with our team</p>
                <a href="tel:+917618040040" class="text-[#003366] hover:text-[#800000] font-semibold text-sm inline-flex items-center">
                    +91-7618040040
                </a>
            </div>

            <div class="text-center p-6 bg-white rounded-2xl hover-lift transition-all duration-300 shadow-sm border border-gray-200">
                <div class="w-16 h-16 bg-[#800000] rounded-2xl flex items-center justify-center mx-auto mb-4">
                    <i class="fas fa-envelope text-white text-xl"></i>
                </div>
                <h3 class="font-semibold text-gray-800 mb-2">Email Us</h3>
                <p class="text-gray-600 mb-4 text-sm">Get detailed information</p>
                <a href="mailto:admissions@khargonecampus.edu" class="text-[#003366] hover:text-[#800000] font-semibold text-sm inline-flex items-center">
                    admissions@khargonecampus.edu
                </a>
            </div>

            <div class="text-center p-6 bg-white rounded-2xl hover-lift transition-all duration-300 shadow-sm border border-gray-200">
                <div class="w-16 h-16 bg-[#D4AF37] rounded-2xl flex items-center justify-center mx-auto mb-4">
                    <i class="fas fa-map-marker-alt text-[#003366] text-xl"></i>
                </div>
                <h3 class="font-semibold text-gray-800 mb-2">Visit Campus</h3>
                <p class="text-gray-600 mb-4 text-sm">Schedule a personal tour</p>
                <a href="<?php echo $base_url; ?>/contact.php" class="text-[#003366] hover:text-[#800000] font-semibold text-sm inline-flex items-center">
                    Book Campus Tour
                </a>
            </div>
        </div>
    </div>
</section>

<!-- Next Steps -->
<section class="py-16 bg-white">
    <div class="max-w-4xl mx-auto px-4">
        <div class="text-center mb-12">
            <h2 class="font-serif text-3xl font-bold text-[#003366] mb-4">
                What Happens <span class="text-[#800000]">Next?</span>
            </h2>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-4 gap-6">
            <div class="text-center p-6 bg-gradient-to-br from-blue-50 to-cyan-50 rounded-2xl shadow-lg hover-lift transition-all duration-300 border border-blue-100">
                <div class="w-12 h-12 bg-[#003366] rounded-full flex items-center justify-center mx-auto mb-4">
                    <span class="text-white font-bold text-lg">1</span>
                </div>
                <h3 class="font-semibold text-[#003366] mb-2">Form Submission</h3>
                <p class="text-gray-600 text-sm">Complete and submit this application form</p>
            </div>
            <div class="text-center p-6 bg-gradient-to-br from-amber-50 to-yellow-50 rounded-2xl shadow-lg hover-lift transition-all duration-300 border border-amber-100">
                <div class="w-12 h-12 bg-[#D4AF37] rounded-full flex items-center justify-center mx-auto mb-4">
                    <span class="text-[#003366] font-bold text-lg">2</span>
                </div>
                <h3 class="font-semibold text-[#003366] mb-2">Pay ₹500 Fee</h3>
                <p class="text-gray-600 text-sm">Secure online payment of application fee</p>
            </div>
            <div class="text-center p-6 bg-gradient-to-br from-emerald-50 to-green-50 rounded-2xl shadow-lg hover-lift transition-all duration-300 border border-emerald-100">
                <div class="w-12 h-12 bg-[#800000] rounded-full flex items-center justify-center mx-auto mb-4">
                    <span class="text-white font-bold text-lg">3</span>
                </div>
                <h3 class="font-semibold text-[#003366] mb-2">Document Verification</h3>
                <p class="text-gray-600 text-sm">Submit required documents for verification</p>
            </div>
            <div class="text-center p-6 bg-gradient-to-br from-purple-50 to-violet-50 rounded-2xl shadow-lg hover-lift transition-all duration-300 border border-purple-100">
                <div class="w-12 h-12 bg-[#003366] rounded-full flex items-center justify-center mx-auto mb-4">
                    <span class="text-white font-bold text-lg">4</span>
                </div>
                <h3 class="font-semibold text-[#003366] mb-2">Admission Confirmation</h3>
                <p class="text-gray-600 text-sm">Receive admission confirmation letter</p>
            </div>
        </div>
    </div>
</section>

<?php include_once '../../../includes/footer.php' ?>

<style>
    .hover-lift {
        transition: transform 0.3s ease, box-shadow 0.3s ease;
    }

    .hover-lift:hover {
        transform: translateY(-5px);
        box-shadow: 0 20px 40px rgba(0, 0, 0, 0.1);
    }

    .form-input:focus,
    .form-select:focus,
    .form-textarea:focus {
        box-shadow: 0 0 0 3px rgba(0, 51, 102, 0.1);
        border-color: #003366;
    }

    .submit-button:disabled {
        opacity: 0.7;
        cursor: not-allowed;
        transform: none !important;
    }

    .animate-fade-in {
        animation: fadeIn 0.5s ease-in-out;
    }

    .field-error {
        border-color: #ef4444 !important;
        box-shadow: 0 0 0 3px rgba(239, 68, 68, 0.1) !important;
    }

    .field-success {
        border-color: #10b981 !important;
        box-shadow: 0 0 0 3px rgba(16, 185, 129, 0.1) !important;
    }

    .error-message {
        display: none;
    }

    @keyframes fadeIn {
        from {
            opacity: 0;
            transform: translateY(-10px);
        }

        to {
            opacity: 1;
            transform: translateY(0);
        }
    }

    /* Custom select arrow */
    .form-select {
        background-image: url("data:image/svg+xml,%3csvg xmlns='http://www.w3.org/2000/svg' fill='none' viewBox='0 0 20 20'%3e%3cpath stroke='%236b7280' stroke-linecap='round' stroke-linejoin='round' stroke-width='1.5' d='m6 8 4 4 4-4'/%3e%3c/svg%3e");
        background-position: right 0.5rem center;
        background-repeat: no-repeat;
        background-size: 1.5em 1.5em;
        padding-right: 2.5rem;
        -webkit-print-color-adjust: exact;
        print-color-adjust: exact;
    }
    
    /* Custom styling for fee highlight */
    .fee-highlight {
        background: linear-gradient(45deg, #D4AF37, #FFD700);
        color: #003366;
        font-weight: bold;
    }
</style>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const form = document.getElementById('admissionForm');
        const submitButton = form.querySelector('.submit-button');
        const submitText = form.querySelector('.submit-text');
        const loadingIcon = form.querySelector('.loading-icon');

        // Simple validation functions
        function validateRequired(value) {
            return value.trim() !== '';
        }

        function validateEmail(email) {
            const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
            return emailRegex.test(email);
        }

        function validatePhone(phone) {
            const phoneRegex = /^[0-9]{10}$/;
            return phoneRegex.test(phone);
        }

        function validatePincode(pincode) {
            const pincodeRegex = /^[0-9]{6}$/;
            return pincodeRegex.test(pincode);
        }

        function validateDate(dob) {
            if (!dob) return false;
            const date = new Date(dob);
            const today = new Date();
            return date <= today;
        }

        // Show error message
        function showError(fieldId, message) {
            const field = document.getElementById(fieldId);
            const errorElement = document.getElementById(fieldId + '_error');
            
            if (field && errorElement) {
                field.classList.add('field-error');
                field.classList.remove('field-success');
                errorElement.textContent = message;
                errorElement.style.display = 'block';
            }
        }

        // Show success state
        function showSuccess(fieldId) {
            const field = document.getElementById(fieldId);
            const errorElement = document.getElementById(fieldId + '_error');
            
            if (field && errorElement) {
                field.classList.remove('field-error');
                field.classList.add('field-success');
                errorElement.style.display = 'none';
            }
        }

        // Clear validation state
        function clearValidation(fieldId) {
            const field = document.getElementById(fieldId);
            const errorElement = document.getElementById(fieldId + '_error');
            
            if (field && errorElement) {
                field.classList.remove('field-error', 'field-success');
                errorElement.style.display = 'none';
            }
        }

        // Validate individual field
        function validateField(fieldId) {
            const field = document.getElementById(fieldId);
            if (!field) return true;

            const value = field.value.trim();
            const isRequired = field.hasAttribute('required');

            if (isRequired && !validateRequired(value)) {
                showError(fieldId, 'This field is required');
                return false;
            }

            // Field-specific validation
            switch (fieldId) {
                case 'email':
                    if (value && !validateEmail(value)) {
                        showError(fieldId, 'Please enter a valid email address');
                        return false;
                    }
                    break;
                case 'phone':
                    if (value && !validatePhone(value)) {
                        showError(fieldId, 'Please enter a valid 10-digit phone number');
                        return false;
                    }
                    break;
                case 'pincode':
                    if (value && !validatePincode(value)) {
                        showError(fieldId, 'Please enter a valid 6-digit pincode');
                        return false;
                    }
                    break;
                case 'dob':
                    if (value && !validateDate(value)) {
                        showError(fieldId, 'Please enter a valid date of birth');
                        return false;
                    }
                    break;
            }

            showSuccess(fieldId);
            return true;
        }

        // Real-time validation on blur (father_name added, parent_name removed)
        const fieldsToValidate = ['student_name', 'father_name', 'email', 'phone', 'dob', 'gender', 'grade', 'academic_year', 'city', 'state', 'pincode'];
        
        fieldsToValidate.forEach(fieldId => {
            const field = document.getElementById(fieldId);
            if (field) {
                field.addEventListener('blur', function() {
                    validateField(fieldId);
                });

                field.addEventListener('input', function() {
                    clearValidation(fieldId);
                });
            }
        });

        // Form submission
        form.addEventListener('submit', function(e) {
            // Prevent default first to handle validation
            e.preventDefault();

            let isValid = true;

            // Validate all required fields
            fieldsToValidate.forEach(fieldId => {
                if (!validateField(fieldId)) {
                    isValid = false;
                }
            });

            if (!isValid) {
                // Scroll to first error
                const firstError = form.querySelector('.field-error');
                if (firstError) {
                    firstError.scrollIntoView({
                        behavior: 'smooth',
                        block: 'center'
                    });
                    firstError.focus();
                }
                
                // Show error message
                const errorDiv = document.createElement('div');
                errorDiv.className = 'bg-red-50 border border-red-200 rounded-xl p-4 mb-6 animate-fade-in';
                errorDiv.innerHTML = `
                    <div class="flex items-center">
                        <i class="fas fa-exclamation-triangle text-red-600 mr-3"></i>
                        <span class="text-red-700 font-semibold">Please correct the errors in the form before submitting.</span>
                    </div>
                `;

                // Remove existing error if any
                const existingError = form.querySelector('.bg-red-50');
                if (existingError) {
                    existingError.remove();
                }

                form.insertBefore(errorDiv, form.firstChild);
                return;
            }

            // Confirm payment
            const confirmed = confirm('You will be redirected to pay ₹500 application fee after form submission. Do you want to proceed?');
            if (!confirmed) {
                return;
            }

            // Show loading state
            submitButton.disabled = true;
            submitText.textContent = 'Processing...';
            loadingIcon.classList.remove('hidden');

            // Submit the form
            form.submit();
        });

        // Phone number formatting - allow only numbers
        const phoneInput = document.getElementById('phone');
        if (phoneInput) {
            phoneInput.addEventListener('input', function(e) {
                let value = e.target.value.replace(/\D/g, '');
                if (value.length > 10) {
                    value = value.substring(0, 10);
                }
                e.target.value = value;
            });
        }

        // Pincode formatting - allow only numbers
        const pincodeInput = document.getElementById('pincode');
        if (pincodeInput) {
            pincodeInput.addEventListener('input', function(e) {
                let value = e.target.value.replace(/\D/g, '');
                if (value.length > 6) {
                    value = value.substring(0, 6);
                }
                e.target.value = value;
            });
        }

        // Set max date for DOB (today)
        const dobInput = document.getElementById('dob');
        if (dobInput) {
            const today = new Date().toISOString().split('T')[0];
            dobInput.setAttribute('max', today);
        }
    });
</script>