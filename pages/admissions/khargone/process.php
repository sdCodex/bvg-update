<?php
require_once 'config.php';

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success' => false, 'message' => 'Invalid request method']);
    exit;
}

// Get form data
$full_name = mysqli_real_escape_string($conn, $_POST['full_name']);
$father_name = mysqli_real_escape_string($conn, $_POST['father_name']);
$mother_name = mysqli_real_escape_string($conn, $_POST['mother_name']);
$dob = mysqli_real_escape_string($conn, $_POST['dob']);
$phone = mysqli_real_escape_string($conn, $_POST['phone']);
$alternate_phone = mysqli_real_escape_string($conn, $_POST['alternate_phone'] ?? '');
$email = mysqli_real_escape_string($conn, $_POST['email'] ?? '');
$previous_school = mysqli_real_escape_string($conn, $_POST['previous_school'] ?? '');
$class = mysqli_real_escape_string($conn, $_POST['class']);
$address = mysqli_real_escape_string($conn, $_POST['address']);

// Handle file uploads
$photo_path = '';
$signature_path = '';

// Upload photo
if (isset($_FILES['photo']) && $_FILES['photo']['error'] === UPLOAD_ERR_OK) {
    $photo_tmp = $_FILES['photo']['tmp_name'];
    $photo_name = time() . '_' . basename($_FILES['photo']['name']);
    $photo_target = 'uploads/photos/' . $photo_name;
    
    // Validate image
    $allowed_types = ['image/jpeg', 'image/png', 'image/jpg'];
    $file_type = mime_content_type($photo_tmp);
    
    if (in_array($file_type, $allowed_types) && $_FILES['photo']['size'] <= 2 * 1024 * 1024) {
        if (move_uploaded_file($photo_tmp, $photo_target)) {
            $photo_path = $photo_target;
        }
    }
}

// Handle signature (base64)
if (!empty($_POST['signature'])) {
    $signature_data = $_POST['signature'];
    
    // Convert base64 to image
    list($type, $signature_data) = explode(';', $signature_data);
    list(, $signature_data) = explode(',', $signature_data);
    $signature_data = base64_decode($signature_data);
    
    $signature_name = time() . '_signature.png';
    $signature_target = 'uploads/signatures/' . $signature_name;
    
    if (file_put_contents($signature_target, $signature_data)) {
        $signature_path = $signature_target;
    }
}

// Validate required files
if (empty($photo_path) || empty($signature_path)) {
    echo json_encode(['success' => false, 'message' => 'Photo and signature are required']);
    exit;
}

// Insert into database
$sql = "INSERT INTO admissions (full_name, father_name, mother_name, dob, phone, alternate_phone, email, photo, signature, previous_school, class, address, payment_status, amount) 
        VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, 'pending', 500.00)";

$stmt = mysqli_prepare($conn, $sql);
mysqli_stmt_bind_param($stmt, 'ssssssssssss', 
    $full_name, $father_name, $mother_name, $dob, $phone, 
    $alternate_phone, $email, $photo_path, $signature_path, 
    $previous_school, $class, $address
);

if (mysqli_stmt_execute($stmt)) {
    $admission_id = mysqli_insert_id($conn);
    
    echo json_encode([
        'success' => true, 
        'admission_id' => $admission_id,
        'message' => 'Admission form submitted successfully'
    ]);
} else {
    echo json_encode(['success' => false, 'message' => 'Database error: ' . mysqli_error($conn)]);
}

mysqli_stmt_close($stmt);
mysqli_close($conn);
?>