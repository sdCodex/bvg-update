<?php
require_once '../../includes/db.php';
require_once '../auth.php';

// Handle form submission
if($_POST) {
    $title = $_POST['title'];
    $description = $_POST['description'];
    $category = $_POST['category'];
    $class_level = $_POST['class_level'];
    $file_type = $_POST['file_type'];
    $is_active = isset($_POST['is_active']) ? 1 : 0;
    
    // Handle file upload
    $file_path = '';
    if(isset($_FILES['file_upload']) && $_FILES['file_upload']['error'] == 0) {
        $upload_dir = '../../../uploads/downloads/';
        if(!is_dir($upload_dir)) {
            mkdir($upload_dir, 0755, true);
        }
        
        $file_extension = pathinfo($_FILES['file_upload']['name'], PATHINFO_EXTENSION);
        $filename = 'download-' . time() . '-' . uniqid() . '.' . $file_extension;
        $file_path_full = $upload_dir . $filename;
        
        if(move_uploaded_file($_FILES['file_upload']['tmp_name'], $file_path_full)) {
            $file_path = '/uploads/downloads/' . $filename;
            $file_type = $file_extension; // Auto-detect file type from extension
        } else {
            $error = "Error uploading file. Please try again.";
        }
    } else {
        $error = "Please select a file to upload.";
    }
    
    if(empty($error)) {
        try {
            $stmt = $pdo->prepare("
                INSERT INTO downloads (title, description, category, class_level, file_type, file_path, is_active, download_count) 
                VALUES (?, ?, ?, ?, ?, ?, ?, 0)
            ");
            
            $stmt->execute([
                $title, $description, $category, $class_level, $file_type, $file_path, $is_active
            ]);
            
            $_SESSION['success'] = "Download added successfully!";
            header("Location: manage-downloads.php");
            exit;
            
        } catch(PDOException $e) {
            $error = "Error creating download: " . $e->getMessage();
        }
    }
}

// Available categories and file types
$categories = [
    'syllabus' => 'Syllabus',
    'question_paper' => 'Question Paper',
    'study_material' => 'Study Material',
    'notes' => 'Class Notes',
    'assignment' => 'Assignment',
    'other' => 'Other'
];

$file_types = [
    'pdf' => 'PDF Document',
    'doc' => 'Word Document',
    'docx' => 'Word Document',
    'xls' => 'Excel Spreadsheet',
    'xlsx' => 'Excel Spreadsheet',
    'ppt' => 'PowerPoint',
    'pptx' => 'PowerPoint',
    'zip' => 'ZIP Archive',
    'txt' => 'Text File'
];

$classes = range(1, 12);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add Download - Admin | Bhaktivedanta Gurukul</title>
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
        .form-input:focus {
            box-shadow: 0 0 0 3px rgba(128, 0, 0, 0.1);
            border-color: #800000;
        }
        .file-drop-zone {
            transition: all 0.3s ease;
        }
        .file-drop-zone.dragover {
            border-color: #800000;
            background-color: #fef2f2;
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
                            <i class="fas fa-download text-xl"></i>
                        </div>
                        <div>
                            <h1 class="text-2xl font-bold">Downloads Management</h1>
                            <p class="text-white/90">Add new downloadable resources</p>
                        </div>
                    </div>
                    
                    <div class="flex flex-wrap gap-3">
                        <a href="../dashboard.php" 
                           class="px-5 py-2 bg-white/20 text-white rounded-lg hover:bg-white/30 transition-all duration-300 flex items-center border border-white/30">
                            <i class="fas fa-tachometer-alt mr-2"></i> Dashboard
                        </a>
                        <a href="manage-downloads.php" 
                           class="px-5 py-2 bg-white/20 text-white rounded-lg hover:bg-white/30 transition-all duration-300 flex items-center border border-white/30">
                            <i class="fas fa-list mr-2"></i> All Downloads
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
                    <h1 class="text-3xl font-bold text-gray-900 mb-2">Add New Download</h1>
                    <p class="text-gray-600 text-lg">Upload and share resources with students</p>
                </div>
                <a href="manage-downloads.php" 
                   class="inline-flex items-center px-5 py-3 brand-brown text-white rounded-lg hover:bg-gray-800 transition-all duration-300 shadow-md">
                    <i class="fas fa-arrow-left mr-2"></i> Back to Downloads
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

            <!-- Main Content Grid -->
            <div class="grid grid-cols-1 lg:grid-cols-4 gap-8">
                <!-- Form Section -->
                <div class="lg:col-span-3">
                    <div class="bg-white rounded-2xl shadow-xl p-8">
                        <div class="flex items-center mb-6 pb-4 border-b border-gray-200">
                            <div class="w-10 h-10 brand-maroon rounded-full flex items-center justify-center mr-4">
                                <i class="fas fa-file-upload text-white"></i>
                            </div>
                            <div>
                                <h2 class="text-xl font-bold text-gray-900">Download Information</h2>
                                <p class="text-gray-600">Fill in the details for your new download</p>
                            </div>
                        </div>

                        <form method="POST" enctype="multipart/form-data" class="space-y-8">
                            <!-- Title and Category Row -->
                            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                                <div>
                                    <label for="title" class="block text-sm font-semibold text-gray-800 mb-3 flex items-center">
                                        <i class="fas fa-heading mr-2 brand-maroon-text"></i>Title *
                                    </label>
                                    <input type="text" 
                                           id="title" 
                                           name="title" 
                                           required
                                           class="w-full px-4 py-4 border border-gray-300 rounded-xl form-input focus:ring-2 focus:ring-red-500 focus:border-red-500 transition-all duration-300 text-gray-800 placeholder-gray-500"
                                           placeholder="Enter download title"
                                           value="<?php echo $_POST['title'] ?? ''; ?>">
                                </div>
                                
                                <div>
                                    <label for="category" class="block text-sm font-semibold text-gray-800 mb-3 flex items-center">
                                        <i class="fas fa-folder mr-2 brand-maroon-text"></i>Category *
                                    </label>
                                    <select id="category" 
                                            name="category"
                                            required
                                            class="w-full px-4 py-4 border border-gray-300 rounded-xl form-input focus:ring-2 focus:ring-red-500 focus:border-red-500 transition-all duration-300 text-gray-800">
                                        <option value="">Select Category</option>
                                        <?php foreach($categories as $key => $value): ?>
                                            <option value="<?php echo $key; ?>" <?php echo ($_POST['category'] ?? '') == $key ? 'selected' : ''; ?>>
                                                <?php echo $value; ?>
                                            </option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                            </div>

                            <!-- Class Level and File Type -->
                            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                                <div>
                                    <label for="class_level" class="block text-sm font-semibold text-gray-800 mb-3 flex items-center">
                                        <i class="fas fa-graduation-cap mr-2 brand-maroon-text"></i>Class Level
                                    </label>
                                    <select id="class_level" 
                                            name="class_level"
                                            class="w-full px-4 py-4 border border-gray-300 rounded-xl form-input focus:ring-2 focus:ring-red-500 focus:border-red-500 transition-all duration-300 text-gray-800">
                                        <option value="">All Classes</option>
                                        <?php foreach($classes as $class): ?>
                                            <option value="<?php echo $class; ?>" <?php echo ($_POST['class_level'] ?? '') == $class ? 'selected' : ''; ?>>
                                                Class <?php echo $class; ?>
                                            </option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                                
                                <div>
                                    <label for="file_type" class="block text-sm font-semibold text-gray-800 mb-3 flex items-center">
                                        <i class="fas fa-file-alt mr-2 brand-maroon-text"></i>File Type
                                    </label>
                                    <select id="file_type" 
                                            name="file_type"
                                            class="w-full px-4 py-4 border border-gray-300 rounded-xl form-input focus:ring-2 focus:ring-red-500 focus:border-red-500 transition-all duration-300 text-gray-800">
                                        <option value="">Auto-detect</option>
                                        <?php foreach($file_types as $key => $value): ?>
                                            <option value="<?php echo $key; ?>" <?php echo ($_POST['file_type'] ?? '') == $key ? 'selected' : ''; ?>>
                                                <?php echo $value; ?>
                                            </option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                            </div>

                            <!-- Description -->
                            <div>
                                <label for="description" class="block text-sm font-semibold text-gray-800 mb-3 flex items-center">
                                    <i class="fas fa-align-left mr-2 brand-maroon-text"></i>Description
                                </label>
                                <textarea id="description" 
                                          name="description" 
                                          rows="4"
                                          class="w-full px-4 py-4 border border-gray-300 rounded-xl form-input focus:ring-2 focus:ring-red-500 focus:border-red-500 transition-all duration-300 text-gray-800 placeholder-gray-500"
                                          placeholder="Describe what this download contains..."><?php echo $_POST['description'] ?? ''; ?></textarea>
                            </div>

                            <!-- File Upload -->
                            <div>
                                <label class="block text-sm font-semibold text-gray-800 mb-3 flex items-center">
                                    <i class="fas fa-file-upload mr-2 brand-maroon-text"></i>File Upload *
                                </label>
                                <div class="file-drop-zone border-2 border-dashed border-gray-300 rounded-xl p-8 text-center hover:border-red-400 transition-all duration-300 bg-gray-50"
                                     id="fileDropZone">
                                    <input type="file" 
                                           id="file_upload" 
                                           name="file_upload" 
                                           required
                                           class="hidden"
                                           onchange="previewFile(this)">
                                    <div id="file-preview" class="text-center">
                                        <i class="fas fa-cloud-upload-alt text-5xl text-gray-400 mb-4"></i>
                                        <p class="text-gray-600 mb-2 font-medium">Drag & drop your file here or click to browse</p>
                                        <p class="text-sm text-gray-500 mb-4">Maximum file size: 50MB</p>
                                        <div class="flex flex-wrap gap-4 justify-center text-xs text-gray-500">
                                            <span class="flex items-center">
                                                <i class="fas fa-file-pdf text-red-500 mr-1"></i> PDF
                                            </span>
                                            <span class="flex items-center">
                                                <i class="fas fa-file-word text-blue-500 mr-1"></i> DOC/DOCX
                                            </span>
                                            <span class="flex items-center">
                                                <i class="fas fa-file-excel text-green-500 mr-1"></i> XLS/XLSX
                                            </span>
                                            <span class="flex items-center">
                                                <i class="fas fa-file-archive text-yellow-500 mr-1"></i> ZIP
                                            </span>
                                        </div>
                                    </div>
                                    <button type="button" 
                                            onclick="document.getElementById('file_upload').click()"
                                            class="mt-6 px-6 py-3 btn-primary text-white rounded-xl font-semibold hover:shadow-lg transition-all duration-300">
                                        <i class="fas fa-folder-open mr-2"></i> Choose File
                                    </button>
                                </div>
                                <div id="selected-file" class="mt-4 hidden">
                                    <!-- Selected file info will appear here -->
                                </div>
                            </div>

                            <!-- Status Toggle -->
                            <div class="flex items-center justify-between p-6 bg-gray-50 rounded-xl border border-gray-200">
                                <div class="flex items-center space-x-4">
                                    <div class="w-12 h-12 bg-green-100 rounded-full flex items-center justify-center">
                                        <i class="fas fa-toggle-on text-green-500 text-xl"></i>
                                    </div>
                                    <div>
                                        <h4 class="font-semibold text-gray-800">Download Status</h4>
                                        <p class="text-sm text-gray-600">Make this download available to students</p>
                                    </div>
                                </div>
                                <label class="flex items-center space-x-4 cursor-pointer">
                                    <div class="relative">
                                        <input type="checkbox" 
                                               name="is_active" 
                                               value="1" 
                                               <?php echo isset($_POST['is_active']) ? 'checked' : 'checked'; ?>
                                               class="sr-only"
                                               id="statusToggle">
                                        <div class="toggle-track w-14 h-7 bg-gray-300 rounded-full shadow-inner transition-colors duration-300"></div>
                                        <div class="toggle-dot absolute left-1 top-1 bg-white w-5 h-5 rounded-full shadow transform transition-transform duration-300"></div>
                                    </div>
                                    <span class="text-gray-800 font-semibold" id="statusText">Active</span>
                                </label>
                            </div>

                            <!-- Form Actions -->
                            <div class="flex flex-wrap gap-4 pt-8 border-t border-gray-200">
                                <button type="submit" 
                                        class="inline-flex items-center px-8 py-4 btn-primary text-white rounded-xl font-bold hover:shadow-xl transition-all duration-300 transform hover:-translate-y-1 shadow-lg">
                                    <i class="fas fa-plus-circle mr-3"></i> Add Download
                                </button>
                                <a href="manage-downloads.php" 
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
                                <i class="fas fa-lightbulb mr-2 text-yellow-500"></i> Upload Tips
                            </h3>
                            <ul class="space-y-3">
                                <li class="flex items-start text-sm text-gray-700">
                                    <i class="fas fa-check text-green-500 mr-2 mt-1"></i>
                                    Use descriptive titles for better search
                                </li>
                                <li class="flex items-start text-sm text-gray-700">
                                    <i class="fas fa-check text-green-500 mr-2 mt-1"></i>
                                    Choose appropriate categories
                                </li>
                                <li class="flex items-start text-sm text-gray-700">
                                    <i class="fas fa-check text-green-500 mr-2 mt-1"></i>
                                    Add clear descriptions
                                </li>
                                <li class="flex items-start text-sm text-gray-700">
                                    <i class="fas fa-check text-green-500 mr-2 mt-1"></i>
                                    Maximum file size: 50MB
                                </li>
                            </ul>
                        </div>

                        <!-- Supported Formats -->
                        <div class="bg-white rounded-2xl shadow-xl p-6 border-l-4 border-blue-500">
                            <h3 class="font-bold text-gray-900 mb-4 flex items-center">
                                <i class="fas fa-file-alt mr-2 text-blue-500"></i> Supported Formats
                            </h3>
                            <div class="space-y-2">
                                <?php foreach($file_types as $key => $value): ?>
                                <div class="flex items-center text-sm text-gray-700">
                                    <i class="fas fa-file-<?php echo getFileIcon($key); ?> text-<?php echo getFileColor($key); ?>-500 mr-2 w-4"></i>
                                    <span class="font-mono text-xs bg-gray-100 px-2 py-1 rounded">.<?php echo $key; ?></span>
                                    <span class="ml-2 text-gray-600"><?php echo $value; ?></span>
                                </div>
                                <?php endforeach; ?>
                            </div>
                        </div>

                        <!-- File Size Info -->
                        <div class="bg-gradient-to-br from-red-600 to-red-800 rounded-2xl shadow-xl p-6 text-white">
                            <h3 class="font-bold mb-4 flex items-center">
                                <i class="fas fa-info-circle mr-2"></i> File Guidelines
                            </h3>
                            <div class="space-y-3 text-sm">
                                <div class="flex items-center">
                                    <i class="fas fa-check-circle mr-2 text-green-300"></i>
                                    <span>Max file size: 50MB</span>
                                </div>
                                <div class="flex items-center">
                                    <i class="fas fa-check-circle mr-2 text-green-300"></i>
                                    <span>Virus scan enabled</span>
                                </div>
                                <div class="flex items-center">
                                    <i class="fas fa-check-circle mr-2 text-green-300"></i>
                                    <span>Auto file type detection</span>
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
        // File upload preview functionality
        function previewFile(input) {
            const preview = document.getElementById('file-preview');
            const selectedFile = document.getElementById('selected-file');
            const file = input.files[0];
            
            if (file) {
                const fileSize = (file.size / (1024 * 1024)).toFixed(2); // Convert to MB
                const fileExtension = file.name.split('.').pop().toLowerCase();
                
                // Show selected file info
                selectedFile.innerHTML = `
                    <div class="bg-green-50 border border-green-200 rounded-xl p-4">
                        <div class="flex items-center justify-between">
                            <div class="flex items-center">
                                <div class="w-10 h-10 bg-green-100 rounded-lg flex items-center justify-center mr-4">
                                    <i class="fas fa-file-${getFileIcon(fileExtension)} text-green-500"></i>
                                </div>
                                <div>
                                    <div class="font-semibold text-gray-900">${file.name}</div>
                                    <div class="text-sm text-gray-600">${fileSize} MB • ${getFileType(fileExtension)}</div>
                                </div>
                            </div>
                            <button type="button" onclick="removeFile()" class="text-red-500 hover:text-red-700">
                                <i class="fas fa-times"></i>
                            </button>
                        </div>
                    </div>
                `;
                selectedFile.classList.remove('hidden');
                
                // Update preview
                preview.innerHTML = `
                    <div class="text-green-500">
                        <i class="fas fa-check-circle text-5xl mb-4"></i>
                        <p class="text-green-600 font-semibold mb-2">File Selected</p>
                        <p class="text-sm text-green-500">Ready to upload</p>
                    </div>
                `;
            }
        }

        function removeFile() {
            document.getElementById('file_upload').value = '';
            document.getElementById('selected-file').classList.add('hidden');
            document.getElementById('file-preview').innerHTML = `
                <i class="fas fa-cloud-upload-alt text-5xl text-gray-400 mb-4"></i>
                <p class="text-gray-600 mb-2 font-medium">Drag & drop your file here or click to browse</p>
                <p class="text-sm text-gray-500 mb-4">Maximum file size: 50MB</p>
                <div class="flex flex-wrap gap-4 justify-center text-xs text-gray-500">
                    <span class="flex items-center">
                        <i class="fas fa-file-pdf text-red-500 mr-1"></i> PDF
                    </span>
                    <span class="flex items-center">
                        <i class="fas fa-file-word text-blue-500 mr-1"></i> DOC/DOCX
                    </span>
                    <span class="flex items-center">
                        <i class="fas fa-file-excel text-green-500 mr-1"></i> XLS/XLSX
                    </span>
                    <span class="flex items-center">
                        <i class="fas fa-file-archive text-yellow-500 mr-1"></i> ZIP
                    </span>
                </div>
            `;
        }

        // Drag and drop functionality
        const dropZone = document.getElementById('fileDropZone');
        const fileInput = document.getElementById('file_upload');

        ['dragenter', 'dragover', 'dragleave', 'drop'].forEach(eventName => {
            dropZone.addEventListener(eventName, preventDefaults, false);
        });

        function preventDefaults(e) {
            e.preventDefault();
            e.stopPropagation();
        }

        ['dragenter', 'dragover'].forEach(eventName => {
            dropZone.addEventListener(eventName, highlight, false);
        });

        ['dragleave', 'drop'].forEach(eventName => {
            dropZone.addEventListener(eventName, unhighlight, false);
        });

        function highlight() {
            dropZone.classList.add('dragover');
        }

        function unhighlight() {
            dropZone.classList.remove('dragover');
        }

        dropZone.addEventListener('drop', handleDrop, false);

        function handleDrop(e) {
            const dt = e.dataTransfer;
            const files = dt.files;
            fileInput.files = files;
            previewFile(fileInput);
        }

        // Toggle switch functionality
        const toggle = document.getElementById('statusToggle');
        const statusText = document.getElementById('statusText');

        function updateToggle() {
            const track = toggle.parentElement.querySelector('.toggle-track');
            const dot = toggle.parentElement.querySelector('.toggle-dot');
            
            if (toggle.checked) {
                track.classList.add('bg-green-500');
                dot.classList.add('translate-x-7');
                statusText.textContent = 'Active';
                statusText.classList.remove('text-gray-600');
                statusText.classList.add('text-green-600');
            } else {
                track.classList.remove('bg-green-500');
                dot.classList.remove('translate-x-7');
                statusText.textContent = 'Inactive';
                statusText.classList.remove('text-green-600');
                statusText.classList.add('text-gray-600');
            }
        }

        toggle.addEventListener('change', updateToggle);
        updateToggle(); // Initialize

        // Helper functions
        function getFileIcon(extension) {
            const icons = {
                'pdf': 'pdf',
                'doc': 'word',
                'docx': 'word',
                'xls': 'excel',
                'xlsx': 'excel',
                'ppt': 'powerpoint',
                'pptx': 'powerpoint',
                'zip': 'archive',
                'rar': 'archive',
                'txt': 'alt'
            };
            return icons[extension] || 'file';
        }

        function getFileType(extension) {
            const types = {
                'pdf': 'PDF Document',
                'doc': 'Word Document',
                'docx': 'Word Document',
                'xls': 'Excel Spreadsheet',
                'xlsx': 'Excel Spreadsheet',
                'ppt': 'PowerPoint',
                'pptx': 'PowerPoint',
                'zip': 'ZIP Archive',
                'txt': 'Text File'
            };
            return types[extension] || 'File';
        }
    </script>
</body>
</html>

<?php
function getFileIcon($fileType) {
    $icons = [
        'pdf' => 'pdf',
        'doc' => 'word',
        'docx' => 'word',
        'xls' => 'excel',
        'xlsx' => 'excel',
        'ppt' => 'powerpoint',
        'pptx' => 'powerpoint',
        'zip' => 'archive',
        'rar' => 'archive',
        'txt' => 'alt'
    ];
    
    return $icons[strtolower($fileType)] ?? 'file';
}

function getFileColor($fileType) {
    $colors = [
        'pdf' => 'red',
        'doc' => 'blue',
        'docx' => 'blue',
        'xls' => 'green',
        'xlsx' => 'green',
        'ppt' => 'orange',
        'pptx' => 'orange',
        'zip' => 'yellow',
        'txt' => 'gray'
    ];
    
    return $colors[strtolower($fileType)] ?? 'gray';
}
?>