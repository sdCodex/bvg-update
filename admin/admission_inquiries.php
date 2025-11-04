<?php
session_start();
if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    header('Location: login.php');
    exit;
}
require_once '../includes/db.php';

$base_url = '/work/Gurkul_Project';

// Get admission inquiries data
try {
    $stmt = $pdo->query("SELECT * FROM admission_inquiries ORDER BY created_at DESC");
    $inquiries = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    $inquiries = [];
}

// Handle status update
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['update_status'])) {
        $inquiry_id = $_POST['inquiry_id'];
        $new_status = $_POST['status'];
        $admin_notes = $_POST['admin_notes'] ?? '';
        
        try {
            $stmt = $pdo->prepare("UPDATE admission_inquiries SET status = ?, admin_notes = ?, updated_at = NOW() WHERE id = ?");
            $stmt->execute([$new_status, $admin_notes, $inquiry_id]);
            
            $_SESSION['success_message'] = "Status updated successfully!";
            header('Location: ' . $_SERVER['PHP_SELF']);
            exit;
        } catch (PDOException $e) {
            $_SESSION['error_message'] = "Error updating status: " . $e->getMessage();
        }
    }
    
    // Handle contact form submission
    if (isset($_POST['send_contact'])) {
        $inquiry_id = $_POST['inquiry_id'];
        $contact_subject = $_POST['contact_subject'];
        $contact_message = $_POST['contact_message'];
        $contact_method = $_POST['contact_method'];
        
        // Here you would typically send email or SMS
        // For now, we'll just update the status and add notes
        
        try {
            $stmt = $pdo->prepare("UPDATE admission_inquiries SET status = 'contacted', admin_notes = CONCAT(IFNULL(admin_notes, ''), '\nContacted via ', ?, ' on ', NOW(), ': ', ?), updated_at = NOW() WHERE id = ?");
            $stmt->execute([$contact_method, $contact_message, $inquiry_id]);
            
            $_SESSION['success_message'] = "Contact message sent successfully!";
            header('Location: ' . $_SERVER['PHP_SELF']);
            exit;
        } catch (PDOException $e) {
            $_SESSION['error_message'] = "Error sending contact: " . $e->getMessage();
        }
    }
}

// Display success/error messages
if (isset($_SESSION['success_message'])) {
    $success_message = $_SESSION['success_message'];
    unset($_SESSION['success_message']);
}

