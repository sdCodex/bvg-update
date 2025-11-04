<?php
// Check if session is already started
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once '../includes/db.php';

// Check if admin is logged in
if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    header('Location: login.php');
    exit;
}

$base_url = '/Gurkul_Project';

// First, create settings table if not exists
try {
    $pdo->exec("CREATE TABLE IF NOT EXISTS settings (
        id INT PRIMARY KEY AUTO_INCREMENT,
        setting_key VARCHAR(100) UNIQUE NOT NULL,
        setting_value TEXT,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
    )");
} catch (PDOException $e) {
    error_log("Settings table creation error: " . $e->getMessage());
}

// Get current admin details with error handling
$admin_data = [];
try {
    $admin_stmt = $pdo->prepare("SELECT * FROM admins WHERE id = ?");
    $admin_stmt->execute([$_SESSION['admin_id']]);
    $admin_data = $admin_stmt->fetch(PDO::FETCH_ASSOC);
    
    // If admin not found, set default values
    if (!$admin_data) {
        $admin_data = [
            'id' => $_SESSION['admin_id'],
            'username' => 'admin',
            'full_name' => 'Administrator',
            'email' => 'admin@gurukul.edu',
            'role' => 'super_admin',
            'last_login' => null,
            'created_at' => date('Y-m-d H:i:s')
        ];
    }
} catch (PDOException $e) {
    // If error, set default admin data
    $admin_data = [
        'id' => $_SESSION['admin_id'],
        'username' => 'admin',
        'full_name' => 'Administrator',
        'email' => 'admin@gurukul.edu',
        'role' => 'super_admin',
        'last_login' => null,
        'created_at' => date('Y-m-d H:i:s')
    ];
}

// Handle form submissions
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['admin_profile'])) {
        // Update admin profile
        $full_name = $_POST['full_name'];
        $email = $_POST['email'];
        $username = $_POST['username'];
        
        try {
            $stmt = $pdo->prepare("UPDATE admins SET full_name = ?, email = ?, username = ? WHERE id = ?");
            $stmt->execute([$full_name, $email, $username, $_SESSION['admin_id']]);
            
            // Update session
            $_SESSION['admin_name'] = $full_name;
            
            $success = "Profile updated successfully!";
            
            // Refresh admin data
            $admin_stmt->execute([$_SESSION['admin_id']]);
            $admin_data = $admin_stmt->fetch(PDO::FETCH_ASSOC);
            
        } catch (PDOException $e) {
            $error = "Error updating profile: " . $e->getMessage();
        }
    }
    
    elseif (isset($_POST['admin_password'])) {
        // Change admin password
        $current_password = $_POST['current_password'];
        $new_password = $_POST['new_password'];
        $confirm_password = $_POST['confirm_password'];
        
        if ($new_password === $confirm_password) {
            try {
                // Hash the new password
                $hashed_password = password_hash($new_password, PASSWORD_DEFAULT);
                $stmt = $pdo->prepare("UPDATE admins SET password_hash = ? WHERE id = ?");
                $stmt->execute([$hashed_password, $_SESSION['admin_id']]);
                $success = "Password changed successfully!";
            } catch (PDOException $e) {
                $error = "Error changing password: " . $e->getMessage();
            }
        } else {
            $error = "New passwords do not match!";
        }
    }
    
    // Handle permission updates
    elseif (isset($_POST['update_permissions'])) {
        $admin_id = $_POST['admin_id'];
        $permissions = implode(',', $_POST['permissions'] ?? []);
        
        try {
            $stmt = $pdo->prepare("UPDATE admins SET permissions = ? WHERE id = ?");
            $stmt->execute([$permissions, $admin_id]);
            $success = "Permissions updated successfully!";
        } catch (PDOException $e) {
            $error = "Error updating permissions: " . $e->getMessage();
        }
    }
    
    // Handle updates for various data tables
    elseif (isset($_POST['update_admission_timeline'])) {
        $id = $_POST['id'];
        $event_name = $_POST['event_name'];
        $event_date = $_POST['event_date'];
        $description = $_POST['description'];
        
        try {
            $stmt = $pdo->prepare("UPDATE admission_timeline SET event_name = ?, event_date = ?, description = ? WHERE id = ?");
            $stmt->execute([$event_name, $event_date, $description, $id]);
            $success = "Admission timeline updated successfully!";
        } catch (PDOException $e) {
            $error = "Error updating admission timeline: " . $e->getMessage();
        }
    }
    
    elseif (isset($_POST['update_fee_structure'])) {
        $id = $_POST['id'];
        $grade_level = $_POST['grade_level'];
        $tuition_fee = $_POST['tuition_fee'];
        $admission_fee = $_POST['admission_fee'];
        $development_fee = $_POST['development_fee'];
        $other_charges = $_POST['other_charges'];
        $total_fee = $tuition_fee + $admission_fee + $development_fee + $other_charges;
        $academic_year = $_POST['academic_year'];
        $active = isset($_POST['active']) ? 1 : 0;
        
        try {
            $stmt = $pdo->prepare("UPDATE fee_structure SET grade_level = ?, tuition_fee = ?, admission_fee = ?, development_fee = ?, other_charges = ?, total_fee = ?, academic_year = ?, active = ? WHERE id = ?");
            $stmt->execute([$grade_level, $tuition_fee, $admission_fee, $development_fee, $other_charges, $total_fee, $academic_year, $active, $id]);
            $success = "Fee structure updated successfully!";
        } catch (PDOException $e) {
            $error = "Error updating fee structure: " . $e->getMessage();
        }
    }
    
    elseif (isset($_POST['update_hostel_fees'])) {
        $id = $_POST['id'];
        $hostel_type = $_POST['hostel_type'];
        $room_charges = $_POST['room_charges'];
        $food_charges = $_POST['food_charges'];
        $other_charges = $_POST['other_charges'];
        $total_fee = $room_charges + $food_charges + $other_charges;
        $academic_year = $_POST['academic_year'];
        $active = isset($_POST['active']) ? 1 : 0;
        
        try {
            $stmt = $pdo->prepare("UPDATE hostel_fees SET hostel_type = ?, room_charges = ?, food_charges = ?, other_charges = ?, total_fee = ?, academic_year = ?, active = ? WHERE id = ?");
            $stmt->execute([$hostel_type, $room_charges, $food_charges, $other_charges, $total_fee, $academic_year, $active, $id]);
            $success = "Hostel fees updated successfully!";
        } catch (PDOException $e) {
            $error = "Error updating hostel fees: " . $e->getMessage();
        }
    }
    
    // Add new fee structure entry
    elseif (isset($_POST['add_fee_structure'])) {
        $grade_level = $_POST['new_grade_level'];
        $tuition_fee = $_POST['new_tuition_fee'];
        $admission_fee = $_POST['new_admission_fee'];
        $development_fee = $_POST['new_development_fee'];
        $other_charges = $_POST['new_other_charges'];
        $total_fee = $tuition_fee + $admission_fee + $development_fee + $other_charges;
        $academic_year = $_POST['new_academic_year'];
        $active = isset($_POST['new_active']) ? 1 : 0;
        
        try {
            $stmt = $pdo->prepare("INSERT INTO fee_structure (grade_level, tuition_fee, admission_fee, development_fee, other_charges, total_fee, academic_year, active) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
            $stmt->execute([$grade_level, $tuition_fee, $admission_fee, $development_fee, $other_charges, $total_fee, $academic_year, $active]);
            $success = "New fee structure added successfully!";
        } catch (PDOException $e) {
            $error = "Error adding fee structure: " . $e->getMessage();
        }
    }
    
    // Add new hostel fee entry
    elseif (isset($_POST['add_hostel_fees'])) {
        $hostel_type = $_POST['new_hostel_type'];
        $room_charges = $_POST['new_room_charges'];
        $food_charges = $_POST['new_food_charges'];
        $other_charges = $_POST['new_other_charges'];
        $total_fee = $room_charges + $food_charges + $other_charges;
        $academic_year = $_POST['new_academic_year'];
        $active = isset($_POST['new_active']) ? 1 : 0;
        
        try {
            $stmt = $pdo->prepare("INSERT INTO hostel_fees (hostel_type, room_charges, food_charges, other_charges, total_fee, academic_year, active) VALUES (?, ?, ?, ?, ?, ?, ?)");
            $stmt->execute([$hostel_type, $room_charges, $food_charges, $other_charges, $total_fee, $academic_year, $active]);
            $success = "New hostel fee structure added successfully!";
        } catch (PDOException $e) {
            $error = "Error adding hostel fee structure: " . $e->getMessage();
        }
    }
}

