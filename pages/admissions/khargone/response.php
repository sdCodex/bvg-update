<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
session_start();

// Database connection
$base_url = 'https://bhaktivedantagurukul.com/';
include_once '../../../includes/db.php';

// Get parameters
$order_id = isset($_GET['order_id']) ? $_GET['order_id'] : '';
$admission_id = isset($_GET['admission_id']) ? $_GET['admission_id'] : '';

// If admission_id is not in GET, try to get it from order_id
if (empty($admission_id) && !empty($order_id)) {
    try {
        $stmt = $pdo->prepare("SELECT id FROM khargone_admissions WHERE order_id = ?");
        $stmt->execute([$order_id]);
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        if ($result) {
            $admission_id = $result['id'];
        }
    } catch (PDOException $e) {
        // Continue without admission_id
    }
}

if (empty($admission_id)) {
    ?>
    <!DOCTYPE html>
    <html>
    <head>
        <title>Error - Admission ID Not Found</title>
        <script src="https://cdn.tailwindcss.com"></script>
    </head>
    <body class="bg-gray-100 flex items-center justify-center min-h-screen">
        <div class="bg-white p-8 rounded-lg shadow-md text-center max-w-md">
            <div class="w-16 h-16 bg-red-100 rounded-full flex items-center justify-center mx-auto mb-4">
                <svg class="w-8 h-8 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
            </div>
            <h2 class="text-xl font-bold text-gray-800 mb-2">Admission ID Not Found</h2>
            <p class="text-gray-600 mb-4">Unable to locate your admission record.</p>
            <div class="space-x-4">
                <a href="/khargone.php" class="inline-block bg-blue-600 text-white px-6 py-2 rounded-lg hover:bg-blue-700 transition">Go Back to Form</a>
                <a href="/" class="inline-block bg-gray-200 text-gray-800 px-6 py-2 rounded-lg hover:bg-gray-300 transition">Go to Home</a>
            </div>
        </div>
    </body>
    </html>
    <?php
    exit;
}

