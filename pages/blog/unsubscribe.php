<?php
require_once '../../includes/db.php';

$email = isset($_GET['email']) ? $_GET['email'] : '';
$success = false;

if (!empty($email)) {
    $stmt = $pdo->prepare("UPDATE blog_subscribers SET is_active = FALSE WHERE email = ?");
    if ($stmt->execute([$email])) {
        $success = true;
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Unsubscribe | Bhaktivedanta Gurukul</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>
<body class="bg-gray-50 font-sans">
    <?php include '../../includes/header.php'; ?>
    
    <section class="min-h-screen py-16 flex items-center justify-center">
        <div class="container mx-auto px-4">
            <div class="max-w-md mx-auto bg-white rounded-lg shadow-lg p-8 text-center">
                <div class="w-16 h-16 <?php echo $success ? 'bg-green-100' : 'bg-red-100'; ?> rounded-full flex items-center justify-center mx-auto mb-4">
                    <i class="fas <?php echo $success ? 'fa-check text-green-600' : 'fa-times text-red-600'; ?> text-2xl"></i>
                </div>
                <h1 class="text-2xl font-bold text-primary mb-4">
                    <?php echo $success ? 'Unsubscribed Successfully' : 'Unsubscribe Failed'; ?>
                </h1>
                <p class="text-gray-600 mb-6">
                    <?php 
                    if ($success) {
                        echo "You have been unsubscribed from our newsletter. You will no longer receive email notifications.";
                    } else {
                        echo "Sorry, we couldn't process your unsubscribe request. Please try again or contact us.";
                    }
                    ?>
                </p>
                <a href="<?php echo $base_url; ?>/pages/blog/" class="bg-primary text-white px-6 py-2 rounded-lg hover:bg-blue-800 transition-colors duration-300 inline-block">
                    Back to Blog
                </a>
            </div>
        </div>
    </section>
    
    <?php include '../../includes/footer.php'; ?>
</body>
</html>