<?php
/**
 * includes/db.php
 * Simple mysqli connection. Update credentials as per your environment.
 */
$DB_HOST = "localhost";
$DB_USER = "u259074831_ourgurukulname";
$DB_PASS = "OurgurukulDb!password1008";
$DB_NAME = "u259074831_ourgurukuldb";

$conn = new mysqli($DB_HOST, $DB_USER, $DB_PASS, $DB_NAME);
if ($conn->connect_error) {
    die("DB Connection failed: " . $conn->connect_error);
}
$conn->set_charset("utf8mb4");
?>
