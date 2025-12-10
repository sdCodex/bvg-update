<?php
// ERROR REPORTING ON KARO DEBUG KE LIYE
error_reporting(E_ALL);
ini_set('display_errors', 1);
date_default_timezone_set('Asia/Kolkata');
// Check if session is already started
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// ✅ Pehle PHPMailer include karo
require_once 'vendor/autoload.php';

// ✅ PHPMailer classes
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

// ✅ Database connection
include '../includes/db.php';

// Check if form data exists in session
if (!isset($_SESSION['form_data'])) {
    echo "Error: No form data found. Redirecting...";
    header('Location: register.php');
    exit;
}

$form_data = $_SESSION['form_data'];

// Handle final submission and save to database
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['final_submit'])) {
    echo "<!-- DEBUG: Form submitted -->";
    
    try {
        // ✅ Generate PhonePe compatible unique ID
        $timestamp = time();
        $random_suffix = mt_rand(1000, 9999);
        $unique_id = 'BVG' . $timestamp . $random_suffix;
        
        echo "<!-- DEBUG: Unique ID generated: $unique_id -->";
        
        // ✅ Database mein save karo
        $stmt = $pdo->prepare("
            INSERT INTO fotuernet50_students 
            (unique_id, name, father_name, mother_name, gender, dob, phone, alt_contact, email, aadhaar, class, 
             school_name, city, district, state, pincode, address, landmark, photo, sign, 
             payment_status, amount, created_at) 
            VALUES 
            (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, 'pending', 500, NOW())
        ");

        $stmt->execute([
            $unique_id,
            $form_data['name'],
            $form_data['father_name'],
            $form_data['mother_name'],
            $form_data['gender'],
            $form_data['dob'],
            $form_data['contact'],
            $form_data['alt_contact'] ?? '',
            $form_data['email'],
            $form_data['aadhaar'],
            $form_data['class'],
            $form_data['school_name'],
            $form_data['city'],
            $form_data['district'],
            $form_data['state'],
            $form_data['pincode'],
            $form_data['address'],
            $form_data['landmark'] ?? '',
            $form_data['photo'] ?? '',
            $form_data['sign'] ?? ''
        ]);

        $registration_id = $pdo->lastInsertId();
        echo "<!-- DEBUG: Database saved. Registration ID: $registration_id -->";

        // ✅ Session mein store karo
        $_SESSION['registration_id'] = $registration_id;
        $_SESSION['unique_id'] = $unique_id;

        // ✅ ✅ ✅ ADMIN KO EMAIL BHEJO
        echo "<!-- DEBUG: Starting email process -->";
        
        $mail = new PHPMailer(true);
        
        // Server settings
        $mail->isSMTP();
        $mail->Host = 'smtp.gmail.com';
        $mail->SMTPAuth = true;
        $mail->Username = 'cmd@ourgurukul.org';
        $mail->Password = 'swdr epfq ffdd fjuk'; // DOUBLE CHECK THIS!
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
        $mail->Port = 587;
        
        // Enable debugging (optional)
        // $mail->SMTPDebug = 2;
        // $mail->Debugoutput = function($str, $level) {
        //     echo "Debug level $level; message: $str<br>";
        // };
        
        // Recipients
        $mail->setFrom('cmd@ourgurukul.org', 'Bhaktivedanta Gurukul Fortunate 51 Scholarship');
        $mail->addAddress('cmd@ourgurukul.org'); // Admin email
        $mail->addReplyTo('cmd@ourgurukul.org', 'Bhaktivedanta Gurukul Support');
        
        // Content
        $mail->isHTML(true);
        $mail->Subject = 'New Scholarship Registration - ' . $unique_id;
        
        // Simple email body for testing
        $mail->Body = '
        <!DOCTYPE html>
        <html>
        <head>
            <style>
                body { font-family: Arial, sans-serif; }
                .container { max-width: 600px; margin: 0 auto; }
                .header { background: #800000; color: white; padding: 20px; }
                .content { padding: 20px; background: #f9f9f9; }
            </style>
        </head>
        <body>
            <div class="container">
                <div class="header">
                    <h2>New Scholarship Application</h2>
                    <p>Registration ID: ' . $unique_id . '</p>
                </div>
                <div class="content">
                    <h3>Student Details:</h3>
                    <p><strong>Name:</strong> ' . htmlspecialchars($form_data['name']) . '</p>
                    <p><strong>Father:</strong> ' . htmlspecialchars($form_data['father_name']) . '</p>
                    <p><strong>Class:</strong> Class ' . htmlspecialchars($form_data['class']) . '</p>
                    <p><strong>School:</strong> ' . htmlspecialchars($form_data['school_name']) . '</p>
                    <p><strong>Phone:</strong> ' . htmlspecialchars($form_data['contact']) . '</p>
                    <p><strong>Email:</strong> ' . htmlspecialchars($form_data['email']) . '</p>
                    
                    <h3>Payment Status:</h3>
                    <p><strong>Status:</strong> Pending</p>
                    <p><strong>Amount:</strong> ₹500.00 </p>
                    <p><strong>Time:</strong> ' . date('d/m/Y h:i A') . '</p>
                </div>
            </div>
        </body>
        </html>
        ';
        
        $mail->AltBody = "New Scholarship Registration\n" .
                         "Registration ID: $unique_id\n" .
                         "Student: " . $form_data['name'] . "\n" .
                         "Class: " . $form_data['class'] . "\n" .
                         "School: " . $form_data['school_name'] . "\n" .
                         "Phone: " . $form_data['contact'] . "\n" .
                         "Payment Status: Pending\n";
        
        echo "<!-- DEBUG: Attempting to send email -->";
        
        if ($mail->send()) {
            echo "<!-- DEBUG: Email sent successfully -->";
        } else {
            echo "<!-- DEBUG: Email sending failed: " . $mail->ErrorInfo . " -->";
        }
        
        // ✅ Redirect to payment page
        echo "<!-- DEBUG: Redirecting to payment page -->";
        header('Location: package/request.php?order_id=' . $unique_id);
        exit;
        
    } catch (PDOException $e) {
        echo "<!-- DEBUG: Database Error: " . $e->getMessage() . " -->";
        $error = "Registration failed: " . $e->getMessage();
    } catch (Exception $emailError) {
        echo "<!-- DEBUG: Email Error: " . $emailError->getMessage() . " -->";
        // Log the error
        error_log("Email sending failed: " . $emailError->getMessage());
        
        // Email fail hua to bhi payment page redirect karo
        header('Location: package/request.php?order_id=' . $unique_id);
        exit;
    }
}

// Handle edit request
if (isset($_GET['edit'])) {
    $_SESSION['edit_mode'] = true;
    header('Location: register.php');
    exit;
}

// Agar koi error ho to dikhaye
if (isset($error)) {
    echo "<!-- ERROR: $error -->";
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Preview Application - BVGF50 Scholarship</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <link rel="icon" href="../images/bvgLogo.png">
    <link rel="stylesheet" href="css/preview.css">
    <style>
        .payment-info {
            background: linear-gradient(135deg, #f0f9ff 0%, #e0f2fe 100%);
            border-left: 4px solid #800000;
        }
        .btn-payment {
            background: linear-gradient(135deg, #22c55e 0%, #16a34a 100%);
            color: white;
            font-weight: bold;
            padding: 12px 24px;
            border-radius: 8px;
            transition: all 0.3s ease;
        }
        .btn-payment:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(34, 197, 94, 0.3);
        }
    </style>
</head>
<body class="bg-gray-50">
    <!-- Header -->
    <?php include '../includes/header.php'; ?>

    <!-- Preview Section -->
    <div class="container mx-auto px-4 py-8">
        <div class="max-w-4xl mx-auto">
            <!-- Page Header -->
            <div class="text-center mb-8">
                <h1 class="text-3xl font-bold maroon-text mb-2">
                    Application Preview / आवेदन पूर्वावलोकन
                </h1>
                <p class="text-gray-600">
                    Please review your application before final submission
                    <br>कृपया अंतिम सबमिशन से पहले अपना आवेदन जांचें
                </p>
            </div>

            <?php if (isset($error)): ?>
                <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-6">
                    <?php echo $error; ?>
                </div>
            <?php endif; ?>

            <div class="bg-white rounded-xl shadow-lg overflow-hidden">
                <!-- Preview Header -->
                <div class="maroon-bg text-white py-4 px-6">
                    <h2 class="text-xl font-bold">Application Summary / आवेदन सारांश</h2>
                    <?php if (isset($_SESSION['unique_id'])): ?>
                        <p class="text-white/80 text-sm mt-1">
                            Registration ID: <?php echo $_SESSION['unique_id']; ?>
                        </p>
                    <?php endif; ?>
                </div>

                <div class="p-6 space-y-6">
                    <!-- Payment Information -->
                    <div class="payment-info rounded-lg p-6">
                        <h3 class="text-lg font-bold maroon-text mb-4 flex items-center">
                            <i class="fas fa-rupee-sign mr-2"></i>
                            Payment Information / भुगतान जानकारी
                        </h3>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                           <div>
    <label class="block text-sm font-medium brown-text">Application Fee / आवेदन शुल्क</label>
    <p class="text-2xl font-bold text-green-600">₹500.00</p> <!-- ₹100 से ₹50 करो -->
</div>

                            <div>
                                <label class="block text-sm font-medium brown-text">Payment Status / भुगतान स्थिति</label>
                                <p class="text-lg font-semibold text-orange-500">Pending / लंबित</p>
                            </div>
                        </div>
                        <div class="mt-4 p-4 bg-yellow-50 rounded-lg">
                            <p class="text-sm text-yellow-800">
                                <i class="fas fa-info-circle mr-2"></i>
                                Your application will be processed only after successful payment.
                                <br>आपका आवेदन केवल सफल भुगतान के बाद संसाधित किया जाएगा।
                            </p>
                        </div>
                    </div>

                    <!-- Personal Details Preview -->
                    <div class="border-2 maroon-border rounded-lg p-6">
                        <h3 class="text-lg font-bold maroon-text mb-4 flex items-center">
                            <i class="fas fa-user mr-2"></i>
                            Personal Details / व्यक्तिगत विवरण
                        </h3>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <label class="block text-sm font-medium brown-text">Full Name / पूरा नाम</label>
                                <p class="text-gray-800 font-semibold"><?php echo htmlspecialchars($form_data['name']); ?></p>
                            </div>
                            <div>
                                <label class="block text-sm font-medium brown-text">Father's Name / पिता का नाम</label>
                                <p class="text-gray-800 font-semibold"><?php echo htmlspecialchars($form_data['father_name']); ?></p>
                            </div>
                            <div>
                                <label class="block text-sm font-medium brown-text">Mother's Name / माता का नाम</label>
                                <p class="text-gray-800 font-semibold"><?php echo htmlspecialchars($form_data['mother_name']); ?></p>
                            </div>
                            <div>
                                <label class="block text-sm font-medium brown-text">Gender / लिंग</label>
                                <p class="text-gray-800 font-semibold"><?php echo htmlspecialchars($form_data['gender']); ?></p>
                            </div>
                            <div>
                                <label class="block text-sm font-medium brown-text">Date of Birth / जन्म तिथि</label>
                                <p class="text-gray-800 font-semibold"><?php echo date('d/m/Y', strtotime($form_data['dob'])); ?></p>
                            </div>
                            <div>
                                <label class="block text-sm font-medium brown-text">Class / कक्षा</label>
                                <p class="text-gray-800 font-semibold">Class <?php echo htmlspecialchars($form_data['class']); ?></p>
                            </div>
                            <div class="md:col-span-2">
                                <label class="block text-sm font-medium brown-text">School Name / विद्यालय का नाम</label>
                                <p class="text-gray-800 font-semibold"><?php echo htmlspecialchars($form_data['school_name']); ?></p>
                            </div>
                        </div>
                    </div>

                    <!-- Address Preview -->
                    <div class="border-2 maroon-border rounded-lg p-6">
                        <h3 class="text-lg font-bold maroon-text mb-4 flex items-center">
                            <i class="fas fa-home mr-2"></i>
                            Complete Address / पूरा पता
                        </h3>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div class="md:col-span-2">
                                <label class="block text-sm font-medium brown-text">House No. & Street / मकान नंबर और गली</label>
                                <p class="text-gray-800 font-semibold"><?php echo htmlspecialchars($form_data['address']); ?></p>
                            </div>
                            <?php if (!empty($form_data['landmark'])): ?>
                                <div class="md:col-span-2">
                                    <label class="block text-sm font-medium brown-text">Landmark / लैंडमार्क</label>
                                    <p class="text-gray-800 font-semibold"><?php echo htmlspecialchars($form_data['landmark']); ?></p>
                                </div>
                            <?php endif; ?>
                            <div>
                                <label class="block text-sm font-medium brown-text">City / शहर</label>
                                <p class="text-gray-800 font-semibold"><?php echo htmlspecialchars($form_data['city']); ?></p>
                            </div>
                            <div>
                                <label class="block text-sm font-medium brown-text">District / जिला</label>
                                <p class="text-gray-800 font-semibold"><?php echo htmlspecialchars($form_data['district']); ?></p>
                            </div>
                            <div>
                                <label class="block text-sm font-medium brown-text">State / राज्य</label>
                                <p class="text-gray-800 font-semibold"><?php echo htmlspecialchars($form_data['state']); ?></p>
                            </div>
                            <div>
                                <label class="block text-sm font-medium brown-text">Pin Code / पिन कोड</label>
                                <p class="text-gray-800 font-semibold"><?php echo htmlspecialchars($form_data['pincode']); ?></p>
                            </div>
                        </div>
                    </div>

                    <!-- Contact Details Preview -->
                    <div class="border-2 maroon-border rounded-lg p-6">
                        <h3 class="text-lg font-bold maroon-text mb-4 flex items-center">
                            <i class="fas fa-phone mr-2"></i>
                            Contact Details / संपर्क विवरण
                        </h3>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <label class="block text-sm font-medium brown-text">Contact No / संपर्क नंबर</label>
                                <p class="text-gray-800 font-semibold"><?php echo htmlspecialchars($form_data['contact']); ?></p>
                            </div>
                            <?php if (!empty($form_data['alt_contact'])): ?>
                                <div>
                                    <label class="block text-sm font-medium brown-text">Alternate Contact No / वैकल्पिक संपर्क नंबर</label>
                                    <p class="text-gray-800 font-semibold"><?php echo htmlspecialchars($form_data['alt_contact']); ?></p>
                                </div>
                            <?php endif; ?>
                            <div>
                                <label class="block text-sm font-medium brown-text">Email / ईमेल</label>
                                <p class="text-gray-800 font-semibold"><?php echo htmlspecialchars($form_data['email']); ?></p>
                            </div>
                            <div>
                                <label class="block text-sm font-medium brown-text">Aadhaar No / आधार नंबर</label>
                                <p class="text-gray-800 font-semibold"><?php echo htmlspecialchars($form_data['aadhaar']); ?></p>
                            </div>
                        </div>
                    </div>

                    <!-- Uploaded Documents Preview -->
                    <div class="border-2 maroon-border rounded-lg p-6">
                        <h3 class="text-lg font-bold maroon-text mb-4 flex items-center">
                            <i class="fas fa-file-upload mr-2"></i>
                            Uploaded Documents / अपलोड किए गए दस्तावेज़
                        </h3>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <?php if (isset($form_data['photo'])): ?>
                                <div>
                                    <label class="block text-sm font-medium brown-text mb-2">Photo / फोटो</label>
                                    <div class="border-2 border-dashed border-gray-300 rounded-lg p-4 text-center">
                                        <img src="../uploads/<?php echo htmlspecialchars($form_data['photo']); ?>"
                                            alt="Student Photo"
                                            class="mx-auto h-32 w-32 object-cover rounded-lg">
                                        <p class="text-xs text-gray-500 mt-2">Uploaded Successfully</p>
                                    </div>
                                </div>
                            <?php endif; ?>

                            <?php if (isset($form_data['sign'])): ?>
                                <div>
                                    <label class="block text-sm font-medium brown-text mb-2">Signature / हस्ताक्षर</label>
                                    <div class="border-2 border-dashed border-gray-300 rounded-lg p-4 text-center">
                                        <img src="../uploads/<?php echo htmlspecialchars($form_data['sign']); ?>"
                                            alt="Student Signature"
                                            class="mx-auto h-32 w-32 object-contain">
                                        <p class="text-xs text-gray-500 mt-2">Uploaded Successfully</p>
                                    </div>
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>

                    <!-- Action Buttons -->
                    <div class="flex flex-col md:flex-row gap-4 justify-center pt-6">
                        <!-- Edit Button -->
                        <a href="preview.php?edit=true" 
                           class="px-6 py-3 border-2 maroon-border text-[#800000] rounded-lg hover:bg-gray-50 transition font-bold text-center">
                            <i class="fas fa-edit mr-2"></i>
                            Edit Application / आवेदन संपादित करें
                        </a> 
                        
                        <!-- Submit and Pay Button -->
                        <form method="POST" action="preview.php">
                            <input type="hidden" name="final_submit" value="1">
                            <button type="submit" class="btn-payment flex items-center">
                                <i class="fas fa-lock mr-2"></i>
                                Submit & Pay Now / जमा करें और भुगतान करें
                            </button>
                        </form>
                    </div>

                    <!-- Payment Security Info -->
                    <div class="text-center mt-4">
                        <div class="flex justify-center items-center space-x-4 text-sm text-gray-600">
                            <div class="flex items-center">
                                <i class="fas fa-shield-alt text-green-500 mr-1"></i>
                                <span>100% Secure</span>
                            </div>
                            <div class="flex items-center">
                                <i class="fas fa-mobile-alt text-blue-500 mr-1"></i>
                                <span>PhonePe Protected</span>
                            </div>
                            <div class="flex items-center">
                                <i class="fas fa-clock text-orange-500 mr-1"></i>
                                <span>Instant Confirmation</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Footer -->
    <?php include '../includes/footer.php'; ?>

    <script>
        // Add confirmation before submission
        document.querySelector('form').addEventListener('submit', function(e) {
            if (!confirm('Are you sure you want to submit the application and proceed to payment?\nक्या आप आवेदन जमा करके भुगतान के लिए आगे बढ़ना चाहते हैं?')) {
                e.preventDefault();
            }
        });
    </script>
</body>
</html>