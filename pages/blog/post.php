<?php
require_once '../../includes/db.php';
include_once '../../includes/header.php';

// Get slug from URL
$slug = isset($_GET['slug']) ? $_GET['slug'] : '';

if (empty($slug)) {
    header("Location: index.php");
    exit();
}

// Get post by slug
$stmt = $pdo->prepare("
    SELECT bp.*, bc.name as category_name 
    FROM blog_posts bp 
    LEFT JOIN blog_categories bc ON bp.category_id = bc.id 
    WHERE bp.slug = ? AND bp.is_published = TRUE
");
$stmt->execute([$slug]);
$post = $stmt->fetch(PDO::FETCH_ASSOC);

// If post not found, redirect to 404 or blog index
if (!$post) {
    header("Location: index.php?error=post_not_found");
    exit();
}

// Update view count
$update_view_stmt = $pdo->prepare("UPDATE blog_posts SET view_count = view_count + 1 WHERE id = ?");
$update_view_stmt->execute([$post['id']]);

// Get related posts (same category)
$related_stmt = $pdo->prepare("
    SELECT bp.*, bc.name as category_name 
    FROM blog_posts bp 
    LEFT JOIN blog_categories bc ON bp.category_id = bc.id 
    WHERE bp.category_id = ? AND bp.id != ? AND bp.is_published = TRUE 
    ORDER BY bp.published_at DESC 
    LIMIT 3
");
$related_stmt->execute([$post['category_id'], $post['id']]);
$related_posts = $related_stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($post['title']); ?> | Bhaktivedanta Gurukul</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        .post-content h2 {
            font-size: 1.5rem;
            font-weight: bold;
            color: #003366;
            margin: 1.5rem 0 1rem 0;
        }
        
        .post-content h3 {
            font-size: 1.25rem;
            font-weight: bold;
            color: #003366;
            margin: 1.25rem 0 0.75rem 0;
        }
        
        .post-content p {
            margin-bottom: 1rem;
            line-height: 1.7;
        }
        
        .post-content ul, .post-content ol {
            margin: 1rem 0;
            padding-left: 2rem;
        }
        
        .post-content li {
            margin-bottom: 0.5rem;
            line-height: 1.6;
        }
        
        .post-content blockquote {
            border-left: 4px solid #800000;
            padding-left: 1.5rem;
            margin: 1.5rem 0;
            font-style: italic;
            color: #666;
        }
        
        .fade-in {
            animation: fadeIn 0.6s ease-in-out;
        }
        
        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(20px); }
            to { opacity: 1; transform: translateY(0); }
        }
    </style>
