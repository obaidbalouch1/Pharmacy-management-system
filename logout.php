<?php
session_start();

// Log activity before destroying session
if (isset($_SESSION['user_id'])) {
    include("config/db.php");
    $user_id = $_SESSION['user_id'];
    $ip = $_SERVER['REMOTE_ADDR'];
    mysqli_query($conn, "INSERT INTO activity_logs (user_id, action, details, ip_address) VALUES ($user_id, 'logout', 'User logged out', '$ip')");
}

session_destroy();
header("Location: login.php");
exit();
?>
