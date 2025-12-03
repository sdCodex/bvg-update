<?php
require_once '../../includes/db.php';
require_once '../auth.php';

// Get post ID from URL
$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

if (!$id) {
    header("Location: manage-posts.php");
    exit;
}

// Get post data
$stmt = $pdo->prepare("
    SELECT bp.*, bc.name as category_name 
    FROM blog_posts bp 
    LEFT JOIN blog_categories bc ON bp.category_id = bc.id 
    WHERE bp.id = ?
");
$stmt->execute([$id]);
$post = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$post) {
    $_SESSION['error'] = "Post not found";
    header("Location: manage-posts.php");
    exit;
}

// Get categories for dropdown
$categories = $pdo->query("SELECT * FROM blog_categories WHERE is_active = TRUE")->fetchAll(PDO::FETCH_ASSOC);

// Handle form submission
if ($_POST) {
    $title = $_POST['title'];
    $slug = createSlug($title);
    $excerpt = $_POST['excerpt'];
    $content = $_POST['content'];
    $category_id = $_POST['category_id'];
    $is_published = isset($_POST['is_published']) ? 1 : 0;
    $is_featured = isset($_POST['is_featured']) ? 1 : 0;

    // Handle file upload
    $featured_image = $post['featured_image']; // Keep existing image by default

    if (isset($_FILES['featured_image']) && $_FILES['featured_image']['error'] == 0) {
        $upload_dir = '../../uploads/blog/';
        
        // Create upload directory if it doesn't exist
        if (!is_dir($upload_dir)) {
            if (!mkdir($upload_dir, 0755, true)) {
                $error = "Failed to create upload directory. Please check permissions.";
            } else {
                // Create .htaccess for security
                file_put_contents($upload_dir . '.htaccess', "Options -Indexes\nDeny from all");
            }
        }

        // Only proceed if directory exists or was created successfully
        if (is_dir($upload_dir)) {
            // Validate file type
            $allowed_types = ['image/jpeg', 'image/jpg', 'image/png', 'image/gif', 'image/webp'];
            $file_type = $_FILES['featured_image']['type'];
            
            if (!in_array($file_type, $allowed_types)) {
                $error = "Invalid file type. Only JPG, JPEG, PNG, GIF, and WEBP images are allowed.";
            } else {
                // Validate file size (5MB limit)
                $max_size = 5 * 1024 * 1024; // 5MB in bytes
                if ($_FILES['featured_image']['size'] > $max_size) {
                    $error = "File size too large. Maximum size allowed is 5MB.";
                } else {
                    // Delete old image if exists
                    if ($post['featured_image'] && file_exists('../..' . $post['featured_image'])) {
                        unlink('../..' . $post['featured_image']);
                    }

                    $file_extension = pathinfo($_FILES['featured_image']['name'], PATHINFO_EXTENSION);
                    $filename = $slug . '-' . time() . '.' . strtolower($file_extension);
                    $file_path = $upload_dir . $filename;

                    if (move_uploaded_file($_FILES['featured_image']['tmp_name'], $file_path)) {
                        $featured_image = '/uploads/blog/' . $filename;
                    } else {
                        $error = "Failed to upload image. Please try again.";
                    }
                }
            }
        }
    }

    // Handle image removal
    if (isset($_POST['remove_image']) && $_POST['remove_image'] == 1) {
        if ($post['featured_image'] && file_exists('../..' . $post['featured_image'])) {
            unlink('../..' . $post['featured_image']);
        }
        $featured_image = '';
    }

    // Only proceed with database update if no errors
    if (!isset($error)) {
        try {
            $stmt = $pdo->prepare("
                UPDATE blog_posts 
                SET title = ?, slug = ?, excerpt = ?, content = ?, featured_image = ?, 
                    category_id = ?, is_published = ?, is_featured = ?, published_at = ?, updated_at = NOW()
                WHERE id = ?
            ");

            $published_at = $is_published ? ($post['published_at'] ?: date('Y-m-d H:i:s')) : null;

            $stmt->execute([
                $title,
                $slug,
                $excerpt,
                $content,
                $featured_image,
                $category_id,
                $is_published,
                $is_featured,
                $published_at,
                $id
            ]);

            $_SESSION['success'] = "Post updated successfully!";
            header("Location: manage-posts.php");
            exit;
        } catch (PDOException $e) {
            $error = "Error updating post: " . $e->getMessage();
        }
    }
}

function createSlug($text)
{
    $text = preg_replace('~[^\pL\d]+~u', '-', $text);
    $text = iconv('utf-8', 'us-ascii//TRANSLIT', $text);
    $text = preg_replace('~[^-\w]+~', '', $text);
    $text = trim($text, '-');
    $text = preg_replace('~-+~', '-', $text);
    $text = strtolower($text);

    if (empty($text)) {
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
    <title>Edit Blog Post - Admin | Bhaktivedanta Gurukul</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <!-- Include CKEditor -->
    <script src="https://cdn.ckeditor.com/4.16.2/standard/ckeditor.js"></script>
    <link rel="icon" type="image/x-icon" href="../../images/bvgLogo.png">

    <style>
        .ck-editor__editable {
            min-height: 300px;
        }

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

        .form-input:focus {
            box-shadow: 0 0 0 3px rgba(128, 0, 0, 0.1);
            border-color: #800000;
        }

        .drag-over {
            background: linear-gradient(135deg, #fff5f5 0%, #fed7d7 100%);
            border-color: #800000;
        }

        .upload-progress {
            height: 4px;
            background: #e5e7eb;
            border-radius: 2px;
            overflow: hidden;
            margin-top: 8px;
        }

        .upload-progress-bar {
            height: 100%;
            background: linear-gradient(90deg, #800000, #a00000);
            transition: width 0.3s ease;
            width: 0%;
        }
    </style>
</head>

<body class="admin-bg font-sans">
    <!-- Main Header Include -->
    <?php include '../../includes/header.php'; ?>

    <div class="min-h-screen pt-20 pb-10">
        <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8">
            <!-- Admin Navigation -->
            <div class="nav-gradient rounded-xl p-6 mb-8 shadow-lg">
                <div class="flex flex-wrap items-center justify-between">
                    <div class="flex items-center space-x-4 text-white">
                        <div class="w-12 h-12 bg-white/20 rounded-full flex items-center justify-center">
                            <i class="fas fa-edit text-xl"></i>
                        </div>
                        <div>
                            <h1 class="text-2xl font-bold">Edit Blog Post</h1>
                            <p class="text-white/90">Update and modify existing blog content</p>
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

            <!-- Page Header -->
            <div class="flex justify-between items-center mb-8">
                <div>
                    <h1 class="text-3xl font-bold text-gray-900 mb-2">Edit Blog Post</h1>
                    <p class="text-gray-600 text-lg">Update: "<?php echo htmlspecialchars($post['title']); ?>"</p>
                </div>
                <div class="flex gap-3">
                    <a href="<?php echo $base_url; ?>/pages/blog/post.php?slug=<?php echo $post['slug']; ?>"
                        target="_blank"
                        class="inline-flex items-center px-5 py-3 bg-blue-500 text-white rounded-lg hover:bg-blue-600 transition-all duration-300 shadow-md">
                        <i class="fas fa-external-link-alt mr-2"></i> View Post
                    </a>
                    <a href="manage-posts.php"
                        class="inline-flex items-center px-5 py-3 brand-brown text-white rounded-lg hover:bg-gray-800 transition-all duration-300 shadow-md">
                        <i class="fas fa-arrow-left mr-2"></i> Back to Posts
                    </a>
                </div>
            </div>

            <!-- Error Message -->
            <?php if (isset($error)): ?>
                <div class="mb-6 bg-red-50 border border-red-200 text-red-700 px-4 py-4 rounded-xl flex items-center animate-pulse">
                    <i class="fas fa-exclamation-circle mr-3 text-red-500"></i>
                    <div>
                        <strong class="font-semibold">Error:</strong> <?php echo $error; ?>
                    </div>
                </div>
            <?php endif; ?>

            <!-- Success Message -->
            <?php if (isset($_SESSION['success'])): ?>
                <div class="mb-6 bg-green-50 border border-green-200 text-green-700 px-4 py-4 rounded-xl flex items-center">
                    <i class="fas fa-check-circle mr-3 text-green-500"></i>
                    <div>
                        <strong class="font-semibold">Success:</strong> <?php echo $_SESSION['success']; unset($_SESSION['success']); ?>
                    </div>
                </div>
            <?php endif; ?>

            <!-- Post Info Card -->
            <div class="bg-white rounded-2xl shadow-xl p-6 mb-6">
                <div class="grid grid-cols-1 md:grid-cols-4 gap-6 text-sm">
                    <div class="flex items-center">
                        <div class="w-10 h-10 bg-blue-100 rounded-lg flex items-center justify-center mr-3">
                            <i class="fas fa-calendar text-blue-500"></i>
                        </div>
                        <div>
                            <p class="text-gray-600">Created</p>
                            <p class="font-semibold text-gray-900"><?php echo date('M j, Y', strtotime($post['created_at'])); ?></p>
                        </div>
                    </div>
                    <div class="flex items-center">
                        <div class="w-10 h-10 bg-green-100 rounded-lg flex items-center justify-center mr-3">
                            <i class="fas fa-eye text-green-500"></i>
                        </div>
                        <div>
                            <p class="text-gray-600">Views</p>
                            <p class="font-semibold text-gray-900"><?php echo $post['view_count']; ?></p>
                        </div>
                    </div>
                    <div class="flex items-center">
                        <div class="w-10 h-10 bg-purple-100 rounded-lg flex items-center justify-center mr-3">
                            <i class="fas fa-folder text-purple-500"></i>
                        </div>
                        <div>
                            <p class="text-gray-600">Category</p>
                            <p class="font-semibold text-gray-900"><?php echo $post['category_name'] ?? 'Uncategorized'; ?></p>
                        </div>
                    </div>
                    <div class="flex items-center">
                        <div class="w-10 h-10 bg-yellow-100 rounded-lg flex items-center justify-center mr-3">
                            <i class="fas fa-star text-yellow-500"></i>
                        </div>
                        <div>
                            <p class="text-gray-600">Status</p>
                            <p class="font-semibold text-gray-900">
                                <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium <?php echo $post['is_published'] ? 'bg-green-100 text-green-800' : 'bg-gray-100 text-gray-800'; ?>">
                                    <?php echo $post['is_published'] ? 'Published' : 'Draft'; ?>
                                </span>
                            </p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Main Content Grid -->
            <div class="grid grid-cols-1 lg:grid-cols-4 gap-8">
                <!-- Form Section -->
                <div class="lg:col-span-3">
                    <div class="bg-white rounded-2xl shadow-xl p-8">
                        <div class="flex items-center mb-6 pb-4 border-b border-gray-200">
                            <div class="w-10 h-10 brand-maroon rounded-full flex items-center justify-center mr-4">
                                <i class="fas fa-edit text-white"></i>
                            </div>
                            <div>
                                <h2 class="text-xl font-bold text-gray-900">Edit Post Information</h2>
                                <p class="text-gray-600">Update the details for your blog post</p>
                            </div>
                        </div>

                        <form method="POST" enctype="multipart/form-data" class="space-y-8" id="postForm">
                            <!-- Title and Category Row -->
                            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                                <div>
                                    <label for="title" class="block text-sm font-semibold text-gray-800 mb-3 flex items-center">
                                        <i class="fas fa-heading mr-2 brand-maroon-text"></i>Post Title *
                                    </label>
                                    <input type="text"
                                        id="title"
                                        name="title"
                                        required
                                        class="w-full px-4 py-4 border border-gray-300 rounded-xl form-input focus:ring-2 focus:ring-red-500 focus:border-red-500 transition-all duration-300 text-gray-800 placeholder-gray-500"
                                        placeholder="Enter post title"
                                        value="<?php echo htmlspecialchars($post['title']); ?>">
                                </div>

                                <div>
                                    <label for="category_id" class="block text-sm font-semibold text-gray-800 mb-3 flex items-center">
                                        <i class="fas fa-folder mr-2 brand-maroon-text"></i>Category
                                    </label>
                                    <select id="category_id"
                                        name="category_id"
                                        class="w-full px-4 py-4 border border-gray-300 rounded-xl form-input focus:ring-2 focus:ring-red-500 focus:border-red-500 transition-all duration-300 text-gray-800">
                                        <option value="">Select Category</option>
                                        <?php foreach ($categories as $category): ?>
                                            <option value="<?php echo $category['id']; ?>" <?php echo $post['category_id'] == $category['id'] ? 'selected' : ''; ?>>
                                                <?php echo htmlspecialchars($category['name']); ?>
                                            </option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                            </div>

                            <!-- Featured Image -->
                            <div>
                                <label class="block text-sm font-semibold text-gray-800 mb-3 flex items-center">
                                    <i class="fas fa-image mr-2 brand-maroon-text"></i>Featured Image
                                </label>

                                <!-- Current Image Preview -->
                                <?php if ($post['featured_image'] && file_exists('../../..' . $post['featured_image'])): ?>
                                    <div class="mb-4 p-4 bg-gray-50 rounded-xl border border-gray-200" id="current-image-container">
                                        <div class="flex items-center justify-between">
                                            <div class="flex items-center">
                                                <div class="w-20 h-20 bg-gray-200 rounded-lg overflow-hidden mr-4 border">
                                                    <img src="<?php echo $base_url . $post['featured_image']; ?>"
                                                        alt="Current featured image"
                                                        class="w-full h-full object-cover"
                                                        onerror="this.style.display='none'; this.nextElementSibling.style.display='flex';">
                                                    <div class="w-full h-full flex items-center justify-center bg-gray-100 hidden">
                                                        <i class="fas fa-image text-gray-400 text-2xl"></i>
                                                    </div>
                                                </div>
                                                <div>
                                                    <p class="font-semibold text-gray-900">Current Image</p>
                                                    <p class="text-sm text-gray-600"><?php echo basename($post['featured_image']); ?></p>
                                                    <p class="text-xs text-gray-500">
                                                        <?php 
                                                        $file_size = file_exists('../../..' . $post['featured_image']) ? 
                                                            round(filesize('../../..' . $post['featured_image']) / 1024) : 
                                                            'Unknown';
                                                        echo $file_size . ' KB';
                                                        ?>
                                                    </p>
                                                </div>
                                            </div>
                                            <div class="flex gap-2">
                                                <button type="button"
                                                    onclick="document.getElementById('file_upload').click()"
                                                    class="px-4 py-2 bg-blue-500 text-white rounded-lg hover:bg-blue-600 transition-colors duration-300 text-sm flex items-center">
                                                    <i class="fas fa-sync mr-1"></i> Replace
                                                </button>
                                                <button type="button"
                                                    onclick="removeCurrentImage()"
                                                    class="px-4 py-2 bg-red-500 text-white rounded-lg hover:bg-red-600 transition-colors duration-300 text-sm flex items-center">
                                                    <i class="fas fa-trash mr-1"></i> Remove
                                                </button>
                                            </div>
                                        </div>
                                        <input type="hidden" name="remove_image" id="remove_image" value="0">
                                    </div>
                                <?php endif; ?>

                                <!-- File Upload Area -->
                                <div class="border-2 border-dashed border-gray-300 rounded-xl p-8 text-center hover:border-red-400 transition-all duration-300 bg-gray-50"
                                    id="upload-area">
                                    <input type="file"
                                        id="file_upload"
                                        name="featured_image"
                                        accept="image/*"
                                        class="hidden"
                                        onchange="previewImage(this)">
                                    <div id="image-preview" class="text-center">
                                        <?php if (!$post['featured_image'] || !file_exists('../../..' . $post['featured_image'])): ?>
                                            <i class="fas fa-cloud-upload-alt text-5xl text-gray-400 mb-4"></i>
                                            <p class="text-gray-600 mb-2 font-medium">Drag & drop or click to upload featured image</p>
                                            <p class="text-sm text-gray-500">PNG, JPG, JPEG, GIF, WEBP up to 5MB</p>
                                        <?php else: ?>
                                            <p class="text-gray-600 mb-2 font-medium">Upload a new image to replace the current one</p>
                                            <p class="text-sm text-gray-500">Or use the buttons above to manage current image</p>
                                        <?php endif; ?>
                                    </div>
                                    <button type="button"
                                        onclick="document.getElementById('file_upload').click()"
                                        class="mt-6 px-6 py-3 btn-primary text-white rounded-xl font-semibold hover:shadow-lg transition-all duration-300">
                                        <i class="fas fa-upload mr-2"></i> Choose Image
                                    </button>
                                </div>

                                <!-- Image Requirements -->
                                <div class="mt-3 text-xs text-gray-500 bg-blue-50 p-3 rounded-lg">
                                    <p class="font-semibold text-blue-800 mb-1">Image Requirements:</p>
                                    <ul class="list-disc list-inside space-y-1">
                                        <li>Recommended size: 1200x800 pixels</li>
                                        <li>Supported formats: JPG, PNG, GIF, WEBP</li>
                                        <li>Maximum file size: 5MB</li>
                                        <li>Images will be stored in: /uploads/blog/</li>
                                    </ul>
                                </div>
                            </div>

                            <!-- Excerpt -->
                            <div>
                                <label for="excerpt" class="block text-sm font-semibold text-gray-800 mb-3 flex items-center">
                                    <i class="fas fa-align-left mr-2 brand-maroon-text"></i>Excerpt
                                </label>
                                <textarea id="excerpt"
                                    name="excerpt"
                                    rows="4"
                                    class="w-full px-4 py-4 border border-gray-300 rounded-xl form-input focus:ring-2 focus:ring-red-500 focus:border-red-500 transition-all duration-300 text-gray-800 placeholder-gray-500"
                                    placeholder="Brief description of the post (appears in blog listing)"><?php echo htmlspecialchars($post['excerpt']); ?></textarea>
                                <p class="text-xs text-gray-500 mt-2">Keep it short and engaging (150-200 characters recommended)</p>
                            </div>

                            <!-- Content -->
                            <div>
                                <label for="content" class="block text-sm font-semibold text-gray-800 mb-3 flex items-center">
                                    <i class="fas fa-edit mr-2 brand-maroon-text"></i>Content *
                                </label>
                                <textarea id="content"
                                    name="content"
                                    required
                                    class="w-full px-4 py-4 border border-gray-300 rounded-xl form-input focus:ring-2 focus:ring-red-500 focus:border-red-500 transition-all duration-300 text-gray-800 placeholder-gray-500"
                                    placeholder="Write your blog post content here..."><?php echo htmlspecialchars($post['content']); ?></textarea>
                            </div>

                            <!-- Checkboxes -->
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 p-6 bg-gray-50 rounded-xl border border-gray-200">
                                <label class="flex items-center space-x-4 cursor-pointer">
                                    <div class="relative">
                                        <input type="checkbox"
                                            name="is_published"
                                            value="1"
                                            <?php echo $post['is_published'] ? 'checked' : ''; ?>
                                            class="sr-only"
                                            id="publishToggle">
                                        <div class="toggle-track w-12 h-6 bg-gray-300 rounded-full shadow-inner transition-colors duration-300"></div>
                                        <div class="toggle-dot absolute left-1 top-1 bg-white w-4 h-4 rounded-full shadow transform transition-transform duration-300"></div>
                                    </div>
                                    <span class="text-gray-800 font-semibold flex items-center">
                                        <i class="fas fa-globe mr-2 text-green-500"></i>Publish Post
                                    </span>
                                </label>

                                <label class="flex items-center space-x-4 cursor-pointer">
                                    <div class="relative">
                                        <input type="checkbox"
                                            name="is_featured"
                                            value="1"
                                            <?php echo $post['is_featured'] ? 'checked' : ''; ?>
                                            class="sr-only"
                                            id="featuredToggle">
                                        <div class="toggle-track w-12 h-6 bg-gray-300 rounded-full shadow-inner transition-colors duration-300"></div>
                                        <div class="toggle-dot absolute left-1 top-1 bg-white w-4 h-4 rounded-full shadow transform transition-transform duration-300"></div>
                                    </div>
                                    <span class="text-gray-800 font-semibold flex items-center">
                                        <i class="fas fa-star mr-2 text-yellow-500"></i>Mark as Featured
                                    </span>
                                </label>
                            </div>

                            <!-- Form Actions -->
                            <div class="flex flex-wrap gap-4 pt-8 border-t border-gray-200">
                                <button type="submit"
                                    class="inline-flex items-center px-8 py-4 btn-primary text-white rounded-xl font-bold hover:shadow-xl transition-all duration-300 transform hover:-translate-y-1 shadow-lg"
                                    id="submitBtn">
                                    <i class="fas fa-save mr-3"></i> Update Post
                                </button>
                                <a href="manage-posts.php"
                                    class="inline-flex items-center px-8 py-4 bg-gray-600 text-white rounded-xl font-bold hover:bg-gray-700 transition-all duration-300 shadow-md">
                                    <i class="fas fa-times mr-3"></i> Cancel
                                </a>
                                <a href="delete-post.php?id=<?php echo $post['id']; ?>"
                                    class="inline-flex items-center px-8 py-4 bg-red-600 text-white rounded-xl font-bold hover:bg-red-700 transition-all duration-300 shadow-md"
                                    onclick="return confirm('Are you sure you want to delete this post? This action cannot be undone.')">
                                    <i class="fas fa-trash mr-3"></i> Delete Post
                                </a>
                            </div>
                        </form>
                    </div>
                </div>

                <!-- Sidebar Tips -->
                <div class="lg:col-span-1">
                    <div class="space-y-6">
                        <!-- Quick Tips -->
                        <div class="bg-white rounded-2xl shadow-xl p-6 border-l-4 border-red-500">
                            <h3 class="font-bold text-gray-900 mb-4 flex items-center">
                                <i class="fas fa-lightbulb mr-2 text-yellow-500"></i> Editing Tips
                            </h3>
                            <ul class="space-y-3">
                                <li class="flex items-start text-sm text-gray-700">
                                    <i class="fas fa-check text-green-500 mr-2 mt-1"></i>
                                    Update titles for better SEO
                                </li>
                                <li class="flex items-start text-sm text-gray-700">
                                    <i class="fas fa-check text-green-500 mr-2 mt-1"></i>
                                    Refresh featured images regularly
                                </li>
                                <li class="flex items-start text-sm text-gray-700">
                                    <i class="fas fa-check text-green-500 mr-2 mt-1"></i>
                                    Keep excerpts compelling
                                </li>
                                <li class="flex items-start text-sm text-gray-700">
                                    <i class="fas fa-check text-green-500 mr-2 mt-1"></i>
                                    Update content for accuracy
                                </li>
                            </ul>
                        </div>

                        <!-- Post Stats -->
                        <div class="bg-gradient-to-br from-red-600 to-red-800 rounded-2xl shadow-xl p-6 text-white">
                            <h3 class="font-bold mb-4 flex items-center">
                                <i class="fas fa-chart-bar mr-2"></i> Post Statistics
                            </h3>
                            <div class="space-y-3 text-sm">
                                <div class="flex justify-between items-center">
                                    <span>Views:</span>
                                    <span class="font-bold"><?php echo $post['view_count']; ?></span>
                                </div>
                                <div class="flex justify-between items-center">
                                    <span>Status:</span>
                                    <span class="font-bold"><?php echo $post['is_published'] ? 'Published' : 'Draft'; ?></span>
                                </div>
                                <div class="flex justify-between items-center">
                                    <span>Featured:</span>
                                    <span class="font-bold"><?php echo $post['is_featured'] ? 'Yes' : 'No'; ?></span>
                                </div>
                                <div class="flex justify-between items-center">
                                    <span>Last Updated:</span>
                                    <span class="font-bold"><?php echo $post['updated_at'] ? date('M j', strtotime($post['updated_at'])) : 'Never'; ?></span>
                                </div>
                            </div>
                        </div>

                        <!-- Quick Actions -->
                        <div class="bg-white rounded-2xl shadow-xl p-6 border-l-4 border-blue-500">
                            <h3 class="font-bold text-gray-900 mb-4 flex items-center">
                                <i class="fas fa-bolt mr-2 text-blue-500"></i> Quick Actions
                            </h3>
                            <div class="space-y-2">
                                <a href="<?php echo $base_url; ?>/pages/blog/post.php?slug=<?php echo $post['slug']; ?>"
                                    target="_blank"
                                    class="w-full flex items-center px-3 py-2 bg-blue-50 text-blue-700 rounded-lg hover:bg-blue-100 transition-colors duration-200 text-sm">
                                    <i class="fas fa-external-link-alt mr-2"></i> View Live
                                </a>
                                <a href="manage-posts.php"
                                    class="w-full flex items-center px-3 py-2 bg-gray-50 text-gray-700 rounded-lg hover:bg-gray-100 transition-colors duration-200 text-sm">
                                    <i class="fas fa-list mr-2"></i> All Posts
                                </a>
                                <a href="add-post.php"
                                    class="w-full flex items-center px-3 py-2 bg-green-50 text-green-700 rounded-lg hover:bg-green-100 transition-colors duration-200 text-sm">
                                    <i class="fas fa-plus mr-2"></i> New Post
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Main Footer Include -->
    <?php include '../../includes/footer.php'; ?>

    <script>
        // Initialize CKEditor
        CKEDITOR.replace('content', {
            toolbar: [{
                    name: 'document',
                    items: ['Source', '-', 'Save', 'NewPage', 'Preview', 'Print', '-', 'Templates']
                },
                {
                    name: 'clipboard',
                    items: ['Cut', 'Copy', 'Paste', 'PasteText', 'PasteFromWord', '-', 'Undo', 'Redo']
                },
                {
                    name: 'editing',
                    items: ['Find', 'Replace', '-', 'SelectAll', '-', 'Scayt']
                },
                {
                    name: 'forms',
                    items: ['Form', 'Checkbox', 'Radio', 'TextField', 'Textarea', 'Select', 'Button', 'ImageButton', 'HiddenField']
                },
                '/',
                {
                    name: 'basicstyles',
                    items: ['Bold', 'Italic', 'Underline', 'Strike', 'Subscript', 'Superscript', '-', 'CopyFormatting', 'RemoveFormat']
                },
                {
                    name: 'paragraph',
                    items: ['NumberedList', 'BulletedList', '-', 'Outdent', 'Indent', '-', 'Blockquote', 'CreateDiv', '-', 'JustifyLeft', 'JustifyCenter', 'JustifyRight', 'JustifyBlock', '-', 'BidiLtr', 'BidiRtl']
                },
                {
                    name: 'links',
                    items: ['Link', 'Unlink', 'Anchor']
                },
                {
                    name: 'insert',
                    items: ['Image', 'Table', 'HorizontalRule', 'Smiley', 'SpecialChar', 'PageBreak', 'Iframe']
                },
                '/',
                {
                    name: 'styles',
                    items: ['Styles', 'Format', 'Font', 'FontSize']
                },
                {
                    name: 'colors',
                    items: ['TextColor', 'BGColor']
                },
                {
                    name: 'tools',
                    items: ['Maximize', 'ShowBlocks']
                }
            ]
        });

        // Image preview functionality
        function previewImage(input) {
            const preview = document.getElementById('image-preview');
            const file = input.files[0];

            if (file) {
                // Validate file type
                const allowedTypes = ['image/jpeg', 'image/jpg', 'image/png', 'image/gif', 'image/webp'];
                if (!allowedTypes.includes(file.type)) {
                    alert('Invalid file type. Please select an image file (JPG, PNG, GIF, WEBP).');
                    input.value = '';
                    return;
                }

                // Validate file size (5MB)
                if (file.size > 5 * 1024 * 1024) {
                    alert('File size too large. Maximum size allowed is 5MB.');
                    input.value = '';
                    return;
                }

                const reader = new FileReader();
                reader.onload = function(e) {
                    preview.innerHTML = `
                        <div class="relative">
                            <img src="${e.target.result}" class="max-h-48 mx-auto rounded-lg shadow-md">
                            <button type="button" onclick="removeNewImage()" class="absolute top-2 right-2 bg-red-500 text-white rounded-full w-6 h-6 flex items-center justify-center text-xs hover:bg-red-600">
                                <i class="fas fa-times"></i>
                            </button>
                        </div>
                        <p class="mt-2 text-sm text-gray-600 font-medium">${file.name}</p>
                        <p class="text-xs text-gray-500">${(file.size / 1024).toFixed(1)} KB</p>
                    `;
                }
                reader.readAsDataURL(file);
            }
        }

        function removeNewImage() {
            document.getElementById('file_upload').value = '';
            document.getElementById('image-preview').innerHTML = `
                <p class="text-gray-600 mb-2 font-medium">Upload a new image to replace the current one</p>
                <p class="text-sm text-gray-500">Or use the buttons above to manage current image</p>
            `;
        }

        function removeCurrentImage() {
            document.getElementById('remove_image').value = '1';
            const currentImageContainer = document.getElementById('current-image-container');
            if (currentImageContainer) {
                currentImageContainer.style.display = 'none';
            }
            document.getElementById('image-preview').innerHTML = `
                <i class="fas fa-cloud-upload-alt text-5xl text-gray-400 mb-4"></i>
                <p class="text-gray-600 mb-2 font-medium">Drag & drop or click to upload featured image</p>
                <p class="text-sm text-gray-500">PNG, JPG, JPEG, GIF, WEBP up to 5MB</p>
            `;
        }

        // Toggle switch functionality
        document.querySelectorAll('input[type="checkbox"]').forEach(checkbox => {
            // Initialize checkbox states
            updateToggle(checkbox);

            checkbox.addEventListener('change', function() {
                updateToggle(this);
            });
        });

        function updateToggle(checkbox) {
            const track = checkbox.parentElement.querySelector('.toggle-track');
            const dot = checkbox.parentElement.querySelector('.toggle-dot');

            if (checkbox.checked) {
                track.classList.add('bg-green-500');
                dot.classList.add('translate-x-6');
            } else {
                track.classList.remove('bg-green-500');
                dot.classList.remove('translate-x-6');
            }
        }

        // Drag and drop functionality
        const uploadArea = document.getElementById('upload-area');
        const fileInput = document.getElementById('file_upload');

        ['dragenter', 'dragover', 'dragleave', 'drop'].forEach(eventName => {
            uploadArea.addEventListener(eventName, preventDefaults, false);
        });

        function preventDefaults(e) {
            e.preventDefault();
            e.stopPropagation();
        }

        ['dragenter', 'dragover'].forEach(eventName => {
            uploadArea.addEventListener(eventName, highlight, false);
        });

        ['dragleave', 'drop'].forEach(eventName => {
            uploadArea.addEventListener(eventName, unhighlight, false);
        });

        function highlight() {
            uploadArea.classList.add('drag-over');
        }

        function unhighlight() {
            uploadArea.classList.remove('drag-over');
        }

        uploadArea.addEventListener('drop', handleDrop, false);

        function handleDrop(e) {
            const dt = e.dataTransfer;
            const files = dt.files;
            fileInput.files = files;
            previewImage(fileInput);
        }

        // Form validation
        document.getElementById('postForm').addEventListener('submit', function(e) {
            const title = document.getElementById('title').value.trim();
            const content = CKEDITOR.instances.content.getData().trim();
            
            if (!title) {
                e.preventDefault();
                alert('Please enter a post title.');
                document.getElementById('title').focus();
                return;
            }
            
            if (!content) {
                e.preventDefault();
                alert('Please enter post content.');
                return;
            }
            
            // Show loading state
            const submitBtn = document.getElementById('submitBtn');
            submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin mr-3"></i> Updating...';
            submitBtn.disabled = true;
        });

        // Character counter for excerpt
        const excerptTextarea = document.getElementById('excerpt');
        if (excerptTextarea) {
            excerptTextarea.addEventListener('input', function() {
                const charCount = this.value.length;
                const counter = document.getElementById('excerpt-counter') || createCounter();
                counter.textContent = `${charCount} characters`;
                
                if (charCount > 200) {
                    counter.classList.add('text-red-500');
                } else {
                    counter.classList.remove('text-red-500');
                }
            });
            
            function createCounter() {
                const counter = document.createElement('p');
                counter.id = 'excerpt-counter';
                counter.className = 'text-xs text-gray-500 mt-1';
                excerptTextarea.parentNode.appendChild(counter);
                return counter;
            }
            
            // Initialize counter
            excerptTextarea.dispatchEvent(new Event('input'));
        }
    </script>
</body>

</html>