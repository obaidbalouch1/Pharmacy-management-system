<?php
include("../../config/db.php");

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    echo json_encode(['error' => 'Unauthorized']);
    exit();
}

if (isset($_GET['id'])) {
    $medicine_id = intval($_GET['id']);
    
    $query = mysqli_query($conn, "SELECT m.*, c.company_name 
        FROM medicines m 
        LEFT JOIN companies c ON m.company_id = c.id 
        WHERE m.id = $medicine_id");
    
    if ($row = mysqli_fetch_assoc($query)) {
        echo json_encode([
            'success' => true,
            'data' => $row
        ]);
    } else {
        echo json_encode([
            'success' => false,
            'error' => 'Medicine not found'
        ]);
    }
} else {
    echo json_encode([
        'success' => false,
        'error' => 'Medicine ID required'
    ]);
}
?>
