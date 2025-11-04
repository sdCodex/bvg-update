<?php
session_start();
if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    header('Location: login.php');
    exit;
}
require_once '../includes/db.php';

$base_url = '/Gurkul_Project';

// Get scholarship submissions data with error handling
try {
    // First, check if table exists and has data
    $table_check = $pdo->query("SHOW TABLES LIKE 'scholarship_submissions'")->fetch();
    
    if ($table_check) {
        // Get all columns to handle dynamic data
        $stmt = $pdo->query("SELECT * FROM scholarship_submissions ORDER BY created_at DESC");
        $scholarships = $stmt->fetchAll(PDO::FETCH_ASSOC);
    } else {
        $scholarships = [];
        $error = "Scholarship submissions table not found in database.";
    }
} catch (PDOException $e) {
    $scholarships = [];
    $error = "Database error: " . $e->getMessage();
}

// Handle deletion
if (isset($_GET['delete_id'])) {
    try {
        $stmt = $pdo->prepare("DELETE FROM scholarship_submissions WHERE id = ?");
        $stmt->execute([$_GET['delete_id']]);
        header('Location: scholarship_submissions.php?success=Scholarship request deleted successfully');
        exit;
    } catch (PDOException $e) {
        header('Location: scholarship_submissions.php?error=Error deleting scholarship request: ' . $e->getMessage());
        exit;
    }
}

// Debug function to see actual data structure
function debugScholarshipData($scholarships) {
    if (!empty($scholarships)) {
        error_log("Scholarship Data Structure: " . print_r($scholarships[0], true));
    }
}

// Call debug function
debugScholarshipData($scholarships);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Scholarship Submissions - Bhaktivedanta Gurukul</title>
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
            max-width: 600px;
            max-height: 80vh;
            overflow-y: auto;
        }
    </style>
