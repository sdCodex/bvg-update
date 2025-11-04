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
$student = [];
$errors = [];

// Get student data for editing/viewing
if (($action === 'edit' || $action === 'view') && !empty($id)) {
    try {
        $stmt = $pdo->prepare("SELECT * FROM students WHERE id = ?");
        $stmt->execute([$id]);
        $student = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if (!$student) {
            $errors[] = "Student not found.";
            $action = 'list'; // Fallback to list mode
        }
    } catch (PDOException $e) {
        $errors[] = "Error fetching student data: " . $e->getMessage();
        $action = 'list'; // Fallback to list mode
    }
}

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Sanitize and validate input
    $admission_number = trim($_POST['admission_number'] ?? '');
    $full_name = trim($_POST['full_name'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $phone = trim($_POST['phone'] ?? '');
    $father_name = trim($_POST['father_name'] ?? '');
    $mother_name = trim($_POST['mother_name'] ?? '');
    $date_of_birth = trim($_POST['date_of_birth'] ?? '');
    $gender = trim($_POST['gender'] ?? '');
    $address = trim($_POST['address'] ?? '');
    $class = trim($_POST['class'] ?? '');
    $section = trim($_POST['section'] ?? '');
    $admission_date = trim($_POST['admission_date'] ?? '');
    $status = $_POST['status'] ?? 'Active';
    
    // Validation
    if (empty($admission_number)) {
        $errors[] = "Admission number is required.";
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
                // Add new student
                $stmt = $pdo->prepare("INSERT INTO students (admission_number, full_name, email, phone, father_name, mother_name, date_of_birth, gender, address, class, section, admission_date, status) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
                $stmt->execute([$admission_number, $full_name, $email, $phone, $father_name, $mother_name, $date_of_birth, $gender, $address, $class, $section, $admission_date, $status]);
                
                $_SESSION['success_message'] = "Student added successfully!";
                header('Location: students.php');
                exit;
            } elseif ($_POST['form_action'] === 'edit') {
                // Update existing student
                $id = $_POST['id'];
                $stmt = $pdo->prepare("UPDATE students SET admission_number = ?, full_name = ?, email = ?, phone = ?, father_name = ?, mother_name = ?, date_of_birth = ?, gender = ?, address = ?, class = ?, section = ?, admission_date = ?, status = ? WHERE id = ?");
                $stmt->execute([$admission_number, $full_name, $email, $phone, $father_name, $mother_name, $date_of_birth, $gender, $address, $class, $section, $admission_date, $status, $id]);
                
                $_SESSION['success_message'] = "Student updated successfully!";
                header('Location: students.php');
                exit;
            }
        } catch (PDOException $e) {
            $errors[] = "Database error: " . $e->getMessage();
        }
    }
}

// Get students data for listing
try {
    $stmt = $pdo->query("SELECT * FROM students ORDER BY created_at DESC");
    $students = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    $students = [];
    $errors[] = "Error fetching students: " . $e->getMessage();
}