// Get all admins for permission management
$all_admins = [];
try {
    $stmt = $pdo->query("SELECT id, username, full_name, email, role, permissions FROM admins");
    $all_admins = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    $all_admins = [$admin_data];
}

// Available modules for permissions
$modules = [
    'blog' => 'Blog Management',
    'downloads' => 'Downloads Management',
    'question_papers' => 'Question Papers',
    'inspiration' => 'Our Inspiration',
    'students' => 'Students Management',
    'teachers' => 'Teachers Management',
    'applications' => 'Applications',
    'settings' => 'Settings'
];

// Get data from various tables for display and editing
$admission_timeline = [];
$fee_structure = [];
$hostel_fees = [];
$admission_inquiries = [];
$contact_messages = [];
$job_applications = [];
$scholarship_submissions = [];

try {
    // Admission Timeline
    $stmt = $pdo->query("SELECT * FROM admission_timeline ORDER BY event_date");
    $admission_timeline = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    // Fee Structure
    $stmt = $pdo->query("SELECT * FROM fee_structure ORDER BY grade_level");
    $fee_structure = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    // Hostel Fees
    $stmt = $pdo->query("SELECT * FROM hostel_fees ORDER BY hostel_type");
    $hostel_fees = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    // Admission Inquiries
    $stmt = $pdo->query("SELECT * FROM admission_inquiries ORDER BY created_at DESC LIMIT 10");
    $admission_inquiries = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    // Contact Messages
    $stmt = $pdo->query("SELECT * FROM contact_messages ORDER BY created_at DESC LIMIT 10");
    $contact_messages = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    // Job Applications
    $stmt = $pdo->query("SELECT * FROM job_applications ORDER BY applied_at DESC LIMIT 10");
    $job_applications = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    // Scholarship Submissions
    $stmt = $pdo->query("SELECT * FROM scholarship_submissions ORDER BY created_at DESC LIMIT 10");
    $scholarship_submissions = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
} catch (PDOException $e) {
    // If tables don't exist, we'll handle it gracefully
    error_log("Database error in settings: " . $e->getMessage());
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Settings - Bhaktivedanta Gurukul</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="icon" type="image/x-icon" href="../images/bvgLogo.png">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        .tab-content {
            display: none;
        }
        .tab-content.active {
            display: block;
        }
        .permission-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 10px;
            margin: 15px 0;
        }
        .permission-item {
            display: flex;
            align-items: center;
            gap: 8px;
            padding: 8px;
            background: #f8fafc;
            border-radius: 6px;
        }
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
    $current_page = 'settings.php';
    $current_directory = 'admin';
    include '../includes/header.php'; 
    ?>

    <div class="container mx-auto px-4 py-8 mt-16">
        <div class="max-w-7xl mx-auto">
            <h1 class="text-3xl font-bold text-gray-800 mb-8">Admin Settings & Dashboard</h1>

            <!-- Success/Error Messages -->
            <?php if (isset($success)): ?>
                <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-6">
                    <i class="fas fa-check-circle mr-2"></i><?php echo $success; ?>
                </div>
            <?php endif; ?>
            
            <?php if (isset($error)): ?>
                <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-6">
                    <i class="fas fa-exclamation-circle mr-2"></i><?php echo $error; ?>
                </div>
            <?php endif; ?>

            <!-- Quick Stats -->
            <div class="grid grid-cols-2 md:grid-cols-4 gap-6 mb-8">
                <div class="bg-white rounded-lg shadow-sm p-6 text-center">
                    <div class="text-2xl font-bold text-blue-600 mb-2">
                        <?php 
                        try {
                            echo $pdo->query("SELECT COUNT(*) FROM students")->fetchColumn();
                        } catch (Exception $e) {
                            echo "0";
                        }
                        ?>
                    </div>
                    <div class="text-gray-600 font-medium">Total Students</div>
                </div>
                <div class="bg-white rounded-lg shadow-sm p-6 text-center">
                    <div class="text-2xl font-bold text-green-600 mb-2">
                        <?php 
                        try {
                            echo $pdo->query("SELECT COUNT(*) FROM teachers")->fetchColumn();
                        } catch (Exception $e) {
                            echo "0";
                        }
                        ?>
                    </div>
                    <div class="text-gray-600 font-medium">Teachers</div>
                </div>
                <div class="bg-white rounded-lg shadow-sm p-6 text-center">
                    <div class="text-2xl font-bold text-purple-600 mb-2">
                        <?php echo count($admission_inquiries); ?>
                    </div>
                    <div class="text-gray-600 font-medium">Admission Inquiries</div>
                </div>
                <div class="bg-white rounded-lg shadow-sm p-6 text-center">
                    <div class="text-2xl font-bold text-orange-600 mb-2">
                        <?php echo count($all_admins); ?>
                    </div>
                    <div class="text-gray-600 font-medium">Admins</div>
                </div>
            </div>

            <!-- Tabs Navigation -->
            <div class="bg-white rounded-lg shadow-sm mb-6">
                <div class="border-b">
                    <nav class="flex -mb-px overflow-x-auto">
                        <button class="tab-btn py-4 px-6 text-center border-b-2 font-medium text-sm whitespace-nowrap active" 
                                data-tab="profile">
                            <i class="fas fa-user-cog mr-2"></i>Profile
                        </button>
                        <button class="tab-btn py-4 px-6 text-center border-b-2 font-medium text-sm whitespace-nowrap" 
                                data-tab="permissions">
                            <i class="fas fa-shield-alt mr-2"></i>Permissions
                        </button>
                        <button class="tab-btn py-4 px-6 text-center border-b-2 font-medium text-sm whitespace-nowrap" 
                                data-tab="admissions">
                            <i class="fas fa-graduation-cap mr-2"></i>Admissions
                        </button>
                        <button class="tab-btn py-4 px-6 text-center border-b-2 font-medium text-sm whitespace-nowrap" 
                                data-tab="fees">
                            <i class="fas fa-money-bill-wave mr-2"></i>Fee Management
                        </button>
                        <button class="tab-btn py-4 px-6 text-center border-b-2 font-medium text-sm whitespace-nowrap" 
                                data-tab="system">
                            <i class="fas fa-cog mr-2"></i>System Info
                        </button>
                    </nav>
                </div>
            </div>

            <!-- Profile Tab -->
            <div class="tab-content active" id="profile-tab">
                <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
                    <!-- Admin Profile -->
                    <div class="bg-white rounded-lg shadow-md p-6">
                        <h2 class="text-xl font-bold text-gray-800 mb-4 flex items-center">
                            <i class="fas fa-user-cog mr-2 text-purple-500"></i> Admin Profile
                        </h2>
                        <form method="POST">
                            <input type="hidden" name="admin_profile" value="1">
                            <div class="space-y-4">
                                <div>
                                    <label class="block text-gray-700 font-semibold mb-2">Full Name *</label>
                                    <input type="text" name="full_name" required 
                                           value="<?php echo htmlspecialchars($admin_data['full_name'] ?? 'Administrator'); ?>"
                                           class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-purple-500">
                                </div>
                                <div>
                                    <label class="block text-gray-700 font-semibold mb-2">Username *</label>
                                    <input type="text" name="username" required 
                                           value="<?php echo htmlspecialchars($admin_data['username'] ?? 'admin'); ?>"
                                           class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-purple-500">
                                </div>
                                <div>
                                    <label class="block text-gray-700 font-semibold mb-2">Email *</label>
                                    <input type="email" name="email" required 
                                           value="<?php echo htmlspecialchars($admin_data['email'] ?? 'admin@gurukul.edu'); ?>"
                                           class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-purple-500">
                                </div>
                                <div class="bg-gray-50 p-3 rounded-lg">
                                    <p class="text-sm text-gray-600">
                                        <strong>Role:</strong> <?php echo htmlspecialchars($admin_data['role'] ?? 'super_admin'); ?><br>
                                        <strong>Last Login:</strong> <?php echo isset($admin_data['last_login']) && $admin_data['last_login'] ? date('M j, Y g:i A', strtotime($admin_data['last_login'])) : 'Never'; ?>
                                    </p>
                                </div>
                                <button type="submit" class="bg-purple-600 text-white px-6 py-2 rounded-lg hover:bg-purple-700 transition-colors w-full">
                                    <i class="fas fa-save mr-2"></i>Update Profile
                                </button>
                            </div>
                        </form>
                    </div>

                    <!-- Change Password -->
                    <div class="bg-white rounded-lg shadow-md p-6">
                        <h2 class="text-xl font-bold text-gray-800 mb-4 flex items-center">
                            <i class="fas fa-lock mr-2 text-red-500"></i> Change Password
                        </h2>
                        <form method="POST">
                            <input type="hidden" name="admin_password" value="1">
                            <div class="space-y-4">
                                <div>
                                    <label class="block text-gray-700 font-semibold mb-2">Current Password *</label>
                                    <input type="password" name="current_password" required 
                                           class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-red-500"
                                           placeholder="Enter current password">
                                </div>
                                <div>
                                    <label class="block text-gray-700 font-semibold mb-2">New Password *</label>
                                    <input type="password" name="new_password" required 
                                           class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-red-500"
                                           placeholder="Enter new password">
                                </div>
                                <div>
                                    <label class="block text-gray-700 font-semibold mb-2">Confirm Password *</label>
                                    <input type="password" name="confirm_password" required 
                                           class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-red-500"
                                           placeholder="Confirm new password">
                                </div>
                                <button type="submit" class="bg-red-600 text-white px-6 py-2 rounded-lg hover:bg-red-700 transition-colors w-full">
                                    <i class="fas fa-key mr-2"></i>Change Password
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>

            <!-- Permissions Tab -->
            <div class="tab-content" id="permissions-tab">
                <div class="bg-white rounded-lg shadow-md p-6">
                    <h2 class="text-xl font-bold text-gray-800 mb-6 flex items-center">
                        <i class="fas fa-shield-alt mr-2 text-blue-500"></i> Admin Permissions Management
                    </h2>
                    
                    <?php foreach($all_admins as $admin): ?>
                    <div class="mb-8 p-4 border border-gray-200 rounded-lg">
                        <form method="POST">
                            <input type="hidden" name="update_permissions" value="1">
                            <input type="hidden" name="admin_id" value="<?php echo $admin['id']; ?>">
                            
                            <div class="mb-4">
                                <h3 class="text-lg font-semibold text-gray-800">
                                    <?php echo htmlspecialchars($admin['full_name']); ?> 
                                    <span class="text-sm text-gray-600">(<?php echo htmlspecialchars($admin['username']); ?>)</span>
                                </h3>
                                <p class="text-sm text-gray-600">
                                    Role: <span class="font-medium"><?php echo htmlspecialchars($admin['role']); ?></span>
                                </p>
                            </div>
                            
                            <div class="permission-grid">
                                <?php 
                                $user_permissions = explode(',', $admin['permissions'] ?? '');
                                foreach($modules as $key => $label): 
                                ?>
                                <label class="permission-item">
                                    <input type="checkbox" name="permissions[]" value="<?php echo $key; ?>" 
                                        <?php echo in_array($key, $user_permissions) ? 'checked' : ''; ?>
                                        class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded">
                                    <span class="text-sm font-medium text-gray-700"><?php echo $label; ?></span>
                                </label>
                                <?php endforeach; ?>
                            </div>
                            
                            <div class="mt-4">
                                <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700 transition-colors text-sm">
                                    <i class="fas fa-save mr-2"></i>Update Permissions
                                </button>
                            </div>
                        </form>
                    </div>
                    <?php endforeach; ?>
                </div>
            </div>

            <!-- Admissions Tab -->
            <div class="tab-content" id="admissions-tab">
                <div class="space-y-8">
                    <!-- Admission Timeline Management -->
                    <div class="bg-white rounded-lg shadow-md p-6">
                        <h2 class="text-xl font-bold text-gray-800 mb-4 flex items-center">
                            <i class="fas fa-calendar-alt mr-2 text-green-500"></i> Admission Timeline
                        </h2>
                        <div class="overflow-x-auto">
                            <table class="min-w-full divide-y divide-gray-200">
                                <thead class="bg-gray-50">
                                    <tr>
                                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Event</th>
                                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Date</th>
                                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Description</th>
                                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white divide-y divide-gray-200">
                                    <?php if (empty($admission_timeline)): ?>
                                        <tr>
                                            <td colspan="4" class="px-4 py-4 text-center text-gray-500">No admission timeline events found</td>
                                        </tr>
                                    <?php else: ?>
                                        <?php foreach ($admission_timeline as $timeline): ?>
                                        <tr>
                                            <form method="POST">
                                                <input type="hidden" name="update_admission_timeline" value="1">
                                                <input type="hidden" name="id" value="<?php echo $timeline['id']; ?>">
                                                <td class="px-4 py-3 whitespace-nowrap">
                                                    <input type="text" name="event_name" value="<?php echo htmlspecialchars($timeline['event_name']); ?>" 
                                                           class="w-full px-2 py-1 border border-gray-300 rounded text-sm">
                                                </td>
                                                <td class="px-4 py-3 whitespace-nowrap">
                                                    <input type="date" name="event_date" value="<?php echo $timeline['event_date']; ?>" 
                                                           class="w-full px-2 py-1 border border-gray-300 rounded text-sm">
                                                </td>
                                                <td class="px-4 py-3">
                                                    <input type="text" name="description" value="<?php echo htmlspecialchars($timeline['description']); ?>" 
                                                           class="w-full px-2 py-1 border border-gray-300 rounded text-sm">
                                                </td>
                                                <td class="px-4 py-3 whitespace-nowrap">
                                                    <button type="submit" class="bg-green-600 text-white px-3 py-1 rounded text-sm hover:bg-green-700">
                                                        <i class="fas fa-save mr-1"></i>Update
                                                    </button>
                                                </td>
                                            </form>
                                        </tr>
                                        <?php endforeach; ?>
                                    <?php endif; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>

                    <!-- Recent Admission Inquiries -->
                    <div class="bg-white rounded-lg shadow-md p-6">
                        <div class="flex justify-between items-center mb-6">
                            <h2 class="text-xl font-bold text-gray-800 flex items-center">
                                <i class="fas fa-question-circle mr-2 text-blue-500"></i> Recent Admission Inquiries
                            </h2>
                            <span class="bg-blue-100 text-blue-800 px-3 py-1 rounded-full text-sm font-medium">
                                <?php echo count($admission_inquiries); ?> Total
                            </span>
                        </div>
                        <div class="overflow-x-auto">
                            <table class="min-w-full divide-y divide-gray-200">
                                <thead class="bg-gray-50">
                                    <tr>
                                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Student Name</th>
                                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Grade</th>
                                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Parent Name</th>
                                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Contact</th>
                                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Date</th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white divide-y divide-gray-200">
                                    <?php if (empty($admission_inquiries)): ?>
                                        <tr>
                                            <td colspan="5" class="px-4 py-4 text-center text-gray-500">No admission inquiries found</td>
                                        </tr>
                                    <?php else: ?>
                                        <?php foreach ($admission_inquiries as $inquiry): ?>
                                        <tr class="hover:bg-gray-50">
                                            <td class="px-4 py-3">
                                                <div class="text-sm font-medium text-gray-900"><?php echo htmlspecialchars($inquiry['student_name'] ?? 'N/A'); ?></div>
                                            </td>
                                            <td class="px-4 py-3">
                                                <div class="text-sm text-gray-900"><?php echo htmlspecialchars($inquiry['grade'] ?? 'N/A'); ?></div>
                                            </td>
                                            <td class="px-4 py-3">
                                                <div class="text-sm text-gray-900"><?php echo htmlspecialchars($inquiry['parent_name'] ?? 'N/A'); ?></div>
                                            </td>
                                            <td class="px-4 py-3">
                                                <div class="text-sm text-gray-900">
                                                    <div><?php echo htmlspecialchars($inquiry['email'] ?? 'N/A'); ?></div>
                                                    <div class="text-gray-500"><?php echo htmlspecialchars($inquiry['phone'] ?? 'N/A'); ?></div>
                                                </div>
                                            </td>
                                            <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-900">
                                                <?php echo date('M j, Y', strtotime($inquiry['created_at'])); ?>
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

            <!-- Fees Management Tab -->
            <div class="tab-content" id="fees-tab">
                <div class="space-y-8">
                    <!-- Fee Structure Management -->
                    <div class="bg-white rounded-lg shadow-md p-6">
                        <div class="flex justify-between items-center mb-6">
                            <h2 class="text-xl font-bold text-gray-800 flex items-center">
                                <i class="fas fa-money-bill-wave mr-2 text-green-500"></i> Fee Structure Management
                            </h2>
                            <button onclick="toggleModal('addFeeModal')" 
                                    class="bg-green-600 text-white px-4 py-2 rounded-lg hover:bg-green-700 transition-colors flex items-center">
                                <i class="fas fa-plus mr-2"></i>Add New
                            </button>
                        </div>
                        <div class="overflow-x-auto">
                            <table class="min-w-full divide-y divide-gray-200">
                                <thead class="bg-gray-50">
                                    <tr>
                                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Grade Level</th>
                                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Tuition Fee</th>
                                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Admission Fee</th>
                                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Development Fee</th>
                                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Other Charges</th>
                                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Total Fee</th>
                                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Academic Year</th>
                                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Active</th>
                                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white divide-y divide-gray-200">
                                    <?php if (empty($fee_structure)): ?>
                                        <tr>
                                            <td colspan="9" class="px-4 py-4 text-center text-gray-500">No fee structure data available</td>
                                        </tr>
                                    <?php else: ?>
                                        <?php foreach ($fee_structure as $fee): ?>
                                        <tr>
                                            <form method="POST" id="fee_form_<?php echo $fee['id']; ?>">
                                                <input type="hidden" name="update_fee_structure" value="1">
                                                <input type="hidden" name="id" value="<?php echo $fee['id']; ?>">
                                                <td class="px-4 py-3 whitespace-nowrap">
                                                    <input type="text" name="grade_level" value="<?php echo htmlspecialchars($fee['grade_level']); ?>" 
                                                           class="w-full px-2 py-1 border border-gray-300 rounded text-sm">
                                                </td>
                                                <td class="px-4 py-3 whitespace-nowrap">
                                                    <input type="number" name="tuition_fee" value="<?php echo $fee['tuition_fee']; ?>" step="0.01"
                                                           onchange="calculateTotalFee('fee_form_<?php echo $fee['id']; ?>', 'total_fee_<?php echo $fee['id']; ?>')"
                                                           class="w-full px-2 py-1 border border-gray-300 rounded text-sm">
                                                </td>
                                                <td class="px-4 py-3 whitespace-nowrap">
                                                    <input type="number" name="admission_fee" value="<?php echo $fee['admission_fee']; ?>" step="0.01"
                                                           onchange="calculateTotalFee('fee_form_<?php echo $fee['id']; ?>', 'total_fee_<?php echo $fee['id']; ?>')"
                                                           class="w-full px-2 py-1 border border-gray-300 rounded text-sm">
                                                </td>
                                                <td class="px-4 py-3 whitespace-nowrap">
                                                    <input type="number" name="development_fee" value="<?php echo $fee['development_fee']; ?>" step="0.01"
                                                           onchange="calculateTotalFee('fee_form_<?php echo $fee['id']; ?>', 'total_fee_<?php echo $fee['id']; ?>')"
                                                           class="w-full px-2 py-1 border border-gray-300 rounded text-sm">
                                                </td>
                                                <td class="px-4 py-3 whitespace-nowrap">
                                                    <input type="number" name="other_charges" value="<?php echo $fee['other_charges']; ?>" step="0.01"
                                                           onchange="calculateTotalFee('fee_form_<?php echo $fee['id']; ?>', 'total_fee_<?php echo $fee['id']; ?>')"
                                                           class="w-full px-2 py-1 border border-gray-300 rounded text-sm">
                                                </td>
                                                <td class="px-4 py-3 whitespace-nowrap">
                                                    <input type="number" id="total_fee_<?php echo $fee['id']; ?>" value="<?php echo $fee['total_fee']; ?>" step="0.01" readonly
                                                           class="w-full px-2 py-1 border border-gray-300 rounded text-sm bg-gray-100">
                                                </td>
                                                <td class="px-4 py-3 whitespace-nowrap">
                                                    <input type="text" name="academic_year" value="<?php echo htmlspecialchars($fee['academic_year']); ?>" 
                                                           class="w-full px-2 py-1 border border-gray-300 rounded text-sm">
                                                </td>
                                                <td class="px-4 py-3 whitespace-nowrap">
                                                    <input type="checkbox" name="active" value="1" <?php echo $fee['active'] ? 'checked' : ''; ?>
                                                           class="h-4 w-4 text-green-600 focus:ring-green-500 border-gray-300 rounded">
                                                </td>
                                                <td class="px-4 py-3 whitespace-nowrap">
                                                    <button type="submit" class="bg-green-600 text-white px-3 py-1 rounded text-sm hover:bg-green-700">
                                                        <i class="fas fa-save mr-1"></i>Update
                                                    </button>
                                                </td>
                                            </form>
                                        </tr>
                                        <?php endforeach; ?>
                                    <?php endif; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>

                    <!-- Hostel Fees Management -->
                    <div class="bg-white rounded-lg shadow-md p-6">
                        <div class="flex justify-between items-center mb-6">
                            <h2 class="text-xl font-bold text-gray-800 flex items-center">
                                <i class="fas fa-bed mr-2 text-yellow-500"></i> Hostel Fees Management
                            </h2>
                            <button onclick="toggleModal('addHostelModal')" 
                                    class="bg-yellow-600 text-white px-4 py-2 rounded-lg hover:bg-yellow-700 transition-colors flex items-center">
                                <i class="fas fa-plus mr-2"></i>Add New
                            </button>
                        </div>
                        <div class="overflow-x-auto">
                            <table class="min-w-full divide-y divide-gray-200">
                                <thead class="bg-gray-50">
                                    <tr>
                                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Hostel Type</th>
                                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Room Charges</th>
                                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Food Charges</th>
                                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Other Charges</th>
                                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Total Fee</th>
                                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Academic Year</th>
                                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Active</th>
                                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white divide-y divide-gray-200">
                                    <?php if (empty($hostel_fees)): ?>
                                        <tr>
                                            <td colspan="8" class="px-4 py-4 text-center text-gray-500">No hostel fees data available</td>
                                        </tr>
                                    <?php else: ?>
                                        <?php foreach ($hostel_fees as $hostel): ?>
                                        <tr>
                                            <form method="POST" id="hostel_form_<?php echo $hostel['id']; ?>">
                                                <input type="hidden" name="update_hostel_fees" value="1">
                                                <input type="hidden" name="id" value="<?php echo $hostel['id']; ?>">
                                                <td class="px-4 py-3 whitespace-nowrap">
                                                    <input type="text" name="hostel_type" value="<?php echo htmlspecialchars($hostel['hostel_type']); ?>" 
                                                           class="w-full px-2 py-1 border border-gray-300 rounded text-sm">
                                                </td>
                                                <td class="px-4 py-3 whitespace-nowrap">
                                                    <input type="number" name="room_charges" value="<?php echo $hostel['room_charges']; ?>" step="0.01"
                                                           onchange="calculateHostelTotal('hostel_form_<?php echo $hostel['id']; ?>', 'hostel_total_<?php echo $hostel['id']; ?>')"
                                                           class="w-full px-2 py-1 border border-gray-300 rounded text-sm">
                                                </td>
                                                <td class="px-4 py-3 whitespace-nowrap">
                                                    <input type="number" name="food_charges" value="<?php echo $hostel['food_charges']; ?>" step="0.01"
                                                           onchange="calculateHostelTotal('hostel_form_<?php echo $hostel['id']; ?>', 'hostel_total_<?php echo $hostel['id']; ?>')"
                                                           class="w-full px-2 py-1 border border-gray-300 rounded text-sm">
                                                </td>
                                                <td class="px-4 py-3 whitespace-nowrap">
                                                    <input type="number" name="other_charges" value="<?php echo $hostel['other_charges']; ?>" step="0.01"
                                                           onchange="calculateHostelTotal('hostel_form_<?php echo $hostel['id']; ?>', 'hostel_total_<?php echo $hostel['id']; ?>')"
                                                           class="w-full px-2 py-1 border border-gray-300 rounded text-sm">
                                                </td>
                                                <td class="px-4 py-3 whitespace-nowrap">
                                                    <input type="number" id="hostel_total_<?php echo $hostel['id']; ?>" value="<?php echo $hostel['total_fee']; ?>" step="0.01" readonly
                                                           class="w-full px-2 py-1 border border-gray-300 rounded text-sm bg-gray-100">
                                                </td>
                                                <td class="px-4 py-3 whitespace-nowrap">
                                                    <input type="text" name="academic_year" value="<?php echo htmlspecialchars($hostel['academic_year']); ?>" 
                                                           class="w-full px-2 py-1 border border-gray-300 rounded text-sm">
                                                </td>
                                                <td class="px-4 py-3 whitespace-nowrap">
                                                    <input type="checkbox" name="active" value="1" <?php echo $hostel['active'] ? 'checked' : ''; ?>
                                                           class="h-4 w-4 text-yellow-600 focus:ring-yellow-500 border-gray-300 rounded">
                                                </td>
                                                <td class="px-4 py-3 whitespace-nowrap">
                                                    <button type="submit" class="bg-yellow-600 text-white px-3 py-1 rounded text-sm hover:bg-yellow-700">
                                                        <i class="fas fa-save mr-1"></i>Update
                                                    </button>
                                                </td>
                                            </form>
                                        </tr>
                                        <?php endforeach; ?>
                                    <?php endif; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>

            <!-- System Info Tab -->
            <div class="tab-content" id="system-tab">
                <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
                    <!-- System Information -->
                    <div class="bg-white rounded-lg shadow-md p-6">
                        <h2 class="text-xl font-bold text-gray-800 mb-4 flex items-center">
                            <i class="fas fa-info-circle mr-2 text-green-500"></i> System Information
                        </h2>
                        <div class="space-y-3 text-sm">
                            <div class="flex justify-between border-b pb-2">
                                <span class="text-gray-600">PHP Version:</span>
                                <span class="font-medium"><?php echo phpversion(); ?></span>
                            </div>
                            <div class="flex justify-between border-b pb-2">
                                <span class="text-gray-600">Database:</span>
                                <span class="font-medium">MySQL</span>
                            </div>
                            <div class="flex justify-between border-b pb-2">
                                <span class="text-gray-600">Server:</span>
                                <span class="font-medium text-xs"><?php echo $_SERVER['SERVER_SOFTWARE'] ?? 'Unknown'; ?></span>
                            </div>
                            <div class="flex justify-between border-b pb-2">
                                <span class="text-gray-600">Logged in as:</span>
                                <span class="font-medium"><?php echo $_SESSION['admin_name'] ?? 'Administrator'; ?></span>
                            </div>
                            <div class="flex justify-between border-b pb-2">
                                <span class="text-gray-600">Session ID:</span>
                                <span class="font-medium text-xs"><?php echo session_id(); ?></span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-gray-600">Current Time:</span>
                                <span class="font-medium"><?php echo date('Y-m-d H:i:s'); ?></span>
                            </div>
                        </div>
                    </div>

                    <!-- Quick Stats -->
                    <div class="bg-white rounded-lg shadow-md p-6">
                        <h2 class="text-xl font-bold text-gray-800 mb-4 flex items-center">
                            <i class="fas fa-chart-bar mr-2 text-orange-500"></i> Quick Stats
                        </h2>
                        <div class="space-y-4">
                            <?php
                            $tables_to_check = ['students', 'teachers', 'admission_inquiries', 'contact_messages', 'blog_posts', 'downloads', 'job_applications'];
                            
                            foreach($tables_to_check as $table):
                                $count = 0;
                                try {
                                    $count = $pdo->query("SELECT COUNT(*) FROM $table")->fetchColumn();
                                } catch (Exception $e) {
                                    $count = 0;
                                }
                            ?>
                            <div class="flex justify-between items-center border-b pb-2">
                                <span class="text-gray-600 capitalize"><?php echo str_replace('_', ' ', $table); ?>:</span>
                                <span class="font-bold text-lg <?php echo $count > 0 ? 'text-green-600' : 'text-gray-400'; ?>">
                                    <?php echo $count; ?>
                                </span>
                            </div>
                            <?php endforeach; ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Add Fee Structure Modal -->
    <div id="addFeeModal" class="modal">
        <div class="modal-content">
            <div class="flex justify-between items-center mb-4">
                <h3 class="text-lg font-medium text-gray-900">Add New Fee Structure</h3>
                <button onclick="toggleModal('addFeeModal')" class="text-gray-400 hover:text-gray-600">
                    <i class="fas fa-times"></i>
                </button>
            </div>
            <form method="POST" class="space-y-4">
                <input type="hidden" name="add_fee_structure" value="1">
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Grade Level</label>
                        <input type="text" name="new_grade_level" required 
                               class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-green-500 focus:border-green-500">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Academic Year</label>
                        <input type="text" name="new_academic_year" required 
                               class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-green-500 focus:border-green-500">
                    </div>
                </div>
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Tuition Fee</label>
                        <input type="number" name="new_tuition_fee" step="0.01" required 
                               class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-green-500 focus:border-green-500">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Admission Fee</label>
                        <input type="number" name="new_admission_fee" step="0.01" required 
                               class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-green-500 focus:border-green-500">
                    </div>
                </div>
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Development Fee</label>
                        <input type="number" name="new_development_fee" step="0.01" required 
                               class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-green-500 focus:border-green-500">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Other Charges</label>
                        <input type="number" name="new_other_charges" step="0.01" 
                               class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-green-500 focus:border-green-500">
                    </div>
                </div>
                <div class="flex items-center">
                    <input type="checkbox" name="new_active" value="1" checked
                           class="h-4 w-4 text-green-600 focus:ring-green-500 border-gray-300 rounded">
                    <label class="ml-2 block text-sm text-gray-900">Active</label>
                </div>
                <div class="flex justify-end space-x-3 mt-6">
                    <button type="button" onclick="toggleModal('addFeeModal')" 
                            class="px-4 py-2 bg-gray-300 text-gray-700 rounded-md hover:bg-gray-400">
                        Cancel
                    </button>
                    <button type="submit" 
                            class="px-4 py-2 bg-green-600 text-white rounded-md hover:bg-green-700">
                        Add Fee Structure
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Add Hostel Fees Modal -->
    <div id="addHostelModal" class="modal">
        <div class="modal-content">
            <div class="flex justify-between items-center mb-4">
                <h3 class="text-lg font-medium text-gray-900">Add New Hostel Fee Structure</h3>
                <button onclick="toggleModal('addHostelModal')" class="text-gray-400 hover:text-gray-600">
                    <i class="fas fa-times"></i>
                </button>
            </div>
            <form method="POST" class="space-y-4">
                <input type="hidden" name="add_hostel_fees" value="1">
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Hostel Type</label>
                        <input type="text" name="new_hostel_type" required 
                               class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-yellow-500 focus:border-yellow-500">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Academic Year</label>
                        <input type="text" name="new_academic_year" required 
                               class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-yellow-500 focus:border-yellow-500">
                    </div>
                </div>
                <div class="grid grid-cols-3 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Room Charges</label>
                        <input type="number" name="new_room_charges" step="0.01" required 
                               class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-yellow-500 focus:border-yellow-500">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Food Charges</label>
                        <input type="number" name="new_food_charges" step="0.01" required 
                               class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-yellow-500 focus:border-yellow-500">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Other Charges</label>
                        <input type="number" name="new_other_charges" step="0.01" 
                               class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-yellow-500 focus:border-yellow-500">
                    </div>
                </div>
                <div class="flex items-center">
                    <input type="checkbox" name="new_active" value="1" checked
                           class="h-4 w-4 text-yellow-600 focus:ring-yellow-500 border-gray-300 rounded">
                    <label class="ml-2 block text-sm text-gray-900">Active</label>
                </div>
                <div class="flex justify-end space-x-3 mt-6">
                    <button type="button" onclick="toggleModal('addHostelModal')" 
                            class="px-4 py-2 bg-gray-300 text-gray-700 rounded-md hover:bg-gray-400">
                        Cancel
                    </button>
                    <button type="submit" 
                            class="px-4 py-2 bg-yellow-600 text-white rounded-md hover:bg-yellow-700">
                        Add Hostel Fee
                    </button>
                </div>
            </form>
        </div>
    </div>

    <script>
        // Tab functionality
        document.addEventListener('DOMContentLoaded', function() {
            const tabButtons = document.querySelectorAll('.tab-btn');
            const tabContents = document.querySelectorAll('.tab-content');
            
            tabButtons.forEach(button => {
                button.addEventListener('click', function() {
                    const tabId = this.getAttribute('data-tab');
                    
                    // Remove active class from all buttons and contents
                    tabButtons.forEach(btn => {
                        btn.classList.remove('active', 'border-blue-500', 'text-blue-600');
                        btn.classList.add('border-transparent', 'text-gray-500', 'hover:text-gray-700', 'hover:border-gray-300');
                    });
                    tabContents.forEach(content => content.classList.remove('active'));
                    
                    // Add active class to current button and content
                    this.classList.add('active', 'border-blue-500', 'text-blue-600');
                    this.classList.remove('border-transparent', 'text-gray-500', 'hover:text-gray-700', 'hover:border-gray-300');
                    document.getElementById(tabId + '-tab').classList.add('active');
                });
            });
        });

        // Modal functionality
        function toggleModal(modalId) {
            const modal = document.getElementById(modalId);
            modal.style.display = modal.style.display === 'block' ? 'none' : 'block';
        }

        // Close modal when clicking outside
        window.onclick = function(event) {
            if (event.target.classList.contains('modal')) {
                event.target.style.display = 'none';
            }
        }

        // Fee calculation functions
        function calculateTotalFee(formId, totalFieldId) {
            const form = document.getElementById(formId);
            const tuition = parseFloat(form.tuition_fee?.value) || 0;
            const admission = parseFloat(form.admission_fee?.value) || 0;
            const development = parseFloat(form.development_fee?.value) || 0;
            const other = parseFloat(form.other_charges?.value) || 0;
            
            const total = tuition + admission + development + other;
            document.getElementById(totalFieldId).value = total.toFixed(2);
        }
        
        function calculateHostelTotal(formId, totalFieldId) {
            const form = document.getElementById(formId);
            const room = parseFloat(form.room_charges?.value) || 0;
            const food = parseFloat(form.food_charges?.value) || 0;
            const other = parseFloat(form.other_charges?.value) || 0;
            
            const total = room + food + other;
            document.getElementById(totalFieldId).value = total.toFixed(2);
        }
    </script>

    <?php include '../includes/footer.php'; ?>
</body>
</html>