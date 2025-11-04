<?php
require_once '../../includes/db.php';

// Get filter parameters
$category = $_GET['category'] ?? '';
$class_level = $_GET['class_level'] ?? '';

// Build query
$sql = "SELECT * FROM downloads WHERE is_active = TRUE";
$params = [];

if($category) {
    $sql .= " AND category = ?";
    $params[] = $category;
}

if($class_level) {
    $sql .= " AND class_level = ?";
    $params[] = $class_level;
}

$sql .= " ORDER BY created_at DESC";

$stmt = $pdo->prepare($sql);
$stmt->execute($params);
$downloads = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Get unique classes and categories for filters
$classes_stmt = $pdo->query("SELECT DISTINCT class_level FROM downloads WHERE class_level IS NOT NULL ORDER BY class_level");
$classes = $classes_stmt->fetchAll(PDO::FETCH_COLUMN);

$categories_stmt = $pdo->query("SELECT DISTINCT category FROM downloads ORDER BY category");
$categories = $categories_stmt->fetchAll(PDO::FETCH_COLUMN);
?>

<?php include '../../includes/header.php'; ?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Downloads - Bhaktivedanta Gurukul</title>
    <link rel="stylesheet" href="<?php echo $base_url; ?>/pages/blog/style.css">
</head>
<body>
    <div class="downloads-container">
        <section class="downloads-hero">
            <div class="container">
                <h1>Downloads</h1>
                <p>Access important documents, forms, and resources</p>
            </div>
        </section>

        <!-- Filters -->
        <section class="filters-section">
            <div class="container">
                <form method="GET" class="filters-form">
                    <div class="filter-group">
                        <label>Category:</label>
                        <select name="category">
                            <option value="">All Categories</option>
                            <?php foreach($categories as $cat): ?>
                                <option value="<?php echo $cat; ?>" <?php echo $category == $cat ? 'selected' : ''; ?>>
                                    <?php echo ucfirst($cat); ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="filter-group">
                        <label>Class:</label>
                        <select name="class_level">
                            <option value="">All Classes</option>
                            <?php foreach($classes as $class): ?>
                                <option value="<?php echo $class; ?>" <?php echo $class_level == $class ? 'selected' : ''; ?>>
                                    Class <?php echo $class; ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <button type="submit" class="filter-btn">Apply Filters</button>
                    <?php if($category || $class_level): ?>
                        <a href="downloads.php" class="clear-filters">Clear All</a>
                    <?php endif; ?>
                </form>
            </div>
        </section>

        <!-- Downloads List -->
        <section class="downloads-list">
            <div class="container">
                <?php if(empty($downloads)): ?>
                    <div class="no-results">
                        <p>No downloads found matching your criteria.</p>
                    </div>
                <?php else: ?>
                    <div class="downloads-grid">
                        <?php foreach($downloads as $download): ?>
                        <div class="download-item">
                            <div class="file-icon">
                                <?php 
                                $icon = '📄';
                                if($download['file_type'] == 'pdf') $icon = '📕';
                                elseif(in_array($download['file_type'], ['doc', 'docx'])) $icon = '📝';
                                elseif(in_array($download['file_type'], ['xls', 'xlsx'])) $icon = '📊';
                                elseif(in_array($download['file_type'], ['jpg', 'png'])) $icon = '🖼️';
                                echo $icon;
                                ?>
                            </div>
                            <div class="download-info">
                                <h3><?php echo htmlspecialchars($download['title']); ?></h3>
                                <p class="download-desc"><?php echo htmlspecialchars($download['description']); ?></p>
                                <div class="download-meta">
                                    <span class="file-size"><?php echo $download['file_size']; ?></span>
                                    <span class="downloads"><?php echo $download['download_count']; ?> downloads</span>
                                    <?php if($download['class_level']): ?>
                                        <span class="class">Class <?php echo $download['class_level']; ?></span>
                                    <?php endif; ?>
                                </div>
                            </div>
                            <div class="download-action">
                                <a href="<?php echo $base_url . $download['file_path']; ?>" download class="download-btn">
                                    Download
                                </a>
                            </div>
                        </div>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
            </div>
        </section>
    </div>

    <?php include '../../includes/footer.php'; ?>
</body>
</html>