try {
    // Get admission details
    $stmt = $pdo->prepare("SELECT * FROM khargone_admissions WHERE id = ?");
    $stmt->execute([$admission_id]);
    $admission_data = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$admission_data) {
        die("Admission record not found");
    }

    // Set default values
    $payment_success = false;
    $payment_status = 'pending'; // 'pending' or 'success'
    $transaction_id = $order_id ?: 'N/A';
    
    // Check payment status from database
    if (!empty($admission_data['status'])) {
        if ($admission_data['status'] == 'payment_completed' || $admission_data['status'] == 'completed') {
            $payment_success = true;
            $payment_status = 'success';
        } elseif ($admission_data['status'] == 'pending') {
            $payment_success = false;
            $payment_status = 'pending';
        }
    }
    
    // Check if this is a callback from PhonePe (check for transaction status parameters)
    if (isset($_GET['transactionStatus']) && $_GET['transactionStatus'] == 'SUCCESS') {
        $payment_success = true;
        $payment_status = 'success';
        $transaction_id = $_GET['transactionId'] ?? $order_id;
        
        // Update database with payment success
        $update_sql = "UPDATE khargone_admissions SET 
                       status = 'payment_completed',
                       payment_date = NOW(),
                       transaction_id = ?,
                       updated_at = NOW()
                       WHERE id = ?";
        $update_stmt = $pdo->prepare($update_sql);
        $update_stmt->execute([$transaction_id, $admission_id]);
    }
    
    // Also check session for payment status (if coming from redirect)
    if (isset($_SESSION['payment_success']) && $_SESSION['payment_success'] === true) {
        $payment_success = true;
        $payment_status = 'success';
        $transaction_id = $_SESSION['transaction_id'] ?? $order_id;
    }

} catch (PDOException $e) {
    ?>
    <!DOCTYPE html>
    <html>
    <head>
        <title>Database Error</title>
        <script src="https://cdn.tailwindcss.com"></script>
    </head>
    <body class="bg-gray-100 flex items-center justify-center min-h-screen">
        <div class="bg-white p-8 rounded-lg shadow-md text-center max-w-md">
            <div class="w-16 h-16 bg-red-100 rounded-full flex items-center justify-center mx-auto mb-4">
                <svg class="w-8 h-8 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
            </div>
            <h2 class="text-xl font-bold text-gray-800 mb-2">Database Error</h2>
            <p class="text-gray-600 mb-4"><?php echo htmlspecialchars($e->getMessage()); ?></p>
            <a href="/khargone.php" class="inline-block bg-blue-600 text-white px-6 py-2 rounded-lg hover:bg-blue-700 transition">Go Back</a>
        </div>
    </body>
    </html>
    <?php
    exit;
}
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
    
    .hover-lift {
        transition: transform 0.3s ease, box-shadow 0.3s ease;
    }

    .hover-lift:hover {
        transform: translateY(-5px);
        box-shadow: 0 20px 40px rgba(0, 0, 0, 0.1);
    }

    .success-badge {
        background: linear-gradient(135deg, #10b981, #059669);
        color: white;
        padding: 8px 20px;
        border-radius: 50px;
        font-weight: 600;
        font-size: 14px;
        display: inline-flex;
        align-items: center;
        gap: 8px;
    }

    .pending-badge {
        background: linear-gradient(135deg, #f59e0b, #d97706);
        color: white;
        padding: 8px 20px;
        border-radius: 50px;
        font-weight: 600;
        font-size: 14px;
        display: inline-flex;
        align-items: center;
        gap: 8px;
    }

    .print-button {
        background: linear-gradient(135deg, #003366, #004080);
        color: white;
        border: none;
        padding: 12px 24px;
        border-radius: 10px;
        font-weight: 600;
        cursor: pointer;
        transition: all 0.3s ease;
        display: inline-flex;
        align-items: center;
        gap: 10px;
    }

    .print-button:hover {
        transform: translateY(-2px);
        box-shadow: 0 5px 15px rgba(0, 51, 102, 0.3);
    }

    @media print {
        .no-print {
            display: none !important;
        }
        
        body {
            background: white !important;
            color: black !important;
        }
        
        .receipt-container {
            box-shadow: none !important;
            border: 2px solid #000 !important;
            margin: 0 !important;
            padding: 20px !important;
        }
    }
</style>

<section class="py-16 bg-[#f7f7f7]">
    <div class="max-w-4xl mx-auto px-4">
        <?php if ($payment_status == 'success'): ?>
            <!-- SUCCESS MESSAGE -->
            <div class="bg-gradient-to-br from-green-50 to-emerald-100 rounded-2xl shadow-xl overflow-hidden border border-green-200 mb-8">
                <div class="p-8 text-center">
                    <div class="w-20 h-20 bg-green-500 rounded-full flex items-center justify-center mx-auto mb-6">
                        <i class="fas fa-check text-white text-3xl"></i>
                    </div>
                    <h1 class="font-serif text-3xl font-bold text-gray-800 mb-4">
                        Payment Successful! 🎉
                    </h1>
                    <p class="text-gray-600 text-lg mb-6">
                        Thank you for submitting your admission application to Khargone Campus.
                    </p>
                    <div class="success-badge mx-auto mb-6">
                        <i class="fas fa-check-circle"></i>
                        Application Submitted Successfully
                    </div>
                </div>
            </div>
        <?php else: ?>
            <!-- PENDING MESSAGE -->
            <div class="bg-gradient-to-br from-amber-50 to-orange-100 rounded-2xl shadow-xl overflow-hidden border border-amber-200 mb-8">
                <div class="p-8 text-center">
                    <div class="w-20 h-20 bg-amber-500 rounded-full flex items-center justify-center mx-auto mb-6">
                        <i class="fas fa-clock text-white text-3xl"></i>
                    </div>
                    <h1 class="font-serif text-3xl font-bold text-gray-800 mb-4">
                        Payment Processing ⏳
                    </h1>
                    <p class="text-gray-600 text-lg mb-6">
                        Your payment is being processed. Please check back in a few minutes.
                    </p>
                    <div class="pending-badge mx-auto mb-6">
                        <i class="fas fa-spinner fa-spin"></i>
                        Payment Under Process
                    </div>
                    
                    <!-- Auto-refresh for pending payments -->
                    <div class="mt-6">
                        <p class="text-sm text-gray-500 mb-2">This page will auto-refresh in <span id="countdown">30</span> seconds</p>
                        <button onclick="location.reload()" class="px-4 py-2 bg-blue-100 text-blue-600 rounded-lg hover:bg-blue-200">
                            <i class="fas fa-redo mr-2"></i> Refresh Now
                        </button>
                    </div>
                    
                    <script>
                        let seconds = 30;
                        const countdownElement = document.getElementById('countdown');
                        const countdown = setInterval(function() {
                            seconds--;
                            countdownElement.textContent = seconds;
                            if (seconds <= 0) {
                                clearInterval(countdown);
                                location.reload();
                            }
                        }, 1000);
                    </script>
                </div>
            </div>
        <?php endif; ?>

        <!-- RECEIPT CONTAINER -->
        <div class="bg-white rounded-2xl shadow-xl overflow-hidden border border-gray-200 receipt-container">
            <!-- Receipt Header -->
            <div class="bg-gradient-to-r from-[#003366] to-[#800000] text-white p-8">
                <div class="flex flex-col md:flex-row items-center justify-between">
                    <div class="mb-6 md:mb-0">
                        <h2 class="font-serif text-2xl md:text-3xl font-bold mb-2">
                            Khargone Campus Admission Receipt
                        </h2>
                        <p class="text-blue-100">
                            Application Fee Payment Confirmation
                        </p>
                    </div>
                    <div class="text-center">
                        <div class="text-4xl font-bold">₹500</div>
                        <div class="text-blue-100 text-sm">Application Fee</div>
                    </div>
                </div>
            </div>

            <!-- Receipt Content -->
            <div class="p-8 space-y-8">
                <!-- Payment Status Card -->
                <div class="bg-gradient-to-br from-blue-50 to-cyan-50 rounded-2xl p-6 border border-blue-100">
                    <div class="flex items-center justify-between">
                        <div class="flex items-center">
                            <div class="w-12 h-12 bg-[#003366] rounded-xl flex items-center justify-center mr-4">
                                <i class="fas fa-receipt text-white"></i>
                            </div>
                            <div>
                                <h3 class="font-serif text-xl font-bold text-[#003366]">Payment Details</h3>
                                <p class="text-gray-600 text-sm">Transaction Summary</p>
                            </div>
                        </div>
                        <div class="text-right">
                            <div class="<?php echo $payment_status == 'success' ? 'success-badge' : 'pending-badge'; ?>">
                                <?php echo $payment_status == 'success' ? 'PAID' : 'PENDING'; ?>
                            </div>
                        </div>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mt-6">
                        <div class="space-y-4">
                            <div class="flex justify-between border-b pb-3">
                                <span class="text-gray-600">Admission ID:</span>
                                <span class="font-bold text-[#003366]">#KH-<?php echo str_pad($admission_id, 6, '0', STR_PAD_LEFT); ?></span>
                            </div>
                            <div class="flex justify-between border-b pb-3">
                                <span class="text-gray-600">Order ID:</span>
                                <span class="font-bold"><?php echo htmlspecialchars($order_id); ?></span>
                            </div>
                            <div class="flex justify-between border-b pb-3">
                                <span class="text-gray-600">Date:</span>
                                <span class="font-bold"><?php echo date('d/m/Y'); ?></span>
                            </div>
                            <div class="flex justify-between border-b pb-3">
                                <span class="text-gray-600">Time:</span>
                                <span class="font-bold"><?php echo date('h:i A'); ?></span>
                            </div>
                        </div>
                        <div class="space-y-4">
                            <div class="flex justify-between border-b pb-3">
                                <span class="text-gray-600">Amount:</span>
                                <span class="font-bold text-[#800000]">₹500</span>
                            </div>
                            <div class="flex justify-between border-b pb-3">
                                <span class="text-gray-600">Transaction ID:</span>
                                <span class="font-bold text-[#003366]"><?php echo htmlspecialchars($transaction_id); ?></span>
                            </div>
                            <div class="flex justify-between border-b pb-3">
                                <span class="text-gray-600">Payment Method:</span>
                                <span class="font-bold">PhonePe (Online)</span>
                            </div>
                            <div class="flex justify-between border-b pb-3">
                                <span class="text-gray-600">Application Status:</span>
                                <span class="font-bold"><?php echo ucfirst($admission_data['status'] ?? 'Pending'); ?></span>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Student Information -->
                <div class="bg-gradient-to-br from-emerald-50 to-green-50 rounded-2xl p-6 border border-emerald-100">
                    <div class="flex items-center mb-6">
                        <div class="w-12 h-12 bg-[#800000] rounded-xl flex items-center justify-center mr-4">
                            <i class="fas fa-user-graduate text-white"></i>
                        </div>
                        <h3 class="font-serif text-xl font-bold text-[#003366]">Student Information</h3>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div class="space-y-4">
                            <div class="flex justify-between">
                                <span class="text-gray-600">Student Name:</span>
                                <span class="font-bold text-[#003366]"><?php echo htmlspecialchars($admission_data['student_name']); ?></span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-gray-600">Father's Name:</span>
                                <span class="font-bold"><?php echo htmlspecialchars($admission_data['father_name']); ?></span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-gray-600">Mother's Name:</span>
                                <span class="font-bold"><?php echo htmlspecialchars($admission_data['mother_name']); ?></span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-gray-600">Date of Birth:</span>
                                <span class="font-bold"><?php echo date('d/m/Y', strtotime($admission_data['dob'])); ?></span>
                            </div>
                        </div>
                        <div class="space-y-4">
                            <div class="flex justify-between">
                                <span class="text-gray-600">Gender:</span>
                                <span class="font-bold"><?php echo htmlspecialchars($admission_data['gender']); ?></span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-gray-600">Grade Applied:</span>
                                <span class="font-bold text-[#800000]"><?php echo htmlspecialchars($admission_data['grade']); ?></span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-gray-600">Academic Year:</span>
                                <span class="font-bold"><?php echo htmlspecialchars($admission_data['academic_year']); ?></span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-gray-600">Campus:</span>
                                <span class="font-bold text-[#003366]">Khargone Campus</span>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Contact Information -->
                <div class="bg-gradient-to-br from-purple-50 to-violet-50 rounded-2xl p-6 border border-purple-100">
                    <div class="flex items-center mb-6">
                        <div class="w-12 h-12 bg-[#D4AF37] rounded-xl flex items-center justify-center mr-4">
                            <i class="fas fa-address-card text-[#003366]"></i>
                        </div>
                        <h3 class="font-serif text-xl font-bold text-[#003366]">Contact Information</h3>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div class="space-y-4">
                            <div class="flex justify-between">
                                <span class="text-gray-600">Email:</span>
                                <span class="font-bold text-[#003366]"><?php echo htmlspecialchars($admission_data['email']); ?></span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-gray-600">Phone:</span>
                                <span class="font-bold"><?php echo htmlspecialchars($admission_data['phone']); ?></span>
                            </div>
                        </div>
                        <div class="space-y-4">
                            <div class="flex justify-between">
                                <span class="text-gray-600">City:</span>
                                <span class="font-bold"><?php echo htmlspecialchars($admission_data['city']); ?></span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-gray-600">State:</span>
                                <span class="font-bold"><?php echo htmlspecialchars($admission_data['state']); ?></span>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Important Instructions -->
                <div class="bg-gradient-to-br from-amber-50 to-yellow-50 rounded-2xl p-6 border border-amber-100">
                    <div class="flex items-center mb-6">
                        <div class="w-12 h-12 bg-amber-500 rounded-xl flex items-center justify-center mr-4">
                            <i class="fas fa-info-circle text-white"></i>
                        </div>
                        <h3 class="font-serif text-xl font-bold text-[#003366]">Important Instructions</h3>
                    </div>

                    <div class="space-y-4">
                        <div class="flex items-start">
                            <i class="fas fa-check-circle text-green-500 mt-1 mr-3"></i>
                            <span class="text-gray-700">Keep this receipt for future reference.</span>
                        </div>
                        <div class="flex items-start">
                            <i class="fas fa-check-circle text-green-500 mt-1 mr-3"></i>
                            <span class="text-gray-700">Our admissions team will contact you within 24-48 hours.</span>
                        </div>
                        <div class="flex items-start">
                            <i class="fas fa-check-circle text-green-500 mt-1 mr-3"></i>
                            <span class="text-gray-700">Please have the following documents ready for verification:</span>
                        </div>
                        
                        <div class="ml-8 mt-2 space-y-2">
                            <div class="flex items-center">
                                <i class="fas fa-file-alt text-amber-600 mr-2 text-sm"></i>
                                <span class="text-gray-600 text-sm">Birth Certificate</span>
                            </div>
                            <div class="flex items-center">
                                <i class="fas fa-file-alt text-amber-600 mr-2 text-sm"></i>
                                <span class="text-gray-600 text-sm">Previous School Report Card</span>
                            </div>
                            <div class="flex items-center">
                                <i class="fas fa-file-alt text-amber-600 mr-2 text-sm"></i>
                                <span class="text-gray-600 text-sm">Aadhaar Card (Student & Parents)</span>
                            </div>
                            <div class="flex items-center">
                                <i class="fas fa-file-alt text-amber-600 mr-2 text-sm"></i>
                                <span class="text-gray-600 text-sm">Passport Size Photographs (4 copies)</span>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Action Buttons -->
                <div class="bg-[#f7f7f7] rounded-2xl p-6 border border-gray-200 no-print">
                    <div class="flex flex-col md:flex-row items-center justify-between space-y-4 md:space-y-0">
                        <div class="text-sm text-gray-600">
                            <p class="flex items-center">
                                <i class="fas fa-shield-alt text-green-500 mr-2"></i>
                                This is an official receipt for your records
                            </p>
                        </div>

                        <div class="flex flex-wrap gap-4">
                            <button onclick="window.print()" class="print-button">
                                <i class="fas fa-print"></i>
                                Print Receipt
                            </button>
                            
                            <a href="<?php echo $base_url; ?>pages/admissions/index.php" 
                               class="px-6 py-3 border border-gray-300 text-gray-700 font-semibold rounded-xl hover:bg-gray-50 transition-all duration-300 inline-flex items-center">
                                <i class="fas fa-home mr-2"></i> Back to Admissions
                            </a>
                            
                            <a href="<?php echo $base_url; ?>index.php" 
                               class="px-6 py-3 bg-[#003366] text-white font-semibold rounded-xl hover:bg-[#002244] transition-all duration-300 inline-flex items-center">
                                <i class="fas fa-globe mr-2"></i> Homepage
                            </a>
                        </div>
                    </div>
                </div>

                <!-- Download Section -->
                <div class="bg-gradient-to-br from-blue-50 to-cyan-50 rounded-2xl p-6 border border-blue-100">
                    <div class="flex items-center mb-6">
                        <div class="w-12 h-12 bg-[#003366] rounded-xl flex items-center justify-center mr-4">
                            <i class="fas fa-download text-white"></i>
                        </div>
                        <h3 class="font-serif text-xl font-bold text-[#003366]">Download Documents</h3>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                        <a href="#" class="bg-white p-4 rounded-xl border border-gray-200 hover:border-[#003366] transition-all duration-300 hover-lift">
                            <div class="flex items-center">
                                <div class="w-10 h-10 bg-blue-100 rounded-lg flex items-center justify-center mr-3">
                                    <i class="fas fa-file-pdf text-blue-600"></i>
                                </div>
                                <div>
                                    <p class="font-semibold text-gray-800">Admission Form</p>
                                    <p class="text-gray-500 text-sm">PDF Format</p>
                                </div>
                            </div>
                        </a>

                        <a href="#" class="bg-white p-4 rounded-xl border border-gray-200 hover:border-[#003366] transition-all duration-300 hover-lift">
                            <div class="flex items-center">
                                <div class="w-10 h-10 bg-green-100 rounded-lg flex items-center justify-center mr-3">
                                    <i class="fas fa-file-word text-green-600"></i>
                                </div>
                                <div>
                                    <p class="font-semibold text-gray-800">Document Checklist</p>
                                    <p class="text-gray-500 text-sm">Word Format</p>
                                </div>
                            </div>
                        </a>

                        <a href="#" class="bg-white p-4 rounded-xl border border-gray-200 hover:border-[#003366] transition-all duration-300 hover-lift">
                            <div class="flex items-center">
                                <div class="w-10 h-10 bg-amber-100 rounded-lg flex items-center justify-center mr-3">
                                    <i class="fas fa-file-alt text-amber-600"></i>
                                </div>
                                <div>
                                    <p class="font-semibold text-gray-800">Fee Structure</p>
                                    <p class="text-gray-500 text-sm">PDF Format</p>
                                </div>
                            </div>
                        </a>
                    </div>
                </div>

                <!-- Contact Support -->
                <div class="bg-gradient-to-br from-[#f7f7f7] to-gray-100 rounded-2xl p-6 border border-gray-200">
                    <div class="text-center">
                        <h3 class="font-serif text-xl font-bold text-[#003366] mb-4">Need Help?</h3>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 max-w-2xl mx-auto">
                            <div class="text-center">
                                <div class="w-12 h-12 bg-[#003366] rounded-xl flex items-center justify-center mx-auto mb-3">
                                    <i class="fas fa-phone text-white"></i>
                                </div>
                                <p class="font-semibold text-gray-800">Call Admissions</p>
                                <p class="text-gray-600">+91-7618040040</p>
                            </div>
                            <div class="text-center">
                                <div class="w-12 h-12 bg-[#800000] rounded-xl flex items-center justify-center mx-auto mb-3">
                                    <i class="fas fa-envelope text-white"></i>
                                </div>
                                <p class="font-semibold text-gray-800">Email Support</p>
                                <p class="text-gray-600">admissions@khargonecampus.edu</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Next Steps -->
        <div class="mt-8 text-center">
            <h3 class="font-serif text-2xl font-bold text-[#003366] mb-6">What Happens Next?</h3>
            <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                <div class="text-center p-4 bg-gradient-to-br from-blue-50 to-cyan-50 rounded-xl border border-blue-100">
                    <div class="w-10 h-10 bg-[#003366] rounded-full flex items-center justify-center mx-auto mb-3">
                        <span class="text-white font-bold">1</span>
                    </div>
                    <p class="font-semibold text-gray-800 mb-1">Form Verification</p>
                    <p class="text-gray-600 text-sm">Our team will verify your application</p>
                </div>
                <div class="text-center p-4 bg-gradient-to-br from-amber-50 to-yellow-50 rounded-xl border border-amber-100">
                    <div class="w-10 h-10 bg-[#D4AF37] rounded-full flex items-center justify-center mx-auto mb-3">
                        <span class="text-[#003366] font-bold">2</span>
                    </div>
                    <p class="font-semibold text-gray-800 mb-1">Document Submission</p>
                    <p class="text-gray-600 text-sm">Submit required documents</p>
                </div>
                <div class="text-center p-4 bg-gradient-to-br from-emerald-50 to-green-50 rounded-xl border border-emerald-100">
                    <div class="w-10 h-10 bg-[#800000] rounded-full flex items-center justify-center mx-auto mb-3">
                        <span class="text-white font-bold">3</span>
                    </div>
                    <p class="font-semibold text-gray-800 mb-1">Admission Test</p>
                    <p class="text-gray-600 text-sm">Schedule admission test (if required)</p>
                </div>
                <div class="text-center p-4 bg-gradient-to-br from-purple-50 to-violet-50 rounded-xl border border-purple-100">
                    <div class="w-10 h-10 bg-[#003366] rounded-full flex items-center justify-center mx-auto mb-3">
                        <span class="text-white font-bold">4</span>
                    </div>
                    <p class="font-semibold text-gray-800 mb-1">Confirmation</p>
                    <p class="text-gray-600 text-sm">Receive admission confirmation</p>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Print Script -->
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Add watermark for print
    const style = document.createElement('style');
    style.innerHTML = `
        @media print {
            @page {
                margin: 20mm;
            }
            
            body::before {
                content: "Khargone Campus Admission Receipt";
                position: fixed;
                top: 50%;
                left: 50%;
                transform: translate(-50%, -50%) rotate(-45deg);
                font-size: 60px;
                color: rgba(0, 51, 102, 0.1);
                z-index: 9999;
                pointer-events: none;
                font-weight: bold;
            }
            
            .receipt-container {
                break-inside: avoid;
            }
        }
    `;
    document.head.appendChild(style);
});
</script>

<?php include_once '../../../includes/footer.php' ?>