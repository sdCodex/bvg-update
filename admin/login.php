<?php
// Check if session is already started
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Agar already logged in hai toh dashboard redirect karo
if (isset($_SESSION['admin_logged_in']) && $_SESSION['admin_logged_in'] === true) {
    header('Location: dashboard.php');
    exit;
}

$base_url = '/Gurkul_Project';
$error = '';

// Include db.php from includes folder
require_once '../includes/db.php';

// Login attempts tracking - initialize if not set
if (!isset($_SESSION['login_attempts'])) {
    $_SESSION['login_attempts'] = 0;
    $_SESSION['last_attempt_time'] = time();
}

// Check if user came from logout
$from_logout = isset($_GET['logout']) && $_GET['logout'] === 'true';

// Input fields will always be empty by default for security
$username_value = '';
$password_value = '';

// Check if form is submitted
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Check if user is locked out
    if ($_SESSION['login_attempts'] >= 5) {
        $lockout_time = 300; // 5 minutes
        $time_since_last_attempt = time() - $_SESSION['last_attempt_time'];
        
        if ($time_since_last_attempt < $lockout_time) {
            $remaining_time = $lockout_time - $time_since_last_attempt;
            $error = "Too many failed attempts. Please try again in " . ceil($remaining_time / 60) . " minutes.";
        } else {
            // Reset attempts after lockout period
            $_SESSION['login_attempts'] = 0;
            $_SESSION['last_attempt_time'] = time();
        }
    }

    if (empty($error)) {
        $username = trim($_POST['username'] ?? '');
        $password = trim($_POST['password'] ?? '');

        // Input validation
        if (empty($username) || empty($password)) {
            $error = "Please enter both username and password!";
            $_SESSION['login_attempts']++;
            $_SESSION['last_attempt_time'] = time();
        } else {
            try {
                // Check for super admin credentials first (without database query)
                if ($username === 'Bvg_gurkul!superAdmin_admin_1002' && $password === 'Gurkul@admin!bvg_superAdmin2025') {
                    // Super admin login successful
                    $_SESSION['admin_logged_in'] = true;
                    $_SESSION['admin_id'] = 0; // Special ID for super admin
                    $_SESSION['admin_name'] = 'Super Administrator';
                    $_SESSION['admin_username'] = $username;
                    $_SESSION['admin_email'] = 'superadmin@bhaktivedantagurukul.edu';
                    $_SESSION['admin_role'] = 'super_admin';
                    $_SESSION['admin_permissions'] = 'dashboard,students,teachers,applications,blog,downloads,question_papers,inspiration,settings,user_management';
                    
                    // Reset login attempts on successful login
                    $_SESSION['login_attempts'] = 0;
                    
                    // Regenerate session ID for security
                    session_regenerate_id(true);

                    // Redirect to dashboard
                    header('Location: dashboard.php');
                    exit;
                } else {
                    // Regular admin authentication with prepared statements
                    $stmt = $pdo->prepare("SELECT id, username, password_hash, email, full_name, role, is_active, permissions, failed_attempts, last_failed_attempt FROM admins WHERE username = ? OR email = ?");
                    $stmt->execute([$username, $username]);
                    $admin = $stmt->fetch(PDO::FETCH_ASSOC);

                    if ($admin) {
                        // Check if admin is active
                        if (!$admin['is_active']) {
                            $error = "Your account is deactivated. Please contact administrator.";
                            $_SESSION['login_attempts']++;
                            $_SESSION['last_attempt_time'] = time();
                            
                            // Update failed attempts in database
                            $update_failed = $pdo->prepare("UPDATE admins SET failed_attempts = failed_attempts + 1, last_failed_attempt = NOW() WHERE id = ?");
                            $update_failed->execute([$admin['id']]);
                        } 
                        // SECURE PASSWORD VERIFICATION
                        elseif (password_verify($password, $admin['password_hash'])) {
                            // Login successful - SET ALL SESSION VARIABLES
                            $_SESSION['admin_logged_in'] = true;
                            $_SESSION['admin_id'] = $admin['id'];
                            $_SESSION['admin_name'] = $admin['full_name'] ?? 'Administrator';
                            $_SESSION['admin_username'] = $admin['username'];
                            $_SESSION['admin_email'] = $admin['email'];
                            $_SESSION['admin_role'] = $admin['role'] ?? 'admin';
                            $_SESSION['admin_permissions'] = $admin['permissions'] ?? 'dashboard,students,teachers,applications,blog,downloads,question_papers,inspiration';
                            
                            // Reset login attempts on successful login
                            $_SESSION['login_attempts'] = 0;
                            
                            // Regenerate session ID for security
                            session_regenerate_id(true);

                            // Update last login and reset failed attempts
                            $update_stmt = $pdo->prepare("UPDATE admins SET last_login = NOW(), failed_attempts = 0, last_failed_attempt = NULL WHERE id = ?");
                            $update_stmt->execute([$admin['id']]);

                            // Redirect to dashboard
                            header('Location: dashboard.php');
                            exit;
                        } else {
                            // Invalid password
                            $_SESSION['login_attempts']++;
                            $_SESSION['last_attempt_time'] = time();
                            $remaining_attempts = 5 - $_SESSION['login_attempts'];
                            
                            // Update failed attempts in database
                            $update_failed = $pdo->prepare("UPDATE admins SET failed_attempts = failed_attempts + 1, last_failed_attempt = NOW() WHERE id = ?");
                            $update_failed->execute([$admin['id']]);
                            
                            if ($remaining_attempts > 0) {
                                $error = "Invalid username or password! {$remaining_attempts} attempt(s) remaining.";
                            } else {
                                $error = "Account temporarily locked. Please try again after 5 minutes.";
                            }
                        }
                    } else {
                        // User not found
                        $_SESSION['login_attempts']++;
                        $_SESSION['last_attempt_time'] = time();
                        $remaining_attempts = 5 - $_SESSION['login_attempts'];
                        
                        if ($remaining_attempts > 0) {
                            $error = "Invalid username or password! {$remaining_attempts} attempt(s) remaining.";
                        } else {
                            $error = "Too many failed attempts. Please try again after 5 minutes.";
                        }
                    }
                }
            } catch (PDOException $e) {
                error_log("Login error: " . $e->getMessage());
                $error = "System error. Please try again later.";
                $_SESSION['login_attempts']++;
                $_SESSION['last_attempt_time'] = time();
            }
        }
    }
}

