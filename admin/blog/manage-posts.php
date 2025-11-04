<?php
require_once '../../includes/db.php';
require_once '../auth.php';

// Handle actions
if(isset($_GET['action']) && isset($_GET['id'])) {
    $id = (int)$_GET['id'];
    
    if($_GET['action'] == 'delete') {
        $stmt = $pdo->prepare("DELETE FROM blog_posts WHERE id = ?");
        $stmt->execute([$id]);
        $_SESSION['success'] = "Post deleted successfully";
    } elseif($_GET['action'] == 'toggle_publish') {
        $stmt = $pdo->prepare("UPDATE blog_posts SET is_published = NOT is_published WHERE id = ?");
        $stmt->execute([$id]);
        $_SESSION['success'] = "Post status updated";
    } elseif($_GET['action'] == 'toggle_featured') {
        $stmt = $pdo->prepare("UPDATE blog_posts SET is_featured = NOT is_featured WHERE id = ?");
        $stmt->execute([$id]);
        $_SESSION['success'] = "Featured status updated";
    }
    header("Location: manage-posts.php");
    exit;
}

// Get all posts with pagination
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$limit = 10;
$offset = ($page - 1) * $limit;

$stmt = $pdo->query("
    SELECT bp.*, bc.name as category_name 
    FROM blog_posts bp 
    LEFT JOIN blog_categories bc ON bp.category_id = bc.id 
    ORDER BY bp.created_at DESC 
    LIMIT $limit OFFSET $offset
");
$posts = $stmt->fetchAll(PDO::FETCH_ASSOC);

$total_stmt = $pdo->query("SELECT COUNT(*) FROM blog_posts");
$total_posts = $total_stmt->fetchColumn();
$total_pages = ceil($total_posts / $limit);

// Get stats
$published_posts = $pdo->query("SELECT COUNT(*) FROM blog_posts WHERE is_published = 1")->fetchColumn();
$featured_posts = $pdo->query("SELECT COUNT(*) FROM blog_posts WHERE is_featured = 1")->fetchColumn();
$total_views = $pdo->query("SELECT SUM(view_count) FROM blog_posts")->fetchColumn();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Blog Posts - Admin | Bhaktivedanta Gurukul</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link rel="icon" type="image/x-icon" href="<?php echo $base_url; ?>/../../images/bvgLogo.png">

    <style>
        .admin-bg {
            background: linear-gradient(135deg, #f8fafc 0%, #f1f5f9 100%);
            min-height: 100vh;
        }
        .brand-maroon {
            background-color: #800000;
        }
        .brand-brown {
            background-color: #3e2723;
        }
        .brand-maroon-text {
            color: #800000;
        }
        .brand-brown-text {
            color: #3e2723;
        }
        .nav-gradient {
            background: linear-gradient(135deg, #3e2723 0%, #5d4037 100%);
        }
        .btn-primary {
            background: linear-gradient(135deg, #800000 0%, #a00000 100%);
        }
        .btn-primary:hover {
            background: linear-gradient(135deg, #a00000 0%, #c00000 100%);
        }
        .table-row:hover {
            background-color: #f8fafc;
            transform: translateY(-1px);
            transition: all 0.2s ease;
        }
    </style>
</head>
<body class="admin-bg font-sans">
    <!-- Main Header Include -->
    <?php include '../../includes/header.php'; ?>
    
    <div class="min-h-screen pt-20 pb-10">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <!-- Admin Navigation -->
            <div class="nav-gradient rounded-xl p-6 mb-8 shadow-lg">
                <div class="flex flex-wrap items-center justify-between">
                    <div class="flex items-center space-x-4 text-white">
                        <div class="w-12 h-12 bg-white/20 rounded-full flex items-center justify-center">
                            <i class="fas fa-blog text-xl"></i>
                        </div>
                        <div>
                            <h1 class="text-2xl font-bold">Blog Management</h1>
                            <p class="text-white/90">Manage all blog posts and content</p>
                        </div>
                    </div>
                    
                    <div class="flex flex-wrap gap-3">
                        <a href="../dashboard.php" 
                           class="px-5 py-2 bg-white/20 text-white rounded-lg hover:bg-white/30 transition-all duration-300 flex items-center border border-white/30">
                            <i class="fas fa-tachometer-alt mr-2"></i> Dashboard
                        </a>
                        <a href="../logout.php" 
                           class="px-5 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700 transition-all duration-300 flex items-center">
                            <i class="fas fa-sign-out-alt mr-2"></i> Logout
                        </a>
                    </div>
                </div>
                
                <!-- Quick Navigation -->
                <div class="flex flex-wrap gap-3 mt-6">
                    <a href="manage-posts.php" class="px-4 py-2 bg-white/30 text-white rounded-lg transition-all duration-300 flex items-center text-sm border border-white/30">
                        <i class="fas fa-list mr-2"></i> All Posts
                    </a>
                    <a href="add-post.php" class="px-4 py-2 bg-white/10 text-white rounded-lg hover:bg-white/20 transition-all duration-300 flex items-center text-sm border border-white/20">
                        <i class="fas fa-plus mr-2"></i> Add New Post
                    </a>
                    <a href="categories.php" class="px-4 py-2 bg-white/10 text-white rounded-lg hover:bg-white/20 transition-all duration-300 flex items-center text-sm border border-white/20">
                        <i class="fas fa-folder mr-2"></i> Categories
                    </a>
                    <a href="../downloads/manage-downloads.php" class="px-4 py-2 bg-white/10 text-white rounded-lg hover:bg-white/20 transition-all duration-300 flex items-center text-sm border border-white/20">
                        <i class="fas fa-download mr-2"></i> Downloads
                    </a>
                </div>
            </div>

            <!-- Stats Cards -->
            <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-8">
                <div class="bg-white rounded-xl shadow-lg p-6 border-l-4 border-blue-500">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm font-medium text-gray-600">Total Posts</p>
                            <p class="text-3xl font-bold text-gray-900"><?php echo $total_posts; ?></p>
                        </div>
                        <div class="w-12 h-12 bg-blue-100 rounded-full flex items-center justify-center">
                            <i class="fas fa-newspaper text-blue-500 text-xl"></i>
                        </div>
                    </div>
                </div>

                <div class="bg-white rounded-xl shadow-lg p-6 border-l-4 border-green-500">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm font-medium text-gray-600">Published</p>
                            <p class="text-3xl font-bold text-gray-900"><?php echo $published_posts; ?></p>
                        </div>
                        <div class="w-12 h-12 bg-green-100 rounded-full flex items-center justify-center">
                            <i class="fas fa-eye text-green-500 text-xl"></i>
                        </div>
                    </div>
                </div>

                <div class="bg-white rounded-xl shadow-lg p-6 border-l-4 border-yellow-500">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm font-medium text-gray-600">Featured</p>
                            <p class="text-3xl font-bold text-gray-900"><?php echo $featured_posts; ?></p>
                        </div>
                        <div class="w-12 h-12 bg-yellow-100 rounded-full flex items-center justify-center">
                            <i class="fas fa-star text-yellow-500 text-xl"></i>
                        </div>
                    </div>
                </div>

                <div class="bg-white rounded-xl shadow-lg p-6 border-l-4 border-purple-500">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm font-medium text-gray-600">Total Views</p>
                            <p class="text-3xl font-bold text-gray-900"><?php echo $total_views ?: '0'; ?></p>
                        </div>
                        <div class="w-12 h-12 bg-purple-100 rounded-full flex items-center justify-center">
                            <i class="fas fa-chart-line text-purple-500 text-xl"></i>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Page Header -->
            <div class="flex justify-between items-center mb-6">
                <div>
                    <h1 class="text-3xl font-bold text-gray-900 mb-2">Manage Blog Posts</h1>
                    <p class="text-gray-600 text-lg">Manage and organize all your blog content</p>
                </div>
                <a href="add-post.php" 
                   class="inline-flex items-center px-6 py-3 btn-primary text-white rounded-xl font-bold hover:shadow-xl transition-all duration-300 transform hover:-translate-y-1 shadow-lg">
                    <i class="fas fa-plus-circle mr-3"></i> Add New Post
                </a>
            </div>

            <!-- Success Message -->
            <?php if(isset($_SESSION['success'])): ?>
                <div class="mb-6 bg-green-50 border border-green-200 text-green-700 px-4 py-4 rounded-xl flex items-center">
                    <i class="fas fa-check-circle mr-3 text-green-500"></i>
                    <div>
                        <strong class="font-semibold">Success:</strong> <?php echo $_SESSION['success']; unset($_SESSION['success']); ?>
                    </div>
                </div>
            <?php endif; ?>

            <!-- Posts Table -->
            <div class="bg-white rounded-2xl shadow-xl overflow-hidden">
                <!-- Table Header -->
                <div class="px-6 py-4 border-b border-gray-200 bg-gray-50">
                    <div class="flex justify-between items-center">
                        <h3 class="text-lg font-semibold text-gray-900">
                            All Posts <span class="text-gray-600">(<?php echo $total_posts; ?>)</span>
                        </h3>
                        <div class="flex items-center space-x-4">
                            <div class="relative">
                                <input type="text" 
                                       placeholder="Search posts..." 
                                       class="pl-10 pr-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-red-500 focus:border-red-500 transition-all duration-300"
                                       onkeyup="filterTable(this.value)">
                                <i class="fas fa-search absolute left-3 top-3 text-gray-400"></i>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Table -->
                <div class="overflow-x-auto">
                    <table class="w-full">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-4 text-left text-xs font-semibold text-gray-700 uppercase tracking-wider">Post</th>
                                <th class="px-6 py-4 text-left text-xs font-semibold text-gray-700 uppercase tracking-wider">Category</th>
                                <th class="px-6 py-4 text-left text-xs font-semibold text-gray-700 uppercase tracking-wider">Status</th>
                                <th class="px-6 py-4 text-left text-xs font-semibold text-gray-700 uppercase tracking-wider">Featured</th>
                                <th class="px-6 py-4 text-left text-xs font-semibold text-gray-700 uppercase tracking-wider">Views</th>
                                <th class="px-6 py-4 text-left text-xs font-semibold text-gray-700 uppercase tracking-wider">Date</th>
                                <th class="px-6 py-4 text-left text-xs font-semibold text-gray-700 uppercase tracking-wider">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200" id="postsTable">
                            <?php if(empty($posts)): ?>
                                <tr>
                                    <td colspan="7" class="px-6 py-12 text-center">
                                        <i class="fas fa-newspaper text-4xl text-gray-300 mb-4"></i>
                                        <p class="text-gray-500 text-lg">No blog posts found</p>
                                        <p class="text-gray-400 mt-2">Get started by creating your first blog post</p>
                                        <a href="add-post.php" class="inline-block mt-4 px-6 py-2 btn-primary text-white rounded-lg">
                                            <i class="fas fa-plus mr-2"></i> Create Post
                                        </a>
                                    </td>
                                </tr>
                            <?php else: ?>
                                <?php foreach($posts as $post): ?>
                                <tr class="table-row transition-all duration-200">
                                    <td class="px-6 py-4">
                                        <div class="flex items-center">
                                            <?php if($post['featured_image']): ?>
                                                <div class="w-12 h-12 bg-gray-200 rounded-lg flex-shrink-0 overflow-hidden mr-4">
                                                    <img src="<?php echo $base_url . $post['featured_image']; ?>" 
                                                         alt="<?php echo htmlspecialchars($post['title']); ?>"
                                                         class="w-full h-full object-cover">
                                                </div>
                                            <?php else: ?>
                                                <div class="w-12 h-12 bg-gray-100 rounded-lg flex items-center justify-center mr-4">
                                                    <i class="fas fa-newspaper text-gray-400"></i>
                                                </div>
                                            <?php endif; ?>
                                            <div class="min-w-0 flex-1">
                                                <div class="text-sm font-semibold text-gray-900 truncate">
                                                    <?php echo htmlspecialchars($post['title']); ?>
                                                    <?php if($post['is_featured']): ?>
                                                        <span class="ml-2 inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800">
                                                            <i class="fas fa-star mr-1 text-xs"></i> Featured
                                                        </span>
                                                    <?php endif; ?>
                                                </div>
                                                <div class="text-sm text-gray-500 truncate max-w-xs">
                                                    <?php echo htmlspecialchars($post['excerpt'] ?? 'No excerpt'); ?>
                                                </div>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                            <?php echo $post['category_name'] ?? 'Uncategorized'; ?>
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium <?php echo $post['is_published'] ? 'bg-green-100 text-green-800' : 'bg-gray-100 text-gray-800'; ?>">
                                            <i class="fas fa-circle mr-1 text-<?php echo $post['is_published'] ? 'green' : 'gray'; ?>-500" style="font-size: 6px;"></i>
                                            <?php echo $post['is_published'] ? 'Published' : 'Draft'; ?>
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                        <?php if($post['is_featured']): ?>
                                            <i class="fas fa-star text-yellow-500" title="Featured"></i>
                                        <?php else: ?>
                                            <i class="far fa-star text-gray-400" title="Not Featured"></i>
                                        <?php endif; ?>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                        <div class="flex items-center">
                                            <i class="fas fa-eye text-gray-400 mr-2"></i>
                                            <?php echo $post['view_count']; ?>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                        <?php echo date('M j, Y', strtotime($post['created_at'])); ?>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                        <div class="flex items-center space-x-2">
                                            <!-- Edit -->
                                            <a href="edit-post.php?id=<?php echo $post['id']; ?>" 
                                               class="text-blue-600 hover:text-blue-900 transition-colors duration-200 p-2 rounded-lg hover:bg-blue-50"
                                               title="Edit">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                            
                                            <!-- Publish/Unpublish -->
                                            <a href="?action=toggle_publish&id=<?php echo $post['id']; ?>" 
                                               class="text-<?php echo $post['is_published'] ? 'yellow' : 'green'; ?>-600 hover:text-<?php echo $post['is_published'] ? 'yellow' : 'green'; ?>-900 transition-colors duration-200 p-2 rounded-lg hover:bg-<?php echo $post['is_published'] ? 'yellow' : 'green'; ?>-50"
                                               title="<?php echo $post['is_published'] ? 'Unpublish' : 'Publish'; ?>">
                                                <i class="fas fa-<?php echo $post['is_published'] ? 'eye-slash' : 'eye'; ?>"></i>
                                            </a>
                                            
                                            <!-- Featured/Unfeature -->
                                            <a href="?action=toggle_featured&id=<?php echo $post['id']; ?>" 
                                               class="text-<?php echo $post['is_featured'] ? 'gray' : 'yellow'; ?>-600 hover:text-<?php echo $post['is_featured'] ? 'gray' : 'yellow'; ?>-900 transition-colors duration-200 p-2 rounded-lg hover:bg-<?php echo $post['is_featured'] ? 'gray' : 'yellow'; ?>-50"
                                               title="<?php echo $post['is_featured'] ? 'Remove Featured' : 'Mark Featured'; ?>">
                                                <i class="fas fa-star"></i>
                                            </a>
                                            
                                            <!-- Delete -->
                                            <a href="?action=delete&id=<?php echo $post['id']; ?>" 
                                               class="text-red-600 hover:text-red-900 transition-colors duration-200 p-2 rounded-lg hover:bg-red-50"
                                               onclick="return confirm('Are you sure you want to delete this post? This action cannot be undone.')"
                                               title="Delete">
                                                <i class="fas fa-trash"></i>
                                            </a>
                                            
                                            <!-- View -->
                                            <a href="<?php echo $base_url; ?>/pages/blog/post.php?slug=<?php echo $post['slug']; ?>" 
                                               target="_blank"
                                               class="text-purple-600 hover:text-purple-900 transition-colors duration-200 p-2 rounded-lg hover:bg-purple-50"
                                               title="View Post">
                                                <i class="fas fa-external-link-alt"></i>
                                            </a>
                                        </div>
                                    </td>
                                </tr>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>

                <!-- Table Footer & Pagination -->
                <div class="px-6 py-4 border-t border-gray-200 bg-gray-50">
                    <div class="flex flex-col sm:flex-row justify-between items-center space-y-4 sm:space-y-0">
                        <div class="text-sm text-gray-700">
                            Showing <span class="font-semibold"><?php echo count($posts); ?></span> of <span class="font-semibold"><?php echo $total_posts; ?></span> posts
                        </div>
                        
                        <?php if($total_pages > 1): ?>
                        <div class="flex space-x-1">
                            <!-- Previous Button -->
                            <?php if($page > 1): ?>
                                <a href="?page=<?php echo $page - 1; ?>" 
                                   class="px-3 py-2 text-sm font-medium text-gray-500 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 hover:text-gray-700 transition-colors duration-200">
                                    <i class="fas fa-chevron-left"></i>
                                </a>
                            <?php endif; ?>
                            
                            <!-- Page Numbers -->
                            <?php 
                            $start_page = max(1, $page - 2);
                            $end_page = min($total_pages, $page + 2);
                            
                            for($i = $start_page; $i <= $end_page; $i++): 
                            ?>
                                <a href="?page=<?php echo $i; ?>" 
                                   class="px-3 py-2 text-sm font-medium border transition-colors duration-200 <?php echo $i == $page ? 'bg-red-500 text-white border-red-500' : 'bg-white text-gray-500 border-gray-300 hover:bg-gray-50'; ?> rounded-lg">
                                    <?php echo $i; ?>
                                </a>
                            <?php endfor; ?>
                            
                            <!-- Next Button -->
                            <?php if($page < $total_pages): ?>
                                <a href="?page=<?php echo $page + 1; ?>" 
                                   class="px-3 py-2 text-sm font-medium text-gray-500 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 hover:text-gray-700 transition-colors duration-200">
                                    <i class="fas fa-chevron-right"></i>
                                </a>
                            <?php endif; ?>
                        </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Main Footer Include -->
    <?php include '../../includes/footer.php'; ?>

    <script>
        function filterTable(searchTerm) {
            const rows = document.querySelectorAll('#postsTable tr');
            searchTerm = searchTerm.toLowerCase();
            
            rows.forEach(row => {
                const text = row.textContent.toLowerCase();
                if (text.includes(searchTerm)) {
                    row.style.display = '';
                } else {
                    row.style.display = 'none';
                }
            });
        }

        // Add loading animation to action buttons
        document.querySelectorAll('a[href*="action=delete"]').forEach(link => {
            link.addEventListener('click', function(e) {
                if (!confirm('Are you sure you want to delete this post? This action cannot be undone.')) {
                    e.preventDefault();
                }
            });
        });
    </script>
</body>
</html>