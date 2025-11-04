<?php 
include '../../includes/header.php'; 
include '../../includes/db.php';

// Handle form submission
$success_message = '';
$error_message = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        // Validate required fields
        $required_fields = ['full_name', 'email', 'phone', 'position_type', 'position_applied'];
        $missing_fields = [];
        
        foreach ($required_fields as $field) {
            if (empty(trim($_POST[$field] ?? ''))) {
                $missing_fields[] = $field;
            }
        }
        
        if (!empty($missing_fields)) {
            $error_message = "Please fill in all required fields: " . implode(', ', $missing_fields);
        } else {
            // Validate file uploads
            $resume_path = '';
            $cover_letter_path = '';
            $upload_errors = [];
            
            // Upload resume - IMPROVED SECURITY
            if (isset($_FILES['resume']) && $_FILES['resume']['error'] === UPLOAD_ERR_OK) {
                $resume_name = uniqid() . '_' . preg_replace("/[^a-zA-Z0-9._-]/", "_", basename($_FILES['resume']['name']));
                $resume_target = "../uploads/resumes/" . $resume_name;
                
                // Create directory if it doesn't exist
                if (!is_dir('../uploads/resumes')) {
                    mkdir('../uploads/resumes', 0755, true);
                }
                
                // Enhanced file type validation
                $allowed_types = ['pdf', 'doc', 'docx'];
                $file_extension = strtolower(pathinfo($_FILES['resume']['name'], PATHINFO_EXTENSION));
                $max_file_size = 5 * 1024 * 1024; // 5MB
                
                if (!in_array($file_extension, $allowed_types)) {
                    $upload_errors[] = "Resume must be PDF, DOC, or DOCX format";
                } elseif ($_FILES['resume']['size'] > $max_file_size) {
                    $upload_errors[] = "Resume file size must be less than 5MB";
                } elseif (!move_uploaded_file($_FILES['resume']['tmp_name'], $resume_target)) {
                    $upload_errors[] = "Failed to upload resume file";
                } else {
                    $resume_path = $resume_name;
                }
            } elseif ($_FILES['resume']['error'] !== UPLOAD_ERR_NO_FILE) {
                $upload_errors[] = "Resume upload error: " . getFileUploadError($_FILES['resume']['error']);
            } else {
                $upload_errors[] = "Resume is required";
            }
            
            // Upload cover letter - OPTIONAL but with validation
            if (isset($_FILES['cover_letter']) && $_FILES['cover_letter']['error'] === UPLOAD_ERR_OK) {
                $cover_letter_name = uniqid() . '_' . preg_replace("/[^a-zA-Z0-9._-]/", "_", basename($_FILES['cover_letter']['name']));
                $cover_letter_target = "../uploads/cover_letters/" . $cover_letter_name;
                
                // Create directory if it doesn't exist
                if (!is_dir('../uploads/cover_letters')) {
                    mkdir('../uploads/cover_letters', 0755, true);
                }
                
                // File validation
                $allowed_types = ['pdf', 'doc', 'docx'];
                $file_extension = strtolower(pathinfo($_FILES['cover_letter']['name'], PATHINFO_EXTENSION));
                $max_file_size = 5 * 1024 * 1024;
                
                if (!in_array($file_extension, $allowed_types)) {
                    $upload_errors[] = "Cover letter must be PDF, DOC, or DOCX format";
                } elseif ($_FILES['cover_letter']['size'] > $max_file_size) {
                    $upload_errors[] = "Cover letter file size must be less than 5MB";
                } elseif (!move_uploaded_file($_FILES['cover_letter']['tmp_name'], $cover_letter_target)) {
                    $upload_errors[] = "Failed to upload cover letter";
                } else {
                    $cover_letter_path = $cover_letter_name;
                }
            } elseif ($_FILES['cover_letter']['error'] !== UPLOAD_ERR_NO_FILE) {
                $upload_errors[] = "Cover letter upload error: " . getFileUploadError($_FILES['cover_letter']['error']);
            }
            
            // If there are upload errors, show them
            if (!empty($upload_errors)) {
                $error_message = implode("<br>", $upload_errors);
            } else {
                // Prepare and execute insert statement
                $stmt = $pdo->prepare("INSERT INTO job_applications 
                    (full_name, email, phone, position_type, position_applied, experience_years, current_organization, 
                     current_position, expected_salary, resume_path, cover_letter_path, additional_info, status, applied_at) 
                    VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, 'pending', NOW())");
                
                $stmt->execute([
                    htmlspecialchars(trim($_POST['full_name'])),
                    htmlspecialchars(trim($_POST['email'])),
                    htmlspecialchars(trim($_POST['phone'])),
                    htmlspecialchars(trim($_POST['position_type'])),
                    htmlspecialchars(trim($_POST['position_applied'])),
                    htmlspecialchars(trim($_POST['experience_years'] ?? '0')),
                    htmlspecialchars(trim($_POST['current_organization'] ?? '')),
                    htmlspecialchars(trim($_POST['current_position'] ?? '')),
                    htmlspecialchars(trim($_POST['expected_salary'] ?? '')),
                    $resume_path,
                    $cover_letter_path,
                    htmlspecialchars(trim($_POST['additional_info'] ?? ''))
                ]);
                
                $application_id = $pdo->lastInsertId();
                $success_message = "Thank you for your application! Your application ID is #{$application_id}. We have received your details and will contact you soon.";
                
                // Clear form fields
                $_POST = [];
                $_FILES = [];
            }
        }
    } catch (PDOException $e) {
        error_log("Job Application Error: " . $e->getMessage());
        $error_message = "There was an error submitting your application. Please try again or contact us directly.";
    }
}

