<?php
require_once '../../includes/db.php';

// Get filter parameters
$class_level = $_GET['class_level'] ?? '';
$subject = $_GET['subject'] ?? '';
$exam_type = $_GET['exam_type'] ?? '';

// Build query
$sql = "SELECT * FROM question_papers WHERE is_active = TRUE";
$params = [];

if($class_level) {
    $sql .= " AND class_level = ?";
    $params[] = $class_level;
}

if($subject) {
    $sql .= " AND subject = ?";
    $params[] = $subject;
}

if($exam_type) {
    $sql .= " AND exam_type = ?";
    $params[] = $exam_type;
}

$sql .= " ORDER BY class_level, subject, academic_year DESC";

$stmt = $pdo->prepare($sql);
$stmt->execute($params);
$question_papers = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Get unique values for filters
$classes_stmt = $pdo->query("SELECT DISTINCT class_level FROM question_papers ORDER BY class_level");
$classes = $classes_stmt->fetchAll(PDO::FETCH_COLUMN);

$subjects_stmt = $pdo->query("SELECT DISTINCT subject FROM question_papers ORDER BY subject");
$subjects = $subjects_stmt->fetchAll(PDO::FETCH_COLUMN);
?>
<?php include '../../includes/header.php'; ?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Question Papers - Bhaktivedanta Gurukul</title>
    <link rel="stylesheet" href="<?php echo $base_url; ?>/pages/blog/style.css">
</head>
<body>
    <div class="question-papers-container">
        <section class="papers-hero">
            <div class="container">
                <h1>Question Papers</h1>
                <p>Access previous year question papers for practice and preparation</p>
            </div>
        </section>

        <!-- Filters -->
        <section class="filters-section">
            <div class="container">
                <form method="GET" class="filters-form">
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
                    <div class="filter-group">
                        <label>Subject:</label>
                        <select name="subject">
                            <option value="">All Subjects</option>
                            <?php foreach($subjects as $subj): ?>
                                <option value="<?php echo $subj; ?>" <?php echo $subject == $subj ? 'selected' : ''; ?>>
                                    <?php echo $subj; ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="filter-group">
                        <label>Exam Type:</label>
                        <select name="exam_type">
                            <option value="">All Types</option>
                            <option value="unit_test" <?php echo $exam_type == 'unit_test' ? 'selected' : ''; ?>>Unit Test</option>
                            <option value="mid_term" <?php echo $exam_type == 'mid_term' ? 'selected' : ''; ?>>Mid Term</option>
                            <option value="final" <?php echo $exam_type == 'final' ? 'selected' : ''; ?>>Final Exam</option>
                        </select>
                    </div>
                    <button type="submit" class="filter-btn">Apply Filters</button>
                    <?php if($class_level || $subject || $exam_type): ?>
                        <a href="question-papers.php" class="clear-filters">Clear All</a>
                    <?php endif; ?>
                </form>
            </div>
        </section>

        <!-- Question Papers List -->
        <section class="papers-list">
            <div class="container">
                <?php if(empty($question_papers)): ?>
                    <div class="no-results">
                        <p>No question papers found matching your criteria.</p>
                    </div>
                <?php else: ?>
                    <div class="papers-table">
                        <table>
                            <thead>
                                <tr>
                                    <th>Title</th>
                                    <th>Class</th>
                                    <th>Subject</th>
                                    <th>Exam Type</th>
                                    <th>Academic Year</th>
                                    <th>Downloads</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach($question_papers as $paper): ?>
                                <tr>
                                    <td><?php echo htmlspecialchars($paper['title']); ?></td>
                                    <td>Class <?php echo $paper['class_level']; ?></td>
                                    <td><?php echo $paper['subject']; ?></td>
                                    <td><?php echo ucfirst(str_replace('_', ' ', $paper['exam_type'])); ?></td>
                                    <td><?php echo $paper['academic_year']; ?></td>
                                    <td><?php echo $paper['download_count']; ?></td>
                                    <td>
                                        <a href="<?php echo $base_url . $paper['file_path']; ?>" download class="download-link">
                                            Download
                                        </a>
                                    </td>
                                </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                <?php endif; ?>
            </div>
        </section>
    </div>
    <?php include '../../includes/footer.php'; ?>
</body>
</html>