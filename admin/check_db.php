<?php
require_once '../includes/db.php';
echo "Database Status: " . ($pdo ? "CONNECTED ✅" : "NOT CONNECTED ❌") . "<br>";

// Check admin exists
$stmt = $pdo->prepare("SELECT * FROM admins WHERE username = 'Bvg_gurkul!superAdmin_admin_1002'");
$stmt->execute();
$admin = $stmt->fetch();

if ($admin) {
    echo "✅ Admin found in database<br>";
    echo "Username: " . $admin['username'] . "<br>";
    echo "Is Active: " . $admin['is_active'] . "<br>";
} else {
    echo "❌ Admin NOT found in database";
}
?>