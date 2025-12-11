<?php
// Check if session is already started
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
include '../../includes/db.php';

// Function to generate Registration ID: BVG+timestamp+dob+random
function generateRegistrationID($dob) {
    $timestamp = time(); // Current timestamp
    $date_part = date('Ymd', $timestamp); // YYYYMMDD format
    
    // Extract year, month, day from DOB
    $dob_parts = explode('-', $dob);
    $dob_code = '';
    if (count($dob_parts) == 3) {
        $dob_code = substr($dob_parts[2], -2) . $dob_parts[1] . substr($dob_parts[0], -2);
    } else {
        $dob_code = date('dmy');
    }
    
    $random = mt_rand(1000, 9999); // 4-digit random number
    
    // Format: BVG + YYYYMMDD + DDMMYY + RANDOM
    $registration_id = 'BVG' . $date_part . $dob_code . $random;
    
    return $registration_id;
}

// Define upload directory path
$upload_dir = __DIR__ . '/uploads/';

// Check if we are in edit mode and preserve form data
$form_data = [];
if (isset($_SESSION['edit_mode']) && $_SESSION['edit_mode'] === true && isset($_SESSION['form_data'])) {
    $form_data = $_SESSION['form_data'];
    unset($_SESSION['edit_mode']); // Clear edit mode flag
}

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Generate Registration ID
    $dob = $_POST['dob'];
    $registration_id = generateRegistrationID($dob);
    
    // Store registration ID in session
    $_SESSION['registration_id'] = $registration_id;
    
    // Store form data in session for payment page
    $_SESSION['form_data'] = $_POST;
    $_SESSION['form_data']['registration_id'] = $registration_id;

    // Auto-create uploads directory if it doesn't exist
    if (!file_exists($upload_dir)) {
        if (!mkdir($upload_dir, 0777, true)) {
            $_SESSION['error'] = 'Failed to create upload directory. Please create "uploads" folder manually in register directory.';
            header('Location: register.php');
            exit;
        }
    }

    // Handle file uploads
    if (isset($_FILES['photo']) && $_FILES['photo']['error'] === 0) {
        $photo_name = $registration_id . '_photo_' . uniqid() . '.' . pathinfo($_FILES['photo']['name'], PATHINFO_EXTENSION);
        $photo_path = $upload_dir . $photo_name;
        
        // Validate file type
        $allowed_extensions = ['jpg', 'jpeg', 'png'];
        $file_extension = strtolower(pathinfo($photo_name, PATHINFO_EXTENSION));
        
        if (in_array($file_extension, $allowed_extensions)) {
            // File size check (2MB = 2097152 bytes)
            if ($_FILES['photo']['size'] <= 2097152) {
                if (move_uploaded_file($_FILES['photo']['tmp_name'], $photo_path)) {
                    $_SESSION['form_data']['photo'] = $photo_name;
                } else {
                    $_SESSION['error'] = 'Photo upload failed! Please try again.';
                }
            } else {
                $_SESSION['error'] = 'Photo size should be less than 2MB!';
            }
        } else {
            $_SESSION['error'] = 'Only JPG, PNG images are allowed for photo!';
        }
    } elseif (isset($form_data['photo'])) {
        // Preserve existing photo if not re-uploaded
        $_SESSION['form_data']['photo'] = $form_data['photo'];
    }

    if (isset($_FILES['sign']) && $_FILES['sign']['error'] === 0) {
        $sign_name = $registration_id . '_sign_' . uniqid() . '.' . pathinfo($_FILES['sign']['name'], PATHINFO_EXTENSION);
        $sign_path = $upload_dir . $sign_name;
        
        // Validate file type
        $allowed_extensions = ['jpg', 'jpeg', 'png'];
        $file_extension = strtolower(pathinfo($sign_name, PATHINFO_EXTENSION));
        
        if (in_array($file_extension, $allowed_extensions)) {
            // File size check (2MB = 2097152 bytes)
            if ($_FILES['sign']['size'] <= 2097152) {
                if (move_uploaded_file($_FILES['sign']['tmp_name'], $sign_path)) {
                    $_SESSION['form_data']['sign'] = $sign_name;
                } else {
                    $_SESSION['error'] = 'Signature upload failed! Please try again.';
                }
            } else {
                $_SESSION['error'] = 'Signature size should be less than 2MB!';
            }
        } else {
            $_SESSION['error'] = 'Only JPG, PNG images are allowed for signature!';
        }
    } elseif (isset($form_data['sign'])) {
        // Preserve existing signature if not re-uploaded
        $_SESSION['form_data']['sign'] = $form_data['sign'];
    }

    // If there's an error, show it on register page
    if (isset($_SESSION['error'])) {
        header('Location: register.php');
        exit;
    }

    // Store temporary data in database before payment
    try {
        $stmt = $conn->prepare("INSERT INTO scholarship_registrations_temp 
            (registration_id, name, father_name, mother_name, gender, dob, class, school_name, 
             address, landmark, city, district, state, pincode, contact, alt_contact, 
             email, aadhaar, photo, signature) 
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
        
        $stmt->bind_param("ssssssisssssssssssss",
            $registration_id,
            $_POST['name'],
            $_POST['father_name'],
            $_POST['mother_name'],
            $_POST['gender'],
            $_POST['dob'],
            $_POST['class'],
            $_POST['school_name'],
            $_POST['address'],
            $_POST['landmark'] ?? '',
            $_POST['city'],
            $_POST['district'],
            $_POST['state'],
            $_POST['pincode'],
            $_POST['contact'],
            $_POST['alt_contact'] ?? '',
            $_POST['email'],
            $_POST['aadhaar'],
            $_SESSION['form_data']['photo'] ?? '',
            $_SESSION['form_data']['sign'] ?? ''
        );
        
        if ($stmt->execute()) {
            $_SESSION['temp_id'] = $stmt->insert_id;
        }
        $stmt->close();
    } catch (Exception $e) {
        // Log error but continue to payment page
        error_log("Temp registration error: " . $e->getMessage());
    }

    // Redirect to payment page
    header('Location: payment.php');
    exit;
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>BVGF50 Scholarship Registration 2025</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <link rel="icon" href="../images/bvgLogo.png">
    <link href="https://fonts.googleapis.com/css2?family=Merriweather:wght@400;700&display=swap" rel="stylesheet">

    <link rel="stylesheet" href="css/register.css">
    <style>
        .maroon-text { color: #800000; }
        .maroon-bg { background-color: #800000; }
        .maroon-border { border-color: #800000; }
        .brown-text { color: #8B4513; }
        .gold-bg { background-color: #FFD700; }
        .form-section { transition: all 0.3s ease; }
        .form-section:hover { box-shadow: 0 5px 15px rgba(128, 0, 0, 0.1); }
    </style>
</head>

<body class="bg-gray-50">
    <!-- Header -->
    <?php include '../../includes/header.php'; ?>

    <!-- Error Message Display -->
    <?php if (isset($_SESSION['error'])): ?>
        <div class="container mx-auto px-4 py-2">
            <div class="max-w-4xl mx-auto">
                <div class="bg-red-100 border-l-4 border-red-500 text-red-700 p-4 mb-4" role="alert">
                    <div class="flex">
                        <div class="py-1">
                            <i class="fas fa-exclamation-circle text-red-500 mr-3"></i>
                        </div>
                        <div>
                            <p class="font-bold">Error!</p>
                            <p><?php echo $_SESSION['error']; unset($_SESSION['error']); ?></p>
                        </div>
                        <button type="button" class="ml-auto -mx-1.5 -my-1.5 bg-red-100 text-red-500 rounded-lg focus:ring-2 focus:ring-red-400 p-1.5 hover:bg-red-200 inline-flex items-center justify-center h-8 w-8" onclick="this.parentElement.parentElement.remove()">
                            <span class="sr-only">Close</span>
                            <i class="fas fa-times"></i>
                        </button>
                    </div>
                </div>
            </div>
        </div>
    <?php endif; ?>

    <!-- Registration Form -->
    <div class="container mx-auto px-4 py-8">
        <div class="max-w-4xl mx-auto">
            <!-- Page Header -->
            <div class="text-center mb-8">
                <h1 class="text-4xl font-bold maroon-text mb-4" style="font-family: 'Merriweather', serif;">
                    Gurukul Fortunate 51 - Scholarship Registration 2026
                </h1>
                <p class="text-lg text-gray-600 mb-2">भक्तिवेदांत गुरुकुल Fortunate 51 - छात्रवृत्ति पंजीकरण 2026</p>
                <div class="gold-bg rounded-lg p-3 inline-block">
                    <p class="maroon-text font-semibold"><i class="fas fa-info-circle mr-2"></i>Classes 6, 7 & 8</p>
                </div>
            </div>

            <div class="bg-white rounded-xl shadow-lg overflow-hidden form-section">
                <div class="maroon-bg text-white py-4 px-6">
                    <h2 class="text-2xl font-bold">Student Registration Form</h2>
                    <p class="text-yellow-200">छात्र पंजीकरण फॉर्म</p>
                    <?php if (!empty($form_data)): ?>
                        <p class="text-sm text-yellow-200 mt-1">
                            <i class="fas fa-edit mr-1"></i>Edit Mode: You can modify your application
                        </p>
                    <?php endif; ?>
                </div>

                <form id="registrationForm" method="POST" enctype="multipart/form-data" class="p-6 space-y-8">
                    <!-- Personal Details -->
                    <div class="border-2 maroon-border rounded-lg p-6 form-section">
                        <h3 class="text-xl font-bold maroon-text mb-4 flex items-center">
                            <i class="fas fa-user-circle mr-3"></i>
                            Personal Details / व्यक्तिगत विवरण
                        </h3>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <label class="block text-sm font-semibold brown-text mb-2">Full Name / पूरा नाम *</label>
                                <input type="text" name="name" required
                                    value="<?php echo htmlspecialchars($form_data['name'] ?? ''); ?>"
                                    class="w-full px-4 py-3 border-2 border-gray-300 rounded-lg focus:ring-2 focus:ring-[#800000] focus:border-[#800000] transition-all"
                                    placeholder="Enter full name">
                            </div>

                            <div>
                                <label class="block text-sm font-semibold brown-text mb-2">Father's Name / पिता का नाम *</label>
                                <input type="text" name="father_name" required
                                    value="<?php echo htmlspecialchars($form_data['father_name'] ?? ''); ?>"
                                    class="w-full px-4 py-3 border-2 border-gray-300 rounded-lg focus:ring-2 focus:ring-[#800000] focus:border-[#800000] transition-all"
                                    placeholder="Enter father's name">
                            </div>

                            <div>
                                <label class="block text-sm font-semibold brown-text mb-2">Mother's Name / माता का नाम *</label>
                                <input type="text" name="mother_name" required
                                    value="<?php echo htmlspecialchars($form_data['mother_name'] ?? ''); ?>"
                                    class="w-full px-4 py-3 border-2 border-gray-300 rounded-lg focus:ring-2 focus:ring-[#800000] focus:border-[#800000] transition-all"
                                    placeholder="Enter mother's name">
                            </div>

                            <div>
                                <label class="block text-sm font-semibold brown-text mb-2">Gender / लिंग *</label>
                                <select name="gender" required
                                    class="w-full px-4 py-3 border-2 border-gray-300 rounded-lg focus:ring-2 focus:ring-[#800000] focus:border-[#800000] transition-all">
                                    <option value="">Select Gender / लिंग चुनें</option>
                                    <option value="Male" <?php echo (isset($form_data['gender']) && $form_data['gender'] === 'Male') ? 'selected' : ''; ?>>Male / पुरुष</option>
                                    <option value="Female" <?php echo (isset($form_data['gender']) && $form_data['gender'] === 'Female') ? 'selected' : ''; ?>>Female / महिला</option>
                                    <option value="Other" <?php echo (isset($form_data['gender']) && $form_data['gender'] === 'Other') ? 'selected' : ''; ?>>Other / अन्य</option>
                                </select>
                            </div>

                            <div>
                                <label class="block text-sm font-semibold brown-text mb-2">
                                    Date of Birth / जन्म तिथि *
                                </label>
                                <input type="date" name="dob" required
                                    max="<?php echo date('Y-m-d'); ?>"
                                    value="<?php echo htmlspecialchars($form_data['dob'] ?? ''); ?>"
                                    class="w-full px-4 py-3 border-2 border-gray-300 rounded-lg focus:ring-2 focus:ring-[#800000] focus:border-[#800000] transition-all">
                            </div>

                            <div>
                                <label class="block text-sm font-semibold brown-text mb-2">Class / कक्षा *</label>
                                <select name="class" required
                                    class="w-full px-4 py-3 border-2 border-gray-300 rounded-lg focus:ring-2 focus:ring-[#800000] focus:border-[#800000] transition-all">
                                    <option value="">Select Class / कक्षा चुनें</option>
                                    <option value="6" <?php echo (isset($form_data['class']) && $form_data['class'] === '6') ? 'selected' : ''; ?>>Class 6 / कक्षा 6</option>
                                    <option value="7" <?php echo (isset($form_data['class']) && $form_data['class'] === '7') ? 'selected' : ''; ?>>Class 7 / कक्षा 7</option>
                                    <option value="8" <?php echo (isset($form_data['class']) && $form_data['class'] === '8') ? 'selected' : ''; ?>>Class 8 / कक्षा 8</option>
                                </select>
                            </div>

                            <div class="md:col-span-2">
                                <label class="block text-sm font-semibold brown-text mb-2">School Name / विद्यालय का नाम *</label>
                                <input type="text" name="school_name" required
                                    value="<?php echo htmlspecialchars($form_data['school_name'] ?? ''); ?>"
                                    class="w-full px-4 py-3 border-2 border-gray-300 rounded-lg focus:ring-2 focus:ring-[#800000] focus:border-[#800000] transition-all"
                                    placeholder="Enter school name">
                            </div>
                        </div>
                    </div>

                    <!-- Complete Address -->
                    <div class="border-2 maroon-border rounded-lg p-6 form-section">
                        <h3 class="text-xl font-bold maroon-text mb-4 flex items-center">
                            <i class="fas fa-home mr-3"></i>
                            Complete Address / पूरा पता
                        </h3>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div class="md:col-span-2">
                                <label class="block text-sm font-semibold brown-text mb-2">House No. & Street / मकान नंबर और गली *</label>
                                <input type="text" name="address" required
                                    value="<?php echo htmlspecialchars($form_data['address'] ?? ''); ?>"
                                    class="w-full px-4 py-3 border-2 border-gray-300 rounded-lg focus:ring-2 focus:ring-[#800000] focus:border-[#800000] transition-all"
                                    placeholder="Enter house number and street">
                            </div>

                            <div class="md:col-span-2">
                                <label class="block text-sm font-semibold brown-text mb-2">Landmark / लैंडमार्क</label>
                                <input type="text" name="landmark"
                                    value="<?php echo htmlspecialchars($form_data['landmark'] ?? ''); ?>"
                                    class="w-full px-4 py-3 border-2 border-gray-300 rounded-lg focus:ring-2 focus:ring-[#800000] focus:border-[#800000] transition-all"
                                    placeholder="Enter nearby landmark">
                            </div>

                            <div>
                                <label class="block text-sm font-semibold brown-text mb-2">City / शहर *</label>
                                <input type="text" name="city" required
                                    placeholder="Enter your city"
                                    value="<?php echo htmlspecialchars($form_data['city'] ?? ''); ?>"
                                    class="w-full px-4 py-3 border-2 border-gray-300 rounded-lg focus:ring-2 focus:ring-[#800000] focus:border-[#800000] transition-all">
                            </div>

                            <div>
                                <label class="block text-sm font-semibold brown-text mb-2">District / जिला *</label>
                                <input type="text" name="district" required
                                    placeholder="Enter your district"
                                    value="<?php echo htmlspecialchars($form_data['district'] ?? ''); ?>"
                                    class="w-full px-4 py-3 border-2 border-gray-300 rounded-lg focus:ring-2 focus:ring-[#800000] focus:border-[#800000] transition-all">
                            </div>

                            <div>
                                <label class="block text-sm font-semibold brown-text mb-2">State / राज्य *</label>
                                <input type="text" name="state" required
                                    placeholder="Enter your state"
                                    value="<?php echo htmlspecialchars($form_data['state'] ?? ''); ?>"
                                    class="w-full px-4 py-3 border-2 border-gray-300 rounded-lg focus:ring-2 focus:ring-[#800000] focus:border-[#800000] transition-all">
                            </div>

                            <div>
                                <label class="block text-sm font-semibold brown-text mb-2">Pin Code / पिन कोड *</label>
                                <input type="text" name="pincode" required pattern="[0-9]{6}"
                                    placeholder="Enter 6 digit PIN code"
                                    value="<?php echo htmlspecialchars($form_data['pincode'] ?? ''); ?>"
                                    class="w-full px-4 py-3 border-2 border-gray-300 rounded-lg focus:ring-2 focus:ring-[#800000] focus:border-[#800000] transition-all">
                            </div>
                        </div>
                    </div>

                    <!-- Contact Details -->
                    <div class="border-2 maroon-border rounded-lg p-6 form-section">
                        <h3 class="text-xl font-bold maroon-text mb-4 flex items-center">
                            <i class="fas fa-phone-alt mr-3"></i>
                            Contact Details / संपर्क विवरण
                        </h3>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <label class="block text-sm font-semibold brown-text mb-2">Contact No / संपर्क नंबर *</label>
                                <input type="tel" name="contact" required pattern="[0-9]{10}"
                                    value="<?php echo htmlspecialchars($form_data['contact'] ?? ''); ?>"
                                    class="w-full px-4 py-3 border-2 border-gray-300 rounded-lg focus:ring-2 focus:ring-[#800000] focus:border-[#800000] transition-all"
                                    placeholder="10 digit mobile number">
                            </div>

                            <div>
                                <label class="block text-sm font-semibold brown-text mb-2">Alternate Contact No / वैकल्पिक संपर्क नंबर</label>
                                <input type="tel" name="alt_contact" pattern="[0-9]{10}"
                                    value="<?php echo htmlspecialchars($form_data['alt_contact'] ?? ''); ?>"
                                    class="w-full px-4 py-3 border-2 border-gray-300 rounded-lg focus:ring-2 focus:ring-[#800000] focus:border-[#800000] transition-all"
                                    placeholder="Optional alternate number">
                            </div>

                            <div>
                                <label class="block text-sm font-semibold brown-text mb-2">Email / ईमेल *</label>
                                <input type="email" name="email" required
                                    value="<?php echo htmlspecialchars($form_data['email'] ?? ''); ?>"
                                    class="w-full px-4 py-3 border-2 border-gray-300 rounded-lg focus:ring-2 focus:ring-[#800000] focus:border-[#800000] transition-all"
                                    placeholder="Enter email address">
                            </div>

                            <div>
                                <label class="block text-sm font-semibold brown-text mb-2">Aadhaar No / आधार नंबर *</label>
                                <input type="text" name="aadhaar" required pattern="[0-9]{12}"
                                    value="<?php echo htmlspecialchars($form_data['aadhaar'] ?? ''); ?>"
                                    class="w-full px-4 py-3 border-2 border-gray-300 rounded-lg focus:ring-2 focus:ring-[#800000] focus:border-[#800000] transition-all"
                                    placeholder="12 digit Aadhaar number">
                            </div>
                        </div>
                    </div>

                    <!-- Upload Documents -->
                    <div class="border-2 maroon-border rounded-lg p-6 form-section">
                        <h3 class="text-xl font-bold maroon-text mb-4 flex items-center">
                            <i class="fas fa-file-upload mr-3"></i>
                            Upload Documents / दस्तावेज़ अपलोड करें
                        </h3>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <label class="block text-sm font-semibold brown-text mb-2">Upload Photo / फोटो अपलोड करें *</label>
                                <input type="file" name="photo" accept="image/*" <?php echo empty($form_data) ? 'required' : ''; ?>
                                    class="w-full px-4 py-3 border-2 border-gray-300 rounded-lg focus:ring-2 focus:ring-[#800000] focus:border-[#800000] transition-all file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-semibold file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100">
                                <p class="text-xs text-gray-500 mt-2"><i class="fas fa-info-circle mr-1"></i>Max 2MB, JPG/PNG format. Recent passport size photo</p>
                                <?php if (isset($form_data['photo'])): ?>
                                    <p class="text-xs text-green-600 mt-2">
                                        <i class="fas fa-check-circle"></i> Currently uploaded: <?php echo htmlspecialchars($form_data['photo']); ?>
                                    </p>
                                <?php endif; ?>
                            </div>

                            <div>
                                <label class="block text-sm font-semibold brown-text mb-2">Upload Signature / हस्ताक्षर अपलोड करें *</label>
                                <input type="file" name="sign" accept="image/*" <?php echo empty($form_data) ? 'required' : ''; ?>
                                    class="w-full px-4 py-3 border-2 border-gray-300 rounded-lg focus:ring-2 focus:ring-[#800000] focus:border-[#800000] transition-all file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-semibold file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100">
                                <p class="text-xs text-gray-500 mt-2"><i class="fas fa-info-circle mr-1"></i>Max 2MB, JPG/PNG format. Clear signature on white background</p>
                                <?php if (isset($form_data['sign'])): ?>
                                    <p class="text-xs text-green-600 mt-2">
                                        <i class="fas fa-check-circle"></i> Currently uploaded: <?php echo htmlspecialchars($form_data['sign']); ?>
                                    </p>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>

                    <!-- Declaration -->
                    <div class="border-2 maroon-border rounded-lg p-6 form-section">
                        <h3 class="text-xl font-bold maroon-text mb-4 flex items-center">
                            <i class="fas fa-file-signature mr-3"></i>
                            Declaration / घोषणा
                        </h3>
                        <div class="flex items-start space-x-4 bg-yellow-50 p-4 rounded-lg">
                            <input type="checkbox" name="declaration" required
                                class="mt-1 w-5 h-5 rounded focus:ring-[#800000] text-[#800000] border-gray-300">
                            <label class="text-sm text-gray-700 flex-1">
                                <span class="font-semibold">I hereby declare</span> that all the information provided above is true and correct to the best of my knowledge. I understand that any false information may lead to cancellation of my application.
                                <br><br>
                                <span class="font-semibold">मैं यहां घोषणा करता/करती हूं</span> कि उपरोक्त दी गई सभी जानकारी मेरी जानकारी के अनुसार सही है। मैं समझता/समझती हूं कि कोई भी गलत जानकारी मेरे आवेदन के रद्द होने का कारण बन सकती है।
                            </label>
                        </div>
                    </div>

                    <!-- Submit Button -->
                    <div class="text-center pt-6">
                        <button type="submit"
                            class="px-12 py-4 maroon-bg text-white rounded-lg hover:bg-[#600000] transition font-bold text-lg shadow-lg hover:shadow-xl transform hover:-translate-y-1 transition-all duration-300">
                            <i class="fas fa-rupee-sign mr-2"></i>
                            <?php echo empty($form_data) ? 'Proceed to Payment (₹500) / भुगतान के लिए आगे बढ़ें (₹500)' : 'Update and Proceed to Payment / अपडेट करें और भुगतान के लिए आगे बढ़ें'; ?>
                        </button>
                    </div>
                </form>
            </div>

            <!-- Important Instructions -->
            <div class="mt-8 bg-blue-50 border border-blue-200 rounded-lg p-6">
                <h3 class="text-lg font-semibold text-blue-800 mb-3 flex items-center">
                    <i class="fas fa-exclamation-circle mr-2"></i>
                    Important Instructions / महत्वपूर्ण निर्देश
                </h3>
                <ul class="text-sm text-blue-700 space-y-2">
                    <li><i class="fas fa-check-circle mr-2 text-green-500"></i> All fields marked with * are mandatory</li>
                    <li><i class="fas fa-check-circle mr-2 text-green-500"></i> Please review all information before submission</li>
                    <li><i class="fas fa-check-circle mr-2 text-green-500"></i> Keep scanned copies of documents ready for upload</li>
                    <li><i class="fas fa-check-circle mr-2 text-green-500"></i> Application can be edited before final submission</li>
                    <li><i class="fas fa-rupee-sign mr-2 text-green-500"></i> Registration fee: ₹500 (Non-refundable)</li>
                    <li><i class="fas fa-id-card mr-2 text-green-500"></i> Unique Registration ID will be generated after form submission</li>
                </ul>
            </div>
        </div>
    </div>

    <!-- Footer -->
    <?php include '../../includes/footer.php'; ?>

    <script>
        // Form validation
        document.getElementById('registrationForm').addEventListener('submit', function(e) {
            const dob = document.querySelector('input[name="dob"]').value;
            const today = new Date();
            const birthDate = new Date(dob);
            const age = today.getFullYear() - birthDate.getFullYear();
            
            if (age < 5 || age > 15) {
                alert('Age must be between 5 and 15 years for scholarship eligibility.');
                e.preventDefault();
            }
            
            // File size validation
            const photo = document.querySelector('input[name="photo"]');
            const sign = document.querySelector('input[name="sign"]');
            
            if (photo.files.length > 0) {
                if (photo.files[0].size > 2 * 1024 * 1024) {
                    alert('Photo size must be less than 2MB');
                    e.preventDefault();
                }
            }
            
            if (sign.files.length > 0) {
                if (sign.files[0].size > 2 * 1024 * 1024) {
                    alert('Signature size must be less than 2MB');
                    e.preventDefault();
                }
            }
        });
    </script>
</body>
</html>