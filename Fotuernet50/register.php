
<?php
// Temporary maintenance redirect - comment out when not needed
// header("Location: /../maintenance.php");
// exit;
?>

<?php
// Check if session is already started
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
include '../includes/db.php';

// Check if we are in edit mode and preserve form data
$form_data = [];
if (isset($_SESSION['edit_mode']) && $_SESSION['edit_mode'] === true && isset($_SESSION['form_data'])) {
    $form_data = $_SESSION['form_data'];
    unset($_SESSION['edit_mode']); // Clear edit mode flag
}

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Store form data in session for preview
    $_SESSION['form_data'] = $_POST;

    // Handle file uploads
    if (isset($_FILES['photo']) && $_FILES['photo']['error'] === 0) {
        $photo_name = uniqid() . '_' . $_FILES['photo']['name'];
        move_uploaded_file($_FILES['photo']['tmp_name'], '../uploads/' . $photo_name);
        $_SESSION['form_data']['photo'] = $photo_name;
    } elseif (isset($form_data['photo'])) {
        // Preserve existing photo if not re-uploaded
        $_SESSION['form_data']['photo'] = $form_data['photo'];
    }

    if (isset($_FILES['sign']) && $_FILES['sign']['error'] === 0) {
        $sign_name = uniqid() . '_' . $_FILES['sign']['name'];
        move_uploaded_file($_FILES['sign']['tmp_name'], '../uploads/' . $sign_name);
        $_SESSION['form_data']['sign'] = $sign_name;
    } elseif (isset($form_data['sign'])) {
        // Preserve existing signature if not re-uploaded
        $_SESSION['form_data']['sign'] = $form_data['sign'];
    }

    // Redirect to preview page
    header('Location: preview.php');
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
</head>

<body class="bg-gray-50">
    <!-- Header -->
    <?php include '../includes/header.php'; ?>

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

                    <!-- Submit -->
                    <div class="text-center pt-6">
                        <button type="submit"
                            class="px-12 py-4 maroon-bg text-white rounded-lg hover:bg-[#600000] transition font-bold text-lg shadow-lg hover:shadow-xl transform hover:-translate-y-1 transition-all duration-300">
                            <i class="fas fa-eye mr-2"></i>
                            <?php echo empty($form_data) ? 'Proceed to Preview / पूर्वावलोकन के लिए आगे बढ़ें' : 'Update and Preview / अपडेट करें और पूर्वावलोकन देखें'; ?>
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
                </ul>
            </div>
        </div>
    </div>

    <!-- Footer -->
    <?php include '../includes/footer.php'; ?>

    <script src="js/register.js"></script>
</body>

</html>