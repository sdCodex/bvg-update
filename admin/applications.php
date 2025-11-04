<?php
session_start();
if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    header('Location: login.php');
    exit;
}
require_once '../includes/db.php';

$base_url = '/work/Gurkul_Project';

// Handle status updates
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_status'])) {
    $application_id = $_POST['application_id'];
    $new_status = $_POST['status'];
    $notes = $_POST['notes'] ?? '';
    
    try {
        $stmt = $pdo->prepare("UPDATE applications SET status = ?, notes = ? WHERE id = ?");
        $stmt->execute([$new_status, $notes, $application_id]);
        
        $_SESSION['success_message'] = "Application status updated successfully!";
        header('Location: ' . $_SERVER['PHP_SELF']);
        exit;
    } catch (PDOException $e) {
        $_SESSION['error_message'] = "Error updating status: " . $e->getMessage();
    }
}

// Get applications data
try {
    $stmt = $pdo->query("SELECT * FROM applications ORDER BY application_date DESC");
    $applications = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    $applications = [];
    error_log("Database error: " . $e->getMessage());
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
    <title>Applications - Bhaktivedanta Gurukul</title>
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
        .status-under-review { background-color: #dbeafe; color: #1e40af; border: 1px solid #3b82f6; }
        
        /* Modal Styles */
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
        
        .modal-open {
            overflow: hidden;
        }
    </style>
</head>
<body class="bg-gray-50">
    <?php 
    $current_page = 'applications.php';
    $current_directory = 'admin';
    include '../includes/header.php'; 
    ?>

    <div class="container mx-auto px-4 py-8 mt-16">
        <div class="max-w-7xl mx-auto">
            <!-- Header Section -->
            <div class="mb-8">
                <div class="flex flex-col md:flex-row md:items-center md:justify-between">
                    <div>
                        <h1 class="text-3xl font-bold text-gray-800 mb-2">Applications Management</h1>
                        <p class="text-gray-600">Manage all student applications</p>
                    </div>
                    <div class="mt-4 md:mt-0 flex items-center space-x-4">
                        <div class="bg-white rounded-lg shadow-sm px-4 py-2 border">
                            <span class="text-sm text-gray-600">Total Applications:</span>
                            <span class="font-semibold text-gray-800 ml-1"><?php echo count($applications); ?></span>
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
                                <?php echo count(array_filter($applications, fn($i) => ($i['status'] ?? 'Pending') === 'Pending')); ?>
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
                            <p class="text-gray-600 text-sm">Approved</p>
                            <p class="text-2xl font-bold text-gray-800 mt-1">
                                <?php echo count(array_filter($applications, fn($i) => ($i['status'] ?? '') === 'Approved')); ?>
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
                            <p class="text-gray-600 text-sm">Rejected</p>
                            <p class="text-2xl font-bold text-gray-800 mt-1">
                                <?php echo count(array_filter($applications, fn($i) => ($i['status'] ?? '') === 'Rejected')); ?>
                            </p>
                        </div>
                        <div class="w-12 h-12 bg-red-100 rounded-full flex items-center justify-center">
                            <i class="fas fa-times text-red-600 text-xl"></i>
                        </div>
                    </div>
                </div>
                
                <div class="bg-white p-6 rounded-xl shadow-lg border border-gray-200">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-gray-600 text-sm">Under Review</p>
                            <p class="text-2xl font-bold text-gray-800 mt-1">
                                <?php echo count(array_filter($applications, fn($i) => ($i['status'] ?? '') === 'Under Review')); ?>
                            </p>
                        </div>
                        <div class="w-12 h-12 bg-blue-100 rounded-full flex items-center justify-center">
                            <i class="fas fa-search text-blue-600 text-xl"></i>
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

            <!-- Debug Info (Remove in production) -->
            <?php if (empty($applications)): ?>
                <div class="bg-yellow-50 border border-yellow-200 text-yellow-700 px-4 py-3 rounded-lg mb-6">
                    <p><strong>Debug Info:</strong> No applications found in database.</p>
                    <p class="text-sm mt-1">Table: applications | Columns: <?php 
                        try {
                            $stmt = $pdo->query("SHOW COLUMNS FROM applications");
                            $columns = $stmt->fetchAll(PDO::FETCH_COLUMN);
                            echo implode(', ', $columns);
                        } catch (Exception $e) {
                            echo "Error fetching columns: " . $e->getMessage();
                        }
                    ?></p>
                </div>
            <?php endif; ?>

            <!-- Main Table -->
            <div class="bg-white rounded-xl shadow-lg overflow-hidden border border-gray-200">
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-4 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Application Info</th>
                                <th class="px-6 py-4 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Parents & Contact</th>
                                <th class="px-6 py-4 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Class & Details</th>
                                <th class="px-6 py-4 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                                <th class="px-6 py-4 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Date</th>
                                <th class="px-6 py-4 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            <?php if (empty($applications)): ?>
                                <tr>
                                    <td colspan="6" class="px-6 py-8 text-center">
                                        <div class="text-gray-400 mb-2">
                                            <i class="fas fa-inbox text-4xl"></i>
                                        </div>
                                        <p class="text-gray-500 text-lg">No applications found</p>
                                        <p class="text-gray-400 text-sm mt-1">New applications will appear here</p>
                                    </td>
                                </tr>
                            <?php else: ?>
                                <?php foreach ($applications as $application): ?>
                                <tr class="table-row-hover transition-all duration-200">
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="flex items-center">
                                            <div class="w-10 h-10 bg-gradient-to-br from-blue-500 to-purple-600 rounded-full flex items-center justify-center text-white font-semibold text-sm mr-3">
                                                <?php echo strtoupper(substr($application['student_name'] ?? 'N', 0, 1)); ?>
                                            </div>
                                            <div>
                                                <div class="text-sm font-semibold text-gray-900"><?php echo htmlspecialchars($application['student_name'] ?? 'N/A'); ?></div>
                                                <div class="text-xs text-gray-500">
                                                    App #: <?php echo htmlspecialchars($application['application_number'] ?? 'N/A'); ?>
                                                </div>
                                                <div class="text-xs text-gray-500">
                                                    DOB: <?php echo !empty($application['date_of_birth']) ? date('M j, Y', strtotime($application['date_of_birth'])) : 'N/A'; ?>
                                                </div>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4">
                                        <div class="text-sm text-gray-900 font-medium">Father: <?php echo htmlspecialchars($application['father_name'] ?? 'N/A'); ?></div>
                                        <div class="text-sm text-gray-600">Mother: <?php echo htmlspecialchars($application['mother_name'] ?? 'N/A'); ?></div>
                                        <div class="text-sm text-gray-600 mt-1"><?php echo htmlspecialchars($application['email'] ?? 'N/A'); ?></div>
                                        <div class="text-sm text-gray-500"><?php echo htmlspecialchars($application['phone'] ?? 'N/A'); ?></div>
                                    </td>
                                    <td class="px-6 py-4">
                                        <div class="text-sm font-medium text-gray-900">Class: <?php echo htmlspecialchars($application['applied_class'] ?? 'N/A'); ?></div>
                                        <div class="text-sm text-gray-600">Gender: <?php echo htmlspecialchars($application['gender'] ?? 'N/A'); ?></div>
                                        <div class="text-xs text-gray-500 mt-1">
                                            <?php 
                                            $address = $application['address'] ?? '';
                                            if (strlen($address) > 50) {
                                                echo htmlspecialchars(substr($address, 0, 50)) . '...';
                                            } else {
                                                echo htmlspecialchars($address);
                                            }
                                            ?>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <span class="status-badge status-<?php echo strtolower(str_replace(' ', '-', $application['status'] ?? 'pending')); ?>">
                                            <i class="fas fa-circle text-xs"></i>
                                            <?php echo htmlspecialchars($application['status'] ?? 'Pending'); ?>
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="text-sm text-gray-900 font-medium">
                                            <?php echo date('M j, Y', strtotime($application['application_date'] ?? $application['created_at'] ?? 'now')); ?>
                                        </div>
                                        <div class="text-xs text-gray-500">
                                            <?php echo date('g:i A', strtotime($application['application_date'] ?? $application['created_at'] ?? 'now')); ?>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                        <div class="flex items-center space-x-2">
                                            <button onclick="openViewModal(<?php echo htmlspecialchars(json_encode($application)); ?>)" 
                                                    class="action-btn bg-blue-50 text-blue-600 hover:bg-blue-100 border border-blue-200">
                                                <i class="fas fa-eye mr-1"></i> View
                                            </button>
                                            <button onclick="openStatusModal(<?php echo $application['id']; ?>, '<?php echo $application['status'] ?? 'Pending'; ?>')" 
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
                        <h3 class="text-2xl font-bold text-gray-800">Application Details</h3>
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

    <!-- Status Update Modal -->
    <div id="statusModal" class="modal-container">
        <div class="modal-overlay" onclick="closeModal('statusModal')"></div>
        <div class="flex items-center justify-center min-h-screen p-4">
            <div class="modal-content max-w-md w-full">
                <div class="p-6">
                    <div class="flex justify-between items-center mb-6">
                        <h3 class="text-xl font-bold text-gray-800">Update Application Status</h3>
                        <button onclick="closeModal('statusModal')" class="text-gray-400 hover:text-gray-600 transition-colors">
                            <i class="fas fa-times text-xl"></i>
                        </button>
                    </div>
                    
                    <form id="statusForm" method="POST">
                        <input type="hidden" name="application_id" id="status_application_id">
                        <input type="hidden" name="update_status" value="1">
                        
                        <div class="space-y-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Select Status</label>
                                <div class="grid grid-cols-2 gap-2">
                                    <label class="status-option flex items-center p-3 border-2 border-gray-200 rounded-lg cursor-pointer hover:border-blue-500 transition-colors">
                                        <input type="radio" name="status" value="Pending" class="sr-only">
                                        <div class="flex items-center">
                                            <div class="w-3 h-3 rounded-full bg-yellow-500 mr-2"></div>
                                            <span class="text-sm font-medium">Pending</span>
                                        </div>
                                    </label>
                                    <label class="status-option flex items-center p-3 border-2 border-gray-200 rounded-lg cursor-pointer hover:border-blue-500 transition-colors">
                                        <input type="radio" name="status" value="Under Review" class="sr-only">
                                        <div class="flex items-center">
                                            <div class="w-3 h-3 rounded-full bg-blue-500 mr-2"></div>
                                            <span class="text-sm font-medium">Under Review</span>
                                        </div>
                                    </label>
                                    <label class="status-option flex items-center p-3 border-2 border-gray-200 rounded-lg cursor-pointer hover:border-blue-500 transition-colors">
                                        <input type="radio" name="status" value="Approved" class="sr-only">
                                        <div class="flex items-center">
                                            <div class="w-3 h-3 rounded-full bg-green-500 mr-2"></div>
                                            <span class="text-sm font-medium">Approved</span>
                                        </div>
                                    </label>
                                    <label class="status-option flex items-center p-3 border-2 border-gray-200 rounded-lg cursor-pointer hover:border-blue-500 transition-colors">
                                        <input type="radio" name="status" value="Rejected" class="sr-only">
                                        <div class="flex items-center">
                                            <div class="w-3 h-3 rounded-full bg-red-500 mr-2"></div>
                                            <span class="text-sm font-medium">Rejected</span>
                                        </div>
                                    </label>
                                </div>
                            </div>
                            
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Admin Notes</label>
                                <textarea name="notes" rows="3" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent" placeholder="Add any notes or comments..."><?php echo htmlspecialchars($application['notes'] ?? ''); ?></textarea>
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
    function openViewModal(application) {
        const modal = document.getElementById('viewModal');
        const content = document.getElementById('viewModalContent');
        
        // Format the application data into HTML
        content.innerHTML = `
            <div class="space-y-6">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div class="bg-gray-50 p-4 rounded-lg">
                        <h4 class="font-semibold text-gray-700 mb-3 flex items-center">
                            <i class="fas fa-user-graduate mr-2 text-blue-600"></i> Student Information
                        </h4>
                        <div class="space-y-2 text-sm">
                            <div class="flex justify-between">
                                <span class="text-gray-600">Application Number:</span>
                                <span class="font-medium">${application.application_number || 'N/A'}</span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-gray-600">Student Name:</span>
                                <span class="font-medium">${application.student_name || 'N/A'}</span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-gray-600">Date of Birth:</span>
                                <span class="font-medium">${application.date_of_birth ? new Date(application.date_of_birth).toLocaleDateString() : 'N/A'}</span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-gray-600">Gender:</span>
                                <span class="font-medium">${application.gender || 'N/A'}</span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-gray-600">Applied Class:</span>
                                <span class="font-medium">${application.applied_class || 'N/A'}</span>
                            </div>
                        </div>
                    </div>
                    
                    <div class="bg-gray-50 p-4 rounded-lg">
                        <h4 class="font-semibold text-gray-700 mb-3 flex items-center">
                            <i class="fas fa-users mr-2 text-green-600"></i> Parent Information
                        </h4>
                        <div class="space-y-2 text-sm">
                            <div class="flex justify-between">
                                <span class="text-gray-600">Father's Name:</span>
                                <span class="font-medium">${application.father_name || 'N/A'}</span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-gray-600">Mother's Name:</span>
                                <span class="font-medium">${application.mother_name || 'N/A'}</span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-gray-600">Email:</span>
                                <span class="font-medium">${application.email || 'N/A'}</span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-gray-600">Phone:</span>
                                <span class="font-medium">${application.phone || 'N/A'}</span>
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="bg-gray-50 p-4 rounded-lg">
                    <h4 class="font-semibold text-gray-700 mb-3 flex items-center">
                        <i class="fas fa-map-marker-alt mr-2 text-red-600"></i> Address Information
                    </h4>
                    <p class="text-sm text-gray-600 whitespace-pre-wrap">${application.address || 'N/A'}</p>
                </div>
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div class="bg-gray-50 p-4 rounded-lg">
                        <h4 class="font-semibold text-gray-700 mb-3 flex items-center">
                            <i class="fas fa-info-circle mr-2 text-purple-600"></i> Application Details
                        </h4>
                        <div class="space-y-2 text-sm">
                            <div class="flex justify-between">
                                <span class="text-gray-600">Status:</span>
                                <span class="status-badge status-${(application.status || 'pending').toLowerCase().replace(' ', '-')}">${application.status || 'Pending'}</span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-gray-600">Application Date:</span>
                                <span class="font-medium">${new Date(application.application_date || application.created_at).toLocaleDateString()}</span>
                            </div>
                        </div>
                    </div>
                    
                    ${application.notes ? `
                    <div class="bg-blue-50 p-4 rounded-lg border border-blue-200">
                        <h4 class="font-semibold text-blue-700 mb-3 flex items-center">
                            <i class="fas fa-sticky-note mr-2 text-blue-600"></i> Admin Notes
                        </h4>
                        <p class="text-sm text-blue-600 whitespace-pre-wrap">${application.notes}</p>
                    </div>
                    ` : ''}
                </div>
            </div>
        `;
        
        modal.style.display = 'block';
        document.body.classList.add('modal-open');
    }

    function openStatusModal(applicationId, currentStatus) {
        const modal = document.getElementById('statusModal');
        document.getElementById('status_application_id').value = applicationId;
        
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