<?php
require_once '../../includes/db.php';
require_once '../auth.php';

// Handle actions
if(isset($_GET['action']) && isset($_GET['id'])) {
    $id = (int)$_GET['id'];
    
    if($_GET['action'] == 'delete') {
        // First get file path to delete physical file
        $stmt = $pdo->prepare("SELECT file_path FROM downloads WHERE id = ?");
        $stmt->execute([$id]);
        $file = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if($file && file_exists('../../..' . $file['file_path'])) {
            unlink('../../..' . $file['file_path']);
        }
        
        $stmt = $pdo->prepare("DELETE FROM downloads WHERE id = ?");
        $stmt->execute([$id]);
        $_SESSION['success'] = "Download deleted successfully";
    }
    header("Location: manage-downloads.php");
    exit;
}

// Get all downloads
$downloads = $pdo->query("
    SELECT * FROM downloads 
    ORDER BY created_at DESC
")->fetchAll(PDO::FETCH_ASSOC);

// Get download stats
$total_downloads = $pdo->query("SELECT COUNT(*) FROM downloads")->fetchColumn();
$active_downloads = $pdo->query("SELECT COUNT(*) FROM downloads WHERE is_active = 1")->fetchColumn();
$total_download_count = $pdo->query("SELECT SUM(download_count) FROM downloads")->fetchColumn();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Downloads - Admin | Bhaktivedanta Gurukul</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="icon" type="image/x-icon" href="../../images/bvgLogo.png">

    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
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
                            <i class="fas fa-download text-xl"></i>
                        </div>
                        <div>
                            <h1 class="text-2xl font-bold">Downloads Management</h1>
                            <p class="text-white/90">Manage all downloadable resources</p>
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
                    <a href="manage-downloads.php" class="px-4 py-2 bg-white/30 text-white rounded-lg transition-all duration-300 flex items-center text-sm border border-white/30">
                        <i class="fas fa-list mr-2"></i> All Downloads
                    </a>
                    <a href="add-download.php" class="px-4 py-2 bg-white/10 text-white rounded-lg hover:bg-white/20 transition-all duration-300 flex items-center text-sm border border-white/20">
                        <i class="fas fa-plus mr-2"></i> Add New Download
                    </a>
                    <a href="../blog/manage-posts.php" class="px-4 py-2 bg-white/10 text-white rounded-lg hover:bg-white/20 transition-all duration-300 flex items-center text-sm border border-white/20">
                        <i class="fas fa-blog mr-2"></i> Blog Posts
                    </a>
                </div>
            </div>

            <!-- Stats Cards -->
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
                <div class="bg-white rounded-xl shadow-lg p-6 border-l-4 border-blue-500">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm font-medium text-gray-600">Total Files</p>
                            <p class="text-3xl font-bold text-gray-900"><?php echo $total_downloads; ?></p>
                        </div>
                        <div class="w-12 h-12 bg-blue-100 rounded-full flex items-center justify-center">
                            <i class="fas fa-file text-blue-500 text-xl"></i>
                        </div>
                    </div>
                </div>

                <div class="bg-white rounded-xl shadow-lg p-6 border-l-4 border-green-500">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm font-medium text-gray-600">Active Files</p>
                            <p class="text-3xl font-bold text-gray-900"><?php echo $active_downloads; ?></p>
                        </div>
                        <div class="w-12 h-12 bg-green-100 rounded-full flex items-center justify-center">
                            <i class="fas fa-check-circle text-green-500 text-xl"></i>
                        </div>
                    </div>
                </div>

                <div class="bg-white rounded-xl shadow-lg p-6 border-l-4 border-purple-500">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm font-medium text-gray-600">Total Downloads</p>
                            <p class="text-3xl font-bold text-gray-900"><?php echo $total_download_count ?: '0'; ?></p>
                        </div>
                        <div class="w-12 h-12 bg-purple-100 rounded-full flex items-center justify-center">
                            <i class="fas fa-download text-purple-500 text-xl"></i>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Page Header -->
            <div class="flex justify-between items-center mb-6">
                <div>
                    <h1 class="text-3xl font-bold text-gray-900 mb-2">Manage Downloads</h1>
                    <p class="text-gray-600 text-lg">Manage all downloadable resources and files</p>
                </div>
                <a href="add-download.php" 
                   class="inline-flex items-center px-6 py-3 btn-primary text-white rounded-xl font-bold hover:shadow-xl transition-all duration-300 transform hover:-translate-y-1 shadow-lg">
                    <i class="fas fa-plus-circle mr-3"></i> Add New Download
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

            <!-- Downloads Table -->
            <div class="bg-white rounded-2xl shadow-xl overflow-hidden">
                <!-- Table Header -->
                <div class="px-6 py-4 border-b border-gray-200 bg-gray-50">
                    <div class="flex justify-between items-center">
                        <h3 class="text-lg font-semibold text-gray-900">
                            All Downloads <span class="text-gray-600">(<?php echo count($downloads); ?>)</span>
                        </h3>
                        <div class="flex items-center space-x-4">
                            <div class="relative">
                                <input type="text" 
                                       placeholder="Search downloads..." 
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
                                <th class="px-6 py-4 text-left text-xs font-semibold text-gray-700 uppercase tracking-wider">Title</th>
                                <th class="px-6 py-4 text-left text-xs font-semibold text-gray-700 uppercase tracking-wider">Category</th>
                                <th class="px-6 py-4 text-left text-xs font-semibold text-gray-700 uppercase tracking-wider">Class</th>
                                <th class="px-6 py-4 text-left text-xs font-semibold text-gray-700 uppercase tracking-wider">File Type</th>
                                <th class="px-6 py-4 text-left text-xs font-semibold text-gray-700 uppercase tracking-wider">Downloads</th>
                                <th class="px-6 py-4 text-left text-xs font-semibold text-gray-700 uppercase tracking-wider">Date</th>
                                <th class="px-6 py-4 text-left text-xs font-semibold text-gray-700 uppercase tracking-wider">Status</th>
                                <th class="px-6 py-4 text-left text-xs font-semibold text-gray-700 uppercase tracking-wider">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200" id="downloadsTable">
                            <?php if(empty($downloads)): ?>
                                <tr>
                                    <td colspan="8" class="px-6 py-12 text-center">
                                        <i class="fas fa-inbox text-4xl text-gray-300 mb-4"></i>
                                        <p class="text-gray-500 text-lg">No downloads found</p>
                                        <p class="text-gray-400 mt-2">Get started by adding your first download</p>
                                        <a href="add-download.php" class="inline-block mt-4 px-6 py-2 btn-primary text-white rounded-lg">
                                            <i class="fas fa-plus mr-2"></i> Add Download
                                        </a>
                                    </td>
                                </tr>
                            <?php else: ?>
                                <?php foreach($downloads as $download): ?>
                                <tr class="table-row transition-all duration-200">
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="flex items-center">
                                            <div class="w-10 h-10 bg-red-100 rounded-lg flex items-center justify-center mr-4">
                                                <i class="fas fa-file-<?php echo getFileIcon($download['file_type']); ?> text-red-500"></i>
                                            </div>
                                            <div>
                                                <div class="text-sm font-semibold text-gray-900"><?php echo htmlspecialchars($download['title']); ?></div>
                                                <div class="text-sm text-gray-500 truncate max-w-xs"><?php echo htmlspecialchars($download['description'] ?? 'No description'); ?></div>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                            <?php echo ucfirst($download['category']); ?>
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                        <?php echo $download['class_level'] ? 'Class ' . $download['class_level'] : '<span class="text-gray-400">All</span>'; ?>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                        <span class="font-mono text-xs bg-gray-100 px-2 py-1 rounded"><?php echo strtoupper($download['file_type']); ?></span>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                        <div class="flex items-center">
                                            <i class="fas fa-download text-gray-400 mr-2"></i>
                                            <?php echo $download['download_count']; ?>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                        <?php echo date('M j, Y', strtotime($download['created_at'])); ?>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium <?php echo $download['is_active'] ? 'bg-green-100 text-green-800' : 'bg-gray-100 text-gray-800'; ?>">
                                            <i class="fas fa-circle mr-1 text-<?php echo $download['is_active'] ? 'green' : 'gray'; ?>-500" style="font-size: 6px;"></i>
                                            <?php echo $download['is_active'] ? 'Active' : 'Inactive'; ?>
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                        <div class="flex items-center space-x-2">
                                            <a href="edit-download.php?id=<?php echo $download['id']; ?>" 
                                               class="text-blue-600 hover:text-blue-900 transition-colors duration-200 p-2 rounded-lg hover:bg-blue-50"
                                               title="Edit">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                            <a href="?action=delete&id=<?php echo $download['id']; ?>" 
                                               class="text-red-600 hover:text-red-900 transition-colors duration-200 p-2 rounded-lg hover:bg-red-50"
                                               onclick="return confirm('Are you sure you want to delete this download? This action cannot be undone.')"
                                               title="Delete">
                                                <i class="fas fa-trash"></i>
                                            </a>
                                            <?php if($download['is_active']): ?>
                                                <span class="text-green-600 p-2" title="Active">
                                                    <i class="fas fa-eye"></i>
                                                </span>
                                            <?php else: ?>
                                                <span class="text-gray-400 p-2" title="Inactive">
                                                    <i class="fas fa-eye-slash"></i>
                                                </span>
                                            <?php endif; ?>
                                        </div>
                                    </td>
                                </tr>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>

                <!-- Table Footer -->
                <div class="px-6 py-4 border-t border-gray-200 bg-gray-50">
                    <div class="flex justify-between items-center">
                        <div class="text-sm text-gray-700">
                            Showing <span class="font-semibold"><?php echo count($downloads); ?></span> downloads
                        </div>
                        <div class="text-sm text-gray-700">
                            Total Downloads: <span class="font-semibold"><?php echo $total_download_count ?: '0'; ?></span>
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
            const rows = document.querySelectorAll('#downloadsTable tr');
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
                if (!confirm('Are you sure you want to delete this download?')) {
                    e.preventDefault();
                }
            });
        });
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
        'jpg' => 'image',
        'jpeg' => 'image',
        'png' => 'image',
        'txt' => 'alt'
    ];
    
    return $icons[strtolower($fileType)] ?? 'file';
}
?>