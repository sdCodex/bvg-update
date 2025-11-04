<?php
require_once '../../includes/db.php';
require_once '../auth.php';

// Get categories for dropdown
$categories = $pdo->query("SELECT * FROM blog_categories WHERE is_active = TRUE")->fetchAll(PDO::FETCH_ASSOC);

// Handle form submission
if($_POST) {
    $title = $_POST['title'];
    $slug = createSlug($title);
    $excerpt = $_POST['excerpt'];
    $content = $_POST['content'];
    $category_id = $_POST['category_id'];
    $is_published = isset($_POST['is_published']) ? 1 : 0;
    $is_featured = isset($_POST['is_featured']) ? 1 : 0;
    $author_id = $_SESSION['admin_id'] ?? 1;
    
    // Handle file upload
    $featured_image = '';
    if(isset($_FILES['featured_image']) && $_FILES['featured_image']['error'] == 0) {
        $upload_dir = '../../../uploads/blog/';
        if(!is_dir($upload_dir)) {
            mkdir($upload_dir, 0755, true);
        }
        
        $file_extension = pathinfo($_FILES['featured_image']['name'], PATHINFO_EXTENSION);
        $filename = $slug . '-' . time() . '.' . $file_extension;
        $file_path = $upload_dir . $filename;
        
        if(move_uploaded_file($_FILES['featured_image']['tmp_name'], $file_path)) {
            $featured_image = '/uploads/blog/' . $filename;
        }
    }
    
    try {
        $stmt = $pdo->prepare("
            INSERT INTO blog_posts (title, slug, excerpt, content, featured_image, category_id, author_id, is_published, is_featured, published_at) 
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)
        ");
        
        $published_at = $is_published ? date('Y-m-d H:i:s') : null;
        
        $stmt->execute([
            $title, $slug, $excerpt, $content, $featured_image, $category_id, $author_id, $is_published, $is_featured, $published_at
        ]);
        
        $_SESSION['success'] = "Blog post created successfully!";
        header("Location: manage-posts.php");
        exit;
        
    } catch(PDOException $e) {
        $error = "Error creating post: " . $e->getMessage();
    }
}

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
    <title>Add Blog Post - Admin | Bhaktivedanta Gurukul</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <!-- Include CKEditor -->
    <script src="https://cdn.ckeditor.com/4.16.2/standard/ckeditor.js"></script>
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
        .form-input:focus {
            box-shadow: 0 0 0 3px rgba(128, 0, 0, 0.1);
            border-color: #800000;
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
                            <i class="fas fa-user-shield text-xl"></i>
                        </div>
                        <div>
                            <h1 class="text-2xl font-bold">Admin Dashboard</h1>
                            <p class="text-white/90">Welcome back, <span class="font-semibold"><?php echo $_SESSION['admin_name'] ?? 'Administrator'; ?></span></p>
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
                    <a href="manage-posts.php" class="px-4 py-2 bg-white/10 text-white rounded-lg hover:bg-white/20 transition-all duration-300 flex items-center text-sm border border-white/20">
                        <i class="fas fa-list mr-2"></i> All Posts
                    </a>
                    <a href="add-post.php" class="px-4 py-2 bg-white/30 text-white rounded-lg transition-all duration-300 flex items-center text-sm border border-white/30">
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

            <!-- Page Header -->
            <div class="flex justify-between items-center mb-8">
                <div>
                    <h1 class="text-3xl font-bold text-gray-900 mb-2">Add New Blog Post</h1>
                    <p class="text-gray-600 text-lg">Create engaging content for your audience</p>
                </div>
                <a href="manage-posts.php" 
                   class="inline-flex items-center px-5 py-3 brand-brown text-white rounded-lg hover:bg-gray-800 transition-all duration-300 shadow-md">
                    <i class="fas fa-arrow-left mr-2"></i> Back to Posts
                </a>
            </div>

            <!-- Error Message -->
            <?php if(isset($error)): ?>
                <div class="mb-6 bg-red-50 border border-red-200 text-red-700 px-4 py-4 rounded-xl flex items-center animate-pulse">
                    <i class="fas fa-exclamation-circle mr-3 text-red-500"></i>
                    <div>
                        <strong class="font-semibold">Error:</strong> <?php echo $error; ?>
                    </div>
                </div>
            <?php endif; ?>

            <!-- Success Message -->
            <?php if(isset($_SESSION['success'])): ?>
                <div class="mb-6 bg-green-50 border border-green-200 text-green-700 px-4 py-4 rounded-xl flex items-center">
                    <i class="fas fa-check-circle mr-3 text-green-500"></i>
                    <div>
                        <strong class="font-semibold">Success:</strong> <?php echo $_SESSION['success']; unset($_SESSION['success']); ?>
                    </div>
                </div>
            <?php endif; ?>

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
                                <h2 class="text-xl font-bold text-gray-900">Post Information</h2>
                                <p class="text-gray-600">Fill in the details for your new blog post</p>
                            </div>
                        </div>

                        <form method="POST" enctype="multipart/form-data" class="space-y-8">
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
                                           placeholder="Enter a compelling title"
                                           value="<?php echo $_POST['title'] ?? ''; ?>">
                                </div>
                                
                                <div>
                                    <label for="category_id" class="block text-sm font-semibold text-gray-800 mb-3 flex items-center">
                                        <i class="fas fa-folder mr-2 brand-maroon-text"></i>Category
                                    </label>
                                    <select id="category_id" 
                                            name="category_id"
                                            class="w-full px-4 py-4 border border-gray-300 rounded-xl form-input focus:ring-2 focus:ring-red-500 focus:border-red-500 transition-all duration-300 text-gray-800">
                                        <option value="">Select Category</option>
                                        <?php foreach($categories as $category): ?>
                                            <option value="<?php echo $category['id']; ?>" <?php echo ($_POST['category_id'] ?? '') == $category['id'] ? 'selected' : ''; ?>>
                                                <?php echo htmlspecialchars($category['name']); ?>
                                            </option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                            </div>

                            <!-- Featured Image -->
                            <div>
                                <label for="featured_image" class="block text-sm font-semibold text-gray-800 mb-3 flex items-center">
                                    <i class="fas fa-image mr-2 brand-maroon-text"></i>Featured Image
                                </label>
                                <div class="border-2 border-dashed border-gray-300 rounded-xl p-8 text-center hover:border-red-400 transition-all duration-300 bg-gray-50">
                                    <input type="file" 
                                           id="featured_image" 
                                           name="featured_image" 
                                           accept="image/*"
                                           class="hidden"
                                           onchange="previewImage(this)">
                                    <div id="image-preview" class="text-center">
                                        <i class="fas fa-cloud-upload-alt text-5xl text-gray-400 mb-4"></i>
                                        <p class="text-gray-600 mb-2 font-medium">Drag & drop or click to upload</p>
                                        <p class="text-sm text-gray-500">PNG, JPG, JPEG up to 5MB</p>
                                    </div>
                                    <button type="button" 
                                            onclick="document.getElementById('featured_image').click()"
                                            class="mt-6 px-6 py-3 btn-primary text-white rounded-xl font-semibold hover:shadow-lg transition-all duration-300">
                                        <i class="fas fa-upload mr-2"></i>Choose Image
                                    </button>
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
                                          placeholder="Write a brief description that will appear in blog listings..."><?php echo $_POST['excerpt'] ?? ''; ?></textarea>
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
                                          placeholder="Write your main content here..."><?php echo $_POST['content'] ?? ''; ?></textarea>
                            </div>

                            <!-- Checkboxes -->
                            <div class="flex flex-wrap gap-8 p-6 bg-gray-50 rounded-xl border border-gray-200">
                                <label class="flex items-center space-x-4 cursor-pointer group">
                                    <div class="relative">
                                        <input type="checkbox" 
                                               name="is_published" 
                                               value="1" 
                                               <?php echo isset($_POST['is_published']) ? 'checked' : 'checked'; ?>
                                               class="sr-only">
                                        <div class="toggle-track w-12 h-6 bg-gray-300 rounded-full shadow-inner transition-colors duration-300 group-hover:bg-gray-400"></div>
                                        <div class="toggle-dot absolute left-1 top-1 bg-white w-4 h-4 rounded-full shadow transform transition-transform duration-300"></div>
                                    </div>
                                    <span class="text-gray-800 font-semibold flex items-center">
                                        <i class="fas fa-globe mr-2 brand-maroon-text"></i>Publish Immediately
                                    </span>
                                </label>
                                
                                <label class="flex items-center space-x-4 cursor-pointer group">
                                    <div class="relative">
                                        <input type="checkbox" 
                                               name="is_featured" 
                                               value="1"
                                               <?php echo isset($_POST['is_featured']) ? 'checked' : ''; ?>
                                               class="sr-only">
                                        <div class="toggle-track w-12 h-6 bg-gray-300 rounded-full shadow-inner transition-colors duration-300 group-hover:bg-gray-400"></div>
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
                                        class="inline-flex items-center px-8 py-4 btn-primary text-white rounded-xl font-bold hover:shadow-xl transition-all duration-300 transform hover:-translate-y-1 shadow-lg">
                                    <i class="fas fa-plus-circle mr-3"></i> Create Blog Post
                                </button>
                                <a href="manage-posts.php" 
                                   class="inline-flex items-center px-8 py-4 bg-gray-600 text-white rounded-xl font-bold hover:bg-gray-700 transition-all duration-300 shadow-md">
                                    <i class="fas fa-times mr-3"></i> Cancel
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
                                <i class="fas fa-lightbulb mr-2 text-yellow-500"></i> Quick Tips
                            </h3>
                            <ul class="space-y-3">
                                <li class="flex items-start text-sm text-gray-700">
                                    <i class="fas fa-check text-green-500 mr-2 mt-1"></i>
                                    Use descriptive, SEO-friendly titles
                                </li>
                                <li class="flex items-start text-sm text-gray-700">
                                    <i class="fas fa-check text-green-500 mr-2 mt-1"></i>
                                    Add compelling excerpts for engagement
                                </li>
                                <li class="flex items-start text-sm text-gray-700">
                                    <i class="fas fa-check text-green-500 mr-2 mt-1"></i>
                                    Use high-quality featured images
                                </li>
                                <li class="flex items-start text-sm text-gray-700">
                                    <i class="fas fa-check text-green-500 mr-2 mt-1"></i>
                                    Format content with proper headings
                                </li>
                            </ul>
                        </div>

                        <!-- Stats Card -->
                        <div class="bg-gradient-to-br from-red-600 to-red-800 rounded-2xl shadow-xl p-6 text-white">
                            <h3 class="font-bold mb-4 flex items-center">
                                <i class="fas fa-chart-bar mr-2"></i> Quick Stats
                            </h3>
                            <div class="space-y-3">
                                <div class="flex justify-between items-center">
                                    <span class="text-sm opacity-90">Total Posts:</span>
                                    <span class="font-bold"><?php 
                                        $total_posts = $pdo->query("SELECT COUNT(*) FROM blog_posts")->fetchColumn();
                                        echo $total_posts;
                                    ?></span>
                                </div>
                                <div class="flex justify-between items-center">
                                    <span class="text-sm opacity-90">Categories:</span>
                                    <span class="font-bold"><?php echo count($categories); ?></span>
                                </div>
                                <div class="flex justify-between items-center">
                                    <span class="text-sm opacity-90">Featured:</span>
                                    <span class="font-bold"><?php 
                                        $featured = $pdo->query("SELECT COUNT(*) FROM blog_posts WHERE is_featured = 1")->fetchColumn();
                                        echo $featured;
                                    ?></span>
                                </div>
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
            toolbar: [
                { name: 'document', items: ['Source', '-', 'Save', 'NewPage', 'Preview', 'Print', '-', 'Templates'] },
                { name: 'clipboard', items: ['Cut', 'Copy', 'Paste', 'PasteText', 'PasteFromWord', '-', 'Undo', 'Redo'] },
                { name: 'editing', items: ['Find', 'Replace', '-', 'SelectAll', '-', 'Scayt'] },
                { name: 'forms', items: ['Form', 'Checkbox', 'Radio', 'TextField', 'Textarea', 'Select', 'Button', 'ImageButton', 'HiddenField'] },
                '/',
                { name: 'basicstyles', items: ['Bold', 'Italic', 'Underline', 'Strike', 'Subscript', 'Superscript', '-', 'CopyFormatting', 'RemoveFormat'] },
                { name: 'paragraph', items: ['NumberedList', 'BulletedList', '-', 'Outdent', 'Indent', '-', 'Blockquote', 'CreateDiv', '-', 'JustifyLeft', 'JustifyCenter', 'JustifyRight', 'JustifyBlock', '-', 'BidiLtr', 'BidiRtl'] },
                { name: 'links', items: ['Link', 'Unlink', 'Anchor'] },
                { name: 'insert', items: ['Image', 'Table', 'HorizontalRule', 'Smiley', 'SpecialChar', 'PageBreak', 'Iframe'] },
                '/',
                { name: 'styles', items: ['Styles', 'Format', 'Font', 'FontSize'] },
                { name: 'colors', items: ['TextColor', 'BGColor'] },
                { name: 'tools', items: ['Maximize', 'ShowBlocks'] }
            ]
        });

        // Image preview functionality
        function previewImage(input) {
            const preview = document.getElementById('image-preview');
            const file = input.files[0];
            
            if (file) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    preview.innerHTML = `
                        <div class="relative">
                            <img src="${e.target.result}" class="max-h-48 mx-auto rounded-lg shadow-md">
                            <button type="button" onclick="removeImage()" class="absolute top-2 right-2 bg-red-500 text-white rounded-full w-6 h-6 flex items-center justify-center text-xs hover:bg-red-600">
                                <i class="fas fa-times"></i>
                            </button>
                        </div>
                        <p class="mt-2 text-sm text-gray-600 font-medium">${file.name}</p>
                    `;
                }
                reader.readAsDataURL(file);
            }
        }

        function removeImage() {
            document.getElementById('featured_image').value = '';
            document.getElementById('image-preview').innerHTML = `
                <i class="fas fa-cloud-upload-alt text-5xl text-gray-400 mb-4"></i>
                <p class="text-gray-600 mb-2 font-medium">Drag & drop or click to upload</p>
                <p class="text-sm text-gray-500">PNG, JPG, JPEG up to 5MB</p>
            `;
        }

        // Custom checkbox functionality
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
    </script>
</body>
</html>