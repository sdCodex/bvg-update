<?php
require_once '../../includes/db.php';

// Get page number for pagination
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$limit = 6;
$offset = ($page - 1) * $limit;

// Get total posts count
$total_stmt = $pdo->query("SELECT COUNT(*) FROM blog_posts WHERE is_published = TRUE");
$total_posts = $total_stmt->fetchColumn();
$total_pages = ceil($total_posts / $limit);

// Get featured posts
$featured_stmt = $pdo->query("
    SELECT bp.*, bc.name as category_name 
    FROM blog_posts bp 
    LEFT JOIN blog_categories bc ON bp.category_id = bc.id 
    WHERE bp.is_published = TRUE AND bp.is_featured = TRUE 
    ORDER BY bp.published_at DESC 
    LIMIT 3
");
$featured_posts = $featured_stmt->fetchAll(PDO::FETCH_ASSOC);

// Get latest posts
$posts_stmt = $pdo->prepare("
    SELECT bp.*, bc.name as category_name 
    FROM blog_posts bp 
    LEFT JOIN blog_categories bc ON bp.category_id = bc.id 
    WHERE bp.is_published = TRUE 
    ORDER BY bp.published_at DESC 
    LIMIT ? OFFSET ?
");
$posts_stmt->bindValue(1, $limit, PDO::PARAM_INT);
$posts_stmt->bindValue(2, $offset, PDO::PARAM_INT);
$posts_stmt->execute();
$posts = $posts_stmt->fetchAll(PDO::FETCH_ASSOC);
?>
<?php include '../../includes/header.php'; ?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Blog - Latest News & Updates | Bhaktivedanta Gurukul</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        .blog-hero {
            background: linear-gradient(135deg, #003366 0%, #004080 100%);
        }
        
        .featured-post, .post-card {
            transition: all 0.3s ease;
            background: white;
            border-radius: 12px;
            overflow: hidden;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.08);
        }
        
        .featured-post:hover, .post-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.15);
        }
        
        .category-tag {
            background: #800000;
            color: white;
        }
        
        .pagination a {
            transition: all 0.3s ease;
            border: 1px solid #e5e7eb;
        }
        
        .pagination a:hover {
            background: #003366;
            color: white;
            border-color: #003366;
        }
        
        .pagination a.active {
            background: #800000;
            color: white;
            border-color: #800000;
        }
        
        .fade-in {
            animation: fadeIn 0.6s ease-in-out;
        }
        
        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(20px); }
            to { opacity: 1; transform: translateY(0); }
        }
        
        .stagger-animation > * {
            opacity: 0;
            animation: fadeIn 0.6s ease-in-out forwards;
        }
        
        .stagger-animation > *:nth-child(1) { animation-delay: 0.1s; }
        .stagger-animation > *:nth-child(2) { animation-delay: 0.2s; }
        .stagger-animation > *:nth-child(3) { animation-delay: 0.3s; }
        .stagger-animation > *:nth-child(4) { animation-delay: 0.4s; }
        .stagger-animation > *:nth-child(5) { animation-delay: 0.5s; }
        .stagger-animation > *:nth-child(6) { animation-delay: 0.6s; }
    </style>
