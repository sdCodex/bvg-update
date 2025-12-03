<?php
// Enable error reporting for debugging
error_reporting(E_ALL);
ini_set('display_errors', 1);

session_start();
if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    header('Location: login.php');
    exit;
}
require_once '../includes/db.php';

$base_url = '/Gurkul_Project';

// Get job applications data
try {
    $stmt = $pdo->query("SELECT * FROM job_applications ORDER BY applied_at DESC");
    $job_applications = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    $job_applications = [];
    $error = "Database error: " . $e->getMessage();
}

// Handle actions (delete, update status)
if (isset($_GET['delete_id'])) {
    try {
        // Get resume path to delete file
        $stmt = $pdo->prepare("SELECT resume_path, cover_letter_path FROM job_applications WHERE id = ?");
        $stmt->execute([$_GET['delete_id']]);
        $files = $stmt->fetch(PDO::FETCH_ASSOC);
        
        // Delete physical files
        if ($files && $files['resume_path'] && file_exists('../' . $files['resume_path'])) {
            unlink('../' . $files['resume_path']);
        }
        if ($files && $files['cover_letter_path'] && file_exists('../' . $files['cover_letter_path'])) {
            unlink('../' . $files['cover_letter_path']);
        }
        
        // Delete from database
        $stmt = $pdo->prepare("DELETE FROM job_applications WHERE id = ?");
        $stmt->execute([$_GET['delete_id']]);
        $_SESSION['success'] = "Job application deleted successfully";
        header('Location: job_applications.php');
        exit;
    } catch (PDOException $e) {
        $_SESSION['error'] = "Error deleting job application: " . $e->getMessage();
        header('Location: job_applications.php');
        exit;
    }
}

// Handle status update
if (isset($_POST['update_status'])) {
    try {
        $stmt = $pdo->prepare("UPDATE job_applications SET status = ? WHERE id = ?");
        $stmt->execute([$_POST['status'], $_POST['application_id']]);
        $_SESSION['success'] = "Application status updated successfully";
        header('Location: job_applications.php');
        exit;
    } catch (PDOException $e) {
        $_SESSION['error'] = "Error updating status: " . $e->getMessage();
        header('Location: job_applications.php');
        exit;
    }
}