</head>
<body class="bg-gray-100">
    <?php 
    $current_page = 'scholarship_submissions.php';
    $current_directory = 'admin';
    include '../includes/header.php'; 
    ?>

    <div class="container mx-auto px-4 py-8 mt-16">
        <div class="max-w-7xl mx-auto">
            <div class="flex justify-between items-center mb-6">
                <div>
                    <h1 class="text-3xl font-bold text-gray-800 mb-2">Scholarship Requests</h1>
                    <p class="text-gray-600">Manage all scholarship applications</p>
                </div>
                <div class="bg-teal-100 text-teal-800 px-4 py-2 rounded-lg">
                    <i class="fas fa-graduation-cap mr-2"></i>
                    Total: <?php echo count($scholarships); ?>
                </div>
            </div>

            <?php if (isset($_GET['success'])): ?>
                <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-6">
                    <i class="fas fa-check-circle mr-2"></i><?php echo htmlspecialchars($_GET['success']); ?>
                </div>
            <?php endif; ?>

            <?php if (isset($_GET['error'])): ?>
                <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-6">
                    <i class="fas fa-exclamation-circle mr-2"></i><?php echo htmlspecialchars($_GET['error']); ?>
                </div>
            <?php endif; ?>

            <?php if (isset($error)): ?>
                <div class="bg-yellow-100 border border-yellow-400 text-yellow-700 px-4 py-3 rounded mb-6">
                    <i class="fas fa-exclamation-triangle mr-2"></i><?php echo htmlspecialchars($error); ?>
                </div>
            <?php endif; ?>

            <div class="bg-white rounded-lg shadow-md overflow-hidden">
                <?php if (empty($scholarships)): ?>
                    <div class="text-center py-12">
                        <i class="fas fa-graduation-cap text-gray-300 text-6xl mb-4"></i>
                        <h3 class="text-lg font-medium text-gray-900 mb-2">No Scholarship Requests</h3>
                        <p class="text-gray-500 mb-4">Scholarship applications will appear here when submitted.</p>
                        <div class="text-sm text-gray-400">
                            <p>If you expect to see data, check:</p>
                            <ul class="mt-2 space-y-1">
                                <li>• Database table 'scholarship_submissions' exists</li>
                                <li>• Scholarship form is working properly</li>
                                <li>• Data is being inserted correctly</li>
                            </ul>
                        </div>
                    </div>
                <?php else: ?>
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Student Info</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Academic Details</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Contact Info</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Additional Info</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Date</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                <?php foreach ($scholarships as $scholarship): ?>
                                <tr class="hover:bg-gray-50 transition-colors">
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="text-sm font-medium text-gray-900">
                                            <?php echo htmlspecialchars($scholarship['student_name'] ?? $scholarship['full_name'] ?? 'N/A'); ?>
                                        </div>
                                        <div class="text-sm text-gray-500">
                                            Parent: <?php echo htmlspecialchars($scholarship['parent_name'] ?? $scholarship['parent_full_name'] ?? 'N/A'); ?>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="text-sm text-gray-900">
                                            Grade: <?php echo htmlspecialchars($scholarship['grade_applying'] ?? $scholarship['applied_grade'] ?? $scholarship['grade'] ?? 'N/A'); ?>
                                        </div>
                                        <div class="text-sm text-gray-500">
                                            Marks: <?php echo htmlspecialchars($scholarship['previous_marks'] ?? $scholarship['marks'] ?? $scholarship['academic_performance'] ?? 'N/A'); ?>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="text-sm text-gray-900">
                                            <?php echo htmlspecialchars($scholarship['email'] ?? 'N/A'); ?>
                                        </div>
                                        <div class="text-sm text-gray-500">
                                            <i class="fas fa-phone mr-1"></i>
                                            <?php echo htmlspecialchars($scholarship['phone'] ?? $scholarship['phone_number'] ?? $scholarship['contact_number'] ?? 'N/A'); ?>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                        <?php echo htmlspecialchars($scholarship['current_school'] ?? $scholarship['school_name'] ?? 'N/A'); ?>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                        <?php echo date('M j, Y', strtotime($scholarship['created_at'] ?? $scholarship['submission_date'] ?? 'N/A')); ?>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                        <button onclick="viewScholarship(<?php echo $scholarship['id']; ?>)" 
                                                class="text-blue-600 hover:text-blue-900 mr-3 transition-colors">
                                            <i class="fas fa-eye mr-1"></i> View
                                        </button>
                                        <a href="scholarship_submissions.php?delete_id=<?php echo $scholarship['id']; ?>" 
                                           class="text-red-600 hover:text-red-900 transition-colors"
                                           onclick="return confirm('Are you sure you want to delete this scholarship request? This action cannot be undone.')">
                                            <i class="fas fa-trash mr-1"></i> Delete
                                        </a>
                                    </td>
                                </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                <?php endif; ?>
            </div>

            <!-- Debug Info (Remove in production) -->
            <?php if (!empty($scholarships) && isset($_GET['debug'])): ?>
            <div class="mt-8 bg-yellow-50 border border-yellow-200 rounded-lg p-4">
                <h3 class="text-lg font-semibold text-yellow-800 mb-2">Debug Information</h3>
                <pre class="text-sm text-yellow-700 overflow-auto"><?php 
                    echo "First scholarship record:\n";
                    print_r($scholarships[0]);
                    echo "\n\nAll column names:\n";
                    if (!empty($scholarships)) {
                        print_r(array_keys($scholarships[0]));
                    }
                ?></pre>
            </div>
            <?php endif; ?>
        </div>
    </div>

    <!-- View Scholarship Modal -->
    <div id="scholarshipModal" class="modal">
        <div class="modal-content">
            <div class="flex justify-between items-center mb-4">
                <h3 class="text-lg font-medium text-gray-900">Scholarship Application Details</h3>
                <button onclick="closeScholarshipModal()" class="text-gray-400 hover:text-gray-600 transition-colors">
                    <i class="fas fa-times text-xl"></i>
                </button>
            </div>
            <div id="scholarshipContent" class="space-y-4">
                <!-- Scholarship content will be loaded here -->
            </div>
            <div class="mt-6 flex justify-end">
                <button onclick="closeScholarshipModal()" class="bg-gray-600 text-white px-4 py-2 rounded-lg hover:bg-gray-700 transition-colors">
                    Close
                </button>
            </div>
        </div>
    </div>

    <?php include '../includes/footer.php'; ?>

    <script>
        // Scholarship data from PHP
        const scholarshipData = <?php echo json_encode($scholarships); ?>;

        function viewScholarship(scholarshipId) {
            const scholarship = scholarshipData.find(s => s.id == scholarshipId);
            if (scholarship) {
                const content = document.getElementById('scholarshipContent');
                
                // Get all available fields dynamically
                const fields = [
                    { label: 'Student Name', key: 'student_name', fallback: ['full_name'] },
                    { label: 'Parent Name', key: 'parent_name', fallback: ['parent_full_name'] },
                    { label: 'Grade Applying', key: 'grade_applying', fallback: ['applied_grade', 'grade'] },
                    { label: 'Previous Marks/Percentage', key: 'previous_marks', fallback: ['marks', 'academic_performance'] },
                    { label: 'Email', key: 'email' },
                    { label: 'Phone', key: 'phone', fallback: ['phone_number', 'contact_number'] },
                    { label: 'Current School', key: 'current_school', fallback: ['school_name'] },
                    { label: 'Address', key: 'address' },
                    { label: 'City', key: 'city' },
                    { label: 'State', key: 'state' },
                    { label: 'Pincode', key: 'pincode' }
                ];

                let html = `
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 text-sm">
                        <div>
                            <h4 class="font-semibold text-gray-800 mb-3 text-blue-600">Student & Contact Information</h4>
                `;

                // Basic info fields
                fields.forEach(field => {
                    const value = getFieldValue(scholarship, field.key, field.fallback);
                    if (value && value !== 'N/A') {
                        html += `<p class="mb-2"><strong class="text-gray-700">${field.label}:</strong> <span class="text-gray-900">${value}</span></p>`;
                    }
                });

                html += `</div><div>`;
                
                // Additional fields
                const additionalFields = [
                    { label: 'Date of Birth', key: 'date_of_birth' },
                    { label: 'Gender', key: 'gender' },
                    { label: 'Category', key: 'category' },
                    { label: 'Annual Income', key: 'annual_income' },
                    { label: 'Father Occupation', key: 'father_occupation' },
                    { label: 'Mother Occupation', key: 'mother_occupation' }
                ];

                html += `<h4 class="font-semibold text-gray-800 mb-3 text-green-600">Additional Information</h4>`;
                
                let hasAdditionalInfo = false;
                additionalFields.forEach(field => {
                    const value = getFieldValue(scholarship, field.key);
                    if (value && value !== 'N/A') {
                        html += `<p class="mb-2"><strong class="text-gray-700">${field.label}:</strong> <span class="text-gray-900">${value}</span></p>`;
                        hasAdditionalInfo = true;
                    }
                });

                if (!hasAdditionalInfo) {
                    html += `<p class="text-gray-500 italic">No additional information provided</p>`;
                }

                html += `</div>`;

                // Text areas (reason, achievements, etc.)
                const textAreas = [
                    { label: 'Reason for Scholarship', key: 'reason' },
                    { label: 'Achievements', key: 'achievements' },
                    { label: 'Extra Curricular Activities', key: 'extra_curricular' },
                    { label: 'Additional Information', key: 'additional_info' }
                ];

                textAreas.forEach(field => {
                    const value = getFieldValue(scholarship, field.key);
                    if (value && value !== 'N/A' && value.trim() !== '') {
                        html += `
                            <div class="md:col-span-2">
                                <h4 class="font-semibold text-gray-800 mb-2 text-purple-600">${field.label}</h4>
                                <div class="bg-gray-50 p-4 rounded-lg border border-gray-200">
                                    <p class="text-gray-700 whitespace-pre-wrap">${value}</p>
                                </div>
                            </div>
                        `;
                    }
                });

                html += `</div>`;
                
                content.innerHTML = html;
                document.getElementById('scholarshipModal').style.display = 'block';
            }
        }

        // Helper function to get field value with fallbacks
        function getFieldValue(data, primaryKey, fallbackKeys = []) {
            if (data[primaryKey] && data[primaryKey] !== '') {
                return data[primaryKey];
            }
            
            for (const key of fallbackKeys) {
                if (data[key] && data[key] !== '') {
                    return data[key];
                }
            }
            
            return 'N/A';
        }

        function closeScholarshipModal() {
            document.getElementById('scholarshipModal').style.display = 'none';
        }

        // Close modal when clicking outside
        window.onclick = function(event) {
            const modal = document.getElementById('scholarshipModal');
            if (event.target === modal) {
                closeScholarshipModal();
            }
        }

        // Close modal with Escape key
        document.addEventListener('keydown', function(event) {
            if (event.key === 'Escape') {
                closeScholarshipModal();
            }
        });
    </script>
</body>
</html>