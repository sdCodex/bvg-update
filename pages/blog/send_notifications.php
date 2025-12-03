<?php
require_once '../../includes/db.php';

function sendNewPostNotification($post_id) {
    global $pdo, $base_url;
    
    // Get post details
    $post_stmt = $pdo->prepare("
        SELECT bp.*, bc.name as category_name 
        FROM blog_posts bp 
        LEFT JOIN blog_categories bc ON bp.category_id = bc.id 
        WHERE bp.id = ?
    ");
    $post_stmt->execute([$post_id]);
    $post = $post_stmt->fetch();
    
    if (!$post) return false;
    
    // Get all verified subscribers
    $subscribers_stmt = $pdo->query("SELECT email FROM blog_subscribers WHERE is_verified = TRUE AND is_active = TRUE");
    $subscribers = $subscribers_stmt->fetchAll(PDO::FETCH_COLUMN);
    
    if (empty($subscribers)) return true;
    
    $post_url = $base_url . "/pages/blog/post.php?slug=" . $post['slug'];
    
    $subject = "New Blog Post: " . $post['title'];
    
    foreach ($subscribers as $email) {
        $message = "
        <html>
        <head>
            <title>New Blog Post</title>
            <!-- 🧩 SEO Optimization -->
<meta name="description" content="Bhaktivedanta Gurukul School of Excellence blends modern education with traditional Vedic values for holistic student development. Enroll now for spiritual and academic excellence.">
<meta name="keywords" content="Bhaktivedanta Gurukul, Gurukul School, Vedic Education, Spiritual Learning, Best School in India, Holistic Development, Education with Values">
<meta name="author" content="Bhaktivedanta Gurukul School of Excellence">
<meta name="robots" content="index, follow">
<meta name="language" content="English">
<meta name="revisit-after" content="7 days">

<!-- 🔗 Canonical (Avoid Duplicate URLs in Google) -->
<link rel="canonical" href="https://bhaktivedantagurukul.com/">

<!-- 🧠 Open Graph for Social Media -->
<meta property="og:title" content="Bhaktivedanta Gurukul School of Excellence | Modern & Vedic Education">
<meta property="og:description" content="Empowering students through modern education combined with ancient Vedic wisdom.">
<meta property="og:image" content="<?php echo $base_url; ?>/images/bvgBanner.jpg">
<meta property="og:url" content="https://bhaktivedantagurukul.com/">
<meta property="og:type" content="website">
<meta property="og:site_name" content="Bhaktivedanta Gurukul">

<!-- 🐦 Twitter Card -->
<meta name="twitter:card" content="summary_large_image">
<meta name="twitter:title" content="Bhaktivedanta Gurukul School of Excellence">
<meta name="twitter:description" content="A unique blend of modern academics and spiritual learning.">
<meta name="twitter:image" content="<?php echo $base_url; ?>/images/bvgBanner.jpg">

<!-- 🎨 Theme Color (Mobile Tab Color) -->
<meta name="theme-color" content="#DC143C">

<!-- ⚡ PERFORMANCE OPTIMIZATION -->
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@400;600;700&family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">


<!-- 🖼️ Favicon -->
<link rel="icon" type="image/png" href="<?php echo $base_url; ?>/images/bvgLogo.png">

        </head>
        <body style='font-family: Arial, sans-serif; line-height: 1.6;'>
            <div style='max-width: 600px; margin: 0 auto; padding: 20px; border: 1px solid #e0e0e0; border-radius: 10px;'>
                <div style='background: linear-gradient(135deg, #003366 0%, #004080 100%); padding: 20px; text-align: center; border-radius: 8px; margin-bottom: 20px;'>
                    <h1 style='color: white; margin: 0;'>Bhaktivedanta Gurukul</h1>
                </div>
                
                <h2 style='color: #003366;'>New Blog Post Published</h2>
                
                <div style='background: #f8f9fa; padding: 15px; border-radius: 8px; margin: 15px 0;'>
                    <h3 style='color: #003366; margin-top: 0;'>{$post['title']}</h3>
                    <p style='color: #666;'>{$post['excerpt']}</p>
                    <p><strong>Category:</strong> {$post['category_name']}</p>
                </div>
                
                <a href='{$post_url}' style='background: #800000; color: white; padding: 12px 24px; text-decoration: none; border-radius: 5px; display: inline-block; margin: 10px 0;'>
                    Read Full Post
                </a>
                
                <hr style='border: none; border-top: 1px solid #e0e0e0; margin: 20px 0;'>
                
                <p style='color: #666; font-size: 14px;'>
                    You received this email because you subscribed to updates from Bhaktivedanta Gurukul Blog.<br>
                    <a href='{$base_url}/pages/blog/unsubscribe.php?email=" . urlencode($email) . "' style='color: #800000;'>Unsubscribe</a>
                </p>
            </div>
        </body>
        </html>
        ";
        
        $headers = "MIME-Version: 1.0" . "\r\n";
        $headers .= "Content-type:text/html;charset=UTF-8" . "\r\n";
        $headers .= "From: no-reply@bhaktivedantagurukul.edu" . "\r\n";
        
        mail($email, $subject, $message, $headers);
    }
    
    // Mark notification as sent
    $update_stmt = $pdo->prepare("UPDATE blog_posts SET notification_sent = TRUE WHERE id = ?");
    $update_stmt->execute([$post_id]);
    
    return true;
}

// You can call this function when a new post is published
// Example usage: sendNewPostNotification(123);
?>