// Show remaining attempts warning
$attempt_warning = '';
if ($_SESSION['login_attempts'] > 0 && $_SESSION['login_attempts'] < 5) {
    $remaining_attempts = 5 - $_SESSION['login_attempts'];
    $attempt_warning = "Warning: {$remaining_attempts} attempt(s) remaining.";
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Login - Bhaktivedanta Gurukul</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="icon" type="image/x-icon" href="../images/bvgLogo.png">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        .login-bg {
            background: linear-gradient(135deg, #1e3a5f 0%, #152642 100%);
            min-height: 100vh;
        }
        .password-toggle {
            cursor: pointer;
            position: absolute;
            right: 12px;
            top: 50%;
            transform: translateY(-50%);
        }
        .loading-spinner {
            display: none;
        }
    </style>
</head>
<body class="login-bg min-h-screen flex items-center justify-center p-4">
    <div class="bg-white p-8 rounded-lg shadow-xl w-full max-w-md">
        <!-- School Logo and Name -->
        <div class="text-center mb-8">
            <div class="flex justify-center mb-4">
                <div class="w-20 h-20 bg-gradient-to-r from-blue-600 to-blue-800 rounded-full flex items-center justify-center shadow-lg">
                    <i class="fas fa-graduation-cap text-white text-3xl"></i>
                </div>
            </div>
            <h2 class="text-2xl font-bold text-gray-800">Bhaktivedanta Gurukul</h2>
            <p class="text-gray-600 mt-2">Admin Portal Login</p>
        </div>

        <!-- Logout Success Message -->
        <?php if ($from_logout): ?>
            <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-6 flex items-center">
                <i class="fas fa-check-circle mr-2"></i>
                You have been successfully logged out.
            </div>
        <?php endif; ?>

        <!-- Attempt Warning -->
        <?php if (!empty($attempt_warning)): ?>
            <div class="bg-yellow-100 border border-yellow-400 text-yellow-700 px-4 py-3 rounded mb-6 flex items-center">
                <i class="fas fa-exclamation-triangle mr-2"></i>
                <?php echo htmlspecialchars($attempt_warning); ?>
            </div>
        <?php endif; ?>

        <!-- Error Message -->
        <?php if (!empty($error)): ?>
            <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-6 flex items-center">
                <i class="fas fa-exclamation-circle mr-2"></i>
                <?php echo htmlspecialchars($error); ?>
            </div>
        <?php endif; ?>

        <!-- Login Form -->
        <form method="POST" class="space-y-6" id="loginForm">
            <div>
                <label class="block text-gray-700 text-sm font-bold mb-2" for="username">
                    <i class="fas fa-user mr-2 text-blue-500"></i>Username or Email
                </label>
                <input type="text" id="username" name="username" required
                    class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all duration-300"
                    placeholder="Enter your username or email"
                    value="<?php echo htmlspecialchars($username_value); ?>"
                    autocomplete="username"
                    <?php echo ($_SESSION['login_attempts'] >= 5) ? 'disabled' : ''; ?>>
            </div>

            <div class="relative">
                <label class="block text-gray-700 text-sm font-bold mb-2" for="password">
                    <i class="fas fa-lock mr-2 text-blue-500"></i>Password
                </label>
                <input type="password" id="password" name="password" required
                    class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all duration-300 pr-10"
                    placeholder="Enter your password"
                    autocomplete="current-password"
                    <?php echo ($_SESSION['login_attempts'] >= 5) ? 'disabled' : ''; ?>>
                <span class="password-toggle text-gray-400 hover:text-gray-600" onclick="togglePassword()">
                    <i class="fas fa-eye" id="passwordIcon"></i>
                </span>
            </div>

            <button type="submit" id="loginBtn"
                class="w-full bg-blue-600 text-white py-3 px-4 rounded-lg font-semibold hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 transition-all duration-300 flex items-center justify-center disabled:opacity-50 disabled:cursor-not-allowed"
                <?php echo ($_SESSION['login_attempts'] >= 5) ? 'disabled' : ''; ?>>
                <i class="fas fa-sign-in-alt mr-2"></i> 
                <span id="btnText">Login to Dashboard</span>
                <i class="fas fa-spinner fa-spin loading-spinner ml-2"></i>
            </button>
        </form>

        <!-- Security Info -->
        <div class="mt-6 p-3 bg-blue-50 rounded-lg border border-blue-200">
            <div class="flex items-center justify-center text-sm text-blue-700">
                <i class="fas fa-shield-alt mr-2"></i>
                Secure admin authentication system
            </div>
        </div>

        <!-- Lockout Message -->
        <?php if ($_SESSION['login_attempts'] >= 5): ?>
            <div class="mt-4 p-3 bg-red-50 rounded-lg border border-red-200 text-center">
                <div class="flex items-center justify-center text-sm text-red-700">
                    <i class="fas fa-lock mr-2"></i>
                    Account temporarily locked due to multiple failed attempts
                </div>
                <div id="countdown" class="text-sm text-orange-600 font-semibold mt-2"></div>
            </div>
        <?php endif; ?>
    </div>

    <script>
        // Form submission animation
        document.addEventListener('DOMContentLoaded', function() {
            const form = document.getElementById('loginForm');
            const loginBtn = document.getElementById('loginBtn');
            const btnText = document.getElementById('btnText');
            const spinner = document.querySelector('.loading-spinner');
            
            form.addEventListener('submit', function(e) {
                if (!loginBtn.disabled) {
                    btnText.textContent = 'Logging in...';
                    spinner.style.display = 'inline-block';
                    loginBtn.disabled = true;
                }
            });

            // Auto-disable form after lockout
            <?php if ($_SESSION['login_attempts'] >= 5): ?>
                let lockoutTime = <?php echo isset($remaining_time) ? $remaining_time : 300; ?>;
                startCountdown(lockoutTime);
            <?php endif; ?>
        });

        // Password visibility toggle
        function togglePassword() {
            const passwordInput = document.getElementById('password');
            const passwordIcon = document.getElementById('passwordIcon');
            
            if (passwordInput.type === 'password') {
                passwordInput.type = 'text';
                passwordIcon.className = 'fas fa-eye-slash';
            } else {
                passwordInput.type = 'password';
                passwordIcon.className = 'fas fa-eye';
            }
        }

        // Countdown timer for lockout
        function startCountdown(seconds) {
            const countdownElement = document.getElementById('countdown');
            const timer = setInterval(function() {
                const minutes = Math.floor(seconds / 60);
                const remainingSeconds = seconds % 60;
                
                countdownElement.textContent = `Try again in: ${minutes}:${remainingSeconds.toString().padStart(2, '0')}`;
                
                if (seconds <= 0) {
                    clearInterval(timer);
                    countdownElement.textContent = 'You can now try logging in again';
                    countdownElement.className = 'text-sm text-green-600 font-semibold mt-2';
                    
                    // Enable form after countdown
                    setTimeout(() => {
                        window.location.reload();
                    }, 2000);
                }
                seconds--;
            }, 1000);
        }
    </script>
</body>
</html>