// Helper function for file upload errors
function getFileUploadError($error_code) {
    $upload_errors = array(
        UPLOAD_ERR_INI_SIZE => 'File exceeds upload_max_filesize directive in php.ini',
        UPLOAD_ERR_FORM_SIZE => 'File exceeds MAX_FILE_SIZE directive in HTML form',
        UPLOAD_ERR_PARTIAL => 'File was only partially uploaded',
        UPLOAD_ERR_NO_FILE => 'No file was uploaded',
        UPLOAD_ERR_NO_TMP_DIR => 'Missing temporary folder',
        UPLOAD_ERR_CANT_WRITE => 'Failed to write file to disk',
        UPLOAD_ERR_EXTENSION => 'File upload stopped by extension',
    );
    return $upload_errors[$error_code] ?? 'Unknown upload error';
}

// Fetch position types for dropdown
$position_types = [
    'teaching' => 'Teaching Position',
    'non_teaching' => 'Non-Teaching Position',
    'administrative' => 'Technical & Administrative Position'
];

// Fetch available positions - UPDATED WITH MORE POSITIONS
$teaching_positions = [
    'primary_teacher' => 'Primary School Teacher (Grade 1-5)',
    'middle_teacher' => 'Middle School Teacher (Grade 6-8)',
    'high_teacher' => 'High School Teacher (Grade 9-12)',
    'math_teacher' => 'Mathematics Teacher',
    'science_teacher' => 'Science Teacher',
    'english_teacher' => 'English Teacher',
    'hindi_teacher' => 'Hindi Teacher',
    'sanskrit_teacher' => 'Sanskrit Teacher',
    'vedic_teacher' => 'Vedic Studies Teacher',
    'sports_teacher' => 'Sports Teacher',
    'arts_teacher' => 'Arts & Music Teacher',
    'computer_teacher' => 'Computer Science Teacher',
    'social_science_teacher' => 'Social Science Teacher'
];

$non_teaching_positions = [
    'hostel_warden' => 'Hostel Warden',
    'librarian' => 'Librarian',
    'lab_assistant' => 'Lab Assistant',
    'counselor' => 'Student Counselor',
    'nurse' => 'School Nurse'
];

$administrative_positions = [
    'Frontend Developer' => 'Frontend Developer',
    'Backend Developer' => 'Backend Developer',
    'Full Stack Developer' => 'Full Stack Developer',
    'Graphic Designer' => 'Graphic Designer',
    'administrator' => 'Administrative Officer',
    'it_support' => 'IT Support Staff',
    'accountant' => 'Accountant',
    'Digital Marketing Specialist' => 'Digital Marketing Specialist',
    'Video Editor' => 'Video Editor',
    'housekeeping' => 'Housekeeping Staff',
    'security' => 'Security Personnel'
];
?>