</head>
<body class="bg-gray-50 font-sans">
    <!-- Hero Section -->
    <section class="blog-hero text-white py-16 lg:py-24">
        <div class="container mx-auto px-4 text-center fade-in">
            <h1 class="text-4xl lg:text-5xl font-bold font-serif mb-6">School Blog & News</h1>
            <p class="text-xl lg:text-2xl opacity-90 max-w-3xl mx-auto leading-relaxed">
                Stay updated with the latest happenings, achievements, and announcements from our Gurukul
            </p>
            <div class="mt-8 flex justify-center space-x-4">
                <div class="w-4 h-4 bg-white/30 rounded-full animate-pulse"></div>
                <div class="w-4 h-4 bg-white/30 rounded-full animate-pulse" style="animation-delay: 0.2s"></div>
                <div class="w-4 h-4 bg-white/30 rounded-full animate-pulse" style="animation-delay: 0.4s"></div>
            </div>
        </div>
    </section>

    <!-- Featured Posts -->
    <?php if(!empty($featured_posts)): ?>
    <section class="py-16 bg-white">
        <div class="container mx-auto px-4">
            <div class="text-center mb-12 fade-in">
                <h2 class="text-3xl lg:text-4xl font-bold text-primary font-serif mb-4">Featured Stories</h2>
                <div class="w-20 h-1 bg-accent mx-auto rounded-full"></div>
            </div>
            
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-8 max-w-7xl mx-auto stagger-animation">
                <?php foreach($featured_posts as $post): ?>
                <article class="featured-post group">
                    <?php if($post['featured_image']): ?>
                    <div class="post-image overflow-hidden">
                        <img 
                            src="<?php echo $base_url . $post['featured_image']; ?>" 
                            alt="<?php echo htmlspecialchars($post['title']); ?>"
                            class="w-full h-48 object-cover group-hover:scale-105 transition-transform duration-500"
                        >
                    </div>
                    <?php else: ?>
                    <div class="post-image bg-gradient-to-br from-primary/10 to-accent/10 h-48 flex items-center justify-center">
                        <i class="fas fa-newspaper text-4xl text-primary/40"></i>
                    </div>
                    <?php endif; ?>
                    
                    <div class="post-content p-6">
                        <span class="category-tag inline-block px-3 py-1 text-xs font-semibold rounded-full mb-4">
                            <?php echo htmlspecialchars($post['category_name']); ?>
                        </span>
                        
                        <h3 class="text-xl font-bold text-primary mb-3 group-hover:text-accent transition-colors duration-300">
                            <a href="<?php echo $base_url; ?>/pages/blog/post.php?slug=<?php echo $post['slug']; ?>" class="hover:underline">
                                <?php echo htmlspecialchars($post['title']); ?>
                            </a>
                        </h3>
                        
                        <p class="post-excerpt text-gray-600 mb-4 leading-relaxed line-clamp-3">
                            <?php echo htmlspecialchars($post['excerpt']); ?>
                        </p>
                        
                        <div class="post-meta flex justify-between items-center text-sm text-gray-500 pt-4 border-t border-gray-200">
                            <span class="date flex items-center">
                                <i class="fas fa-calendar-alt mr-2"></i>
                                <?php echo date('M j, Y', strtotime($post['published_at'])); ?>
                            </span>
                            <span class="views flex items-center">
                                <i class="fas fa-eye mr-2"></i>
                                <?php echo $post['view_count']; ?> views
                            </span>
                        </div>
                    </div>
                </article>
                <?php endforeach; ?>
            </div>
        </div>
    </section>
    <?php endif; ?>

    <!-- Latest Posts -->
    <section class="py-16 bg-gray-50">
        <div class="container mx-auto px-4">
            <div class="text-center mb-12 fade-in">
                <h2 class="text-3xl lg:text-4xl font-bold text-primary font-serif mb-4">Latest Updates</h2>
                <p class="text-gray-600 max-w-2xl mx-auto">Discover the most recent news and articles from our school community</p>
                <div class="w-20 h-1 bg-accent mx-auto rounded-full mt-4"></div>
            </div>
            
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8 max-w-7xl mx-auto stagger-animation">
                <?php foreach($posts as $post): ?>
                <article class="post-card group">
                    <?php if($post['featured_image']): ?>
                    <div class="post-image overflow-hidden rounded-t-lg">
                        <img 
                            src="<?php echo $base_url . $post['featured_image']; ?>" 
                            alt="<?php echo htmlspecialchars($post['title']); ?>"
                            class="w-full h-48 object-cover group-hover:scale-105 transition-transform duration-500"
                        >
                    </div>
                    <?php else: ?>
                    <div class="post-image bg-gradient-to-br from-primary/10 to-accent/10 h-48 rounded-t-lg flex items-center justify-center">
                        <i class="fas fa-file-alt text-4xl text-primary/40"></i>
                    </div>
                    <?php endif; ?>
                    
                    <div class="post-content p-6">
                        <span class="category-tag inline-block px-3 py-1 text-xs font-semibold rounded-full mb-4">
                            <?php echo htmlspecialchars($post['category_name']); ?>
                        </span>
                        
                        <h3 class="text-lg font-bold text-primary mb-3 group-hover:text-accent transition-colors duration-300 line-clamp-2">
                            <a href="<?php echo $base_url; ?>/pages/blog/post.php?slug=<?php echo $post['slug']; ?>" class="hover:underline">
                                <?php echo htmlspecialchars($post['title']); ?>
                            </a>
                        </h3>
                        
                        <p class="post-excerpt text-gray-600 mb-4 leading-relaxed line-clamp-3 text-sm">
                            <?php echo htmlspecialchars($post['excerpt']); ?>
                        </p>
                        
                        <div class="post-meta flex justify-between items-center text-sm text-gray-500 pt-4 border-t border-gray-200">
                            <span class="date flex items-center">
                                <i class="fas fa-calendar-alt mr-2"></i>
                                <?php echo date('M j, Y', strtotime($post['published_at'])); ?>
                            </span>
                            <a href="<?php echo $base_url; ?>/pages/blog/post.php?slug=<?php echo $post['slug']; ?>" 
                               class="text-accent hover:text-primary font-medium flex items-center transition-colors duration-300">
                                Read More <i class="fas fa-arrow-right ml-1 text-xs"></i>
                            </a>
                        </div>
                    </div>
                </article>
                <?php endforeach; ?>
            </div>

            <!-- Pagination -->
            <?php if($total_pages > 1): ?>
            <div class="pagination flex justify-center items-center space-x-2 mt-16 fade-in">
                <?php if($page > 1): ?>
                    <a href="?page=<?php echo $page - 1; ?>" class="prev px-4 py-2 rounded-lg border border-gray-300 text-gray-700 hover:bg-primary hover:text-white hover:border-primary transition-all duration-300 flex items-center">
                        <i class="fas fa-chevron-left mr-2 text-xs"></i> Previous
                    </a>
                <?php endif; ?>
                
                <div class="flex space-x-1">
                    <?php 
                    $start_page = max(1, $page - 2);
                    $end_page = min($total_pages, $page + 2);
                    
                    for($i = $start_page; $i <= $end_page; $i++): 
                    ?>
                        <a href="?page=<?php echo $i; ?>" class="px-4 py-2 rounded-lg border border-gray-300 text-gray-700 hover:bg-primary hover:text-white hover:border-primary transition-all duration-300 <?php echo $i == $page ? 'active bg-accent text-white border-accent' : ''; ?>">
                            <?php echo $i; ?>
                        </a>
                    <?php endfor; ?>
                </div>
                
                <?php if($page < $total_pages): ?>
                    <a href="?page=<?php echo $page + 1; ?>" class="next px-4 py-2 rounded-lg border border-gray-300 text-gray-700 hover:bg-primary hover:text-white hover:border-primary transition-all duration-300 flex items-center">
                        Next <i class="fas fa-chevron-right ml-2 text-xs"></i>
                    </a>
                <?php endif; ?>
            </div>
            <?php endif; ?>
        </div>
    </section>

    <!-- Newsletter Section -->
    <section class="py-16 bg-primary text-white">
        <div class="container mx-auto px-4 text-center">
            <div class="max-w-3xl mx-auto fade-in">
                <h2 class="text-3xl lg:text-4xl font-bold font-serif mb-4">Stay Connected</h2>
                <p class="text-xl opacity-90 mb-8 leading-relaxed">
                    Subscribe to our newsletter to receive the latest updates and news directly in your inbox
                </p>
                <div class="flex flex-col sm:flex-row gap-4 justify-center max-w-md mx-auto">
                    <input 
                        type="email" 
                        placeholder="Enter your email address" 
                        class="flex-1 px-4 py-3 rounded-lg text-gray-900 focus:outline-none focus:ring-2 focus:ring-accent"
                    >
                    <button class="bg-accent text-white px-6 py-3 rounded-lg font-semibold hover:bg-red-700 transition-colors duration-300">
                        Subscribe
                    </button>
                </div>
            </div>
        </div>
    </section>

    <script>
        // Add intersection observer for scroll animations
        document.addEventListener('DOMContentLoaded', function() {
            const observerOptions = {
                threshold: 0.1,
                rootMargin: '0px 0px -50px 0px'
            };

            const observer = new IntersectionObserver(function(entries) {
                entries.forEach(entry => {
                    if (entry.isIntersecting) {
                        entry.target.classList.add('fade-in');
                    }
                });
            }, observerOptions);

            // Observe all sections for animation
            document.querySelectorAll('section').forEach(section => {
                observer.observe(section);
            });
            
            // Add line-clamp utility for text truncation
            const style = document.createElement('style');
            style.textContent = `
                .line-clamp-2 {
                    display: -webkit-box;
                    -webkit-line-clamp: 2;
                    -webkit-box-orient: vertical;
                    overflow: hidden;
                }
                .line-clamp-3 {
                    display: -webkit-box;
                    -webkit-line-clamp: 3;
                    -webkit-box-orient: vertical;
                    overflow: hidden;
                }
            `;
            document.head.appendChild(style);
        });
    </script>

    <?php include '../../includes/footer.php'; ?>
</body>
</html>