// Store messages in session to persist after redirect
$success = $_SESSION['success'] ?? '';
$error = $_SESSION['error'] ?? ($error ?? '');
unset($_SESSION['success'], $_SESSION['error']);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Job Applications - Bhaktivedanta Gurukul</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="icon" type="image/x-icon" href="../images/bvgLogo.png">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        .modal {
            display: none;
            position: fixed;
            z-index: 1000;
            left: 0;
            top: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0,0,0,0.5);
        }
        .modal-content {
            background-color: white;
            margin: 5% auto;
            padding: 20px;
            border-radius: 8px;
            width: 90%;
            max-width: 700px;
            max-height: 80vh;
            overflow-y: auto;
        }
        .status-pending { background-color: #fef3c7; color: #92400e; }
        .status-reviewed { background-color: #dbeafe; color: #1e40af; }
        .status-interview { background-color: #fce7f3; color: #be185d; }
        .status-approved { background-color: #d1fae5; color: #065f46; }
        .status-rejected { background-color: #fee2e2; color: #991b1b; }
        .info-section {
            background: #f8fafc;
            border: 1px solid #e2e8f0;
            border-radius: 8px;
            padding: 16px;
            margin-bottom: 16px;
        }
        @media (max-width: 768px) {
            .table-container {
                overflow-x: auto;
            }
            .modal-content {
                margin: 10% auto;
                width: 95%;
            }
        }
    </style>
</head>
<body class="bg-gray-100">
    <?php 
    $current_page = 'job_applications.php';
    $current_directory = 'admin';
    include '../includes/header.php'; 
    ?>

    <div class="container mx-auto px-4 py-8 mt-16">
        <div class="max-w-7xl mx-auto">
            <!-- Header Section -->
            <div class="flex flex-col md:flex-row justify-between items-start md:items-center mb-6 gap-4">
                <div>
                    <h1 class="text-2xl md:text-3xl font-bold text-gray-800 mb-2">Job Applications</h1>
                    <p class="text-gray-600">Manage all job applications and candidate profiles</p>
                </div>
                <div class="bg-red-100 text-red-800 px-4 py-2 rounded-lg">
                    <i class="fas fa-briefcase mr-2"></i>
                    Total: <?php echo count($job_applications); ?>
                </div>
            </div>

            <!-- Status Summary -->
            <?php if (!empty($job_applications)): ?>
            <div class="grid grid-cols-2 md:grid-cols-5 gap-3 md:gap-4 mb-6">
                <?php
                $status_counts = [
                    'pending' => 0,
                    'reviewed' => 0,
                    'interview' => 0,
                    'approved' => 0,
                    'rejected' => 0
                ];
                
                foreach ($job_applications as $app) {
                    $status = strtolower($app['status'] ?? 'pending');
                    if (isset($status_counts[$status])) {
                        $status_counts[$status]++;
                    } else {
                        $status_counts['pending']++;
                    }
                }
                ?>
                <div class="bg-white rounded-lg p-3 md:p-4 text-center border-l-4 border-yellow-500">
                    <div class="text-xl md:text-2xl font-bold text-yellow-600"><?php echo $status_counts['pending']; ?></div>
                    <div class="text-xs md:text-sm text-gray-600">Pending</div>
                </div>
                <div class="bg-white rounded-lg p-3 md:p-4 text-center border-l-4 border-blue-500">
                    <div class="text-xl md:text-2xl font-bold text-blue-600"><?php echo $status_counts['reviewed']; ?></div>
                    <div class="text-xs md:text-sm text-gray-600">Reviewed</div>
                </div>
                <div class="bg-white rounded-lg p-3 md:p-4 text-center border-l-4 border-pink-500">
                    <div class="text-xl md:text-2xl font-bold text-pink-600"><?php echo $status_counts['interview']; ?></div>
                    <div class="text-xs md:text-sm text-gray-600">Interview</div>
                </div>
                <div class="bg-white rounded-lg p-3 md:p-4 text-center border-l-4 border-green-500">
                    <div class="text-xl md:text-2xl font-bold text-green-600"><?php echo $status_counts['approved']; ?></div>
                    <div class="text-xs md:text-sm text-gray-600">Approved</div>
                </div>
                <div class="bg-white rounded-lg p-3 md:p-4 text-center border-l-4 border-red-500">
                    <div class="text-xl md:text-2xl font-bold text-red-600"><?php echo $status_counts['rejected']; ?></div>
                    <div class="text-xs md:text-sm text-gray-600">Rejected</div>
                </div>
            </div>
            <?php endif; ?>

            <!-- Messages -->
            <?php if (!empty($success)): ?>
                <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-6">
                    <i class="fas fa-check-circle mr-2"></i><?php echo htmlspecialchars($success); ?>
                </div>
            <?php endif; ?>

            <?php if (!empty($error)): ?>
                <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-6">
                    <i class="fas fa-exclamation-circle mr-2"></i><?php echo htmlspecialchars($error); ?>
                </div>
            <?php endif; ?>

            <!-- Applications Table -->
            <div class="bg-white rounded-lg shadow-md overflow-hidden">
                <?php if (empty($job_applications)): ?>
                    <div class="text-center py-12">
                        <i class="fas fa-briefcase text-gray-300 text-6xl mb-4"></i>
                        <h3 class="text-lg font-medium text-gray-900 mb-2">No Job Applications</h3>
                        <p class="text-gray-500">Job applications will appear here when candidates apply.</p>
                    </div>
                <?php else: ?>
                    <div class="table-container">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Candidate</th>
                                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Position</th>
                                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Experience</th>
                                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Current Position</th>
                                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Expected Salary</th>
                                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Applied</th>
                                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                <?php foreach ($job_applications as $application): ?>
                                <tr class="hover:bg-gray-50 transition-colors">
                                    <!-- Candidate Info -->
                                    <td class="px-4 py-4">
                                        <div class="flex items-center">
                                            <div class="flex-shrink-0 h-10 w-10 bg-blue-100 rounded-full flex items-center justify-center">
                                                <i class="fas fa-user text-blue-600"></i>
                                            </div>
                                            <div class="ml-4">
                                                <div class="text-sm font-medium text-gray-900">
                                                    <?php echo htmlspecialchars($application['full_name']); ?>
                                                </div>
                                                <div class="text-sm text-gray-500">
                                                    <?php echo htmlspecialchars($application['email']); ?>
                                                </div>
                                                <?php if (!empty($application['phone'])): ?>
                                                <div class="text-sm text-gray-500">
                                                    <i class="fas fa-phone mr-1"></i><?php echo htmlspecialchars($application['phone']); ?>
                                                </div>
                                                <?php endif; ?>
                                            </div>
                                        </div>
                                    </td>

                                    <!-- Position Info -->
                                    <td class="px-4 py-4">
                                        <div class="text-sm font-medium text-gray-900">
                                            <?php echo htmlspecialchars($application['position_applied']); ?>
                                        </div>
                                        <div class="text-sm text-gray-500 capitalize">
                                            <?php echo htmlspecialchars($application['position_type']); ?>
                                        </div>
                                    </td>

                                    <!-- Experience -->
                                    <td class="px-4 py-4 text-sm text-gray-900">
                                        <?php echo htmlspecialchars($application['experience_years']); ?> years
                                    </td>

                                    <!-- Current Position -->
                                    <td class="px-4 py-4">
                                        <div class="text-sm text-gray-900">
                                            <?php echo htmlspecialchars($application['current_organization'] ?? 'N/A'); ?>
                                        </div>
                                        <div class="text-sm text-gray-500">
                                            <?php echo htmlspecialchars($application['current_position'] ?? 'N/A'); ?>
                                        </div>
                                    </td>

                                    <!-- Expected Salary -->
                                    <td class="px-4 py-4 text-sm text-gray-900">
    ₹<?php 
    $salary = $application['expected_salary'] ?? 0;
    // Convert string to float if needed
    if (is_string($salary)) {
        $salary = (float) $salary;
    }
    echo number_format($salary); 
    ?>
</td>

                                    <!-- Status -->
                                    <td class="px-4 py-4">
                                        <?php
                                        $status = strtolower($application['status'] ?? 'pending');
                                        $statusClass = 'status-' . $status;
                                        $statusText = ucfirst($status);
                                        ?>
                                        <span class="px-2 py-1 text-xs font-medium rounded-full <?php echo $statusClass; ?>">
                                            <?php echo $statusText; ?>
                                        </span>
                                    </td>

                                    <!-- Applied Date -->
                                    <td class="px-4 py-4 text-sm text-gray-900">
                                        <?php echo date('M j, Y', strtotime($application['applied_at'])); ?>
                                    </td>

                                    <!-- Actions -->
                                    <td class="px-4 py-4 text-sm font-medium">
                                        <div class="flex flex-col space-y-2">
                                            <button onclick="viewApplication(<?php echo $application['id']; ?>)" 
                                                    class="text-blue-600 hover:text-blue-900 transition-colors text-left">
                                                <i class="fas fa-eye mr-1"></i> View
                                            </button>
                                            <button onclick="openStatusModal(<?php echo $application['id']; ?>, '<?php echo $application['status'] ?? 'pending'; ?>')" 
                                                    class="text-green-600 hover:text-green-900 transition-colors text-left">
                                                <i class="fas fa-edit mr-1"></i> Status
                                            </button>
                                            <a href="job_applications.php?delete_id=<?php echo $application['id']; ?>" 
                                               class="text-red-600 hover:text-red-900 transition-colors text-left"
                                               onclick="return confirm('Are you sure you want to delete this job application? This will also remove the resume and cover letter files.')">
                                                <i class="fas fa-trash mr-1"></i> Delete
                                            </a>
                                        </div>
                                    </td>
                                </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <!-- View Application Modal -->
    <div id="applicationModal" class="modal">
        <div class="modal-content">
            <div class="flex justify-between items-center mb-4">
                <h3 class="text-lg font-medium text-gray-900">Job Application Details</h3>
                <button onclick="closeApplicationModal()" class="text-gray-400 hover:text-gray-600 transition-colors">
                    <i class="fas fa-times text-xl"></i>
                </button>
            </div>
            <div id="applicationContent" class="space-y-4">
                <!-- Application content will be loaded here -->
            </div>
            <div class="mt-6 flex justify-end">
                <button onclick="closeApplicationModal()" class="bg-gray-600 text-white px-4 py-2 rounded-lg hover:bg-gray-700 transition-colors">
                    Close
                </button>
            </div>
        </div>
    </div>

    <!-- Status Update Modal -->
    <div id="statusModal" class="modal">
        <div class="modal-content">
            <div class="flex justify-between items-center mb-4">
                <h3 class="text-lg font-medium text-gray-900">Update Application Status</h3>
                <button onclick="closeStatusModal()" class="text-gray-400 hover:text-gray-600 transition-colors">
                    <i class="fas fa-times text-xl"></i>
                </button>
            </div>
            <form id="statusForm" method="POST" class="space-y-4">
                <input type="hidden" name="update_status" value="1">
                <input type="hidden" id="application_id" name="application_id" value="">
                
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Select Status</label>
                    <select id="statusSelect" name="status" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                        <option value="pending">Pending</option>
                        <option value="reviewed">Reviewed</option>
                        <option value="interview">Interview Scheduled</option>
                        <option value="approved">Approved</option>
                        <option value="rejected">Rejected</option>
                    </select>
                </div>
                
                <div class="flex justify-end space-x-3">
                    <button type="button" onclick="closeStatusModal()" class="bg-gray-600 text-white px-4 py-2 rounded-lg hover:bg-gray-700 transition-colors">
                        Cancel
                    </button>
                    <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700 transition-colors">
                        Update Status
                    </button>
                </div>
            </form>
        </div>
    </div>

    <?php include '../includes/footer.php'; ?>

    <script>
        // Job applications data from PHP
        const jobApplications = <?php echo !empty($job_applications) ? json_encode($job_applications) : '[]'; ?>;

        console.log('Loaded applications:', jobApplications);

        function viewApplication(applicationId) {
            console.log('Viewing application ID:', applicationId);
            const application = jobApplications.find(app => app.id == applicationId);
            
            if (!application) {
                console.error('Application not found:', applicationId);
                alert('Error: Application data not found!');
                return;
            }

            const content = document.getElementById('applicationContent');
            
            let html = `
                <div class="space-y-6">
                    <!-- Application Information -->
                    <div class="info-section">
                        <h4 class="text-lg font-semibold text-gray-800 mb-3 flex items-center">
                            <i class="fas fa-id-card mr-2 text-blue-500"></i>
                            Application Information
                        </h4>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <p><strong class="text-gray-700">Application ID:</strong> ${application.id}</p>
                                <p><strong class="text-gray-700">Applied On:</strong> ${new Date(application.applied_at).toLocaleDateString('en-US', { weekday: 'long', year: 'numeric', month: 'long', day: 'numeric' })}</p>
                            </div>
                            <div>
                                <p>
                                    <strong class="text-gray-700">Status:</strong> 
                                    <span class="px-2 py-1 text-xs font-medium rounded-full status-${application.status ? application.status.toLowerCase() : 'pending'}">
                                        ${application.status ? application.status.charAt(0).toUpperCase() + application.status.slice(1) : 'Pending'}
                                    </span>
                                </p>
                            </div>
                        </div>
                    </div>

                    <!-- Personal Information -->
                    <div class="info-section">
                        <h4 class="text-lg font-semibold text-gray-800 mb-3 flex items-center">
                            <i class="fas fa-user mr-2 text-green-500"></i>
                            Personal Information
                        </h4>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <p><strong class="text-gray-700">Full Name:</strong> ${application.full_name || 'N/A'}</p>
                                <p><strong class="text-gray-700">Email:</strong> ${application.email || 'N/A'}</p>
                            </div>
                            <div>
                                ${application.phone ? `<p><strong class="text-gray-700">Phone:</strong> ${application.phone}</p>` : ''}
                            </div>
                        </div>
                    </div>

                    <!-- Position Information -->
                    <div class="info-section">
                        <h4 class="text-lg font-semibold text-gray-800 mb-3 flex items-center">
                            <i class="fas fa-briefcase mr-2 text-purple-500"></i>
                            Position Information
                        </h4>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <p><strong class="text-gray-700">Position Applied:</strong> ${application.position_applied || 'N/A'}</p>
                                <p><strong class="text-gray-700">Position Type:</strong> ${application.position_type ? application.position_type.charAt(0).toUpperCase() + application.position_type.slice(1) : 'N/A'}</p>
                            </div>
                            <div>
                                <p><strong class="text-gray-700">Experience:</strong> ${application.experience_years || '0'} years</p>
                                <p><strong class="text-gray-700">Expected Salary:</strong> ₹${application.expected_salary ? application.expected_salary.toLocaleString('en-IN') : '0'}</p>
                            </div>
                        </div>
                    </div>

                    <!-- Current Employment -->
                    <div class="info-section">
                        <h4 class="text-lg font-semibold text-gray-800 mb-3 flex items-center">
                            <i class="fas fa-building mr-2 text-orange-500"></i>
                            Current Employment
                        </h4>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <p><strong class="text-gray-700">Organization:</strong> ${application.current_organization || 'Not specified'}</p>
                            </div>
                            <div>
                                <p><strong class="text-gray-700">Position:</strong> ${application.current_position || 'Not specified'}</p>
                            </div>
                        </div>
                    </div>
            `;

            // Files section
            html += `
                <div class="info-section">
                    <h4 class="text-lg font-semibold text-gray-800 mb-3 flex items-center">
                        <i class="fas fa-paperclip mr-2 text-red-500"></i>
                        Attachments
                    </h4>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            `;

            if (application.resume_path) {
                html += `
                    <div class="border rounded-lg p-3 bg-gray-50 flex items-center justify-between">
                        <div class="flex items-center">
                            <i class="fas fa-file-pdf text-red-500 mr-3 text-xl"></i>
                            <div>
                                <div class="font-medium">Resume</div>
                                <div class="text-sm text-gray-500 truncate max-w-xs">${application.resume_path.split('/').pop()}</div>
                            </div>
                        </div>
                        <div class="flex space-x-2">
                            <a href="../${application.resume_path}" target="_blank" class="text-blue-600 hover:text-blue-800 text-sm bg-white px-2 py-1 rounded border">
                                <i class="fas fa-eye mr-1"></i>View
                            </a>
                            <a href="../${application.resume_path}" download class="text-green-600 hover:text-green-800 text-sm bg-white px-2 py-1 rounded border">
                                <i class="fas fa-download mr-1"></i>Download
                            </a>
                        </div>
                    </div>
                `;
            } else {
                html += `
                    <div class="border rounded-lg p-3 bg-gray-50">
                        <div class="flex items-center">
                            <i class="fas fa-file-pdf text-gray-400 mr-3 text-xl"></i>
                            <div>
                                <div class="font-medium text-gray-500">Resume</div>
                                <div class="text-sm text-gray-400">Not provided</div>
                            </div>
                        </div>
                    </div>
                `;
            }

            if (application.cover_letter_path) {
                html += `
                    <div class="border rounded-lg p-3 bg-gray-50 flex items-center justify-between">
                        <div class="flex items-center">
                            <i class="fas fa-file-alt text-blue-500 mr-3 text-xl"></i>
                            <div>
                                <div class="font-medium">Cover Letter</div>
                                <div class="text-sm text-gray-500 truncate max-w-xs">${application.cover_letter_path.split('/').pop()}</div>
                            </div>
                        </div>
                        <div class="flex space-x-2">
                            <a href="../${application.cover_letter_path}" target="_blank" class="text-blue-600 hover:text-blue-800 text-sm bg-white px-2 py-1 rounded border">
                                <i class="fas fa-eye mr-1"></i>View
                            </a>
                            <a href="../${application.cover_letter_path}" download class="text-green-600 hover:text-green-800 text-sm bg-white px-2 py-1 rounded border">
                                <i class="fas fa-download mr-1"></i>Download
                            </a>
                        </div>
                    </div>
                `;
            } else {
                html += `
                    <div class="border rounded-lg p-3 bg-gray-50">
                        <div class="flex items-center">
                            <i class="fas fa-file-alt text-gray-400 mr-3 text-xl"></i>
                            <div>
                                <div class="font-medium text-gray-500">Cover Letter</div>
                                <div class="text-sm text-gray-400">Not provided</div>
                            </div>
                        </div>
                    </div>
                `;
            }

            html += `</div></div>`;

            // Additional Information
            if (application.additional_info) {
                html += `
                    <div class="info-section">
                        <h4 class="text-lg font-semibold text-gray-800 mb-3 flex items-center">
                            <i class="fas fa-sticky-note mr-2 text-teal-500"></i>
                            Additional Information
                        </h4>
                        <div class="bg-white p-4 rounded-lg border border-gray-200">
                            <p class="text-gray-700 whitespace-pre-wrap">${application.additional_info || 'No additional information provided.'}</p>
                        </div>
                    </div>
                `;
            }

            html += `</div>`;
            
            content.innerHTML = html;
            document.getElementById('applicationModal').style.display = 'block';
        }

        function openStatusModal(applicationId, currentStatus) {
            document.getElementById('application_id').value = applicationId;
            document.getElementById('statusSelect').value = currentStatus.toLowerCase();
            document.getElementById('statusModal').style.display = 'block';
        }

        function closeApplicationModal() {
            document.getElementById('applicationModal').style.display = 'none';
        }

        function closeStatusModal() {
            document.getElementById('statusModal').style.display = 'none';
        }

        // Close modals when clicking outside
        window.onclick = function(event) {
            const applicationModal = document.getElementById('applicationModal');
            const statusModal = document.getElementById('statusModal');
            
            if (event.target === applicationModal) {
                closeApplicationModal();
            }
            if (event.target === statusModal) {
                closeStatusModal();
            }
        }

        // Close modals with Escape key
        document.addEventListener('keydown', function(event) {
            if (event.key === 'Escape') {
                closeApplicationModal();
                closeStatusModal();
            }
        });
    </script>
</body>
</html>