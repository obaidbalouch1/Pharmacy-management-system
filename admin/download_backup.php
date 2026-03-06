<?php
include("../config/db.php");

// Check if user is logged in and is admin
if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'admin') {
    http_response_code(403);
    die("Access denied!");
}

// Get filename from query parameter
$filename = isset($_GET['file']) ? basename($_GET['file']) : '';

if (empty($filename)) {
    http_response_code(400);
    die("No file specified!");
}

// Validate file extension
if (pathinfo($filename, PATHINFO_EXTENSION) !== 'sql') {
    http_response_code(400);
    die("Invalid file type!");
}

// Build file path
$file_path = realpath('../database/backups/' . $filename);
$backup_dir = realpath('../database/backups/');

// Security check: ensure file is within backups directory
if ($file_path === false || strpos($file_path, $backup_dir) !== 0) {
    http_response_code(403);
    die("Invalid file path!");
}

// Check if file exists
if (!file_exists($file_path)) {
    http_response_code(404);
    die("File not found: " . htmlspecialchars($filename));
}

// Get file size
$file_size = filesize($file_path);

// Log activity
$user_id = $_SESSION['user_id'];
$ip = $_SERVER['REMOTE_ADDR'];
mysqli_query($conn, "INSERT INTO activity_logs (user_id, action, details, ip_address) 
    VALUES ($user_id, 'Backup Downloaded', 'Downloaded file: " . mysqli_real_escape_string($conn, $filename) . "', '$ip')");

// Clear any output buffers
while (ob_get_level()) {
    ob_end_clean();
}

// Set headers for download
header('Content-Type: application/octet-stream');
header('Content-Disposition: attachment; filename="' . $filename . '"');
header('Content-Length: ' . $file_size);
header('Content-Transfer-Encoding: binary');
header('Cache-Control: no-cache, must-revalidate');
header('Pragma: public');
header('Expires: 0');

// Disable time limit for large files
set_time_limit(0);

// Read and output file in chunks
$handle = fopen($file_path, 'rb');
if ($handle === false) {
    http_response_code(500);
    die("Failed to open file!");
}

while (!feof($handle)) {
    echo fread($handle, 8192);
    flush();
}

fclose($handle);
exit();
?>
