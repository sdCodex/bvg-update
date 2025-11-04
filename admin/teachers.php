<?php
session_start();
if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    header('Location: login.php');
    exit;
}
require_once '../includes/db.php';

$base_url = '/work/Gurkul_Project';

// Handle form submissions
$action = $_GET['action'] ?? '';
$id = $_GET['id'] ?? '';

// Initialize variables
$teacher = [];
$errors = [];

// Get teacher data for editing
if ($action === 'edit' && !empty($id)) {
    try {
        $stmt = $pdo->prepare("SELECT * FROM teachers WHERE id = ?");
        $stmt->execute([$id]);
        $teacher = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if (!$teacher) {
            $errors[] = "Teacher not found.";
            $action = 'add'; // Fallback to add mode
        }
    } catch (PDOException $e) {
        $errors[] = "Error fetching teacher data: " . $e->getMessage();
        $action = 'add'; // Fallback to add mode
    }
}

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Sanitize and validate input
    $employee_id = trim($_POST['employee_id'] ?? '');
    $full_name = trim($_POST['full_name'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $phone = trim($_POST['phone'] ?? '');
    $qualification = trim($_POST['qualification'] ?? '');
    $subject_specialization = trim($_POST['subject_specialization'] ?? '');
    $experience_years = trim($_POST['experience_years'] ?? '');
    $joining_date = trim($_POST['joining_date'] ?? '');
    $salary = trim($_POST['salary'] ?? '');
    $address = trim($_POST['address'] ?? '');
    $status = $_POST['status'] ?? 'Active';
    
    // Validation
    if (empty($employee_id)) {
        $errors[] = "Employee ID is required.";
    }
    
    if (empty($full_name)) {
        $errors[] = "Full name is required.";
    }
    
    if (empty($email)) {
        $errors[] = "Email is required.";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = "Invalid email format.";
    }
    
    // If no errors, save to database
    if (empty($errors)) {
        try {
            if ($_POST['form_action'] === 'add') {
                // Add new teacher
                $stmt = $pdo->prepare("INSERT INTO teachers (employee_id, full_name, email, phone, qualification, subject_specialization, experience_years, joining_date, salary, address, status) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
                $stmt->execute([$employee_id, $full_name, $email, $phone, $qualification, $subject_specialization, $experience_years, $joining_date, $salary, $address, $status]);
                
                $_SESSION['success_message'] = "Teacher added successfully!";
                header('Location: teachers.php');
                exit;
            } elseif ($_POST['form_action'] === 'edit') {
                // Update existing teacher
                $id = $_POST['id'];
                $stmt = $pdo->prepare("UPDATE teachers SET employee_id = ?, full_name = ?, email = ?, phone = ?, qualification = ?, subject_specialization = ?, experience_years = ?, joining_date = ?, salary = ?, address = ?, status = ? WHERE id = ?");
                $stmt->execute([$employee_id, $full_name, $email, $phone, $qualification, $subject_specialization, $experience_years, $joining_date, $salary, $address, $status, $id]);
                
                $_SESSION['success_message'] = "Teacher updated successfully!";
                header('Location: teachers.php');
                exit;
            }
        } catch (PDOException $e) {
            $errors[] = "Database error: " . $e->getMessage();
        }
    }
}

// Get teachers data for listing
try {
    $stmt = $pdo->query("SELECT * FROM teachers ORDER BY created_at DESC");
    $teachers = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    $teachers = [];
    $errors[] = "Error fetching teachers: " . $e->getMessage();
}

// Handle delete action
if ($action === 'delete' && !empty($id)) {
    try {
        $stmt = $pdo->prepare("DELETE FROM teachers WHERE id = ?");
        $stmt->execute([$id]);
        
        $_SESSION['success_message'] = "Teacher deleted successfully!";
        header('Location: teachers.php');
        exit;
    } catch (PDOException $e) {
        $errors[] = "Error deleting teacher: " . $e->getMessage();
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Teachers - Bhaktivedanta Gurukul</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="icon" type="image/x-icon" href="../images/bvgLogo.png">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>
<body class="bg-gray-100">
    <?php 
    $current_page = 'teachers.php';
    $current_directory = 'admin';
    include '../includes/header.php'; 
    ?>

    <div class="container mx-auto px-4 py-8 mt-16">
        <div class="max-w-7xl mx-auto">
            <!-- Success/Error Messages -->
            <?php if (isset($_SESSION['success_message'])): ?>
                <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4">
                    <?php 
                    echo $_SESSION['success_message']; 
                    unset($_SESSION['success_message']);
                    ?>
                </div>
            <?php endif; ?>
            
            <?php if (!empty($errors)): ?>
                <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4">
                    <ul>
                        <?php foreach ($errors as $error): ?>
                            <li><?php echo $error; ?></li>
                        <?php endforeach; ?>
                    </ul>
                </div>
            <?php endif; ?>
            
            <div class="flex justify-between items-center mb-6">
                <div>
                    <h1 class="text-3xl font-bold text-gray-800 mb-2">
                        <?php echo $action === 'add' ? 'Add New Teacher' : ($action === 'edit' ? 'Edit Teacher' : 'Teachers Management'); ?>
                    </h1>
                    <p class="text-gray-600">
                        <?php echo $action === 'add' ? 'Add a new teacher to the system' : ($action === 'edit' ? 'Update teacher information' : 'Manage all teaching staff'); ?>
                    </p>
                </div>
                <div class="flex space-x-4">
                    <?php if ($action === 'add' || $action === 'edit'): ?>
                        <a href="teachers.php" class="bg-gray-500 hover:bg-gray-600 text-white px-4 py-2 rounded-lg flex items-center">
                            <i class="fas fa-arrow-left mr-2"></i> Back to List
                        </a>
                    <?php else: ?>
                        <div class="bg-green-100 text-green-800 px-4 py-2 rounded-lg">
                            <i class="fas fa-chalkboard-teacher mr-2"></i>
                            Total: <?php echo count($teachers); ?>
                        </div>
                        <a href="teachers.php?action=add" class="bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-lg flex items-center">
                            <i class="fas fa-user-plus mr-2"></i> Add Teacher
                        </a>
                    <?php endif; ?>
                </div>
            </div>

            <?php if ($action === 'add' || $action === 'edit'): ?>
                <!-- Add/Edit Form -->
                <div class="bg-white rounded-lg shadow-md p-6 mb-8">
                    <form method="POST" action="teachers.php">
                        <input type="hidden" name="form_action" value="<?php echo $action; ?>">
                        <?php if ($action === 'edit'): ?>
                            <input type="hidden" name="id" value="<?php echo $teacher['id']; ?>">
                        <?php endif; ?>
                        
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <label for="employee_id" class="block text-sm font-medium text-gray-700 mb-1">Employee ID *</label>
                                <input type="text" id="employee_id" name="employee_id" value="<?php echo htmlspecialchars($teacher['employee_id'] ?? ''); ?>" 
                                       class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-green-500" required>
                            </div>
                            
                            <div>
                                <label for="full_name" class="block text-sm font-medium text-gray-700 mb-1">Full Name *</label>
                                <input type="text" id="full_name" name="full_name" value="<?php echo htmlspecialchars($teacher['full_name'] ?? ''); ?>" 
                                       class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-green-500" required>
                            </div>
                            
                            <div>
                                <label for="email" class="block text-sm font-medium text-gray-700 mb-1">Email *</label>
                                <input type="email" id="email" name="email" value="<?php echo htmlspecialchars($teacher['email'] ?? ''); ?>" 
                                       class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-green-500" required>
                            </div>
                            
                            <div>
                                <label for="phone" class="block text-sm font-medium text-gray-700 mb-1">Phone</label>
                                <input type="text" id="phone" name="phone" value="<?php echo htmlspecialchars($teacher['phone'] ?? ''); ?>" 
                                       class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-green-500">
                            </div>
                            
                            <div>
                                <label for="qualification" class="block text-sm font-medium text-gray-700 mb-1">Qualification</label>
                                <input type="text" id="qualification" name="qualification" value="<?php echo htmlspecialchars($teacher['qualification'] ?? ''); ?>" 
                                       class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-green-500">
                            </div>
                            
                            <div>
                                <label for="subject_specialization" class="block text-sm font-medium text-gray-700 mb-1">Subject Specialization</label>
                                <input type="text" id="subject_specialization" name="subject_specialization" value="<?php echo htmlspecialchars($teacher['subject_specialization'] ?? ''); ?>" 
                                       class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-green-500">
                            </div>
                            
                            <div>
                                <label for="experience_years" class="block text-sm font-medium text-gray-700 mb-1">Experience (Years)</label>
                                <input type="number" id="experience_years" name="experience_years" value="<?php echo htmlspecialchars($teacher['experience_years'] ?? ''); ?>" 
                                       class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-green-500" min="0" step="0.1">
                            </div>
                            
                            <div>
                                <label for="joining_date" class="block text-sm font-medium text-gray-700 mb-1">Joining Date</label>
                                <input type="date" id="joining_date" name="joining_date" value="<?php echo htmlspecialchars($teacher['joining_date'] ?? ''); ?>" 
                                       class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-green-500">
                            </div>
                            
                            <div>
                                <label for="salary" class="block text-sm font-medium text-gray-700 mb-1">Salary</label>
                                <input type="number" id="salary" name="salary" value="<?php echo htmlspecialchars($teacher['salary'] ?? ''); ?>" 
                                       class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-green-500" min="0" step="0.01">
                            </div>
                            
                            <div>
                                <label for="status" class="block text-sm font-medium text-gray-700 mb-1">Status</label>
                                <select id="status" name="status" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-green-500">
                                    <option value="Active" <?php echo ($teacher['status'] ?? 'Active') === 'Active' ? 'selected' : ''; ?>>Active</option>
                                    <option value="Inactive" <?php echo ($teacher['status'] ?? 'Active') === 'Inactive' ? 'selected' : ''; ?>>Inactive</option>
                                </select>
                            </div>
                            
                            <div class="md:col-span-2">
                                <label for="address" class="block text-sm font-medium text-gray-700 mb-1">Address</label>
                                <textarea id="address" name="address" rows="3" 
                                          class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-green-500"><?php echo htmlspecialchars($teacher['address'] ?? ''); ?></textarea>
                            </div>
                        </div>
                        
                        <div class="mt-6 flex justify-end space-x-4">
                            <a href="teachers.php" class="bg-gray-500 hover:bg-gray-600 text-white px-4 py-2 rounded-lg">Cancel</a>
                            <button type="submit" class="bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-lg">
                                <?php echo $action === 'add' ? 'Add Teacher' : 'Update Teacher'; ?>
                            </button>
                        </div>
                    </form>
                </div>
            <?php else: ?>
                <!-- Teachers List -->
                <div class="bg-white rounded-lg shadow-md overflow-hidden">
                    <?php if (empty($teachers)): ?>
                        <div class="text-center py-12">
                            <i class="fas fa-chalkboard-teacher text-gray-300 text-6xl mb-4"></i>
                            <h3 class="text-lg font-medium text-gray-900 mb-2">No Teachers Found</h3>
                            <p class="text-gray-500">Teacher records will appear here when added.</p>
                            <a href="teachers.php?action=add" class="inline-flex items-center px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 mt-4">
                                <i class="fas fa-user-plus mr-2"></i>Add First Teacher
                            </a>
                        </div>
                    <?php else: ?>
                        <div class="overflow-x-auto">
                            <table class="min-w-full divide-y divide-gray-200">
                                <thead class="bg-gray-50">
                                    <tr>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Employee Info</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Contact Info</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Qualification & Specialization</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Experience & Joining</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Salary</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white divide-y divide-gray-200">
                                    <?php foreach ($teachers as $teacher): ?>
                                    <tr class="hover:bg-gray-50">
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <div class="text-sm font-medium text-gray-900"><?php echo htmlspecialchars($teacher['full_name'] ?? 'N/A'); ?></div>
                                            <div class="text-sm text-gray-500">Emp ID: <?php echo htmlspecialchars($teacher['employee_id'] ?? 'N/A'); ?></div>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <div class="text-sm text-gray-900"><?php echo htmlspecialchars($teacher['email'] ?? 'N/A'); ?></div>
                                            <div class="text-sm text-gray-500"><?php echo htmlspecialchars($teacher['phone'] ?? 'N/A'); ?></div>
                                        </td>
                                        <td class="px-6 py-4">
                                            <div class="text-sm text-gray-900"><?php echo htmlspecialchars($teacher['qualification'] ?? 'N/A'); ?></div>
                                            <div class="text-sm text-gray-500"><?php echo htmlspecialchars($teacher['subject_specialization'] ?? 'N/A'); ?></div>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <div class="text-sm text-gray-900">Exp: <?php echo htmlspecialchars($teacher['experience_years'] ?? '0'); ?> years</div>
                                            <div class="text-sm text-gray-500">Joined: <?php echo !empty($teacher['joining_date']) ? date('d M Y', strtotime($teacher['joining_date'])) : 'N/A'; ?></div>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                            ₹<?php echo !empty($teacher['salary']) ? number_format($teacher['salary'], 2) : '0.00'; ?>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full 
                                                <?php echo ($teacher['status'] ?? 'Active') == 'Active' ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800'; ?>">
                                                <?php echo $teacher['status'] ?? 'Active'; ?>
                                            </span>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                            <a href="teachers.php?action=edit&id=<?php echo $teacher['id']; ?>" class="text-blue-600 hover:text-blue-900 mr-3">
                                                <i class="fas fa-edit"></i> Edit
                                            </a>
                                            <a href="#" class="text-green-600 hover:text-green-900 mr-3">
                                                <i class="fas fa-eye"></i> View
                                            </a>
                                            <a href="teachers.php?action=delete&id=<?php echo $teacher['id']; ?>" 
                                               class="text-red-600 hover:text-red-900" 
                                               onclick="return confirm('Are you sure you want to delete this teacher?')">
                                                <i class="fas fa-trash"></i> Delete
                                            </a>
                                        </td>
                                    </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    <?php endif; ?>
                </div>
            <?php endif; ?>
        </div>
    </div>

    <?php include '../includes/footer.php'; ?>
</body>
</html>