<!-- Hero Section -->
<section class="bg-gradient-to-br from-primary to-accent text-white py-20 lg:py-28">
    <div class="max-w-7xl mx-auto px-4">
        <div class="text-center">
            <h1 class="font-serif text-4xl md:text-5xl lg:text-6xl font-bold mb-6">
                Join Our <span class="text-yellow-300">Team</span>
            </h1>
            <p class="text-xl md:text-2xl mb-8 text-gray-200 max-w-3xl mx-auto">
                Build Your Career at Bhaktivedanta Gurukul
            </p>
            <p class="text-lg text-gray-300 max-w-2xl mx-auto">
                We're looking for passionate educators and staff to join our mission of holistic education
            </p>
            
            <!-- Quick Stats -->
            <div class="grid grid-cols-2 md:grid-cols-4 gap-6 mt-12 max-w-2xl mx-auto">
                <div class="text-center">
                    <div class="text-2xl font-bold text-yellow-300 mb-2">50+</div>
                    <div class="text-gray-300 text-sm">Faculty Members</div>
                </div>
                <div class="text-center">
                    <div class="text-2xl font-bold text-yellow-300 mb-2">100+</div>
                    <div class="text-gray-300 text-sm">Staff Members</div>
                </div>
                <div class="text-center">
                    <div class="text-2xl font-bold text-yellow-300 mb-2">24/7</div>
                    <div class="text-gray-300 text-sm">Support</div>
                </div>
                <div class="text-center">
                    <div class="text-2xl font-bold text-yellow-300 mb-2">Fast</div>
                    <div class="text-gray-300 text-sm">Response</div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Application Form Section -->
