<?php
require_once '../../includes/db.php';
require_once '../auth.php';

// Handle actions
if(isset($_GET['action']) && isset($_GET['id'])) {
    $id = (int)$_GET['id'];
    
    if($_GET['action'] == 'delete') {
        // Check if category has posts
        $check_stmt = $pdo->prepare("SELECT COUNT(*) FROM blog_posts WHERE category_id = ?");
        $check_stmt->execute([$id]);
        $post_count = $check_stmt->fetchColumn();
        
        if($post_count > 0) {
            $_SESSION['error'] = "Cannot delete category. It has $post_count posts associated with it.";
        } else {
            $stmt = $pdo->prepare("DELETE FROM blog_categories WHERE id = ?");
            $stmt->execute([$id]);
            $_SESSION['success'] = "Category deleted successfully";
        }
    } elseif($_GET['action'] == 'toggle_status') {
        $stmt = $pdo->prepare("UPDATE blog_categories SET is_active = NOT is_active WHERE id = ?");
        $stmt->execute([$id]);
        $_SESSION['success'] = "Category status updated";
    }
    header("Location: categories.php");
    exit;
}

// Handle form submission for adding/editing category
if($_POST) {
    $name = trim($_POST['name']);
    $description = trim($_POST['description']);
    $is_active = isset($_POST['is_active']) ? 1 : 0;
    
    // Generate slug
    $slug = createSlug($name);
    
    if(empty($name)) {
        $error = "Category name is required";
    } else {
        try {
            // Check if editing or adding
            if(isset($_POST['category_id']) && !empty($_POST['category_id'])) {
                // Update existing category
                $category_id = (int)$_POST['category_id'];
                $stmt = $pdo->prepare("
                    UPDATE blog_categories 
                    SET name = ?, slug = ?, description = ?, is_active = ?, updated_at = NOW() 
                    WHERE id = ?
                ");
                $stmt->execute([$name, $slug, $description, $is_active, $category_id]);
                $_SESSION['success'] = "Category updated successfully!";
            } else {
                // Add new category
                // Check if category already exists
                $check_stmt = $pdo->prepare("SELECT id FROM blog_categories WHERE name = ? OR slug = ?");
                $check_stmt->execute([$name, $slug]);
                $existing = $check_stmt->fetch();
                
                if($existing) {
                    $error = "Category with this name already exists";
                } else {
                    $stmt = $pdo->prepare("
                        INSERT INTO blog_categories (name, slug, description, is_active) 
                        VALUES (?, ?, ?, ?)
                    ");
                    $stmt->execute([$name, $slug, $description, $is_active]);
                    $_SESSION['success'] = "Category added successfully!";
                }
            }
            
            if(!isset($error)) {
                header("Location: categories.php");
                exit;
            }
            
        } catch(PDOException $e) {
            $error = "Error saving category: " . $e->getMessage();
        }
    }
}

// Get category for editing
$edit_category = null;
if(isset($_GET['edit'])) {
    $edit_id = (int)$_GET['edit'];
    $stmt = $pdo->prepare("SELECT * FROM blog_categories WHERE id = ?");
    $stmt->execute([$edit_id]);
    $edit_category = $stmt->fetch(PDO::FETCH_ASSOC);
}

// Get all categories with post counts
$categories = $pdo->query("
    SELECT bc.*, COUNT(bp.id) as post_count 
    FROM blog_categories bc 
    LEFT JOIN blog_posts bp ON bc.id = bp.category_id 
    GROUP BY bc.id 
    ORDER BY bc.name ASC
")->fetchAll(PDO::FETCH_ASSOC);

function createSlug($text) {
    $text = preg_replace('~[^\pL\d]+~u', '-', $text);
    $text = iconv('utf-8', 'us-ascii//TRANSLIT', $text);
    $text = preg_replace('~[^-\w]+~', '', $text);
    $text = trim($text, '-');
    $text = preg_replace('~-+~', '-', $text);
    $text = strtolower($text);
    
    if(empty($text)) {
        return 'n-a';
    }
    
    return $text;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Categories - Admin | Bhaktivedanta Gurukul</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link rel="icon" type="image/x-icon" href="<?php echo '../../images/bvgLogo.png'; ?>">

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
        .form-input:focus {
            box-shadow: 0 0 0 3px rgba(128, 0, 0, 0.1);
            border-color: #800000;
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
                            <i class="fas fa-folder text-xl"></i>
                        </div>
                        <div>
                            <h1 class="text-2xl font-bold">Blog Categories</h1>
                            <p class="text-white/90">Manage blog categories and organization</p>
                        </div>
                    </div>
                    
                    <div class="flex flex-wrap gap-3">
                        <a href="../dashboard.php" 
                           class="px-5 py-2 bg-white/20 text-white rounded-lg hover:bg-white/30 transition-all duration-300 flex items-center border border-white/30">
                            <i class="fas fa-tachometer-alt mr-2"></i> Dashboard
                        </a>
                        <a href="manage-posts.php" 
                           class="px-5 py-2 bg-white/20 text-white rounded-lg hover:bg-white/30 transition-all duration-300 flex items-center border border-white/30">
                            <i class="fas fa-list mr-2"></i> All Posts
                        </a>
                        <a href="../logout.php" 
                           class="px-5 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700 transition-all duration-300 flex items-center">
                            <i class="fas fa-sign-out-alt mr-2"></i> Logout
                        </a>
                    </div>
                </div>
            </div>

            <!-- Stats Cards -->
            <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-8">
                <div class="bg-white rounded-xl shadow-lg p-6 border-l-4 border-blue-500">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm font-medium text-gray-600">Total Categories</p>
                            <p class="text-3xl font-bold text-gray-900"><?php echo count($categories); ?></p>
                        </div>
                        <div class="w-12 h-12 bg-blue-100 rounded-full flex items-center justify-center">
                            <i class="fas fa-folder text-blue-500 text-xl"></i>
                        </div>
                    </div>
                </div>

                <div class="bg-white rounded-xl shadow-lg p-6 border-l-4 border-green-500">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm font-medium text-gray-600">Active Categories</p>
                            <p class="text-3xl font-bold text-gray-900">
                                <?php 
                                    $active_categories = array_filter($categories, function($cat) {
                                        return $cat['is_active'];
                                    });
                                    echo count($active_categories);
                                ?>
                            </p>
                        </div>
                        <div class="w-12 h-12 bg-green-100 rounded-full flex items-center justify-center">
                            <i class="fas fa-check-circle text-green-500 text-xl"></i>
                        </div>
                    </div>
                </div>

                <div class="bg-white rounded-xl shadow-lg p-6 border-l-4 border-purple-500">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm font-medium text-gray-600">Total Posts</p>
                            <p class="text-3xl font-bold text-gray-900">
                                <?php 
                                    $total_posts = array_sum(array_column($categories, 'post_count'));
                                    echo $total_posts;
                                ?>
                            </p>
                        </div>
                        <div class="w-12 h-12 bg-purple-100 rounded-full flex items-center justify-center">
                            <i class="fas fa-newspaper text-purple-500 text-xl"></i>
                        </div>
                    </div>
                </div>

                <div class="bg-white rounded-xl shadow-lg p-6 border-l-4 border-yellow-500">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm font-medium text-gray-600">Most Used</p>
                            <p class="text-3xl font-bold text-gray-900">
                                <?php
                                    $most_used = array_reduce($categories, function($carry, $item) {
                                        return $item['post_count'] > $carry['post_count'] ? $item : $carry;
                                    }, ['name' => 'None', 'post_count' => 0]);
                                    echo $most_used['post_count'];
                                ?>
                            </p>
                        </div>
                        <div class="w-12 h-12 bg-yellow-100 rounded-full flex items-center justify-center">
                            <i class="fas fa-chart-bar text-yellow-500 text-xl"></i>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Main Content Grid -->
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
                <!-- Categories List -->
                <div class="lg:col-span-2">
                    <div class="bg-white rounded-2xl shadow-xl overflow-hidden">
                        <!-- Table Header -->
                        <div class="px-6 py-4 border-b border-gray-200 bg-gray-50">
                            <div class="flex justify-between items-center">
                                <h3 class="text-lg font-semibold text-gray-900">
                                    All Categories <span class="text-gray-600">(<?php echo count($categories); ?>)</span>
                                </h3>
                                <div class="flex items-center space-x-4">
                                    <div class="relative">
                                        <input type="text" 
                                               placeholder="Search categories..." 
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
                                        <th class="px-6 py-4 text-left text-xs font-semibold text-gray-700 uppercase tracking-wider">Category</th>
                                        <th class="px-6 py-4 text-left text-xs font-semibold text-gray-700 uppercase tracking-wider">Posts</th>
                                        <th class="px-6 py-4 text-left text-xs font-semibold text-gray-700 uppercase tracking-wider">Status</th>
                                        <th class="px-6 py-4 text-left text-xs font-semibold text-gray-700 uppercase tracking-wider">Created</th>
                                        <th class="px-6 py-4 text-left text-xs font-semibold text-gray-700 uppercase tracking-wider">Actions</th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white divide-y divide-gray-200" id="categoriesTable">
                                    <?php if(empty($categories)): ?>
                                        <tr>
                                            <td colspan="5" class="px-6 py-12 text-center">
                                                <i class="fas fa-folder-open text-4xl text-gray-300 mb-4"></i>
                                                <p class="text-gray-500 text-lg">No categories found</p>
                                                <p class="text-gray-400 mt-2">Get started by creating your first category</p>
                                            </td>
                                        </tr>
                                    <?php else: ?>
                                        <?php foreach($categories as $category): ?>
                                        <tr class="table-row transition-all duration-200">
                                            <td class="px-6 py-4">
                                                <div class="flex items-center">
                                                    <div class="w-10 h-10 bg-blue-100 rounded-lg flex items-center justify-center mr-4">
                                                        <i class="fas fa-folder text-blue-500"></i>
                                                    </div>
                                                    <div>
                                                        <div class="text-sm font-semibold text-gray-900">
                                                            <?php echo htmlspecialchars($category['name']); ?>
                                                        </div>
                                                        <div class="text-sm text-gray-500 truncate max-w-xs">
                                                            <?php echo htmlspecialchars($category['description'] ?? 'No description'); ?>
                                                        </div>
                                                    </div>
                                                </div>
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap">
                                                <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-gray-100 text-gray-800">
                                                    <i class="fas fa-file-alt mr-1"></i>
                                                    <?php echo $category['post_count']; ?> posts
                                                </span>
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap">
                                                <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium <?php echo $category['is_active'] ? 'bg-green-100 text-green-800' : 'bg-gray-100 text-gray-800'; ?>">
                                                    <i class="fas fa-circle mr-1 text-<?php echo $category['is_active'] ? 'green' : 'gray'; ?>-500" style="font-size: 6px;"></i>
                                                    <?php echo $category['is_active'] ? 'Active' : 'Inactive'; ?>
                                                </span>
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                                <?php echo date('M j, Y', strtotime($category['created_at'])); ?>
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                                <div class="flex items-center space-x-2">
                                                    <!-- Edit -->
                                                    <a href="?edit=<?php echo $category['id']; ?>" 
                                                       class="text-blue-600 hover:text-blue-900 transition-colors duration-200 p-2 rounded-lg hover:bg-blue-50"
                                                       title="Edit">
                                                        <i class="fas fa-edit"></i>
                                                    </a>
                                                    
                                                    <!-- Toggle Status -->
                                                    <a href="?action=toggle_status&id=<?php echo $category['id']; ?>" 
                                                       class="text-<?php echo $category['is_active'] ? 'yellow' : 'green'; ?>-600 hover:text-<?php echo $category['is_active'] ? 'yellow' : 'green'; ?>-900 transition-colors duration-200 p-2 rounded-lg hover:bg-<?php echo $category['is_active'] ? 'yellow' : 'green'; ?>-50"
                                                       title="<?php echo $category['is_active'] ? 'Deactivate' : 'Activate'; ?>">
                                                        <i class="fas fa-<?php echo $category['is_active'] ? 'eye-slash' : 'eye'; ?>"></i>
                                                    </a>
                                                    
                                                    <!-- Delete -->
                                                    <a href="?action=delete&id=<?php echo $category['id']; ?>" 
                                                       class="text-red-600 hover:text-red-900 transition-colors duration-200 p-2 rounded-lg hover:bg-red-50"
                                                       onclick="return confirm('<?php echo $category['post_count'] > 0 ? 'This category has ' . $category['post_count'] . ' posts. Are you sure you want to delete it?' : 'Are you sure you want to delete this category?'; ?>')"
                                                       title="Delete">
                                                        <i class="fas fa-trash"></i>
                                                    </a>
                                                </div>
                                            </td>
                                        </tr>
                                        <?php endforeach; ?>
                                    <?php endif; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

                <!-- Add/Edit Category Form -->
                <div class="lg:col-span-1">
                    <div class="bg-white rounded-2xl shadow-xl p-6 sticky top-24">
                        <div class="flex items-center mb-6 pb-4 border-b border-gray-200">
                            <div class="w-10 h-10 brand-maroon rounded-full flex items-center justify-center mr-4">
                                <i class="fas fa-<?php echo $edit_category ? 'edit' : 'plus'; ?> text-white"></i>
                            </div>
                            <div>
                                <h2 class="text-xl font-bold text-gray-900">
                                    <?php echo $edit_category ? 'Edit Category' : 'Add New Category'; ?>
                                </h2>
                                <p class="text-gray-600">
                                    <?php echo $edit_category ? 'Update category details' : 'Create a new blog category'; ?>
                                </p>
                            </div>
                        </div>

                        <!-- Success/Error Messages -->
                        <?php if(isset($_SESSION['success'])): ?>
                            <div class="mb-4 bg-green-50 border border-green-200 text-green-700 px-4 py-3 rounded-lg flex items-center">
                                <i class="fas fa-check-circle mr-2 text-green-500"></i>
                                <?php echo $_SESSION['success']; unset($_SESSION['success']); ?>
                            </div>
                        <?php endif; ?>

                        <?php if(isset($_SESSION['error'])): ?>
                            <div class="mb-4 bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded-lg flex items-center">
                                <i class="fas fa-exclamation-circle mr-2 text-red-500"></i>
                                <?php echo $_SESSION['error']; unset($_SESSION['error']); ?>
                            </div>
                        <?php endif; ?>

                        <?php if(isset($error)): ?>
                            <div class="mb-4 bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded-lg flex items-center">
                                <i class="fas fa-exclamation-circle mr-2 text-red-500"></i>
                                <?php echo $error; ?>
                            </div>
                        <?php endif; ?>

                        <form method="POST" class="space-y-6">
                            <?php if($edit_category): ?>
                                <input type="hidden" name="category_id" value="<?php echo $edit_category['id']; ?>">
                            <?php endif; ?>

                            <!-- Category Name -->
                            <div>
                                <label for="name" class="block text-sm font-semibold text-gray-800 mb-3 flex items-center">
                                    <i class="fas fa-heading mr-2 brand-maroon-text"></i>Category Name *
                                </label>
                                <input type="text" 
                                       id="name" 
                                       name="name" 
                                       required
                                       class="w-full px-4 py-3 border border-gray-300 rounded-lg form-input focus:ring-2 focus:ring-red-500 focus:border-red-500 transition-all duration-300"
                                       placeholder="Enter category name"
                                       value="<?php echo $edit_category ? htmlspecialchars($edit_category['name']) : (isset($_POST['name']) ? htmlspecialchars($_POST['name']) : ''); ?>">
                            </div>

                            <!-- Description -->
                            <div>
                                <label for="description" class="block text-sm font-semibold text-gray-800 mb-3 flex items-center">
                                    <i class="fas fa-align-left mr-2 brand-maroon-text"></i>Description
                                </label>
                                <textarea id="description" 
                                          name="description" 
                                          rows="3"
                                          class="w-full px-4 py-3 border border-gray-300 rounded-lg form-input focus:ring-2 focus:ring-red-500 focus:border-red-500 transition-all duration-300"
                                          placeholder="Brief description of the category"><?php echo $edit_category ? htmlspecialchars($edit_category['description']) : (isset($_POST['description']) ? htmlspecialchars($_POST['description']) : ''); ?></textarea>
                            </div>

                            <!-- Status Toggle -->
                            <div class="flex items-center justify-between p-4 bg-gray-50 rounded-lg border border-gray-200">
                                <div class="flex items-center space-x-3">
                                    <div class="w-8 h-8 bg-green-100 rounded-full flex items-center justify-center">
                                        <i class="fas fa-toggle-on text-green-500 text-sm"></i>
                                    </div>
                                    <div>
                                        <p class="text-sm font-semibold text-gray-800">Category Status</p>
                                        <p class="text-xs text-gray-600">Make category available for use</p>
                                    </div>
                                </div>
                                <label class="flex items-center space-x-3 cursor-pointer">
                                    <div class="relative">
                                        <input type="checkbox" 
                                               name="is_active" 
                                               value="1" 
                                               <?php echo ($edit_category && $edit_category['is_active']) || (!isset($edit_category) && !isset($_POST['is_active'])) ? 'checked' : (isset($_POST['is_active']) ? 'checked' : ''); ?>
                                               class="sr-only"
                                               id="statusToggle">
                                        <div class="toggle-track w-12 h-6 bg-gray-300 rounded-full shadow-inner transition-colors duration-300"></div>
                                        <div class="toggle-dot absolute left-1 top-1 bg-white w-4 h-4 rounded-full shadow transform transition-transform duration-300"></div>
                                    </div>
                                    <span class="text-gray-800 font-semibold text-sm" id="statusText">Active</span>
                                </label>
                            </div>

                            <!-- Form Actions -->
                            <div class="flex flex-col space-y-3 pt-4 border-t border-gray-200">
                                <button type="submit" 
                                        class="w-full inline-flex items-center justify-center px-6 py-3 btn-primary text-white rounded-xl font-bold hover:shadow-xl transition-all duration-300 transform hover:-translate-y-1 shadow-lg">
                                    <i class="fas fa-<?php echo $edit_category ? 'save' : 'plus-circle'; ?> mr-3"></i>
                                    <?php echo $edit_category ? 'Update Category' : 'Add Category'; ?>
                                </button>
                                
                                <?php if($edit_category): ?>
                                    <a href="categories.php" 
                                       class="w-full inline-flex items-center justify-center px-6 py-3 bg-gray-500 text-white rounded-xl font-bold hover:bg-gray-600 transition-all duration-300 text-center">
                                        <i class="fas fa-times mr-3"></i> Cancel Edit
                                    </a>
                                <?php endif; ?>
                            </div>
                        </form>

                        <!-- Quick Tips -->
                        <div class="mt-6 p-4 bg-blue-50 rounded-lg border border-blue-200">
                            <h4 class="font-semibold text-blue-800 mb-2 flex items-center text-sm">
                                <i class="fas fa-lightbulb mr-2 text-yellow-500"></i> Tips
                            </h4>
                            <ul class="text-blue-700 space-y-1 text-xs">
                                <li class="flex items-start">
                                    <i class="fas fa-check mr-2 mt-1 text-green-500 text-xs"></i>
                                    Use clear, descriptive names
                                </li>
                                <li class="flex items-start">
                                    <i class="fas fa-check mr-2 mt-1 text-green-500 text-xs"></i>
                                    Add helpful descriptions
                                </li>
                                <li class="flex items-start">
                                    <i class="fas fa-check mr-2 mt-1 text-green-500 text-xs"></i>
                                    Deactivate unused categories
                                </li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Main Footer Include -->
    <?php include '../../includes/footer.php'; ?>

    <script>
        function filterTable(searchTerm) {
            const rows = document.querySelectorAll('#categoriesTable tr');
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

        // Toggle switch functionality
        const toggle = document.getElementById('statusToggle');
        const statusText = document.getElementById('statusText');

        function updateToggle() {
            const track = toggle.parentElement.querySelector('.toggle-track');
            const dot = toggle.parentElement.querySelector('.toggle-dot');
            
            if (toggle.checked) {
                track.classList.add('bg-green-500');
                dot.classList.add('translate-x-6');
                statusText.textContent = 'Active';
                statusText.classList.remove('text-gray-600');
                statusText.classList.add('text-green-600');
            } else {
                track.classList.remove('bg-green-500');
                dot.classList.remove('translate-x-6');
                statusText.textContent = 'Inactive';
                statusText.classList.remove('text-green-600');
                statusText.classList.add('text-gray-600');
            }
        }

        toggle.addEventListener('change', updateToggle);
        updateToggle(); // Initialize

        // Auto-generate slug from name
        document.getElementById('name').addEventListener('input', function() {
            // You can add slug generation preview here if needed
        });

        // Scroll to form when editing
        <?php if($edit_category): ?>
            document.addEventListener('DOMContentLoaded', function() {
                document.querySelector('.bg-white.rounded-2xl').scrollIntoView({ 
                    behavior: 'smooth',
                    block: 'start'
                });
            });
        <?php endif; ?>
    </script>
</body>
</html>