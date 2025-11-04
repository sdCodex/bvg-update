<?php
// Check if session is already started
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Debug: Check session data
error_log("Auth check - Session data: " . print_r($_SESSION, true));

// Check if admin is logged in
if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    error_log("Auth failed - Redirecting to login");
    header("Location: login.php");
    exit;
}

// Simple permission check function
function hasPermission($module) {
    // Super admin has all permissions
    if (($_SESSION['admin_role'] ?? '') === 'super_admin') {
        return true;
    }
    
    // Check permissions from session
    if (!isset($_SESSION['admin_permissions'])) {
        return false;
    }
    
    $permissions = explode(',', $_SESSION['admin_permissions']);
    return in_array($module, $permissions);
}

// Get current module based on script name
function getCurrentModule() {
    $script_name = $_SERVER['SCRIPT_NAME'];
    
    if (strpos($script_name, 'blog') !== false) return 'blog';
    if (strpos($script_name, 'downloads') !== false) return 'downloads';
    if (strpos($script_name, 'question-papers') !== false) return 'question_papers';
    if (strpos($script_name, 'inspiration') !== false) return 'inspiration';
    if (strpos($script_name, 'students') !== false) return 'students';
    if (strpos($script_name, 'teachers') !== false) return 'teachers';
    if (strpos($script_name, 'applications') !== false) return 'applications';
    if (strpos($script_name, 'settings') !== false) return 'settings';
    
    return 'dashboard';
}

// Check permission for current page (optional - you can remove if not needed)
$current_module = getCurrentModule();
if (!hasPermission($current_module)) {
    $_SESSION['error'] = "You don't have permission to access this module";
    header("Location: dashboard.php");
    exit;
}
?>