<section class="py-20 bg-gray-50">
    <div class="max-w-4xl mx-auto px-4">
        <div class="bg-white rounded-2xl p-6 md:p-8 shadow-lg border border-gray-200">
            <!-- Form Header -->
            <div class="text-center mb-8">
                <h2 class="font-serif text-3xl font-bold text-primary mb-4">
                    Job Application Form
                </h2>
                <p class="text-gray-600 max-w-2xl mx-auto">
                    Complete the form below to apply for a position at Bhaktivedanta Gurukul. 
                    Fields marked with <span class="text-red-500">*</span> are required.
                </p>
            </div>
            
            <!-- Success/Error Messages -->
            <?php if ($success_message): ?>
            <div class="bg-green-50 border border-green-200 rounded-xl p-4 mb-6 animate-fade-in">
                <div class="flex items-start">
                    <div class="w-10 h-10 bg-green-100 rounded-lg flex items-center justify-center mr-4 flex-shrink-0">
                        <i class="fas fa-check text-green-600"></i>
                    </div>
                    <div>
                        <h3 class="font-semibold text-green-800 text-lg mb-2">Application Submitted Successfully!</h3>
                        <p class="text-green-700"><?php echo $success_message; ?></p>
                    </div>
                </div>
            </div>
            <?php endif; ?>
            
            <?php if ($error_message): ?>
            <div class="bg-red-50 border border-red-200 rounded-xl p-4 mb-6 animate-fade-in">
                <div class="flex items-start">
                    <div class="w-10 h-10 bg-red-100 rounded-lg flex items-center justify-center mr-4 flex-shrink-0">
                        <i class="fas fa-exclamation-triangle text-red-600"></i>
                    </div>
                    <div>
                        <h3 class="font-semibold text-red-800 text-lg mb-2">Submission Error</h3>
                        <p class="text-red-700"><?php echo $error_message; ?></p>
                    </div>
                </div>
            </div>
            <?php endif; ?>
            
            <!-- Progress Indicator -->
            <div class="mb-8">
                <div class="flex items-center justify-between mb-2">
                    <span class="text-sm font-medium text-gray-700">Application Progress</span>
                    <span class="text-sm font-medium text-gray-700"><span id="progress-percentage">0</span>%</span>
                </div>
                <div class="w-full bg-gray-200 rounded-full h-2">
                    <div id="progress-bar" class="bg-green-600 h-2 rounded-full transition-all duration-500" style="width: 0%"></div>
                </div>
            </div>
            
            <form method="POST" class="space-y-8" id="application-form" enctype="multipart/form-data">
                <!-- Personal Information -->
                <div class="bg-blue-50 rounded-xl p-6 border border-blue-200">
                    <h3 class="font-serif text-xl font-bold text-primary mb-6 flex items-center">
                        <i class="fas fa-user-circle text-blue-500 mr-3"></i>
                        Personal Information
                    </h3>
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label for="full_name" class="block text-sm font-semibold text-primary mb-2">
                                Full Name <span class="text-red-500">*</span>
                            </label>
                            <input type="text" id="full_name" name="full_name" required 
                                value="<?php echo htmlspecialchars($_POST['full_name'] ?? ''); ?>"
                                class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all duration-300"
                                placeholder="Enter your full name">
                        </div>
                        
                        <div>
                            <label for="email" class="block text-sm font-semibold text-primary mb-2">
                                Email Address <span class="text-red-500">*</span>
                            </label>
                            <input type="email" id="email" name="email" required 
                                value="<?php echo htmlspecialchars($_POST['email'] ?? ''); ?>"
                                class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all duration-300"
                                placeholder="Enter your email address">
                        </div>
                        
                        <div>
                            <label for="phone" class="block text-sm font-semibold text-primary mb-2">
                                Phone Number <span class="text-red-500">*</span>
                            </label>
                            <input type="tel" id="phone" name="phone" required 
                                value="<?php echo htmlspecialchars($_POST['phone'] ?? ''); ?>"
                                class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all duration-300"
                                placeholder="Enter your 10-digit phone number">
                            <p class="text-xs text-gray-500 mt-1">We'll contact you on this number</p>
                        </div>
                        
                        <div>
                            <label for="experience_years" class="block text-sm font-semibold text-primary mb-2">
                                Total Experience (Years)
                            </label>
                            <input type="number" id="experience_years" name="experience_years" 
                                value="<?php echo htmlspecialchars($_POST['experience_years'] ?? '0'); ?>"
                                class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all duration-300"
                                placeholder="Enter years of experience" min="0" max="50" step="0.5">
                        </div>
                    </div>
                </div>

                <!-- Position Information -->
                <div class="bg-green-50 rounded-xl p-6 border border-green-200">
                    <h3 class="font-serif text-xl font-bold text-primary mb-6 flex items-center">
                        <i class="fas fa-briefcase text-green-500 mr-3"></i>
                        Position Information
                    </h3>
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label for="position_type" class="block text-sm font-semibold text-primary mb-2">
                                Position Type <span class="text-red-500">*</span>
                            </label>
                            <select id="position_type" name="position_type" required 
                                class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-transparent transition-all duration-300">
                                <option value="">Select Position Type</option>
                                <?php foreach($position_types as $value => $label): ?>
                                <option value="<?php echo $value; ?>" <?php echo (isset($_POST['position_type']) && $_POST['position_type'] == $value) ? 'selected' : ''; ?>>
                                    <?php echo $label; ?>
                                </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        
                        <div>
                            <label for="position_applied" class="block text-sm font-semibold text-primary mb-2">
                                Position Applied For <span class="text-red-500">*</span>
                            </label>
                            <select id="position_applied" name="position_applied" required 
                                class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-transparent transition-all duration-300">
                                <option value="">Select Position Type First</option>
                            </select>
                        </div>
                        
                        <div>
                            <label for="current_organization" class="block text-sm font-semibold text-primary mb-2">
                                Current Organization
                            </label>
                            <input type="text" id="current_organization" name="current_organization" 
                                value="<?php echo htmlspecialchars($_POST['current_organization'] ?? ''); ?>"
                                class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-transparent transition-all duration-300"
                                placeholder="Enter current organization name">
                        </div>
                        
                        <div>
                            <label for="current_position" class="block text-sm font-semibold text-primary mb-2">
                                Current Position
                            </label>
                            <input type="text" id="current_position" name="current_position" 
                                value="<?php echo htmlspecialchars($_POST['current_position'] ?? ''); ?>"
                                class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-transparent transition-all duration-300"
                                placeholder="Enter your current position">
                        </div>
                    </div>
                    
                    <div class="mt-6 grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label for="expected_salary" class="block text-sm font-semibold text-primary mb-2">
                                Expected Salary (per month)
                            </label>
                            <input type="text" id="expected_salary" name="expected_salary" 
                                value="<?php echo htmlspecialchars($_POST['expected_salary'] ?? ''); ?>"
                                class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-transparent transition-all duration-300"
                                placeholder="e.g., ₹35,000 - ₹45,000">
                        </div>
                        
                        <div class="flex items-end">
                            <div class="text-sm text-gray-600 bg-gray-100 p-3 rounded-lg w-full">
                                <i class="fas fa-info-circle text-blue-500 mr-2"></i>
                                Salary will be discussed based on experience and qualifications
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Documents Upload -->
                <div class="bg-purple-50 rounded-xl p-6 border border-purple-200">
                    <h3 class="font-serif text-xl font-bold text-primary mb-6 flex items-center">
                        <i class="fas fa-file-upload text-purple-500 mr-3"></i>
                        Documents Upload
                    </h3>
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label for="resume" class="block text-sm font-semibold text-primary mb-2">
                                Resume/CV <span class="text-red-500">*</span>
                            </label>
                            <div class="border-2 border-dashed border-gray-300 rounded-lg p-4 text-center hover:border-purple-400 transition-colors duration-300">
                                <input type="file" id="resume" name="resume" accept=".pdf,.doc,.docx" required 
                                    class="hidden" onchange="updateFileName('resume', 'resume-name')">
                                <div id="resume-upload-area" class="cursor-pointer">
                                    <i class="fas fa-cloud-upload-alt text-3xl text-gray-400 mb-2"></i>
                                    <p class="text-sm text-gray-600 mb-1">Click to upload resume</p>
                                    <p class="text-xs text-gray-500">PDF, DOC, DOCX (Max: 5MB)</p>
                                </div>
                                <div id="resume-name" class="text-sm text-green-600 font-medium mt-2 hidden"></div>
                            </div>
                        </div>
                        
                        <div>
                            <label for="cover_letter" class="block text-sm font-semibold text-primary mb-2">
                                Cover Letter (Optional)
                            </label>
                            <div class="border-2 border-dashed border-gray-300 rounded-lg p-4 text-center hover:border-purple-400 transition-colors duration-300">
                                <input type="file" id="cover_letter" name="cover_letter" accept=".pdf,.doc,.docx" 
                                    class="hidden" onchange="updateFileName('cover_letter', 'cover-letter-name')">
                                <div id="cover-letter-upload-area" class="cursor-pointer">
                                    <i class="fas fa-cloud-upload-alt text-3xl text-gray-400 mb-2"></i>
                                    <p class="text-sm text-gray-600 mb-1">Click to upload cover letter</p>
                                    <p class="text-xs text-gray-500">PDF, DOC, DOCX (Max: 5MB)</p>
                                </div>
                                <div id="cover-letter-name" class="text-sm text-green-600 font-medium mt-2 hidden"></div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Additional Information -->
                <div class="bg-yellow-50 rounded-xl p-6 border border-yellow-200">
                    <h3 class="font-serif text-xl font-bold text-primary mb-6 flex items-center">
                        <i class="fas fa-comment-alt text-yellow-500 mr-3"></i>
                        Additional Information
                    </h3>
                    
                    <div>
                        <label for="additional_info" class="block text-sm font-semibold text-primary mb-2">
                            Why do you want to work at Bhaktivedanta Gurukul?
                        </label>
                        <textarea id="additional_info" name="additional_info" rows="6"
                            class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-yellow-500 focus:border-transparent transition-all duration-300 resize-none"
                            placeholder="Tell us about your motivation, relevant experience, teaching philosophy, and why you're interested in joining our institution..."><?php echo htmlspecialchars($_POST['additional_info'] ?? ''); ?></textarea>
                        <div class="flex justify-between items-center mt-2">
                            <p class="text-xs text-gray-500">
                                Minimum 100 characters recommended
                            </p>
                            <span id="char-count" class="text-xs text-gray-500">0 characters</span>
                        </div>
                    </div>
                </div>

                <!-- Consent and Submission -->
                <div class="bg-white rounded-xl p-6 border border-gray-300 shadow-sm">
                    <div class="space-y-4">
                        <div class="flex items-start space-x-3">
                            <input type="checkbox" id="consent" name="consent" required 
                                class="w-5 h-5 text-accent border-gray-300 rounded focus:ring-accent mt-1">
                            <label for="consent" class="text-sm text-gray-700 flex-1">
                                <span class="font-semibold">Declaration:</span> I hereby declare that all the information provided in this application is true and correct to the best of my knowledge. I understand that any false information may lead to disqualification or termination of employment.
                            </label>
                        </div>
                        
                        <div class="flex items-start space-x-3">
                            <input type="checkbox" id="privacy" name="privacy" required 
                                class="w-5 h-5 text-accent border-gray-300 rounded focus:ring-accent mt-1">
                            <label for="privacy" class="text-sm text-gray-700 flex-1">
                                I agree to the <a href="../privacy-policy.php" class="text-accent hover:underline font-medium">privacy policy</a> and consent to the processing of my personal data for recruitment purposes.
                            </label>
                        </div>
                    </div>
                    
                    <div class="text-center mt-8">
                        <button type="submit" class="bg-gradient-to-r from-accent to-red-600 text-white hover:from-red-600 hover:to-accent font-semibold py-4 px-12 rounded-lg transition-all duration-300 text-lg transform hover:scale-105 shadow-lg hover:shadow-xl flex items-center mx-auto">
                            <i class="fas fa-paper-plane mr-3"></i> 
                            <span class="submit-text">Submit Application</span>
                            <i class="fas fa-spinner fa-spin ml-2 hidden loading-icon"></i>
                        </button>
                        <p class="text-sm text-gray-500 mt-3">
                            You'll receive a confirmation email after successful submission
                        </p>
                    </div>
                </div>
            </form>
        </div>
    </div>