// Handle delete action
if ($action === 'delete' && !empty($id)) {
    try {
        $stmt = $pdo->prepare("DELETE FROM students WHERE id = ?");
        $stmt->execute([$id]);
        
        $_SESSION['success_message'] = "Student deleted successfully!";
        header('Location: students.php');
        exit;
    } catch (PDOException $e) {
        $errors[] = "Error deleting student: " . $e->getMessage();
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Students - Bhaktivedanta Gurukul</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="icon" type="image/x-icon" href="../images/bvgLogo.png">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>
<body class="bg-gray-100">
    <?php 
    $current_page = 'students.php';
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
                        <?php 
                        if ($action === 'add') echo 'Add New Student';
                        elseif ($action === 'edit') echo 'Edit Student';
                        elseif ($action === 'view') echo 'Student Details';
                        else echo 'Students Management';
                        ?>
                    </h1>
                    <p class="text-gray-600">
                        <?php 
                        if ($action === 'add') echo 'Add a new student to the system';
                        elseif ($action === 'edit') echo 'Update student information';
                        elseif ($action === 'view') echo 'View complete student details';
                        else echo 'Manage all registered students';
                        ?>
                    </p>
                </div>
                <div class="flex space-x-4">
                    <?php if ($action === 'add' || $action === 'edit' || $action === 'view'): ?>
                        <a href="students.php" class="bg-gray-500 hover:bg-gray-600 text-white px-4 py-2 rounded-lg flex items-center">
                            <i class="fas fa-arrow-left mr-2"></i> Back to List
                        </a>
                    <?php else: ?>
                        <div class="bg-blue-100 text-blue-800 px-4 py-2 rounded-lg">
                            <i class="fas fa-users mr-2"></i>
                            Total: <?php echo count($students); ?>
                        </div>
                        <a href="students.php?action=add" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg flex items-center">
                            <i class="fas fa-user-plus mr-2"></i> Add Student
                        </a>
                    <?php endif; ?>
                </div>
            </div>

            <?php if ($action === 'add' || $action === 'edit'): ?>
                <!-- Add/Edit Form -->
                <div class="bg-white rounded-lg shadow-md p-6 mb-8">
                    <form method="POST" action="students.php">
                        <input type="hidden" name="form_action" value="<?php echo $action; ?>">
                        <?php if ($action === 'edit'): ?>
                            <input type="hidden" name="id" value="<?php echo $student['id']; ?>">
                        <?php endif; ?>
                        
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <label for="admission_number" class="block text-sm font-medium text-gray-700 mb-1">Admission Number *</label>
                                <input type="text" id="admission_number" name="admission_number" value="<?php echo htmlspecialchars($student['admission_number'] ?? ''); ?>" 
                                       class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500" required>
                            </div>
                            
                            <div>
                                <label for="full_name" class="block text-sm font-medium text-gray-700 mb-1">Full Name *</label>
                                <input type="text" id="full_name" name="full_name" value="<?php echo htmlspecialchars($student['full_name'] ?? ''); ?>" 
                                       class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500" required>
                            </div>
                            
                            <div>
                                <label for="email" class="block text-sm font-medium text-gray-700 mb-1">Email *</label>
                                <input type="email" id="email" name="email" value="<?php echo htmlspecialchars($student['email'] ?? ''); ?>" 
                                       class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500" required>
                            </div>
                            
                            <div>
                                <label for="phone" class="block text-sm font-medium text-gray-700 mb-1">Phone</label>
                                <input type="text" id="phone" name="phone" value="<?php echo htmlspecialchars($student['phone'] ?? ''); ?>" 
                                       class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                            </div>
                            
                            <div>
                                <label for="father_name" class="block text-sm font-medium text-gray-700 mb-1">Father's Name</label>
                                <input type="text" id="father_name" name="father_name" value="<?php echo htmlspecialchars($student['father_name'] ?? ''); ?>" 
                                       class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                            </div>
                            
                            <div>
                                <label for="mother_name" class="block text-sm font-medium text-gray-700 mb-1">Mother's Name</label>
                                <input type="text" id="mother_name" name="mother_name" value="<?php echo htmlspecialchars($student['mother_name'] ?? ''); ?>" 
                                       class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                            </div>
                            
                            <div>
                                <label for="date_of_birth" class="block text-sm font-medium text-gray-700 mb-1">Date of Birth</label>
                                <input type="date" id="date_of_birth" name="date_of_birth" value="<?php echo htmlspecialchars($student['date_of_birth'] ?? ''); ?>" 
                                       class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                            </div>
                            
                            <div>
                                <label for="gender" class="block text-sm font-medium text-gray-700 mb-1">Gender</label>
                                <select id="gender" name="gender" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                                    <option value="">Select Gender</option>
                                    <option value="Male" <?php echo ($student['gender'] ?? '') === 'Male' ? 'selected' : ''; ?>>Male</option>
                                    <option value="Female" <?php echo ($student['gender'] ?? '') === 'Female' ? 'selected' : ''; ?>>Female</option>
                                    <option value="Other" <?php echo ($student['gender'] ?? '') === 'Other' ? 'selected' : ''; ?>>Other</option>
                                </select>
                            </div>
                            
                            <div>
                                <label for="class" class="block text-sm font-medium text-gray-700 mb-1">Class</label>
                                <select id="class" name="class" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                                    <option value="">Select Class</option>
                                    <option value="Nursery" <?php echo ($student['class'] ?? '') === 'Nursery' ? 'selected' : ''; ?>>Nursery</option>
                                    <option value="LKG" <?php echo ($student['class'] ?? '') === 'LKG' ? 'selected' : ''; ?>>LKG</option>
                                    <option value="UKG" <?php echo ($student['class'] ?? '') === 'UKG' ? 'selected' : ''; ?>>UKG</option>
                                    <option value="1" <?php echo ($student['class'] ?? '') === '1' ? 'selected' : ''; ?>>1</option>
                                    <option value="2" <?php echo ($student['class'] ?? '') === '2' ? 'selected' : ''; ?>>2</option>
                                    <option value="3" <?php echo ($student['class'] ?? '') === '3' ? 'selected' : ''; ?>>3</option>
                                    <option value="4" <?php echo ($student['class'] ?? '') === '4' ? 'selected' : ''; ?>>4</option>
                                    <option value="5" <?php echo ($student['class'] ?? '') === '5' ? 'selected' : ''; ?>>5</option>
                                    <option value="6" <?php echo ($student['class'] ?? '') === '6' ? 'selected' : ''; ?>>6</option>
                                    <option value="7" <?php echo ($student['class'] ?? '') === '7' ? 'selected' : ''; ?>>7</option>
                                    <option value="8" <?php echo ($student['class'] ?? '') === '8' ? 'selected' : ''; ?>>8</option>
                                    <option value="9" <?php echo ($student['class'] ?? '') === '9' ? 'selected' : ''; ?>>9</option>
                                    <option value="10" <?php echo ($student['class'] ?? '') === '10' ? 'selected' : ''; ?>>10</option>
                                    <option value="11" <?php echo ($student['class'] ?? '') === '11' ? 'selected' : ''; ?>>11</option>
                                    <option value="12" <?php echo ($student['class'] ?? '') === '12' ? 'selected' : ''; ?>>12</option>
                                </select>
                            </div>
                            
                            <div>
                                <label for="section" class="block text-sm font-medium text-gray-700 mb-1">Section</label>
                                <select id="section" name="section" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                                    <option value="">Select Section</option>
                                    <option value="A" <?php echo ($student['section'] ?? '') === 'A' ? 'selected' : ''; ?>>A</option>
                                    <option value="B" <?php echo ($student['section'] ?? '') === 'B' ? 'selected' : ''; ?>>B</option>
                                    <option value="C" <?php echo ($student['section'] ?? '') === 'C' ? 'selected' : ''; ?>>C</option>
                                    <option value="D" <?php echo ($student['section'] ?? '') === 'D' ? 'selected' : ''; ?>>D</option>
                                </select>
                            </div>
                            
                            <div>
                                <label for="admission_date" class="block text-sm font-medium text-gray-700 mb-1">Admission Date</label>
                                <input type="date" id="admission_date" name="admission_date" value="<?php echo htmlspecialchars($student['admission_date'] ?? ''); ?>" 
                                       class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                            </div>
                            
                            <div>
                                <label for="status" class="block text-sm font-medium text-gray-700 mb-1">Status</label>
                                <select id="status" name="status" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                                    <option value="Active" <?php echo ($student['status'] ?? 'Active') === 'Active' ? 'selected' : ''; ?>>Active</option>
                                    <option value="Inactive" <?php echo ($student['status'] ?? 'Active') === 'Inactive' ? 'selected' : ''; ?>>Inactive</option>
                                    <option value="Transferred" <?php echo ($student['status'] ?? 'Active') === 'Transferred' ? 'selected' : ''; ?>>Transferred</option>
                                </select>
                            </div>
                            
                            <div class="md:col-span-2">
                                <label for="address" class="block text-sm font-medium text-gray-700 mb-1">Address</label>
                                <textarea id="address" name="address" rows="3" 
                                          class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"><?php echo htmlspecialchars($student['address'] ?? ''); ?></textarea>
                            </div>
                        </div>
                        
                        <div class="mt-6 flex justify-end space-x-4">
                            <a href="students.php" class="bg-gray-500 hover:bg-gray-600 text-white px-4 py-2 rounded-lg">Cancel</a>
                            <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg">
                                <?php echo $action === 'add' ? 'Add Student' : 'Update Student'; ?>
                            </button>
                        </div>
                    </form>
                </div>

            <?php elseif ($action === 'view'): ?>
                <!-- View Student Details -->
                <div class="bg-white rounded-lg shadow-md p-6 mb-8">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div class="space-y-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Admission Number</label>
                                <p class="text-lg font-semibold text-gray-900"><?php echo htmlspecialchars($student['admission_number'] ?? 'N/A'); ?></p>
                            </div>
                            
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Full Name</label>
                                <p class="text-lg font-semibold text-gray-900"><?php echo htmlspecialchars($student['full_name'] ?? 'N/A'); ?></p>
                            </div>
                            
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Email</label>
                                <p class="text-gray-900"><?php echo htmlspecialchars($student['email'] ?? 'N/A'); ?></p>
                            </div>
                            
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Phone</label>
                                <p class="text-gray-900"><?php echo htmlspecialchars($student['phone'] ?? 'N/A'); ?></p>
                            </div>
                            
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Date of Birth</label>
                                <p class="text-gray-900"><?php echo !empty($student['date_of_birth']) ? date('M j, Y', strtotime($student['date_of_birth'])) : 'N/A'; ?></p>
                            </div>
                            
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Gender</label>
                                <p class="text-gray-900"><?php echo htmlspecialchars($student['gender'] ?? 'N/A'); ?></p>
                            </div>
                        </div>
                        
                        <div class="space-y-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Father's Name</label>
                                <p class="text-gray-900"><?php echo htmlspecialchars($student['father_name'] ?? 'N/A'); ?></p>
                            </div>
                            
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Mother's Name</label>
                                <p class="text-gray-900"><?php echo htmlspecialchars($student['mother_name'] ?? 'N/A'); ?></p>
                            </div>
                            
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Class & Section</label>
                                <p class="text-gray-900"><?php echo htmlspecialchars($student['class'] ?? 'N/A'); ?> - <?php echo htmlspecialchars($student['section'] ?? 'N/A'); ?></p>
                            </div>
                            
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Admission Date</label>
                                <p class="text-gray-900"><?php echo !empty($student['admission_date']) ? date('M j, Y', strtotime($student['admission_date'])) : 'N/A'; ?></p>
                            </div>
                            
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Status</label>
                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full 
                                    <?php echo ($student['status'] ?? 'Active') == 'Active' ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800'; ?>">
                                    <?php echo $student['status'] ?? 'Active'; ?>
                                </span>
                            </div>
                            
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Address</label>
                                <p class="text-gray-900"><?php echo nl2br(htmlspecialchars($student['address'] ?? 'N/A')); ?></p>
                            </div>
                        </div>
                    </div>
                    
                    <div class="mt-6 flex justify-end space-x-4">
                        <a href="students.php" class="bg-gray-500 hover:bg-gray-600 text-white px-4 py-2 rounded-lg">Back to List</a>
                        <a href="students.php?action=edit&id=<?php echo $student['id']; ?>" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg">Edit Student</a>
                    </div>
                </div>

            <?php else: ?>
                <!-- Students List -->
                <div class="bg-white rounded-lg shadow-md overflow-hidden">
                    <?php if (empty($students)): ?>
                        <div class="text-center py-12">
                            <i class="fas fa-user-graduate text-gray-300 text-6xl mb-4"></i>
                            <h3 class="text-lg font-medium text-gray-900 mb-2">No Students Found</h3>
                            <p class="text-gray-500">Student records will appear here when added.</p>
                            <a href="students.php?action=add" class="inline-flex items-center px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 mt-4">
                                <i class="fas fa-user-plus mr-2"></i>Add First Student
                            </a>
                        </div>
                    <?php else: ?>
                        <div class="overflow-x-auto">
                            <table class="min-w-full divide-y divide-gray-200">
                                <thead class="bg-gray-50">
                                    <tr>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Student Info</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Class & Section</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Parent Info</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Admission Date</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white divide-y divide-gray-200">
                                    <?php foreach ($students as $student): ?>
                                    <tr class="hover:bg-gray-50">
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <div class="text-sm font-medium text-gray-900"><?php echo htmlspecialchars($student['full_name'] ?? 'N/A'); ?></div>
                                            <div class="text-sm text-gray-500">Adm No: <?php echo htmlspecialchars($student['admission_number'] ?? 'N/A'); ?></div>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <div class="text-sm text-gray-900">Class: <?php echo htmlspecialchars($student['class'] ?? 'N/A'); ?></div>
                                            <div class="text-sm text-gray-500">Section: <?php echo htmlspecialchars($student['section'] ?? 'N/A'); ?></div>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <div class="text-sm text-gray-900"><?php echo htmlspecialchars($student['father_name'] ?? 'N/A'); ?></div>
                                            <div class="text-sm text-gray-500"><?php echo htmlspecialchars($student['mother_name'] ?? 'N/A'); ?></div>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full 
                                                <?php echo ($student['status'] ?? 'Active') == 'Active' ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800'; ?>">
                                                <?php echo $student['status'] ?? 'Active'; ?>
                                            </span>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                            <?php echo date('M j, Y', strtotime($student['admission_date'] ?? $student['created_at'])); ?>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                            <a href="students.php?action=edit&id=<?php echo $student['id']; ?>" class="text-blue-600 hover:text-blue-900 mr-3">
                                                <i class="fas fa-edit"></i> Edit
                                            </a>
                                            <a href="students.php?action=view&id=<?php echo $student['id']; ?>" class="text-green-600 hover:text-green-900 mr-3">
                                                <i class="fas fa-eye"></i> View
                                            </a>
                                            <a href="students.php?action=delete&id=<?php echo $student['id']; ?>" 
                                               class="text-red-600 hover:text-red-900" 
                                               onclick="return confirm('Are you sure you want to delete this student?')">
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