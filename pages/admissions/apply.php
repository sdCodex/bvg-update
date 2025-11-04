<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Start session at the very beginning
session_start();

// Database connection and base URL setup
$base_url = '/Gurkul_Project';

// Include database connection
include '../../includes/db.php';

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
        $parent_name = isset($_POST['parent_name']) ? htmlspecialchars(trim($_POST['parent_name'])) : '';
        $email = isset($_POST['email']) ? filter_var(trim($_POST['email']), FILTER_VALIDATE_EMAIL) : '';
        $phone = isset($_POST['phone']) ? htmlspecialchars(trim($_POST['phone'])) : '';
        $grade = isset($_POST['grade']) ? htmlspecialchars(trim($_POST['grade'])) : '';
        $program = isset($_POST['program']) ? htmlspecialchars(trim($_POST['program'])) : '';
        $address = isset($_POST['address']) ? htmlspecialchars(trim($_POST['address'])) : '';
        $city = isset($_POST['city']) ? htmlspecialchars(trim($_POST['city'])) : '';
        $state = isset($_POST['state']) ? htmlspecialchars(trim($_POST['state'])) : '';
        $pincode = isset($_POST['pincode']) ? htmlspecialchars(trim($_POST['pincode'])) : '';
        $previous_school = isset($_POST['previous_school']) ? htmlspecialchars(trim($_POST['previous_school'])) : '';
        $message = isset($_POST['message']) ? htmlspecialchars(trim($_POST['message'])) : '';

        // Debug: Check individual fields
        error_log("Student Name: " . $student_name);
        error_log("Parent Name: " . $parent_name);
        error_log("Email: " . $email);
        error_log("Phone: " . $phone);
        error_log("Grade: " . $grade);

        // Validate required fields
        if (empty($student_name) || empty($parent_name) || empty($email) || empty($phone) || empty($grade)) {
            $error_message = "Please fill all required fields.";
            error_log("Validation failed: Required fields missing");
        } elseif (!$email) {
            $error_message = "Please enter a valid email address.";
            error_log("Validation failed: Invalid email");
        } elseif (!preg_match('/^[0-9]{10}$/', $phone)) {
            $error_message = "Please enter a valid 10-digit phone number.";
            error_log("Validation failed: Invalid phone");
        } else {
            // All validation passed, insert into database
            error_log("All validations passed, inserting into database");
            
            $stmt = $pdo->prepare("
                INSERT INTO admission_inquiries 
                (student_name, parent_name, email, phone, grade, program, address, city, state, pincode, previous_school, message, status, created_at) 
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, 'pending', NOW())
            ");

            // Execute the statement
            $result = $stmt->execute([
                $student_name,
                $parent_name,
                $email,
                $phone,
                $grade,
                $program,
                $address,
                $city,
                $state,
                $pincode,
                $previous_school,
                $message
            ]);

            // Check if insertion was successful
            if ($result) {
                $last_id = $pdo->lastInsertId();
                error_log("Database insertion successful. Last insert ID: " . $last_id);
                
                $success_message = "Thank you! Your admission inquiry has been submitted successfully. We will contact you within 24 hours.";

                // Store success message in session for redirect
                $_SESSION['form_success'] = $success_message;
                
                // Clear POST data
                $_POST = array();
                
                // Redirect to prevent form resubmission
                header("Location: " . $base_url . "/pages/admissions/index.php");
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

<?php include '../../includes/header.php'; ?>

<!-- Hero Section -->
<section class="relative bg-gradient-to-br from-primary via-primary to-accent text-white overflow-hidden">
    <!-- Background Pattern -->
    <div class="absolute inset-0 opacity-10">
        <div class="absolute top-10 left-10 w-20 h-20 bg-white rounded-full"></div>
        <div class="absolute bottom-10 right-10 w-32 h-32 bg-white rounded-full"></div>
        <div class="absolute top-1/2 left-1/4 w-16 h-16 bg-white rounded-full"></div>
    </div>

    <div class="max-w-7xl mx-auto px-4 py-20 text-center relative z-10">
        <!-- Breadcrumb -->
        <div class="flex justify-center mb-8">
            <nav class="flex items-center space-x-2 text-white/80 text-sm">
                <a href="<?php echo $base_url; ?>/index.php" class="hover:text-white transition-colors">Home</a>
                <span class="text-white/60">/</span>
                <a href="<?php echo $base_url; ?>/admissions/index.php" class="hover:text-white transition-colors">Admissions</a>
                <span class="text-white/60">/</span>
                <span class="text-white font-medium">Apply Now</span>
            </nav>
        </div>

        <h1 class="font-serif text-4xl md:text-5xl font-bold mb-6 leading-tight">
            Admission <span class="text-yellow-300">Application</span>
        </h1>
        <p class="text-xl text-gray-200 max-w-2xl mx-auto leading-relaxed">
            Begin Your Transformational Journey at Bhaktivedanta Gurukul
        </p>

        <!-- Quick Stats -->
        <div class="grid grid-cols-2 md:grid-cols-4 gap-8 mt-12 max-w-2xl mx-auto">
            <div class="text-center">
                <div class="text-2xl font-bold text-yellow-300 mb-2">24hrs</div>
                <div class="text-gray-300 text-sm">Response Time</div>
            </div>
            <div class="text-center">
                <div class="text-2xl font-bold text-yellow-300 mb-2">4</div>
                <div class="text-gray-300 text-sm">Simple Steps</div>
            </div>
            <div class="text-center">
                <div class="text-2xl font-bold text-yellow-300 mb-2">100%</div>
                <div class="text-gray-300 text-sm">Secure</div>
            </div>
            <div class="text-center">
                <div class="text-2xl font-bold text-yellow-300 mb-2">Free</div>
                <div class="text-gray-300 text-sm">Application</div>
            </div>
        </div>
    </div>
</section>

<!-- Progress Steps -->
<section class="bg-white border-b border-gray-200 py-8">
    <div class="max-w-4xl mx-auto px-4">
        <div class="flex justify-between items-center">
            <?php
            $steps = [
                ['icon' => 'fas fa-user-edit', 'label' => 'Application', 'active' => true],
                ['icon' => 'fas fa-calendar-check', 'label' => 'Schedule Visit', 'active' => false],
                ['icon' => 'fas fa-file-alt', 'label' => 'Documentation', 'active' => false],
                ['icon' => 'fas fa-check-circle', 'label' => 'Confirmation', 'active' => false]
            ];

            foreach ($steps as $index => $step):
            ?>
                <div class="flex flex-col items-center flex-1">
                    <div class="w-12 h-12 rounded-full flex items-center justify-center mb-3 
                    <?php echo $step['active'] ? 'bg-accent text-white shadow-lg' : 'bg-gray-200 text-gray-500'; ?> 
                    transition-all duration-300">
                        <i class="<?php echo $step['icon']; ?>"></i>
                    </div>
                    <span class="text-sm font-medium <?php echo $step['active'] ? 'text-accent' : 'text-gray-500'; ?>">
                        <?php echo $step['label']; ?>
                    </span>
                    <?php if ($index < count($steps) - 1): ?>
                        <div class="hidden md:block w-full h-1 bg-gray-200 -mt-6 relative z-0">
                            <div class="h-1 bg-accent transition-all duration-500"
                                style="width: <?php echo $step['active'] ? '0%' : '0%'; ?>"></div>
                        </div>
                    <?php endif; ?>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
</section>

<!-- Main Form Section -->
<section class="py-16 bg-gray-50">
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
            <div class="bg-gradient-to-r from-primary to-accent text-white p-8">
                <div class="flex items-center justify-between">
                    <div>
                        <h2 class="font-serif text-2xl md:text-3xl font-bold mb-2">
                            Admission Inquiry Form
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
                <!-- Student Information Section -->
                <div class="bg-blue-50 rounded-2xl p-6 transition-all duration-300 hover:shadow-md">
                    <div class="flex items-center mb-6">
                        <div class="w-10 h-10 bg-blue-500 rounded-xl flex items-center justify-center mr-4">
                            <i class="fas fa-user-graduate text-white"></i>
                        </div>
                        <h3 class="font-serif text-xl font-bold text-gray-800">Student Information</h3>
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
                                    class="w-full px-4 py-3 pl-11 border border-gray-300 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all duration-300 form-input"
                                    placeholder="Enter student's full name">
                                <div class="absolute left-3 top-1/2 transform -translate-y-1/2 text-gray-400">
                                    <i class="fas fa-user"></i>
                                </div>
                            </div>
                            <div class="error-message text-red-500 text-sm mt-1 hidden" id="student_name_error"></div>
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
                                    class="w-full px-4 py-3 pl-11 border border-gray-300 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all duration-300 form-select appearance-none bg-white">
                                    <option value="">Select Grade Level</option>
                                    <option value="Grade 1" <?php echo (isset($_POST['grade']) && $_POST['grade'] == 'Grade 1') ? 'selected' : ''; ?>>Grade 1</option>
                                    <option value="Grade 2" <?php echo (isset($_POST['grade']) && $_POST['grade'] == 'Grade 2') ? 'selected' : ''; ?>>Grade 2</option>
                                    <option value="Grade 3" <?php echo (isset($_POST['grade']) && $_POST['grade'] == 'Grade 3') ? 'selected' : ''; ?>>Grade 3</option>
                                    <option value="Grade 4" <?php echo (isset($_POST['grade']) && $_POST['grade'] == 'Grade 4') ? 'selected' : ''; ?>>Grade 4</option>
                                    <option value="Grade 5" <?php echo (isset($_POST['grade']) && $_POST['grade'] == 'Grade 5') ? 'selected' : ''; ?>>Grade 5</option>
                                    <option value="Grade 6" <?php echo (isset($_POST['grade']) && $_POST['grade'] == 'Grade 6') ? 'selected' : ''; ?>>Grade 6</option>
                                    <option value="Grade 7" <?php echo (isset($_POST['grade']) && $_POST['grade'] == 'Grade 7') ? 'selected' : ''; ?>>Grade 7</option>
                                    <option value="Grade 8" <?php echo (isset($_POST['grade']) && $_POST['grade'] == 'Grade 8') ? 'selected' : ''; ?>>Grade 8</option>
                                    <option value="Grade 9" <?php echo (isset($_POST['grade']) && $_POST['grade'] == 'Grade 9') ? 'selected' : ''; ?>>Grade 9</option>
                                    <option value="Grade 10" <?php echo (isset($_POST['grade']) && $_POST['grade'] == 'Grade 10') ? 'selected' : ''; ?>>Grade 10</option>
                                    <option value="Grade 11" <?php echo (isset($_POST['grade']) && $_POST['grade'] == 'Grade 11') ? 'selected' : ''; ?>>Grade 11</option>
                                    <option value="Grade 12" <?php echo (isset($_POST['grade']) && $_POST['grade'] == 'Grade 12') ? 'selected' : ''; ?>>Grade 12</option>
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
                                class="w-full px-4 py-3 pl-11 border border-gray-300 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all duration-300 form-input"
                                placeholder="Name of previous school attended">
                            <div class="absolute left-3 top-1/2 transform -translate-y-1/2 text-gray-400">
                                <i class="fas fa-school"></i>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Parent/Guardian Information -->
                <div class="bg-green-50 rounded-2xl p-6 transition-all duration-300 hover:shadow-md">
                    <div class="flex items-center mb-6">
                        <div class="w-10 h-10 bg-green-500 rounded-xl flex items-center justify-center mr-4">
                            <i class="fas fa-users text-white"></i>
                        </div>
                        <h3 class="font-serif text-xl font-bold text-gray-800">Parent/Guardian Information</h3>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <!-- Parent Name -->
                        <div class="form-group">
                            <label for="parent_name" class="block text-sm font-semibold text-gray-700 mb-3 flex items-center">
                                <span class="flex items-center">
                                    <i class="fas fa-asterisk text-red-500 text-xs mr-1"></i>
                                    Parent/Guardian Name
                                </span>
                            </label>
                            <div class="relative">
                                <input type="text" id="parent_name" name="parent_name" required
                                    value="<?php echo isset($_POST['parent_name']) ? htmlspecialchars($_POST['parent_name']) : ''; ?>"
                                    class="w-full px-4 py-3 pl-11 border border-gray-300 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all duration-300 form-input"
                                    placeholder="Enter parent/guardian full name">
                                <div class="absolute left-3 top-1/2 transform -translate-y-1/2 text-gray-400">
                                    <i class="fas fa-user-friends"></i>
                                </div>
                            </div>
                            <div class="error-message text-red-500 text-sm mt-1 hidden" id="parent_name_error"></div>
                        </div>

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
                                    class="w-full px-4 py-3 pl-11 border border-gray-300 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all duration-300 form-input"
                                    placeholder="Enter active email address">
                                <div class="absolute left-3 top-1/2 transform -translate-y-1/2 text-gray-400">
                                    <i class="fas fa-envelope"></i>
                                </div>
                            </div>
                            <div class="error-message text-red-500 text-sm mt-1 hidden" id="email_error"></div>
                        </div>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mt-6">
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
                                    class="w-full px-4 py-3 pl-11 border border-gray-300 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all duration-300 form-input"
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

                        <!-- Preferred Program -->
                        <div class="form-group">
                            <label for="program" class="block text-sm font-semibold text-gray-700 mb-3">
                                Preferred Program
                            </label>
                            <div class="relative">
                                <select id="program" name="program"
                                    class="w-full px-4 py-3 pl-11 border border-gray-300 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all duration-300 form-select appearance-none bg-white">
                                    <option value="">Select Program</option>
                                    <option value="CBSE" <?php echo (isset($_POST['program']) && $_POST['program'] == 'CBSE') || $selected_program == 'CBSE' ? 'selected' : ''; ?>>CBSE</option>
                                    <option value="Vedic Studies" <?php echo (isset($_POST['program']) && $_POST['program'] == 'Vedic Studies') || $selected_program == 'Vedic Studies' ? 'selected' : ''; ?>>Vedic Studies</option>
                                    <option value="Integrated Program" <?php echo (isset($_POST['program']) && $_POST['program'] == 'Integrated Program') || $selected_program == 'Integrated Program' ? 'selected' : ''; ?>>Integrated Program</option>
                                    <option value="Residential Program" <?php echo (isset($_POST['program']) && $_POST['program'] == 'Residential Program') || $selected_program == 'Residential Program' ? 'selected' : ''; ?>>Residential Program</option>
                                </select>
                                <div class="absolute left-3 top-1/2 transform -translate-y-1/2 text-gray-400">
                                    <i class="fas fa-book-open"></i>
                                </div>
                                <div class="absolute right-3 top-1/2 transform -translate-y-1/2 text-gray-400 pointer-events-none">
                                    <i class="fas fa-chevron-down"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Address Information -->
                <div class="bg-purple-50 rounded-2xl p-6 transition-all duration-300 hover:shadow-md">
                    <div class="flex items-center mb-6">
                        <div class="w-10 h-10 bg-purple-500 rounded-xl flex items-center justify-center mr-4">
                            <i class="fas fa-map-marker-alt text-white"></i>
                        </div>
                        <h3 class="font-serif text-xl font-bold text-gray-800">Address Information</h3>
                    </div>

                    <!-- Address -->
                    <div class="form-group mb-6">
                        <label for="address" class="block text-sm font-semibold text-gray-700 mb-3">
                            Complete Address
                        </label>
                        <div class="relative">
                            <textarea id="address" name="address" rows="3"
                                class="w-full px-4 py-3 pl-11 border border-gray-300 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all duration-300 form-textarea resize-none"
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
                                <input type="text" id="city" name="city"
                                    value="<?php echo isset($_POST['city']) ? htmlspecialchars($_POST['city']) : ''; ?>"
                                    class="w-full px-4 py-3 pl-11 border border-gray-300 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all duration-300 form-input"
                                    placeholder="Enter city name">
                                <div class="absolute left-3 top-1/2 transform -translate-y-1/2 text-gray-400">
                                    <i class="fas fa-city"></i>
                                </div>
                            </div>
                        </div>

                        <!-- State -->
                        <div class="form-group">
                            <label for="state" class="block text-sm font-semibold text-gray-700 mb-3">
                                State
                            </label>
                            <div class="relative">
                                <input type="text" id="state" name="state"
                                    value="<?php echo isset($_POST['state']) ? htmlspecialchars($_POST['state']) : ''; ?>"
                                    class="w-full px-4 py-3 pl-11 border border-gray-300 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all duration-300 form-input"
                                    placeholder="Enter state">
                                <div class="absolute left-3 top-1/2 transform -translate-y-1/2 text-gray-400">
                                    <i class="fas fa-map"></i>
                                </div>
                            </div>
                        </div>

                        <!-- Pincode -->
                        <div class="form-group">
                            <label for="pincode" class="block text-sm font-semibold text-gray-700 mb-3">
                                Pincode
                            </label>
                            <div class="relative">
                                <input type="text" id="pincode" name="pincode"
                                    value="<?php echo isset($_POST['pincode']) ? htmlspecialchars($_POST['pincode']) : ''; ?>"
                                    class="w-full px-4 py-3 pl-11 border border-gray-300 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all duration-300 form-input"
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
                <div class="bg-orange-50 rounded-2xl p-6 transition-all duration-300 hover:shadow-md">
                    <div class="flex items-center mb-6">
                        <div class="w-10 h-10 bg-orange-500 rounded-xl flex items-center justify-center mr-4">
                            <i class="fas fa-edit text-white"></i>
                        </div>
                        <h3 class="font-serif text-xl font-bold text-gray-800">Additional Information</h3>
                    </div>

                    <div class="form-group">
                        <label for="message" class="block text-sm font-semibold text-gray-700 mb-3">
                            Additional Questions or Information
                        </label>
                        <div class="relative">
                            <textarea id="message" name="message" rows="4"
                                class="w-full px-4 py-3 pl-11 border border-gray-300 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all duration-300 form-textarea resize-none"
                                placeholder="Any specific requirements, questions about curriculum, hostel facilities, or additional information you'd like to share..."><?php echo isset($_POST['message']) ? htmlspecialchars($_POST['message']) : ''; ?></textarea>
                            <div class="absolute left-3 top-4 transform text-gray-400">
                                <i class="fas fa-comment-dots"></i>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Form Actions -->
                <div class="bg-gray-50 rounded-2xl p-6">
                    <div class="flex flex-col md:flex-row items-center justify-between space-y-4 md:space-y-0">
                        <div class="text-sm text-gray-600">
                            <p class="flex items-center">
                                <i class="fas fa-shield-alt text-green-500 mr-2"></i>
                                Your information is secure and confidential
                            </p>
                        </div>

                        <div class="flex space-x-4">
                            <button type="button" onclick="window.location.href='<?php echo $base_url; ?>/admissions/index.php'"
                                class="px-6 py-3 border border-gray-300 text-gray-700 font-semibold rounded-xl hover:bg-gray-50 transition-all duration-300 inline-flex items-center">
                                <i class="fas fa-arrow-left mr-2"></i> Back
                            </button>
                            <button type="submit"
                                class="bg-accent hover:bg-red-700 text-white font-semibold py-3 px-8 rounded-xl transition-all duration-300 transform hover:scale-105 shadow-lg hover:shadow-xl inline-flex items-center submit-button">
                                <i class="fas fa-paper-plane mr-3"></i>
                                <span class="submit-text">Submit Application</span>
                                <i class="fas fa-spinner fa-spin ml-2 hidden loading-icon"></i>
                            </button>
                        </div>
                    </div>

                    <div class="mt-4 text-center">
                        <p class="text-xs text-gray-500">
                            <i class="fas fa-asterisk text-red-500 mr-1"></i>
                            Required fields. By submitting this form, you agree to our
                            <a href="#" class="text-accent hover:underline">Privacy Policy</a> and
                            <a href="#" class="text-accent hover:underline">Terms of Service</a>.
                        </p>
                    </div>
                </div>
            </form>
        </div>
    </div>
</section>

<!-- Support Section -->
<section class="py-16 bg-white">
    <div class="max-w-4xl mx-auto px-4">
        <div class="text-center mb-12">
            <h2 class="font-serif text-3xl font-bold text-primary mb-4">
                Need <span class="text-accent">Help?</span>
            </h2>
            <p class="text-gray-600">
                Our admissions team is here to assist you every step of the way
            </p>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
            <div class="text-center p-6 bg-gradient-to-br from-blue-50 to-blue-100 rounded-2xl hover-lift transition-all duration-300">
                <div class="w-16 h-16 bg-blue-500 rounded-2xl flex items-center justify-center mx-auto mb-4">
                    <i class="fas fa-phone text-white text-xl"></i>
                </div>
                <h3 class="font-semibold text-gray-800 mb-2">Call Admissions</h3>
                <p class="text-gray-600 mb-4 text-sm">Speak directly with our team</p>
                <a href="tel:+917618040040" class="text-blue-600 hover:text-blue-700 font-semibold text-sm inline-flex items-center">
                    +91-7618040040
                </a>
            </div>

            <div class="text-center p-6 bg-gradient-to-br from-green-50 to-green-100 rounded-2xl hover-lift transition-all duration-300">
                <div class="w-16 h-16 bg-green-500 rounded-2xl flex items-center justify-center mx-auto mb-4">
                    <i class="fas fa-envelope text-white text-xl"></i>
                </div>
                <h3 class="font-semibold text-gray-800 mb-2">Email Us</h3>
                <p class="text-gray-600 mb-4 text-sm">Get detailed information</p>
                <a href="mailto:admissions@bhaktivedantagurukul.edu" class="text-green-600 hover:text-green-700 font-semibold text-sm inline-flex items-center">
                    admissions@gurukul.edu
                </a>
            </div>

            <div class="text-center p-6 bg-gradient-to-br from-purple-50 to-purple-100 rounded-2xl hover-lift transition-all duration-300">
                <div class="w-16 h-16 bg-purple-500 rounded-2xl flex items-center justify-center mx-auto mb-4">
                    <i class="fas fa-map-marker-alt text-white text-xl"></i>
                </div>
                <h3 class="font-semibold text-gray-800 mb-2">Visit Campus</h3>
                <p class="text-gray-600 mb-4 text-sm">Schedule a personal tour</p>
                <a href="<?php echo $base_url; ?>/contact.php" class="text-purple-600 hover:text-purple-700 font-semibold text-sm inline-flex items-center">
                    Book Campus Tour
                </a>
            </div>
        </div>
    </div>
</section>

<!-- Next Steps -->
<section class="py-16 bg-gray-50">
    <div class="max-w-4xl mx-auto px-4">
        <div class="text-center mb-12">
            <h2 class="font-serif text-3xl font-bold text-primary mb-4">
                What Happens <span class="text-accent">Next?</span>
            </h2>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-4 gap-6">
            <div class="text-center p-6 bg-white rounded-2xl shadow-lg hover-lift transition-all duration-300">
                <div class="w-12 h-12 bg-accent/10 rounded-full flex items-center justify-center mx-auto mb-4">
                    <span class="text-accent font-bold text-lg">1</span>
                </div>
                <h3 class="font-semibold text-gray-800 mb-2">Form Review</h3>
                <p class="text-gray-600 text-sm">We'll review your application within 24 hours</p>
            </div>
            <div class="text-center p-6 bg-white rounded-2xl shadow-lg hover-lift transition-all duration-300">
                <div class="w-12 h-12 bg-accent/10 rounded-full flex items-center justify-center mx-auto mb-4">
                    <span class="text-accent font-bold text-lg">2</span>
                </div>
                <h3 class="font-semibold text-gray-800 mb-2">Initial Contact</h3>
                <p class="text-gray-600 text-sm">Our team will contact you for next steps</p>
            </div>
            <div class="text-center p-6 bg-white rounded-2xl shadow-lg hover-lift transition-all duration-300">
                <div class="w-12 h-12 bg-accent/10 rounded-full flex items-center justify-center mx-auto mb-4">
                    <span class="text-accent font-bold text-lg">3</span>
                </div>
                <h3 class="font-semibold text-gray-800 mb-2">Campus Visit</h3>
                <p class="text-gray-600 text-sm">Schedule a campus tour and interaction</p>
            </div>
            <div class="text-center p-6 bg-white rounded-2xl shadow-lg hover-lift transition-all duration-300">
                <div class="w-12 h-12 bg-accent/10 rounded-full flex items-center justify-center mx-auto mb-4">
                    <span class="text-accent font-bold text-lg">4</span>
                </div>
                <h3 class="font-semibold text-gray-800 mb-2">Admission</h3>
                <p class="text-gray-600 text-sm">Complete documentation and join us</p>
            </div>
        </div>
    </div>
</section>

<?php include '../../includes/footer.php'; ?>

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
        box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.1);
        border-color: #3b82f6;
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
            if (pincode === '') return true; // Optional field
            const pincodeRegex = /^[0-9]{6}$/;
            return pincodeRegex.test(pincode);
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
            }

            showSuccess(fieldId);
            return true;
        }

        // Real-time validation on blur
        const fieldsToValidate = ['student_name', 'parent_name', 'email', 'phone', 'grade', 'pincode'];
        
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

            // Show loading state
            submitButton.disabled = true;
            submitText.textContent = 'Submitting...';
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
    });
</script>