</section>

<!-- Contact Information -->
<section class="py-20 bg-white">
    <div class="max-w-4xl mx-auto px-4">
        <div class="text-center mb-12">
            <h2 class="font-serif text-3xl md:text-4xl font-bold text-primary mb-4">
                Need <span class="text-accent">Assistance?</span>
            </h2>
            <p class="text-xl text-gray-600 max-w-2xl mx-auto">
                Our HR team is available to help you with the application process
            </p>
        </div>
        
        <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
            <div class="text-center p-6 bg-gray-50 rounded-xl hover:shadow-md transition-shadow duration-300">
                <div class="w-16 h-16 bg-accent rounded-full flex items-center justify-center mx-auto mb-4">
                    <i class="fas fa-phone text-white text-xl"></i>
                </div>
                <h3 class="font-semibold text-primary mb-2 text-lg">Call Us</h3>
                <p class="text-gray-600 font-medium">+91-7618040040</p>
                <p class="text-gray-500 text-sm mt-1">Mon-Sat, 8:00 AM - 5:00 PM</p>
            </div>
            
            <div class="text-center p-6 bg-gray-50 rounded-xl hover:shadow-md transition-shadow duration-300">
                <div class="w-16 h-16 bg-accent rounded-full flex items-center justify-center mx-auto mb-4">
                    <i class="fas fa-envelope text-white text-xl"></i>
                </div>
                <h3 class="font-semibold text-primary mb-2 text-lg">Email Us</h3>
                <p class="text-gray-600 font-medium">hr@ourgurukul.org</p>
                <p class="text-gray-500 text-sm mt-1">We respond within 24 hours</p>
            </div>
            
            <div class="text-center p-6 bg-gray-50 rounded-xl hover:shadow-md transition-shadow duration-300">
                <div class="w-16 h-16 bg-accent rounded-full flex items-center justify-center mx-auto mb-4">
                    <i class="fas fa-map-marker-alt text-white text-xl"></i>
                </div>
                <h3 class="font-semibold text-primary mb-2 text-lg">Visit Campus</h3>
                <p class="text-gray-600 font-medium">Bhaktivedanta Gurukul</p>
                <p class="text-gray-500 text-sm mt-1">Prayagraj, Uttar Pradesh</p>
            </div>
        </div>
    </div>