</head>
<body class="bg-gray-50 font-sans">
    <!-- Hero Section -->
    <section class="bg-gradient-to-r from-primary to-primary/90 text-white py-12 lg:py-16">
        <div class="container mx-auto px-4">
            <div class="max-w-4xl mx-auto text-center fade-in">
                <nav class="text-sm mb-6 opacity-80">
                    <a href="<?php echo $base_url; ?>/index.php" class="hover:text-accent transition-colors">Home</a> 
                    <span class="mx-2">/</span>
                    <a href="<?php echo $base_url; ?>/pages/blog/index.php" class="hover:text-accent transition-colors">Blog</a>
                    <span class="mx-2">/</span>
                    <span class="text-accent"><?php echo htmlspecialchars($post['category_name']); ?></span>
                </nav>
                
                <h1 class="text-3xl lg:text-4xl font-bold font-serif mb-6 leading-tight">
                    <?php echo htmlspecialchars($post['title']); ?>
                </h1>
                
                <div class="flex flex-wrap justify-center items-center gap-4 text-sm opacity-90">
                    <span class="flex items-center">
                        <i class="fas fa-calendar-alt mr-2"></i>
                        <?php echo date('F j, Y', strtotime($post['published_at'])); ?>
                    </span>
                    <span class="flex items-center">
                        <i class="fas fa-clock mr-2"></i>
                        <?php echo round(str_word_count(strip_tags($post['content'])) / 200); ?> min read
                    </span>
                    <span class="flex items-center">
                        <i class="fas fa-eye mr-2"></i>
                        <?php echo ($post['view_count'] + 1); ?> views
                    </span>
                    <span class="bg-accent px-3 py-1 rounded-full text-xs font-semibold">
                        <?php echo htmlspecialchars($post['category_name']); ?>
                    </span>
                </div>
            </div>
        </div>
    </section>

    <!-- Featured Image -->
    <?php if($post['featured_image']): ?>
    <section class="py-8 fade-in">
        <div class="container mx-auto px-4">
            <div class="max-w-4xl mx-auto">
                <img 
                    src="<?php echo $base_url . $post['featured_image']; ?>" 
                    alt="<?php echo htmlspecialchars($post['title']); ?>"
                    class="w-full h-64 lg:h-96 object-cover rounded-xl shadow-lg"
                >
            </div>
        </div>
    </section>
    <?php endif; ?>

    <!-- Post Content -->
    <section class="py-8 lg:py-12">
        <div class="container mx-auto px-4">
            <div class="max-w-4xl mx-auto">
                <div class="bg-white rounded-xl shadow-lg p-6 lg:p-8 fade-in">
                    <div class="post-content text-gray-700 leading-relaxed">
                        <?php 
                        // Display the content with proper HTML formatting
                        echo nl2br(htmlspecialchars_decode($post['content'])); 
                        ?>
                    </div>
                    
                    <!-- Post Meta Footer -->
                    <div class="mt-12 pt-8 border-t border-gray-200">
                        <div class="flex flex-wrap justify-between items-center text-sm text-gray-600">
                            <div class="flex items-center space-x-4">
                                <span class="flex items-center">
                                    <i class="fas fa-calendar-alt mr-2"></i>
                                    Published: <?php echo date('M j, Y', strtotime($post['published_at'])); ?>
                                </span>
                                <span class="flex items-center">
                                    <i class="fas fa-eye mr-2"></i>
                                    <?php echo ($post['view_count'] + 1); ?> views
                                </span>
                            </div>
                            <div class="flex space-x-2 mt-4 lg:mt-0">
                                <span class="text-xs text-gray-500">Share:</span>
                                <a href="#" class="text-primary hover:text-accent transition-colors">
                                    <i class="fab fa-facebook"></i>
                                </a>
                                <a href="#" class="text-primary hover:text-accent transition-colors">
                                    <i class="fab fa-twitter"></i>
                                </a>
                                <a href="#" class="text-primary hover:text-accent transition-colors">
                                    <i class="fab fa-linkedin"></i>
                                </a>
                                <a href="#" class="text-primary hover:text-accent transition-colors">
                                    <i class="fab fa-whatsapp"></i>
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Related Posts -->
    <?php if(!empty($related_posts)): ?>
    <section class="py-12 bg-white">
        <div class="container mx-auto px-4">
            <div class="max-w-6xl mx-auto">
                <h2 class="text-2xl lg:text-3xl font-bold text-primary font-serif mb-8 text-center">Related Articles</h2>
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
                    <?php foreach($related_posts as $related_post): ?>
                    <article class="bg-gray-50 rounded-xl overflow-hidden shadow-lg hover:shadow-xl transition-shadow duration-300 group">
                        <?php if($related_post['featured_image']): ?>
                        <div class="overflow-hidden">
                            <img 
                                src="<?php echo $base_url . $related_post['featured_image']; ?>" 
                                alt="<?php echo htmlspecialchars($related_post['title']); ?>"
                                class="w-full h-48 object-cover group-hover:scale-105 transition-transform duration-500"
                            >
                        </div>
                        <?php else: ?>
                        <div class="bg-gradient-to-br from-primary/10 to-accent/10 h-48 flex items-center justify-center">
                            <i class="fas fa-newspaper text-4xl text-primary/40"></i>
                        </div>
                        <?php endif; ?>
                        
                        <div class="p-6">
                            <span class="bg-accent text-white px-3 py-1 rounded-full text-xs font-semibold inline-block mb-3">
                                <?php echo htmlspecialchars($related_post['category_name']); ?>
                            </span>
                            
                            <h3 class="text-lg font-bold text-primary mb-3 group-hover:text-accent transition-colors duration-300 line-clamp-2">
                                <a href="<?php echo $base_url; ?>/pages/blog/post.php?slug=<?php echo $related_post['slug']; ?>">
                                    <?php echo htmlspecialchars($related_post['title']); ?>
                                </a>
                            </h3>
                            
                            <p class="text-gray-600 text-sm mb-4 line-clamp-2">
                                <?php echo htmlspecialchars($related_post['excerpt']); ?>
                            </p>
                            
                            <div class="flex justify-between items-center text-sm text-gray-500">
                                <span class="flex items-center">
                                    <i class="fas fa-calendar-alt mr-2"></i>
                                    <?php echo date('M j, Y', strtotime($related_post['published_at'])); ?>
                                </span>
                                <a href="<?php echo $base_url; ?>/pages/blog/post.php?slug=<?php echo $related_post['slug']; ?>" 
                                   class="text-accent hover:text-primary font-medium flex items-center transition-colors duration-300">
                                    Read <i class="fas fa-arrow-right ml-1 text-xs"></i>
                                </a>
                            </div>
                        </div>
                    </article>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>
    </section>
    <?php endif; ?>

    <!-- Call to Action -->
    <section class="py-16 bg-gradient-to-r from-primary to-accent text-white">
        <div class="container mx-auto px-4 text-center">
            <div class="max-w-3xl mx-auto fade-in">
                <h2 class="text-3xl lg:text-4xl font-bold font-serif mb-6">Explore More Stories</h2>
                <p class="text-xl opacity-90 mb-8 leading-relaxed">
                    Discover more inspiring stories and updates from our Gurukul community
                </p>
                <div class="flex flex-col sm:flex-row gap-4 justify-center">
                    <a href="<?php echo $base_url; ?>/pages/blog/index.php" 
                       class="bg-white text-primary px-8 py-4 rounded-xl font-semibold hover:bg-gray-100 transition-all duration-300 transform hover:scale-105 shadow-lg">
                        <i class="fas fa-newspaper mr-2"></i>View All Posts
                    </a>
                    <a href="<?php echo $base_url; ?>/contact.php" 
                       class="border-2 border-white text-white px-8 py-4 rounded-xl font-semibold hover:bg-white hover:text-primary transition-all duration-300 transform hover:scale-105">
                        <i class="fas fa-envelope mr-2"></i>Contact Us
                    </a>
                </div>
            </div>
        </div>
    </section>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Add line-clamp utility
            const style = document.createElement('style');
            style.textContent = `
                .line-clamp-2 {
                    display: -webkit-box;
                    -webkit-line-clamp: 2;
                    -webkit-box-orient: vertical;
                    overflow: hidden;
                }
            `;
            document.head.appendChild(style);
            
            // Add social sharing functionality
            const currentUrl = encodeURIComponent(window.location.href);
            const title = encodeURIComponent(document.title);
            
            document.querySelectorAll('a[href="#"]').forEach(link => {
                link.addEventListener('click', function(e) {
                    e.preventDefault();
                    const platform = this.querySelector('i').className;
                    
                    let shareUrl = '';
                    if (platform.includes('facebook')) {
                        shareUrl = `https://www.facebook.com/sharer/sharer.php?u=${currentUrl}`;
                    } else if (platform.includes('twitter')) {
                        shareUrl = `https://twitter.com/intent/tweet?url=${currentUrl}&text=${title}`;
                    } else if (platform.includes('linkedin')) {
                        shareUrl = `https://www.linkedin.com/sharing/share-offsite/?url=${currentUrl}`;
                    } else if (platform.includes('whatsapp')) {
                        shareUrl = `https://wa.me/?text=${title}%20${currentUrl}`;
                    }
                    
                    if (shareUrl) {
                        window.open(shareUrl, '_blank', 'width=600,height=400');
                    }
                });
            });
        });
    </script>

    <?php include '../../includes/footer.php'; ?>
</body>
</html>