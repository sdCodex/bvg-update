<?php
// Vendor Configuration
$vendors = [
    'fortunate' => [
        'name' => 'Gurukul Fortunate 51',
        'fee' => 500,
        'admin_email' => 'fortunate_admin@example.com',
        'success_page' => '../success-pages/fortunate.php',
        'table_suffix' => '_fortunate' // Optional if using single table
    ],
    'khargone' => [
        'name' => 'Gurukul Khargone',
        'fee' => 500,
        'admin_email' => 'khargone_admin@example.com',
        'success_page' => '../success-pages/khargone-success.php',
        'table_suffix' => '_khargone'
    ],
    'prayagraj' => [
        'name' => 'Gurukul Prayagraj',
        'fee' => 500,
        'admin_email' => 'prayagraj_admin@example.com',
        'success_page' => '../success-pages/paryagraj-success.php',
        'table_suffix' => '_prayagraj'
    ]
];

// Get current vendor from session or URL
function getCurrentVendor() {
    if (isset($_SESSION['vendor_type'])) {
        return $_SESSION['vendor_type'];
    } elseif (isset($_GET['vendor'])) {
        return $_GET['vendor'];
    }
    return 'fortunate'; // default
}

// Get vendor details
function getVendorDetails($vendor) {
    global $vendors;
    return $vendors[$vendor] ?? $vendors['fortunate'];
}
?>