</section>

<style>
.animate-fade-in {
    animation: fadeIn 0.5s ease-in-out;
}

@keyframes fadeIn {
    from { opacity: 0; transform: translateY(-10px); }
    to { opacity: 1; transform: translateY(0); }
}

.file-upload-area:hover {
    border-color: #8b5cf6;
    background-color: #faf5ff;
}
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const applicationForm = document.getElementById('application-form');
    const positionTypeSelect = document.getElementById('position_type');
    const positionAppliedSelect = document.getElementById('position_applied');
    const progressBar = document.getElementById('progress-bar');
    const progressPercentage = document.getElementById('progress-percentage');
    const charCount = document.getElementById('char-count');
    const additionalInfo = document.getElementById('additional_info');
    
    // Position options data
    const positionOptions = {
        teaching: <?php echo json_encode($teaching_positions); ?>,
        non_teaching: <?php echo json_encode($non_teaching_positions); ?>,
        administrative: <?php echo json_encode($administrative_positions); ?>
    };
    
    // Update position applied options based on position type
    positionTypeSelect.addEventListener('change', function() {
        const selectedType = this.value;
        positionAppliedSelect.innerHTML = '<option value="">Select Position</option>';
        
        if (selectedType && positionOptions[selectedType]) {
            Object.entries(positionOptions[selectedType]).forEach(([value, label]) => {
                const option = document.createElement('option');
                option.value = value;
                option.textContent = label;
                positionAppliedSelect.appendChild(option);
            });
        }
        updateProgress();
    });
    
    // Character count for additional info
    additionalInfo.addEventListener('input', function() {
        const count = this.value.length;
        charCount.textContent = count + ' characters';
        updateProgress();
    });
    
    // File upload click handlers
    document.getElementById('resume-upload-area').addEventListener('click', function() {
        document.getElementById('resume').click();
    });
    
    document.getElementById('cover-letter-upload-area').addEventListener('click', function() {
        document.getElementById('cover_letter').click();
    });
    
    // Progress tracking
    function updateProgress() {
        let progress = 0;
        const fields = [
            'full_name', 'email', 'phone', 'position_type', 'position_applied', 'resume'
        ];
        
        fields.forEach(field => {
            const element = document.getElementById(field);
            if (element && element.value.trim()) {
                progress += 15; // Each field is 15%
            }
        });
        
        // Additional info bonus
        if (additionalInfo.value.length >= 100) {
            progress += 10;
        }
        
        progress = Math.min(progress, 100);
        progressBar.style.width = progress + '%';
        progressPercentage.textContent = progress;
    }
    
    // Update progress on any input
    const formInputs = applicationForm.querySelectorAll('input, select, textarea');
    formInputs.forEach(input => {
        input.addEventListener('input', updateProgress);
        input.addEventListener('change', updateProgress);
    });
    
    // Initialize progress
    updateProgress();
    
    // Form validation
    if (applicationForm) {
        applicationForm.addEventListener('submit', function(e) {
            let isValid = true;
            
            // Basic validation
            const requiredFields = applicationForm.querySelectorAll('[required]');
            requiredFields.forEach(field => {
                if (!field.value.trim()) {
                    isValid = false;
                    field.classList.add('border-red-500');
                }
            });
            
            // File validation
            const resumeFile = document.getElementById('resume').files[0];
            if (resumeFile) {
                const maxSize = 5 * 1024 * 1024; // 5MB
                const allowedTypes = ['application/pdf', 'application/msword', 'application/vnd.openxmlformats-officedocument.wordprocessingml.document'];
                
                if (resumeFile.size > maxSize) {
                    alert('Resume file size must be less than 5MB');
                    isValid = false;
                } else if (!allowedTypes.includes(resumeFile.type)) {
                    alert('Resume must be PDF, DOC, or DOCX format');
                    isValid = false;
                }
            }
            
            if (!isValid) {
                e.preventDefault();
                alert('Please fill in all required fields correctly.');
            } else {
                // Add loading state
                const submitBtn = applicationForm.querySelector('button[type="submit"]');
                const submitText = submitBtn.querySelector('.submit-text');
                const loadingIcon = submitBtn.querySelector('.loading-icon');
                
                submitText.textContent = 'Submitting...';
                loadingIcon.classList.remove('hidden');
                submitBtn.disabled = true;
            }
        });
    }
});

// Update file name display
function updateFileName(inputId, displayId) {
    const fileInput = document.getElementById(inputId);
    const displayElement = document.getElementById(displayId);
    const uploadArea = document.getElementById(inputId + '-upload-area');
    
    if (fileInput.files.length > 0) {
        const fileName = fileInput.files[0].name;
        displayElement.textContent = fileName;
        displayElement.classList.remove('hidden');
        uploadArea.classList.add('hidden');
    } else {
        displayElement.classList.add('hidden');
        uploadArea.classList.remove('hidden');
    }
}

// Initialize position options if type is already selected
const positionTypeSelect = document.getElementById('position_type');
if (positionTypeSelect.value) {
    positionTypeSelect.dispatchEvent(new Event('change'));
    
    // Set previously selected position applied value
    const previousPosition = "<?php echo $_POST['position_applied'] ?? ''; ?>";
    if (previousPosition) {
        setTimeout(() => {
            document.getElementById('position_applied').value = previousPosition;
        }, 100);
    }
}
</script>

<?php include '../../includes/footer.php'; ?>