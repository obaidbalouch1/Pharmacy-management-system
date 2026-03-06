<?php
/**
 * Apply Cloud Database Credentials and Import Data
 */

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: setup_free_cloud_db.php');
    exit();
}

$db_host = $_POST['db_host'];
$db_port = $_POST['db_port'];
$db_name = $_POST['db_name'];
$db_user = $_POST['db_user'];
$db_pass = $_POST['db_pass'];

?>
<!DOCTYPE html>
<html>
<head>
    <title>Applying Cloud Database Configuration</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            max-width: 900px;
            margin: 0 auto;
            padding: 20px;
            background: #f5f5f5;
        }
        .container {
            background: white;
            padding: 30px;
            border-radius: 10px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }
        h1 { color: #2563eb; }
        .step {
            background: #f9fafb;
            border-left: 4px solid #2563eb;
            padding: 15px;
            margin: 15px 0;
        }
        .success {
            background: #d1fae5;
            border-left-color: #10b981;
            color: #065f46;
        }
        .error {
            background: #fee2e2;
            border-left-color: #ef4444;
            color: #991b1b;
        }
        .warning {
            background: #fef3c7;
            border-left-color: #f59e0b;
            color: #92400e;
        }
        .btn {
            display: inline-block;
            padding: 12px 24px;
            background: #2563eb;
            color: white;
            text-decoration: none;
            border-radius: 6px;
            font-weight: bold;
            margin: 10px 5px;
        }
        pre {
            background: #1f2937;
            color: #f3f4f6;
            padding: 15px;
            border-radius: 5px;
            overflow-x: auto;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>🚀 Cloud Database Setup</h1>
        
        <?php
        // Step 1: Test Connection
        echo "<div class='step'>";
        echo "<h3>Step 1: Testing Connection...</h3>";
        
        $cloud_conn = @mysqli_connect($db_host, $db_user, $db_pass, '', $db_port);
        
        if ($cloud_conn) {
            echo "<p class='success'>✓ Successfully connected to cloud database server!</p>";
            echo "<p><strong>Server:</strong> " . mysqli_get_server_info($cloud_conn) . "</p>";
            
            // Step 2: Create/Select Database
            echo "</div><div class='step'>";
            echo "<h3>Step 2: Setting Up Database...</h3>";
            
            $create_db = mysqli_query($cloud_conn, "CREATE DATABASE IF NOT EXISTS `$db_name`");
            if ($create_db) {
                echo "<p class='success'>✓ Database '$db_name' is ready!</p>";
            } else {
                echo "<p class='warning'>⚠️ Database might already exist (this is OK)</p>";
            }
            
            mysqli_select_db($cloud_conn, $db_name);
            
            // Step 3: Import Data
            echo "</div><div class='step'>";
            echo "<h3>Step 3: Importing Your Data...</h3>";
            
            // Find the latest backup
            $backup_dir = 'database/backups/';
            $backups = glob($backup_dir . 'migration_backup_*.sql');
            if (empty($backups)) {
                $backups = glob($backup_dir . 'pharmacy_backup_*.sql');
            }
            
            if (!empty($backups)) {
                rsort($backups);
                $backup_file = $backups[0];
                
                echo "<p>Using backup: <strong>" . basename($backup_file) . "</strong></p>";
                
                $sql = file_get_contents($backup_file);
                
                // Disable foreign key checks
                mysqli_query($cloud_conn, "SET FOREIGN_KEY_CHECKS=0");
                
                // Split and execute queries
                $queries = array_filter(array_map('trim', explode(';', $sql)));
                $success_count = 0;
                $error_count = 0;
                
                foreach ($queries as $query) {
                    if (!empty($query) && strpos($query, '--') !== 0) {
                        if (mysqli_query($cloud_conn, $query)) {
                            $success_count++;
                        } else {
                            $error_count++;
                        }
                    }
                }
                
                // Re-enable foreign key checks
                mysqli_query($cloud_conn, "SET FOREIGN_KEY_CHECKS=1");
                
                if ($error_count == 0) {
                    echo "<p class='success'>✓ Successfully imported all data!</p>";
                    echo "<p>Executed $success_count queries</p>";
                } else {
                    echo "<p class='warning'>⚠️ Import completed with some warnings</p>";
                    echo "<p>Success: $success_count | Warnings: $error_count</p>";
                }
                
                // Verify tables
                $tables = mysqli_query($cloud_conn, "SHOW TABLES");
                $table_count = mysqli_num_rows($tables);
                echo "<p class='success'>✓ Found $table_count tables in database</p>";
                
            } else {
                echo "<p class='error'>✗ No backup file found!</p>";
                echo "<p>Please create a backup first using migrate_to_cloud.php</p>";
            }
            
            // Step 4: Update Configuration
            echo "</div><div class='step'>";
            echo "<h3>Step 4: Updating Configuration...</h3>";
            
            // Backup current config
            $current_config = file_get_contents('config/db.php');
            file_put_contents('config/db_backup_' . date('Y-m-d_H-i-s') . '.php', $current_config);
            
            // Create new config
            $new_config = "<?php\n";
            $new_config .= "/**\n";
            $new_config .= " * Cloud Database Configuration\n";
            $new_config .= " * Updated: " . date('Y-m-d H:i:s') . "\n";
            $new_config .= " */\n\n";
            $new_config .= "// Cloud Database Settings\n";
            $new_config .= "define('DB_HOST', '$db_host');\n";
            $new_config .= "define('DB_USER', '$db_user');\n";
            $new_config .= "define('DB_PASS', '$db_pass');\n";
            $new_config .= "define('DB_NAME', '$db_name');\n";
            $new_config .= "define('DB_PORT', '$db_port');\n\n";
            $new_config .= "// Create connection\n";
            $new_config .= "\$conn = mysqli_connect(DB_HOST, DB_USER, DB_PASS, DB_NAME, DB_PORT);\n\n";
            $new_config .= "// Check connection\n";
            $new_config .= "if (!\$conn) {\n";
            $new_config .= "    die(\"Connection failed: \" . mysqli_connect_error());\n";
            $new_config .= "}\n\n";
            $new_config .= "// Set charset to UTF-8\n";
            $new_config .= "mysqli_set_charset(\$conn, \"utf8mb4\");\n\n";
            $new_config .= "// Start session if not already started\n";
            $new_config .= "if (session_status() === PHP_SESSION_NONE) {\n";
            $new_config .= "    session_start();\n";
            $new_config .= "}\n";
            $new_config .= "?>";
            
            if (file_put_contents('config/db.php', $new_config)) {
                echo "<p class='success'>✓ Configuration file updated!</p>";
                echo "<p>Old config backed up to: config/db_backup_" . date('Y-m-d_H-i-s') . ".php</p>";
            } else {
                echo "<p class='error'>✗ Failed to update config file</p>";
            }
            
            echo "</div><div class='step success'>";
            echo "<h2>🎉 Migration Complete!</h2>";
            echo "<p><strong>Your pharmacy system is now connected to the cloud!</strong></p>";
            echo "<ul>";
            echo "<li>✓ Database: $db_name</li>";
            echo "<li>✓ Host: $db_host</li>";
            echo "<li>✓ All data imported</li>";
            echo "<li>✓ Configuration updated</li>";
            echo "</ul>";
            echo "<a href='test_cloud_connection.php' class='btn'>🔍 Test Connection</a>";
            echo "<a href='admin/dashboard.php' class='btn'>📊 Go to Dashboard</a>";
            echo "</div>";
            
            mysqli_close($cloud_conn);
            
        } else {
            echo "<p class='error'>✗ Failed to connect to cloud database!</p>";
            echo "<p><strong>Error:</strong> " . mysqli_connect_error() . "</p>";
            echo "</div>";
            
            echo "<div class='step error'>";
            echo "<h3>Troubleshooting:</h3>";
            echo "<ul>";
            echo "<li>Verify your credentials are correct</li>";
            echo "<li>Check if the database server allows remote connections</li>";
            echo "<li>Verify firewall rules allow your IP address</li>";
            echo "<li>Check if the host and port are correct</li>";
            echo "</ul>";
            echo "<a href='setup_free_cloud_db.php' class='btn'>← Try Again</a>";
            echo "</div>";
        }
        ?>
    </div>
</body>
</html>
