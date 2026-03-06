<?php
/**
 * Database Migration Tool
 * Migrates data from XAMPP local MySQL to Cloud MySQL
 */

echo "<h1>Database Migration Tool</h1>";
echo "<p>This tool will help you migrate from XAMPP to Cloud MySQL</p>";
echo "<hr>";

// Step 1: Create backup of current database
echo "<h2>Step 1: Creating Backup of Current Database</h2>";

include("config/db.php");

$backup_file = 'database/backups/migration_backup_' . date('Y-m-d_H-i-s') . '.sql';

try {
    $tables = [];
    $result = mysqli_query($conn, "SHOW TABLES");
    while ($row = mysqli_fetch_row($result)) {
        $tables[] = $row[0];
    }
    
    $sql_dump = "-- Pharmacy Management System Database Backup for Migration\n";
    $sql_dump .= "-- Generated: " . date('Y-m-d H:i:s') . "\n";
    $sql_dump .= "-- Database: pharmacy_db\n\n";
    $sql_dump .= "CREATE DATABASE IF NOT EXISTS pharmacy_db;\n";
    $sql_dump .= "USE pharmacy_db;\n\n";
    $sql_dump .= "SET FOREIGN_KEY_CHECKS=0;\n\n";
    
    foreach ($tables as $table) {
        // Get table structure
        $result = mysqli_query($conn, "SHOW CREATE TABLE `$table`");
        $row = mysqli_fetch_row($result);
        $sql_dump .= "\n\n-- Table structure for `$table`\n";
        $sql_dump .= "DROP TABLE IF EXISTS `$table`;\n";
        $sql_dump .= $row[1] . ";\n\n";
        
        // Get table data
        $result = mysqli_query($conn, "SELECT * FROM `$table`");
        if (mysqli_num_rows($result) > 0) {
            $sql_dump .= "-- Data for table `$table`\n";
            while ($row = mysqli_fetch_assoc($result)) {
                $sql_dump .= "INSERT INTO `$table` VALUES (";
                $values = [];
                foreach ($row as $value) {
                    if ($value === null) {
                        $values[] = 'NULL';
                    } else {
                        $values[] = "'" . mysqli_real_escape_string($conn, $value) . "'";
                    }
                }
                $sql_dump .= implode(', ', $values) . ");\n";
            }
            $sql_dump .= "\n";
        }
    }
    
    $sql_dump .= "SET FOREIGN_KEY_CHECKS=1;\n";
    
    if (file_put_contents($backup_file, $sql_dump)) {
        $file_size = filesize($backup_file);
        echo "<p style='color: green;'>✓ Backup created successfully!</p>";
        echo "<p><strong>File:</strong> $backup_file</p>";
        echo "<p><strong>Size:</strong> " . number_format($file_size / 1024, 2) . " KB</p>";
        echo "<p><a href='$backup_file' download style='padding: 10px 20px; background: #10b981; color: white; text-decoration: none; border-radius: 5px;'>📥 Download Backup File</a></p>";
    } else {
        throw new Exception("Failed to write backup file");
    }
    
} catch (Exception $e) {
    echo "<p style='color: red;'>✗ Error: " . $e->getMessage() . "</p>";
}

echo "<hr>";
echo "<h2>Step 2: Configure Cloud Database Connection</h2>";
echo "<p>Edit <code>config/db.php</code> with your cloud database credentials:</p>";
echo "<pre style='background: #f3f4f6; padding: 15px; border-radius: 5px;'>";
echo htmlspecialchars("<?php
// Cloud Database Configuration
define('DB_HOST', 'your-cloud-host.com');  // e.g., mysql.example.com or IP address
define('DB_USER', 'your_username');
define('DB_PASS', 'your_password');
define('DB_NAME', 'pharmacy_db');
define('DB_PORT', '3306');  // Default MySQL port

// Create connection
\$conn = mysqli_connect(DB_HOST, DB_USER, DB_PASS, DB_NAME, DB_PORT);

// Check connection
if (!\$conn) {
    die(\"Connection failed: \" . mysqli_connect_error());
}

// Set charset to UTF-8
mysqli_set_charset(\$conn, \"utf8mb4\");

// Start session if not already started
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
?>");
echo "</pre>";

echo "<hr>";
echo "<h2>Step 3: Import to Cloud Database</h2>";
echo "<p>You have two options:</p>";

echo "<h3>Option A: Using MySQL Workbench (Recommended)</h3>";
echo "<ol>";
echo "<li>Open MySQL Workbench</li>";
echo "<li>Connect to your cloud database</li>";
echo "<li>Go to <strong>Server → Data Import</strong></li>";
echo "<li>Select <strong>Import from Self-Contained File</strong></li>";
echo "<li>Browse and select: <code>$backup_file</code></li>";
echo "<li>Click <strong>Start Import</strong></li>";
echo "</ol>";

echo "<h3>Option B: Using Command Line</h3>";
echo "<pre style='background: #f3f4f6; padding: 15px; border-radius: 5px;'>";
echo "mysql -h your-cloud-host.com -u your_username -p pharmacy_db < $backup_file";
echo "</pre>";

echo "<h3>Option C: Using phpMyAdmin (if available on cloud)</h3>";
echo "<ol>";
echo "<li>Login to your cloud phpMyAdmin</li>";
echo "<li>Create database: <code>pharmacy_db</code></li>";
echo "<li>Click on the database</li>";
echo "<li>Go to <strong>Import</strong> tab</li>";
echo "<li>Choose file: <code>$backup_file</code></li>";
echo "<li>Click <strong>Go</strong></li>";
echo "</ol>";

echo "<hr>";
echo "<h2>Step 4: Test Connection</h2>";
echo "<p>After importing, test your cloud connection:</p>";
echo "<p><a href='test_cloud_connection.php' style='padding: 10px 20px; background: #2563eb; color: white; text-decoration: none; border-radius: 5px;'>🔗 Test Cloud Connection</a></p>";

echo "<hr>";
echo "<h2>Important Notes</h2>";
echo "<ul>";
echo "<li>✓ Backup file created and ready for import</li>";
echo "<li>⚠️ Make sure your cloud database allows remote connections</li>";
echo "<li>⚠️ Update firewall rules to allow your IP address</li>";
echo "<li>⚠️ Use SSL connection for security (if available)</li>";
echo "<li>⚠️ Keep your XAMPP backup as a safety copy</li>";
echo "</ul>";

echo "<hr>";
echo "<p><a href='admin/dashboard.php'>← Back to Dashboard</a></p>";
?>
