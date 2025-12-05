<?php
require_once '../../includes/db.php';

function sendNewPostNotification($post_id)
{
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
        <meta name=\"language\" content=\"English\">
        <meta name=\"revisit-after\" content=\"7 days\">
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
        $headers .= "Content-type: text/html; charset=UTF-8" . "\r\n";

        mail($email, $subject, $message, $headers);
    }

    return true;
}
