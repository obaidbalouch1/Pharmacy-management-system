<?php
// Activity logging helper function
function log_activity($conn, $user_id, $action, $table_name = null, $record_id = null, $details = null) {
    $ip = $_SERVER['REMOTE_ADDR'] ?? 'Unknown';
    
    $action = mysqli_real_escape_string($conn, $action);
    $table_name = $table_name ? mysqli_real_escape_string($conn, $table_name) : null;
    $record_id = $record_id ? intval($record_id) : null;
    $details = $details ? mysqli_real_escape_string($conn, $details) : null;
    
    $query = "INSERT INTO activity_logs (user_id, action, table_name, record_id, details, ip_address) 
              VALUES ($user_id, '$action', " . 
              ($table_name ? "'$table_name'" : "NULL") . ", " .
              ($record_id ? $record_id : "NULL") . ", " .
              ($details ? "'$details'" : "NULL") . ", '$ip')";
    
    mysqli_query($conn, $query);
}
?>