if (isset($_SESSION['error_message'])) {
    $error_message = $_SESSION['error_message'];
    unset($_SESSION['error_message']);
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admission Inquiries - Bhaktivedanta Gurukul</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="icon" type="image/x-icon" href="../images/bvgLogo.png">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        .status-badge {
            padding: 6px 12px;
            border-radius: 20px;
            font-size: 0.75rem;
            font-weight: 600;
            text-transform: capitalize;
            display: inline-flex;
            align-items: center;
            gap: 4px;
        }
        .status-pending { background-color: #fef3c7; color: #92400e; border: 1px solid #fbbf24; }
        .status-approved { background-color: #d1fae5; color: #065f46; border: 1px solid #10b981; }
        .status-rejected { background-color: #fee2e2; color: #991b1b; border: 1px solid #ef4444; }
        .status-contacted { background-color: #dbeafe; color: #1e40af; border: 1px solid #3b82f6; }
        .status-registered { background-color: #ede9fe; color: #5b21b6; border: 1px solid #8b5cf6; }
        
        /* Fixed Modal Styles */
        .modal-container {
            position: fixed;
            inset: 0;
            z-index: 50;
            display: none;
        }
        
        .modal-overlay {
            position: absolute;
            inset: 0;
            background-color: rgba(0, 0, 0, 0.5);
        }
        
        .modal-content {
            position: relative;
            background: white;
            margin: 2rem auto;
            border-radius: 1rem;
            box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.25);
            max-width: 90%;
            max-height: 90vh;
            overflow-y: auto;
        }
        
        .action-btn {
            transition: all 0.2s ease-in-out;
            border-radius: 8px;
            padding: 6px 12px;
            font-size: 0.875rem;
            font-weight: 500;
        }
        
        .action-btn:hover {
            transform: translateY(-1px);
        }
        
        .info-card {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            border-radius: 12px;
        }
        
        .table-row-hover:hover {
            background-color: #f8fafc;
            transform: translateY(-1px);
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
        }
        
        /* Ensure modal is above everything */
        .modal-open {
            overflow: hidden;
        }
    </style>
</head>
<body class="bg-gray-50">
    <?php 
    $current_page = 'admission_inquiries.php';
    $current_directory = 'admin';
    include '../includes/header.php'; 
    ?>

    <div class="container mx-auto px-4 py-8 mt-16">
        <div class="max-w-7xl mx-auto">
            <!-- Header Section -->
            <div class="mb-8">
                <div class="flex flex-col md:flex-row md:items-center md:justify-between">
                    <div>
                        <h1 class="text-3xl font-bold text-gray-800 mb-2">Admission Inquiries</h1>
                        <p class="text-gray-600">Manage all admission inquiries from website</p>
                    </div>
                    <div class="mt-4 md:mt-0 flex items-center space-x-4">
                        <div class="bg-white rounded-lg shadow-sm px-4 py-2 border">
                            <span class="text-sm text-gray-600">Total Inquiries:</span>
                            <span class="font-semibold text-gray-800 ml-1"><?php echo count($inquiries); ?></span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Stats Cards -->
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
                <div class="info-card text-white p-6 rounded-xl shadow-lg">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-blue-100 text-sm">Pending</p>
                            <p class="text-2xl font-bold mt-1">
                                <?php echo count(array_filter($inquiries, fn($i) => $i['status'] === 'pending')); ?>
                            </p>
                        </div>
                        <div class="w-12 h-12 bg-white bg-opacity-20 rounded-full flex items-center justify-center">
                            <i class="fas fa-clock text-xl"></i>
                        </div>
                    </div>
                </div>
                
                <div class="bg-white p-6 rounded-xl shadow-lg border border-gray-200">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-gray-600 text-sm">Contacted</p>
                            <p class="text-2xl font-bold text-gray-800 mt-1">
                                <?php echo count(array_filter($inquiries, fn($i) => $i['status'] === 'contacted')); ?>
                            </p>
                        </div>
                        <div class="w-12 h-12 bg-blue-100 rounded-full flex items-center justify-center">
                            <i class="fas fa-phone text-blue-600 text-xl"></i>
                        </div>
                    </div>
                </div>
                
                <div class="bg-white p-6 rounded-xl shadow-lg border border-gray-200">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-gray-600 text-sm">Approved</p>
                            <p class="text-2xl font-bold text-gray-800 mt-1">
                                <?php echo count(array_filter($inquiries, fn($i) => $i['status'] === 'approved')); ?>
                            </p>
                        </div>
                        <div class="w-12 h-12 bg-green-100 rounded-full flex items-center justify-center">
                            <i class="fas fa-check text-green-600 text-xl"></i>
                        </div>
                    </div>
                </div>
                
                <div class="bg-white p-6 rounded-xl shadow-lg border border-gray-200">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-gray-600 text-sm">Registered</p>
                            <p class="text-2xl font-bold text-gray-800 mt-1">
                                <?php echo count(array_filter($inquiries, fn($i) => $i['status'] === 'registered')); ?>
                            </p>
                        </div>
                        <div class="w-12 h-12 bg-purple-100 rounded-full flex items-center justify-center">
                            <i class="fas fa-user-graduate text-purple-600 text-xl"></i>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Success/Error Messages -->
            <?php if (isset($success_message)): ?>
                <div class="bg-green-50 border border-green-200 text-green-700 px-4 py-3 rounded-lg mb-6 flex items-center">
                    <i class="fas fa-check-circle mr-2"></i>
                    <?php echo $success_message; ?>
                </div>
            <?php endif; ?>

            <?php if (isset($error_message)): ?>
                <div class="bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded-lg mb-6 flex items-center">
                    <i class="fas fa-exclamation-circle mr-2"></i>
                    <?php echo $error_message; ?>
                </div>
            <?php endif; ?>

            <!-- Main Table -->
            <div class="bg-white rounded-xl shadow-lg overflow-hidden border border-gray-200">
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-4 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Student Info</th>
                                <th class="px-6 py-4 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Parent & Contact</th>
                                <th class="px-6 py-4 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Program Details</th>
                                <th class="px-6 py-4 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                                <th class="px-6 py-4 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Date</th>
                                <th class="px-6 py-4 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            <?php if (empty($inquiries)): ?>
                                <tr>
                                    <td colspan="6" class="px-6 py-8 text-center">
                                        <div class="text-gray-400 mb-2">
                                            <i class="fas fa-inbox text-4xl"></i>
                                        </div>
                                        <p class="text-gray-500 text-lg">No admission inquiries found</p>
                                        <p class="text-gray-400 text-sm mt-1">New inquiries will appear here</p>
                                    </td>
                                </tr>
                            <?php else: ?>
                                <?php foreach ($inquiries as $inquiry): ?>
                                <tr class="table-row-hover transition-all duration-200">
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="flex items-center">
                                            <div class="w-10 h-10 bg-gradient-to-br from-blue-500 to-purple-600 rounded-full flex items-center justify-center text-white font-semibold text-sm mr-3">
                                                <?php echo strtoupper(substr($inquiry['student_name'] ?? 'N', 0, 1)); ?>
                                            </div>
                                            <div>
                                                <div class="text-sm font-semibold text-gray-900"><?php echo htmlspecialchars($inquiry['student_name'] ?? 'N/A'); ?></div>
                                                <div class="text-xs text-gray-500">ID: <?php echo $inquiry['id']; ?></div>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4">
                                        <div class="text-sm text-gray-900 font-medium"><?php echo htmlspecialchars($inquiry['parent_name'] ?? 'N/A'); ?></div>
                                        <div class="text-sm text-gray-600 mt-1"><?php echo htmlspecialchars($inquiry['email'] ?? 'N/A'); ?></div>
                                        <div class="text-sm text-gray-500"><?php echo htmlspecialchars($inquiry['phone'] ?? 'N/A'); ?></div>
                                    </td>
                                    <td class="px-6 py-4">
                                        <div class="text-sm font-medium text-gray-900"><?php echo htmlspecialchars($inquiry['grade'] ?? 'N/A'); ?></div>
                                        <div class="text-sm text-gray-600"><?php echo htmlspecialchars($inquiry['program'] ?? 'General Inquiry'); ?></div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <span class="status-badge status-<?php echo $inquiry['status'] ?? 'pending'; ?>">
                                            <i class="fas fa-circle text-xs"></i>
                                            <?php echo ucfirst($inquiry['status'] ?? 'pending'); ?>
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="text-sm text-gray-900 font-medium"><?php echo date('M j, Y', strtotime($inquiry['created_at'])); ?></div>
                                        <div class="text-xs text-gray-500"><?php echo date('g:i A', strtotime($inquiry['created_at'])); ?></div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                        <div class="flex items-center space-x-2">
                                            <button onclick="openViewModal(<?php echo htmlspecialchars(json_encode($inquiry)); ?>)" 
                                                    class="action-btn bg-blue-50 text-blue-600 hover:bg-blue-100 border border-blue-200">
                                                <i class="fas fa-eye mr-1"></i> View
                                            </button>
                                            <button onclick="openContactModal(<?php echo htmlspecialchars(json_encode($inquiry)); ?>)" 
                                                    class="action-btn bg-green-50 text-green-600 hover:bg-green-100 border border-green-200">
                                                <i class="fas fa-phone mr-1"></i> Contact
                                            </button>
                                            <button onclick="openStatusModal(<?php echo $inquiry['id']; ?>, '<?php echo $inquiry['status']; ?>')" 
                                                    class="action-btn bg-purple-50 text-purple-600 hover:bg-purple-100 border border-purple-200">
                                                <i class="fas fa-edit mr-1"></i> Status
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <!-- View Details Modal -->
    <div id="viewModal" class="modal-container">
        <div class="modal-overlay" onclick="closeModal('viewModal')"></div>
        <div class="flex items-center justify-center min-h-screen p-4">
            <div class="modal-content max-w-4xl w-full">
                <div class="p-6">
                    <div class="flex justify-between items-center mb-6">
                        <h3 class="text-2xl font-bold text-gray-800">Inquiry Details</h3>
                        <button onclick="closeModal('viewModal')" class="text-gray-400 hover:text-gray-600 transition-colors">
                            <i class="fas fa-times text-xl"></i>
                        </button>
                    </div>
                    
                    <div id="viewModalContent">
                        <!-- Content will be loaded here by JavaScript -->
                    </div>
                    
                    <div class="flex justify-end mt-6">
                        <button onclick="closeModal('viewModal')" class="px-4 py-2 bg-gray-500 text-white rounded-lg hover:bg-gray-600 font-medium transition-colors">
                            Close
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Contact Modal -->
    <div id="contactModal" class="modal-container">
        <div class="modal-overlay" onclick="closeModal('contactModal')"></div>
        <div class="flex items-center justify-center min-h-screen p-4">
            <div class="modal-content max-w-md w-full">
                <div class="p-6">
                    <div class="flex justify-between items-center mb-6">
                        <h3 class="text-xl font-bold text-gray-800">Contact Parent</h3>
                        <button onclick="closeModal('contactModal')" class="text-gray-400 hover:text-gray-600 transition-colors">
                            <i class="fas fa-times text-xl"></i>
                        </button>
                    </div>
                    
                    <form id="contactForm" method="POST">
                        <input type="hidden" name="inquiry_id" id="contact_inquiry_id">
                        <input type="hidden" name="send_contact" value="1">
                        
                        <div class="space-y-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Contact Method</label>
                                <select name="contact_method" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent" required>
                                    <option value="email">Email</option>
                                    <option value="phone">Phone Call</option>
                                    <option value="sms">SMS</option>
                                    <option value="whatsapp">WhatsApp</option>
                                </select>
                            </div>
                            
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Subject</label>
                                <input type="text" name="contact_subject" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent" placeholder="Enter subject..." required>
                            </div>
                            
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Message</label>
                                <textarea name="contact_message" rows="4" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent" placeholder="Type your message here..." required></textarea>
                            </div>
                        </div>
                        
                        <div class="flex justify-end space-x-3 mt-6">
                            <button type="button" onclick="closeModal('contactModal')" class="px-4 py-2 text-gray-600 hover:text-gray-800 font-medium transition-colors">Cancel</button>
                            <button type="submit" class="px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 font-medium transition-colors">
                                <i class="fas fa-paper-plane mr-2"></i> Send Message
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Status Update Modal -->
    <div id="statusModal" class="modal-container">
        <div class="modal-overlay" onclick="closeModal('statusModal')"></div>
        <div class="flex items-center justify-center min-h-screen p-4">
            <div class="modal-content max-w-md w-full">
                <div class="p-6">
                    <div class="flex justify-between items-center mb-6">
                        <h3 class="text-xl font-bold text-gray-800">Update Status</h3>
                        <button onclick="closeModal('statusModal')" class="text-gray-400 hover:text-gray-600 transition-colors">
                            <i class="fas fa-times text-xl"></i>
                        </button>
                    </div>
                    
                    <form id="statusForm" method="POST">
                        <input type="hidden" name="inquiry_id" id="status_inquiry_id">
                        <input type="hidden" name="update_status" value="1">
                        
                        <div class="space-y-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Select Status</label>
                                <div class="grid grid-cols-2 gap-2">
                                    <label class="status-option flex items-center p-3 border-2 border-gray-200 rounded-lg cursor-pointer hover:border-blue-500 transition-colors">
                                        <input type="radio" name="status" value="pending" class="sr-only">
                                        <div class="flex items-center">
                                            <div class="w-3 h-3 rounded-full bg-yellow-500 mr-2"></div>
                                            <span class="text-sm font-medium">Pending</span>
                                        </div>
                                    </label>
                                    <label class="status-option flex items-center p-3 border-2 border-gray-200 rounded-lg cursor-pointer hover:border-blue-500 transition-colors">
                                        <input type="radio" name="status" value="contacted" class="sr-only">
                                        <div class="flex items-center">
                                            <div class="w-3 h-3 rounded-full bg-blue-500 mr-2"></div>
                                            <span class="text-sm font-medium">Contacted</span>
                                        </div>
                                    </label>
                                    <label class="status-option flex items-center p-3 border-2 border-gray-200 rounded-lg cursor-pointer hover:border-blue-500 transition-colors">
                                        <input type="radio" name="status" value="approved" class="sr-only">
                                        <div class="flex items-center">
                                            <div class="w-3 h-3 rounded-full bg-green-500 mr-2"></div>
                                            <span class="text-sm font-medium">Approved</span>
                                        </div>
                                    </label>
                                    <label class="status-option flex items-center p-3 border-2 border-gray-200 rounded-lg cursor-pointer hover:border-blue-500 transition-colors">
                                        <input type="radio" name="status" value="registered" class="sr-only">
                                        <div class="flex items-center">
                                            <div class="w-3 h-3 rounded-full bg-purple-500 mr-2"></div>
                                            <span class="text-sm font-medium">Registered</span>
                                        </div>
                                    </label>
                                </div>
                            </div>
                            
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Admin Notes</label>
                                <textarea name="admin_notes" rows="3" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent" placeholder="Add any notes or comments..."></textarea>
                            </div>
                        </div>
                        
                        <div class="flex justify-end space-x-3 mt-6">
                            <button type="button" onclick="closeModal('statusModal')" class="px-4 py-2 text-gray-600 hover:text-gray-800 font-medium transition-colors">Cancel</button>
                            <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 font-medium transition-colors">
                                <i class="fas fa-save mr-2"></i> Update Status
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script>
    // Modal Functions
    function openViewModal(inquiry) {
        const modal = document.getElementById('viewModal');
        const content = document.getElementById('viewModalContent');
        
        // Format the inquiry data into HTML
        content.innerHTML = `
            <div class="space-y-6">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div class="bg-gray-50 p-4 rounded-lg">
                        <h4 class="font-semibold text-gray-700 mb-3 flex items-center">
                            <i class="fas fa-user-graduate mr-2 text-blue-600"></i> Student Information
                        </h4>
                        <div class="space-y-2 text-sm">
                            <div class="flex justify-between">
                                <span class="text-gray-600">Name:</span>
                                <span class="font-medium">${inquiry.student_name || 'N/A'}</span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-gray-600">Grade:</span>
                                <span class="font-medium">${inquiry.grade || 'N/A'}</span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-gray-600">Program:</span>
                                <span class="font-medium">${inquiry.program || 'N/A'}</span>
                            </div>
                        </div>
                    </div>
                    
                    <div class="bg-gray-50 p-4 rounded-lg">
                        <h4 class="font-semibold text-gray-700 mb-3 flex items-center">
                            <i class="fas fa-users mr-2 text-green-600"></i> Parent Information
                        </h4>
                        <div class="space-y-2 text-sm">
                            <div class="flex justify-between">
                                <span class="text-gray-600">Parent Name:</span>
                                <span class="font-medium">${inquiry.parent_name || 'N/A'}</span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-gray-600">Email:</span>
                                <span class="font-medium">${inquiry.email || 'N/A'}</span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-gray-600">Phone:</span>
                                <span class="font-medium">${inquiry.phone || 'N/A'}</span>
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div class="bg-gray-50 p-4 rounded-lg">
                        <h4 class="font-semibold text-gray-700 mb-3 flex items-center">
                            <i class="fas fa-map-marker-alt mr-2 text-red-600"></i> Address Information
                        </h4>
                        <div class="space-y-2 text-sm">
                            <div class="flex justify-between">
                                <span class="text-gray-600">Address:</span>
                                <span class="font-medium text-right">${inquiry.address || 'N/A'}</span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-gray-600">City:</span>
                                <span class="font-medium">${inquiry.city || 'N/A'}</span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-gray-600">State:</span>
                                <span class="font-medium">${inquiry.state || 'N/A'}</span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-gray-600">Pincode:</span>
                                <span class="font-medium">${inquiry.pincode || 'N/A'}</span>
                            </div>
                        </div>
                    </div>
                    
                    <div class="bg-gray-50 p-4 rounded-lg">
                        <h4 class="font-semibold text-gray-700 mb-3 flex items-center">
                            <i class="fas fa-school mr-2 text-purple-600"></i> Educational Background
                        </h4>
                        <div class="space-y-2 text-sm">
                            <div class="flex justify-between">
                                <span class="text-gray-600">Previous School:</span>
                                <span class="font-medium text-right">${inquiry.previous_school || 'N/A'}</span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-gray-600">Status:</span>
                                <span class="status-badge status-${inquiry.status || 'pending'}">${inquiry.status || 'pending'}</span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-gray-600">Submitted:</span>
                                <span class="font-medium">${new Date(inquiry.created_at).toLocaleDateString()}</span>
                            </div>
                        </div>
                    </div>
                </div>
                
                ${inquiry.message ? `
                <div class="bg-gray-50 p-4 rounded-lg">
                    <h4 class="font-semibold text-gray-700 mb-3 flex items-center">
                        <i class="fas fa-comment mr-2 text-orange-600"></i> Additional Message
                    </h4>
                    <p class="text-sm text-gray-600">${inquiry.message}</p>
                </div>
                ` : ''}
                
                ${inquiry.admin_notes ? `
                <div class="bg-blue-50 p-4 rounded-lg border border-blue-200">
                    <h4 class="font-semibold text-blue-700 mb-3 flex items-center">
                        <i class="fas fa-sticky-note mr-2 text-blue-600"></i> Admin Notes
                    </h4>
                    <p class="text-sm text-blue-600 whitespace-pre-wrap">${inquiry.admin_notes}</p>
                </div>
                ` : ''}
            </div>
        `;
        
        modal.style.display = 'block';
        document.body.classList.add('modal-open');
    }

    function openContactModal(inquiry) {
        const modal = document.getElementById('contactModal');
        document.getElementById('contact_inquiry_id').value = inquiry.id;
        
        // Pre-fill the subject
        const subjectField = document.querySelector('#contactModal input[name="contact_subject"]');
        subjectField.value = `Regarding ${inquiry.student_name}'s Admission Inquiry - Bhaktivedanta Gurukul`;
        
        modal.style.display = 'block';
        document.body.classList.add('modal-open');
    }

    function openStatusModal(inquiryId, currentStatus) {
        const modal = document.getElementById('statusModal');
        document.getElementById('status_inquiry_id').value = inquiryId;
        
        // Set the current status as checked
        const radioButtons = document.querySelectorAll('#statusModal input[name="status"]');
        radioButtons.forEach(radio => {
            if (radio.value === currentStatus) {
                radio.checked = true;
                // Also update the parent label styling
                radio.closest('.status-option').classList.add('border-blue-500', 'bg-blue-50');
            }
        });
        
        modal.style.display = 'block';
        document.body.classList.add('modal-open');
    }

    function closeModal(modalId) {
        const modal = document.getElementById(modalId);
        modal.style.display = 'none';
        document.body.classList.remove('modal-open');
    }

    // Add interactivity to status radio buttons
    document.addEventListener('DOMContentLoaded', function() {
        // Status radio button styling
        const statusOptions = document.querySelectorAll('.status-option');
        statusOptions.forEach(option => {
            const radio = option.querySelector('input[type="radio"]');
            
            option.addEventListener('click', function() {
                // Remove all active styles
                statusOptions.forEach(opt => {
                    opt.classList.remove('border-blue-500', 'bg-blue-50');
                });
                
                // Add active style to clicked option
                this.classList.add('border-blue-500', 'bg-blue-50');
            });
        });

        // Close modals with Escape key
        document.addEventListener('keydown', function(e) {
            if (e.key === 'Escape') {
                closeModal('viewModal');
                closeModal('contactModal');
                closeModal('statusModal');
            }
        });

        // Initialize any checked status options
        const checkedOptions = document.querySelectorAll('.status-option input:checked');
        checkedOptions.forEach(radio => {
            radio.closest('.status-option').classList.add('border-blue-500', 'bg-blue-50');
        });
    });
    </script>

    <?php include '../includes/footer.php'; ?>
</body>
</html>