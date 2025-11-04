<?php
session_start();

// Check if admin is logged in
if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    header('Location: login.php');
    exit;
}

// Include db.php from includes folder
require_once '../includes/db.php';

$base_url = '/Gurkul_Project';

// Get counts for dashboard from ALL tables with error handling
try {
    // Active counts with table existence check
    $students_count = 0;
    $teachers_count = 0;
    $applications_count = 0;
    $total_applications = 0;
    $admission_inquiries_count = 0;
    $pending_inquiries = 0;
    $contact_messages_count = 0;
    $job_applications_count = 0;
    $scholarship_submissions_count = 0;
    
    // New module counts
    $blog_count = 0;
    $downloads_count = 0;
    $question_papers_count = 0;
    $inspiration_count = 0;

    // Check each table and get counts
    $tables = [
        'students' => "SELECT COUNT(*) FROM students WHERE status = 'Active'",
        'teachers' => "SELECT COUNT(*) FROM teachers WHERE status = 'Active'",
        'applications' => "SELECT COUNT(*) FROM applications",
        'applications_pending' => "SELECT COUNT(*) FROM applications WHERE status = 'Pending'",
        'admission_inquiries' => "SELECT COUNT(*) FROM admission_inquiries",
        'admission_inquiries_pending' => "SELECT COUNT(*) FROM admission_inquiries WHERE status = 'pending'",
        'contact_messages' => "SELECT COUNT(*) FROM contact_messages",
        'job_applications' => "SELECT COUNT(*) FROM job_applications",
        'scholarship_submissions' => "SELECT COUNT(*) FROM scholarship_submissions",
        'blog_posts' => "SELECT COUNT(*) FROM blog_posts",
        'downloads' => "SELECT COUNT(*) FROM downloads",
        'question_papers' => "SELECT COUNT(*) FROM question_papers",
        'inspiration' => "SELECT COUNT(*) FROM inspiration"
    ];

    foreach ($tables as $key => $query) {
        try {
            if (strpos($key, 'pending') !== false) {
                if ($key === 'applications_pending') {
                    $applications_count = $pdo->query($query)->fetchColumn();
                } elseif ($key === 'admission_inquiries_pending') {
                    $pending_inquiries = $pdo->query($query)->fetchColumn();
                }
            } else {
                switch ($key) {
                    case 'students':
                        $students_count = $pdo->query($query)->fetchColumn();
                        break;
                    case 'teachers':
                        $teachers_count = $pdo->query($query)->fetchColumn();
                        break;
                    case 'applications':
                        $total_applications = $pdo->query($query)->fetchColumn();
                        break;
                    case 'admission_inquiries':
                        $admission_inquiries_count = $pdo->query($query)->fetchColumn();
                        break;
                    case 'contact_messages':
                        $contact_messages_count = $pdo->query($query)->fetchColumn();
                        break;
                    case 'job_applications':
                        $job_applications_count = $pdo->query($query)->fetchColumn();
                        break;
                    case 'scholarship_submissions':
                        $scholarship_submissions_count = $pdo->query($query)->fetchColumn();
                        break;
                    case 'blog_posts':
                        $blog_count = $pdo->query($query)->fetchColumn();
                        break;
                    case 'downloads':
                        $downloads_count = $pdo->query($query)->fetchColumn();
                        break;
                    case 'question_papers':
                        $question_papers_count = $pdo->query($query)->fetchColumn();
                        break;
                    case 'inspiration':
                        $inspiration_count = $pdo->query($query)->fetchColumn();
                        break;
                }
            }
        } catch (PDOException $e) {
            // Table doesn't exist or error, continue with next
            error_log("Table $key not found: " . $e->getMessage());
        }
    }

    // Get recent data from ALL tables with error handling
    $recent_data_queries = [
        'applications' => "SELECT * FROM applications ORDER BY created_at DESC LIMIT 5",
        'admission_inquiries' => "SELECT * FROM admission_inquiries ORDER BY created_at DESC LIMIT 5",
        'contact_messages' => "SELECT * FROM contact_messages ORDER BY created_at DESC LIMIT 5",
        'job_applications' => "SELECT * FROM job_applications ORDER BY applied_at DESC LIMIT 5",
        'scholarship_submissions' => "SELECT * FROM scholarship_submissions ORDER BY created_at DESC LIMIT 5",
        'blog_posts' => "SELECT title, created_at FROM blog_posts ORDER BY created_at DESC LIMIT 5",
        'downloads' => "SELECT title, created_at FROM downloads ORDER BY created_at DESC LIMIT 5"
    ];

    $recent_applications = [];
    $recent_inquiries = [];
    $recent_contacts = [];
    $recent_jobs = [];
    $recent_scholarships = [];
    $recent_posts = [];
    $recent_downloads = [];

    foreach ($recent_data_queries as $key => $query) {
        try {
            $stmt = $pdo->prepare($query);
            $stmt->execute();
            switch ($key) {
                case 'applications':
                    $recent_applications = $stmt->fetchAll(PDO::FETCH_ASSOC);
                    break;
                case 'admission_inquiries':
                    $recent_inquiries = $stmt->fetchAll(PDO::FETCH_ASSOC);
                    break;
                case 'contact_messages':
                    $recent_contacts = $stmt->fetchAll(PDO::FETCH_ASSOC);
                    break;
                case 'job_applications':
                    $recent_jobs = $stmt->fetchAll(PDO::FETCH_ASSOC);
                    break;
                case 'scholarship_submissions':
                    $recent_scholarships = $stmt->fetchAll(PDO::FETCH_ASSOC);
                    break;
                case 'blog_posts':
                    $recent_posts = $stmt->fetchAll(PDO::FETCH_ASSOC);
                    break;
                case 'downloads':
                    $recent_downloads = $stmt->fetchAll(PDO::FETCH_ASSOC);
                    break;
            }
        } catch (PDOException $e) {
            error_log("Error fetching recent $key: " . $e->getMessage());
        }
    }

} catch(PDOException $e) {
    // General database error
    error_log("Dashboard database error: " . $e->getMessage());
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard - Bhaktivedanta Gurukul</title>
    <link rel="icon" type="image/x-icon" href="../images/bvgLogo.png">
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        .stat-card {
            transition: transform 0.3s ease, box-shadow 0.3s ease;
        }
        .stat-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 25px rgba(0,0,0,0.1);
        }
        .recent-item {
            transition: background-color 0.3s ease;
        }
        .recent-item:hover {
            background-color: #f8fafc;
        }
        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(20px); }
            to { opacity: 1; transform: translateY(0); }
        }
        .animate-fade-in {
            animation: fadeIn 0.6s ease-out;
        }
        .admin-badge {
            background: linear-gradient(135deg, #059669 0%, #047857 100%);
        }
    </style>
</head>
<body class="bg-gray-100">
    <!-- Header -->
    <?php 
    $current_page = 'dashboard.php';
    $current_directory = 'admin';
    include '../includes/header.php'; 
    ?>

    <!-- Main Content -->
    <div class="container mx-auto px-4 py-8 mt-16">
        <!-- Welcome Section -->
        <div class="mb-8 flex justify-between items-center">
            <div>
                <h1 class="text-3xl font-bold text-gray-800">Admin Dashboard</h1>
                <p class="text-gray-600">Welcome back, <?php echo htmlspecialchars($_SESSION['admin_name']); ?>!</p>
                <p class="text-sm text-gray-500 mt-1"><?php echo date('l, F j, Y'); ?></p>
            </div>
            <div class="flex items-center space-x-4">
                <span class="admin-badge text-white px-3 py-1 rounded-full text-sm font-medium">
                    <i class="fas fa-cog mr-1"></i><?php echo $_SESSION['admin_role'] === 'super_admin' ? 'Super Admin' : 'Admin'; ?>
                </span>
                <span class="bg-blue-100 text-blue-800 px-3 py-1 rounded-full text-sm font-medium">
                    <i class="fas fa-clock mr-1"></i><?php echo date('g:i A'); ?>
                </span>
            </div>
        </div>

        <!-- Main Stats Cards Grid -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
            <!-- Students Card -->
            <div class="stat-card bg-white rounded-lg shadow-md p-6 border-l-4 border-blue-500 animate-fade-in">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-gray-600 text-sm font-medium">Active Students</p>
                        <h3 class="text-2xl font-bold text-gray-800"><?php echo $students_count; ?></h3>
                        <p class="text-xs text-gray-500 mt-1">Total enrolled students</p>
                    </div>
                    <div class="bg-blue-100 p-3 rounded-full">
                        <i class="fas fa-users text-blue-500 text-xl"></i>
                    </div>
                </div>
                <a href="students.php" class="text-blue-600 hover:text-blue-800 text-sm mt-3 inline-block font-medium">
                    Manage Students →
                </a>
            </div>

            <!-- Teachers Card -->
            <div class="stat-card bg-white rounded-lg shadow-md p-6 border-l-4 border-green-500 animate-fade-in">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-gray-600 text-sm font-medium">Active Teachers</p>
                        <h3 class="text-2xl font-bold text-gray-800"><?php echo $teachers_count; ?></h3>
                        <p class="text-xs text-gray-500 mt-1">Teaching staff</p>
                    </div>
                    <div class="bg-green-100 p-3 rounded-full">
                        <i class="fas fa-chalkboard-teacher text-green-500 text-xl"></i>
                    </div>
                </div>
                <a href="teachers.php" class="text-green-600 hover:text-green-800 text-sm mt-3 inline-block font-medium">
                    Manage Teachers →
                </a>
            </div>

            <!-- Pending Applications -->
            <div class="stat-card bg-white rounded-lg shadow-md p-6 border-l-4 border-yellow-500 animate-fade-in">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-gray-600 text-sm font-medium">Pending Applications</p>
                        <h3 class="text-2xl font-bold text-gray-800"><?php echo $applications_count; ?></h3>
                        <p class="text-xs text-gray-500 mt-1">Need review</p>
                    </div>
                    <div class="bg-yellow-100 p-3 rounded-full">
                        <i class="fas fa-file-alt text-yellow-500 text-xl"></i>
                    </div>
                </div>
                <a href="applications.php" class="text-yellow-600 hover:text-yellow-800 text-sm mt-3 inline-block font-medium">
                    Review Now →
                </a>
            </div>

            <!-- Admission Inquiries -->
            <div class="stat-card bg-white rounded-lg shadow-md p-6 border-l-4 border-purple-500 animate-fade-in">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-gray-600 text-sm font-medium">Admission Inquiries</p>
                        <h3 class="text-2xl font-bold text-gray-800"><?php echo $admission_inquiries_count; ?></h3>
                        <p class="text-xs text-gray-500 mt-1"><?php echo $pending_inquiries; ?> pending</p>
                    </div>
                    <div class="bg-purple-100 p-3 rounded-full">
                        <i class="fas fa-question-circle text-purple-500 text-xl"></i>
                    </div>
                </div>
                <a href="admission_inquiries.php" class="text-purple-600 hover:text-purple-800 text-sm mt-3 inline-block font-medium">
                    View Inquiries →
                </a>
            </div>
        </div>

        <!-- Second Row Stats -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
            <!-- Contact Messages -->
            <div class="stat-card bg-white rounded-lg shadow-md p-6 border-l-4 border-indigo-500 animate-fade-in">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-gray-600 text-sm font-medium">Contact Messages</p>
                        <h3 class="text-2xl font-bold text-gray-800"><?php echo $contact_messages_count; ?></h3>
                        <p class="text-xs text-gray-500 mt-1">Website inquiries</p>
                    </div>
                    <div class="bg-indigo-100 p-3 rounded-full">
                        <i class="fas fa-envelope text-indigo-500 text-xl"></i>
                    </div>
                </div>
                <a href="contact_messages.php" class="text-indigo-600 hover:text-indigo-800 text-sm mt-3 inline-block font-medium">
                    View Messages →
                </a>
            </div>

            <!-- Job Applications -->
            <div class="stat-card bg-white rounded-lg shadow-md p-6 border-l-4 border-red-500 animate-fade-in">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-gray-600 text-sm font-medium">Job Applications</p>
                        <h3 class="text-2xl font-bold text-gray-800"><?php echo $job_applications_count; ?></h3>
                        <p class="text-xs text-gray-500 mt-1">Career inquiries</p>
                    </div>
                    <div class="bg-red-100 p-3 rounded-full">
                        <i class="fas fa-briefcase text-red-500 text-xl"></i>
                    </div>
                </div>
                <a href="job_applications.php" class="text-red-600 hover:text-red-800 text-sm mt-3 inline-block font-medium">
                    View Applications →
                </a>
            </div>

            <!-- Blog Posts -->
            <div class="stat-card bg-white rounded-lg shadow-md p-6 border-l-4 border-pink-500 animate-fade-in">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-gray-600 text-sm font-medium">Blog Posts</p>
                        <h3 class="text-2xl font-bold text-gray-800"><?php echo $blog_count; ?></h3>
                        <p class="text-xs text-gray-500 mt-1">Published articles</p>
                    </div>
                    <div class="bg-pink-100 p-3 rounded-full">
                        <i class="fas fa-blog text-pink-500 text-xl"></i>
                    </div>
                </div>
                <a href="blog/manage-posts.php" class="text-pink-600 hover:text-pink-800 text-sm mt-3 inline-block font-medium">
                    Manage Posts →
                </a>
            </div>

            <!-- Downloads -->
            <div class="stat-card bg-white rounded-lg shadow-md p-6 border-l-4 border-teal-500 animate-fade-in">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-gray-600 text-sm font-medium">Downloads</p>
                        <h3 class="text-2xl font-bold text-gray-800"><?php echo $downloads_count; ?></h3>
                        <p class="text-xs text-gray-500 mt-1">Available files</p>
                    </div>
                    <div class="bg-teal-100 p-3 rounded-full">
                        <i class="fas fa-download text-teal-500 text-xl"></i>
                    </div>
                </div>
                <a href="downloads/manage-downloads.php" class="text-teal-600 hover:text-teal-800 text-sm mt-3 inline-block font-medium">
                    Manage Downloads →
                </a>
            </div>
        </div>

        <!-- Third Row Stats -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
            <!-- Scholarship Submissions -->
            <div class="stat-card bg-white rounded-lg shadow-md p-6 border-l-4 border-orange-500 animate-fade-in">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-gray-600 text-sm font-medium">Scholarship Requests</p>
                        <h3 class="text-2xl font-bold text-gray-800"><?php echo $scholarship_submissions_count; ?></h3>
                        <p class="text-xs text-gray-500 mt-1">Financial aid requests</p>
                    </div>
                    <div class="bg-orange-100 p-3 rounded-full">
                        <i class="fas fa-graduation-cap text-orange-500 text-xl"></i>
                    </div>
                </div>
                <a href="scholarship_submissions.php" class="text-orange-600 hover:text-orange-800 text-sm mt-3 inline-block font-medium">
                    View Requests →
                </a>
            </div>

            <!-- Question Papers -->
            <div class="stat-card bg-white rounded-lg shadow-md p-6 border-l-4 border-cyan-500 animate-fade-in">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-gray-600 text-sm font-medium">Question Papers</p>
                        <h3 class="text-2xl font-bold text-gray-800"><?php echo $question_papers_count; ?></h3>
                        <p class="text-xs text-gray-500 mt-1">Academic resources</p>
                    </div>
                    <div class="bg-cyan-100 p-3 rounded-full">
                        <i class="fas fa-file-alt text-cyan-500 text-xl"></i>
                    </div>
                </div>
                <a href="downloads/manage-question-papers.php" class="text-cyan-600 hover:text-cyan-800 text-sm mt-3 inline-block font-medium">
                    Manage Papers →
                </a>
            </div>

            <!-- Inspiration Pages -->
            <div class="stat-card bg-white rounded-lg shadow-md p-6 border-l-4 border-rose-500 animate-fade-in">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-gray-600 text-sm font-medium">Inspiration Pages</p>
                        <h3 class="text-2xl font-bold text-gray-800"><?php echo $inspiration_count; ?></h3>
                        <p class="text-xs text-gray-500 mt-1">Motivational content</p>
                    </div>
                    <div class="bg-rose-100 p-3 rounded-full">
                        <i class="fas fa-heart text-rose-500 text-xl"></i>
                    </div>
                </div>
                <a href="inspiration/manage-inspiration.php" class="text-rose-600 hover:text-rose-800 text-sm mt-3 inline-block font-medium">
                    Manage Content →
                </a>
            </div>
        </div>

        <!-- Recent Activities & Quick Actions -->
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
            <!-- Left Column - Recent Activities -->
            <div class="lg:col-span-2 space-y-8">
                <!-- Recent Applications -->
                <div class="bg-white rounded-lg shadow-md p-6 animate-fade-in">
                    <div class="flex justify-between items-center mb-6">
                        <h2 class="text-xl font-bold text-gray-800 flex items-center">
                            <i class="fas fa-file-alt text-yellow-500 mr-2"></i>
                            Recent Applications
                        </h2>
                        <a href="applications.php" class="text-blue-600 hover:text-blue-800 text-sm font-medium">View All</a>
                    </div>
                    
                    <div class="space-y-3">
                        <?php if (count($recent_applications) > 0): ?>
                            <?php foreach ($recent_applications as $application): ?>
                                <div class="recent-item flex items-center justify-between p-4 border border-gray-200 rounded-lg">
                                    <div class="flex-1">
                                        <h4 class="font-semibold text-gray-800"><?php echo htmlspecialchars($application['student_name'] ?? 'N/A'); ?></h4>
                                        <div class="flex items-center space-x-4 mt-1">
                                            <p class="text-sm text-gray-600">
                                                <i class="fas fa-graduation-cap mr-1"></i>
                                                Class: <?php echo htmlspecialchars($application['class_applied'] ?? $application['applied_class'] ?? 'N/A'); ?>
                                            </p>
                                            <p class="text-xs text-gray-500">
                                                <i class="far fa-clock mr-1"></i>
                                                <?php echo date('M j, Y', strtotime($application['application_date'] ?? $application['created_at'] ?? 'N/A')); ?>
                                            </p>
                                        </div>
                                    </div>
                                    <span class="px-3 py-1 rounded-full text-xs font-medium 
                                        <?php echo ($application['status'] ?? 'Pending') == 'Pending' ? 'bg-yellow-100 text-yellow-800' : 
                                               (($application['status'] ?? 'Pending') == 'Approved' ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800'); ?>">
                                        <?php echo $application['status'] ?? 'Pending'; ?>
                                    </span>
                                </div>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <div class="text-center py-6">
                                <i class="fas fa-file-alt text-gray-300 text-4xl mb-3"></i>
                                <p class="text-gray-500">No applications found.</p>
                                <p class="text-sm text-gray-400 mt-1">Applications will appear here when submitted</p>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>

                <!-- Recent Admission Inquiries -->
                <div class="bg-white rounded-lg shadow-md p-6 animate-fade-in">
                    <div class="flex justify-between items-center mb-6">
                        <h2 class="text-xl font-bold text-gray-800 flex items-center">
                            <i class="fas fa-question-circle text-purple-500 mr-2"></i>
                            Recent Admission Inquiries
                        </h2>
                        <a href="admission_inquiries.php" class="text-blue-600 hover:text-blue-800 text-sm font-medium">View All</a>
                    </div>
                    
                    <div class="space-y-3">
                        <?php if (count($recent_inquiries) > 0): ?>
                            <?php foreach ($recent_inquiries as $inquiry): ?>
                                <div class="recent-item flex items-center justify-between p-4 border border-gray-200 rounded-lg">
                                    <div class="flex-1">
                                        <h4 class="font-semibold text-gray-800"><?php echo htmlspecialchars($inquiry['student_name'] ?? 'N/A'); ?></h4>
                                        <div class="flex items-center space-x-4 mt-1">
                                            <p class="text-sm text-gray-600">
                                                <i class="fas fa-user-graduate mr-1"></i>
                                                Grade: <?php echo htmlspecialchars($inquiry['grade'] ?? 'N/A'); ?>
                                            </p>
                                            <p class="text-sm text-gray-600">
                                                <i class="fas fa-phone mr-1"></i>
                                                <?php echo htmlspecialchars($inquiry['phone'] ?? 'N/A'); ?>
                                            </p>
                                            <p class="text-xs text-gray-500">
                                                <?php echo date('M j, Y', strtotime($inquiry['created_at'])); ?>
                                            </p>
                                        </div>
                                    </div>
                                    <span class="px-3 py-1 rounded-full text-xs font-medium 
                                        <?php echo ($inquiry['status'] ?? 'pending') == 'approved' ? 'bg-green-100 text-green-800' : 
                                               (($inquiry['status'] ?? 'pending') == 'pending' ? 'bg-yellow-100 text-yellow-800' : 'bg-red-100 text-red-800'); ?>">
                                        <?php echo ucfirst($inquiry['status'] ?? 'pending'); ?>
                                    </span>
                                </div>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <div class="text-center py-6">
                                <i class="fas fa-question-circle text-gray-300 text-4xl mb-3"></i>
                                <p class="text-gray-500">No admission inquiries found.</p>
                                <p class="text-sm text-gray-400 mt-1">Inquiries will appear here when submitted via website</p>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>

                <!-- Recent Blog Posts -->
                <div class="bg-white rounded-lg shadow-md p-6 animate-fade-in">
                    <div class="flex justify-between items-center mb-6">
                        <h2 class="text-xl font-bold text-gray-800 flex items-center">
                            <i class="fas fa-blog text-pink-500 mr-2"></i>
                            Recent Blog Posts
                        </h2>
                        <a href="blog/manage-posts.php" class="text-blue-600 hover:text-blue-800 text-sm font-medium">View All</a>
                    </div>
                    
                    <div class="space-y-3">
                        <?php if (count($recent_posts) > 0): ?>
                            <?php foreach ($recent_posts as $post): ?>
                                <div class="recent-item flex items-center justify-between p-4 border border-gray-200 rounded-lg">
                                    <div class="flex-1">
                                        <h4 class="font-semibold text-gray-800"><?php echo htmlspecialchars($post['title'] ?? 'N/A'); ?></h4>
                                        <p class="text-xs text-gray-500 mt-1">
                                            <i class="far fa-clock mr-1"></i>
                                            <?php echo date('M j, Y', strtotime($post['created_at'])); ?>
                                        </p>
                                    </div>
                                    <span class="px-3 py-1 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                        Published
                                    </span>
                                </div>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <div class="text-center py-6">
                                <i class="fas fa-blog text-gray-300 text-4xl mb-3"></i>
                                <p class="text-gray-500">No blog posts found.</p>
                                <p class="text-sm text-gray-400 mt-1">Create your first blog post to get started</p>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>

            <!-- Right Column - Quick Actions & Other Recent Activities -->
            <div class="space-y-8">
                <!-- Quick Actions -->
                <div class="bg-white rounded-lg shadow-md p-6 animate-fade-in">
                    <h2 class="text-xl font-bold text-gray-800 mb-6 flex items-center">
                        <i class="fas fa-bolt text-orange-500 mr-2"></i>
                        Quick Actions
                    </h2>
                    
                    <div class="space-y-4">
                        <a href="students.php?action=add" class="flex items-center p-4 border border-gray-200 rounded-lg hover:bg-blue-50 transition-colors group">
                            <div class="bg-blue-100 p-3 rounded-full mr-4 group-hover:bg-blue-200 transition-colors">
                                <i class="fas fa-user-plus text-blue-500"></i>
                            </div>
                            <div>
                                <h4 class="font-semibold text-gray-800">Add New Student</h4>
                                <p class="text-sm text-gray-600">Register a new student</p>
                            </div>
                        </a>

                        <a href="teachers.php?action=add" class="flex items-center p-4 border border-gray-200 rounded-lg hover:bg-green-50 transition-colors group">
                            <div class="bg-green-100 p-3 rounded-full mr-4 group-hover:bg-green-200 transition-colors">
                                <i class="fas fa-chalkboard-teacher text-green-500"></i>
                            </div>
                            <div>
                                <h4 class="font-semibold text-gray-800">Add New Teacher</h4>
                                <p class="text-sm text-gray-600">Register a new teacher</p>
                            </div>
                        </a>

                        <a href="blog/add-post.php" class="flex items-center p-4 border border-gray-200 rounded-lg hover:bg-pink-50 transition-colors group">
                            <div class="bg-pink-100 p-3 rounded-full mr-4 group-hover:bg-pink-200 transition-colors">
                                <i class="fas fa-edit text-pink-500"></i>
                            </div>
                            <div>
                                <h4 class="font-semibold text-gray-800">Write Blog Post</h4>
                                <p class="text-sm text-gray-600">Create new blog content</p>
                            </div>
                        </a>

                        <a href="downloads/add-download.php" class="flex items-center p-4 border border-gray-200 rounded-lg hover:bg-teal-50 transition-colors group">
                            <div class="bg-teal-100 p-3 rounded-full mr-4 group-hover:bg-teal-200 transition-colors">
                                <i class="fas fa-upload text-teal-500"></i>
                            </div>
                            <div>
                                <h4 class="font-semibold text-gray-800">Add Download</h4>
                                <p class="text-sm text-gray-600">Upload new resources</p>
                            </div>
                        </a>

                        <a href="settings.php" class="flex items-center p-4 border border-gray-200 rounded-lg hover:bg-purple-50 transition-colors group">
                            <div class="bg-purple-100 p-3 rounded-full mr-4 group-hover:bg-purple-200 transition-colors">
                                <i class="fas fa-cog text-purple-500"></i>
                            </div>
                            <div>
                                <h4 class="font-semibold text-gray-800">System Settings</h4>
                                <p class="text-sm text-gray-600">Manage system configuration</p>
                            </div>
                        </a>
                    </div>
                </div>

                <!-- Recent Downloads -->
                <div class="bg-white rounded-lg shadow-md p-6 animate-fade-in">
                    <div class="flex justify-between items-center mb-6">
                        <h2 class="text-xl font-bold text-gray-800 flex items-center">
                            <i class="fas fa-download text-teal-500 mr-2"></i>
                            Recent Downloads
                        </h2>
                        <a href="downloads/manage-downloads.php" class="text-blue-600 hover:text-blue-800 text-sm font-medium">View All</a>
                    </div>
                    
                    <div class="space-y-3">
                        <?php if (count($recent_downloads) > 0): ?>
                            <?php foreach ($recent_downloads as $download): ?>
                                <div class="recent-item p-3 border border-gray-200 rounded-lg">
                                    <h4 class="font-semibold text-gray-800 text-sm"><?php echo htmlspecialchars($download['title'] ?? 'N/A'); ?></h4>
                                    <p class="text-xs text-gray-500 mt-1">
                                        <i class="far fa-clock mr-1"></i>
                                        <?php echo date('M j, Y', strtotime($download['created_at'])); ?>
                                    </p>
                                </div>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <div class="text-center py-4">
                                <i class="fas fa-download text-gray-300 text-2xl mb-2"></i>
                                <p class="text-gray-500 text-sm">No downloads available</p>
                                <p class="text-xs text-gray-400 mt-1">Upload files to make them available</p>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>

                <!-- System Info -->
                <div class="bg-white rounded-lg shadow-md p-6 animate-fade-in">
                    <h2 class="text-xl font-bold text-gray-800 mb-4 flex items-center">
                        <i class="fas fa-info-circle text-cyan-500 mr-2"></i>
                        System Info
                    </h2>
                    
                    <div class="space-y-3 text-sm">
                        <div class="flex justify-between items-center">
                            <span class="text-gray-600">PHP Version:</span>
                            <span class="font-medium"><?php echo phpversion(); ?></span>
                        </div>
                        <div class="flex justify-between items-center">
                            <span class="text-gray-600">Server Time:</span>
                            <span class="font-medium"><?php echo date('H:i:s'); ?></span>
                        </div>
                        <div class="flex justify-between items-center">
                            <span class="text-gray-600">Admin Role:</span>
                            <span class="font-medium capitalize"><?php echo $_SESSION['admin_role']; ?></span>
                        </div>
                        <div class="flex justify-between items-center">
                            <span class="text-gray-600">Total Modules:</span>
                            <span class="font-medium">12 Active</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Footer -->
    <?php include '../includes/footer.php'; ?>

    <script>
        // Auto refresh time every minute
        function updateTime() {
            const now = new Date();
            const timeString = now.toLocaleTimeString('en-US', { 
                hour: '2-digit', 
                minute: '2-digit',
                second: '2-digit',
                hour12: true 
            });
            document.querySelector('.bg-blue-100 .fa-clock').parentElement.innerHTML = 
                `<i class="fas fa-clock mr-1"></i>${timeString}`;
        }

        // Update time immediately and then every minute
        updateTime();
        setInterval(updateTime, 60000);

        // Add hover effects to stat cards
        document.addEventListener('DOMContentLoaded', function() {
            const statCards = document.querySelectorAll('.stat-card');
            statCards.forEach(card => {
                card.addEventListener('mouseenter', function() {
                    this.style.transform = 'translateY(-5px)';
                });
                card.addEventListener('mouseleave', function() {
                    this.style.transform = 'translateY(0)';
                });
            });
        });
    </script>
</body>
</html>