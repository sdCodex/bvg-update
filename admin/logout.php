<?php
// Check if session is already started
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$base_url = '/Gurkul_Project';

// If logout is confirmed, destroy session
if (isset($_GET['confirm']) && $_GET['confirm'] === 'true') {
    $admin_name = $_SESSION['admin_name'] ?? 'Administrator';
    
    // Complete session cleanup
    session_unset();
    session_destroy();
    session_write_close();
    setcookie(session_name(), '', 0, '/');
    
    // Redirect to index page after logout
    header('Location: ../index.php');
    exit;
}

// If not logged in, redirect to login
if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    header('Location: login.php');
    exit;
}

// If not confirmed, show logout confirmation page
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Logout - Bhaktivedanta Gurukul Admin</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="icon" type="image/x-icon" href="../images/bvgLogo.png">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        .logout-bg {
            background: linear-gradient(135deg, #1e3a5f 0%, #152642 100%);
            min-height: 100vh;
        }
    </style>
</head>
<body class="logout-bg min-h-screen flex items-center justify-center p-4">
    <div class="bg-white p-8 rounded-lg shadow-xl w-full max-w-md text-center">
        <!-- Logout Icon -->
        <div class="flex justify-center mb-6">
            <div class="w-20 h-20 bg-red-100 rounded-full flex items-center justify-center">
                <i class="fas fa-sign-out-alt text-red-500 text-3xl"></i>
            </div>
        </div>

        <!-- Logout Message -->
        <h2 class="text-2xl font-bold text-gray-800 mb-4">Logout Confirmation</h2>
        <p class="text-gray-600 mb-2">Are you sure you want to logout?</p>
        <p class="text-sm text-gray-500 mb-6">
            You are logged in as: <strong><?php echo htmlspecialchars($_SESSION['admin_name'] ?? 'Administrator'); ?></strong>
        </p>

        <!-- Action Buttons -->
        <div class="flex space-x-4">
            <a href="?confirm=true" 
               class="flex-1 bg-red-600 text-white py-3 px-4 rounded-lg font-semibold hover:bg-red-700 transition-colors duration-300 flex items-center justify-center">
                <i class="fas fa-check mr-2"></i> Yes, Logout
            </a>
            <a href="dashboard.php" 
               class="flex-1 bg-gray-600 text-white py-3 px-4 rounded-lg font-semibold hover:bg-gray-700 transition-colors duration-300 flex items-center justify-center">
                <i class="fas fa-times mr-2"></i> Cancel
            </a>
        </div>

        <!-- Security Info -->
        <div class="mt-6 p-3 bg-blue-50 rounded-lg border border-blue-200">
            <div class="flex items-center justify-center text-sm text-blue-700">
                <i class="fas fa-shield-alt mr-2"></i>
                For security, please close your browser after logout
            </div>
        </div>
    </div>
</body>
</html>