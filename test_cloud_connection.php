<?php
/**
 * Test Cloud Database Connection
 */

echo "<h1>Cloud Database Connection Test</h1>";
echo "<hr>";

// Read current config
$config_file = 'config/db.php';
$config_content = file_get_contents($config_file);

// Extract current settings
preg_match("/define\('DB_HOST',\s*'([^']*)'\)/", $config_content, $host_match);
preg_match("/define\('DB_USER',\s*'([^']*)'\)/", $config_content, $user_match);
preg_match("/define\('DB_NAME',\s*'([^']*)'\)/", $config_content, $name_match);

$current_host = $host_match[1] ?? 'localhost';
$current_user = $user_match[1] ?? 'root';
$current_db = $name_match[1] ?? 'pharmacy_db';

echo "<h2>Current Configuration</h2>";
echo "<table border='1' cellpadding='10' style='border-collapse: collapse;'>";
echo "<tr><th>Setting</th><th>Value</th></tr>";
echo "<tr><td>Host</td><td><strong>$current_host</strong></td></tr>";
echo "<tr><td>Username</td><td><strong>$current_user</strong></td></tr>";
echo "<tr><td>Database</td><td><strong>$current_db</strong></td></tr>";
echo "</table>";

echo "<hr>";
echo "<h2>Connection Test</h2>";

try {
    include("config/db.php");
    
    if ($conn) {
        echo "<p style='color: green; font-size: 18px;'>✓ <strong>Connection Successful!</strong></p>";
        
        // Get server info
        $server_info = mysqli_get_server_info($conn);
        $host_info = mysqli_get_host_info($conn);
        
        echo "<h3>Server Information</h3>";
        echo "<table border='1' cellpadding='10' style='border-collapse: collapse;'>";
        echo "<tr><td>MySQL Version</td><td>$server_info</td></tr>";
        echo "<tr><td>Connection Type</td><td>$host_info</td></tr>";
        echo "</table>";
        
        // Test database access
        echo "<h3>Database Tables</h3>";
        $tables_result = mysqli_query($conn, "SHOW TABLES");
        
        if ($tables_result) {
            $table_count = mysqli_num_rows($tables_result);
            echo "<p style='color: green;'>✓ Found $table_count tables</p>";
            
            echo "<table border='1' cellpadding='10' style='border-collapse: collapse;'>";
            echo "<tr><th>Table Name</th><th>Row Count</th></tr>";
            
            while ($row = mysqli_fetch_row($tables_result)) {
                $table = $row[0];
                $count_result = mysqli_query($conn, "SELECT COUNT(*) as count FROM `$table`");
                $count = mysqli_fetch_assoc($count_result)['count'];
                echo "<tr><td>$table</td><td>$count</td></tr>";
            }
            echo "</table>";
            
            echo "<p style='color: green; font-size: 16px; margin-top: 20px;'>✓ <strong>Database is working correctly!</strong></p>";
            
        } else {
            echo "<p style='color: orange;'>⚠️ Connected but cannot access tables</p>";
        }
        
    } else {
        throw new Exception("Connection failed");
    }
    
} catch (Exception $e) {
    echo "<p style='color: red; font-size: 18px;'>✗ <strong>Connection Failed!</strong></p>";
    echo "<p style='color: red;'>Error: " . mysqli_connect_error() . "</p>";
    
    echo "<h3>Troubleshooting Steps:</h3>";
    echo "<ol>";
    echo "<li>Verify your cloud database credentials in <code>config/db.php</code></li>";
    echo "<li>Check if your cloud database allows remote connections</li>";
    echo "<li>Verify firewall rules allow your IP address</li>";
    echo "<li>Check if the database name exists on the cloud server</li>";
    echo "<li>Ensure the user has proper permissions</li>";
    echo "</ol>";
}

echo "<hr>";
echo "<p><a href='migrate_to_cloud.php'>← Back to Migration Tool</a> | <a href='admin/dashboard.php'>Dashboard</a></p>";
?>
