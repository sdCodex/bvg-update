<?php
require_once '../../includes/db.php';

$email = isset($_GET['email']) ? $_GET['email'] : '';
$token = isset($_GET['token']) ? $_GET['token'] : '';

if (!empty($email) && !empty($token)) {
    // Verify the token
    $stmt = $pdo->prepare("SELECT id FROM blog_subscribers WHERE email = ? AND verification_token = ? AND is_verified = FALSE");
    $stmt->execute([$email, $token]);
    $subscriber = $stmt->fetch();
    
    if ($subscriber) {
        // Mark as verified
        $update_stmt = $pdo->prepare("UPDATE blog_subscribers SET is_verified = TRUE, verification_token = NULL WHERE id = ?");
        if ($update_stmt->execute([$subscriber['id']])) {
            $message = "Thank you! Your email has been verified. You will now receive notifications for new blog posts.";
        } else {
            $message = "Sorry, there was an error verifying your email. Please try again.";
        }
    } else {
        $message = "Invalid verification link or email already verified.";
    }
} else {
    $message = "Invalid verification link.";
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Email Verification | Bhaktivedanta Gurukul</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>
<body class="bg-gray-50 font-sans">
    <?php include '../../includes/header.php'; ?>
    
    <section class="min-h-screen py-16 flex items-center justify-center">
        <div class="container mx-auto px-4">
            <div class="max-w-md mx-auto bg-white rounded-lg shadow-lg p-8 text-center">
                <div class="w-16 h-16 bg-green-100 rounded-full flex items-center justify-center mx-auto mb-4">
                    <i class="fas fa-check text-green-600 text-2xl"></i>
                </div>
                <h1 class="text-2xl font-bold text-primary mb-4">Email Verification</h1>
                <p class="text-gray-600 mb-6"><?php echo $message; ?></p>
                <a href="<?php echo $base_url; ?>/pages/blog/" class="bg-primary text-white px-6 py-2 rounded-lg hover:bg-blue-800 transition-colors duration-300 inline-block">
                    Back to Blog
                </a>
            </div>
        </div>
    </section>
    
    <?php include '../../includes/footer.php'; ?>
</body>
</html>