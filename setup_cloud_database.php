<!DOCTYPE html>
<html>
<head>
    <title>Cloud Database Setup</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            max-width: 800px;
            margin: 50px auto;
            padding: 20px;
            background: #f5f5f5;
        }
        .container {
            background: white;
            padding: 30px;
            border-radius: 10px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }
        h1 {
            color: #2563eb;
            border-bottom: 3px solid #2563eb;
            padding-bottom: 10px;
        }
        .form-group {
            margin-bottom: 20px;
        }
        label {
            display: block;
            font-weight: bold;
            margin-bottom: 5px;
            color: #333;
        }
        input, select {
            width: 100%;
            padding: 10px;
            border: 2px solid #ddd;
            border-radius: 5px;
            font-size: 14px;
        }
        input:focus {
            border-color: #2563eb;
            outline: none;
        }
        .btn {
            padding: 12px 30px;
            background: #2563eb;
            color: white;
            border: none;
            border-radius: 5px;
            font-size: 16px;
            cursor: pointer;
            margin-right: 10px;
        }
        .btn:hover {
            background: #1d4ed8;
        }
        .btn-success {
            background: #10b981;
        }
        .btn-success:hover {
            background: #059669;
        }
        .alert {
            padding: 15px;
            margin-bottom: 20px;
            border-radius: 5px;
        }
        .alert-success {
            background: #d1fae5;
            color: #065f46;
            border: 1px solid #10b981;
        }
        .alert-error {
            background: #fee2e2;
            color: #991b1b;
            border: 1px solid #ef4444;
        }
        .alert-info {
            background: #dbeafe;
            color: #1e40af;
            border: 1px solid #3b82f6;
        }
        .step {
            background: #f9fafb;
            padding: 15px;
            margin: 20px 0;
            border-left: 4px solid #2563eb;
            border-radius: 5px;
        }
        .help-text {
            font-size: 12px;
            color: #6b7280;
            margin-top: 5px;
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
        <h1>🌐 Cloud Database Setup Wizard</h1>
        
        <?php
        if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['action'])) {
            
            if ($_POST['action'] == 'test_connection') {
                // Test connection
                $host = $_POST['db_host'];
                $user = $_POST['db_user'];
                $pass = $_POST['db_pass'];
                $name = $_POST['db_name'];
                $port = $_POST['db_port'];
                
                echo "<div class='alert alert-info'>Testing connection to <strong>$host</strong>...</div>";
                
                $test_conn = @mysqli_connect($host, $user, $pass, $name, $port);
                
                if ($test_conn) {
                    echo "<div class='alert alert-success'>";
                    echo "<h3>✓ Connection Successful!</h3>";
                    echo "<p>Successfully connected to cloud database.</p>";
                    echo "<p><strong>Server:</strong> " . mysqli_get_server_info($test_conn) . "</p>";
                    echo "<p><strong>Host Info:</strong> " . mysqli_get_host_info($test_conn) . "</p>";
                    echo "</div>";
                    
                    // Show next step
                    echo "<div class='step'>";
                    echo "<h3>Next Step: Update Configuration</h3>";
                    echo "<form method='POST'>";
                    echo "<input type='hidden' name='action' value='update_config'>";
                    echo "<input type='hidden' name='db_host' value='$host'>";
                    echo "<input type='hidden' name='db_user' value='$user'>";
                    echo "<input type='hidden' name='db_pass' value='$pass'>";
                    echo "<input type='hidden' name='db_name' value='$name'>";
                    echo "<input type='hidden' name='db_port' value='$port'>";
                    echo "<button type='submit' class='btn btn-success'>✓ Update config/db.php with these settings</button>";
                    echo "</form>";
                    echo "</div>";
                    
                    mysqli_close($test_conn);
                } else {
                    echo "<div class='alert alert-error'>";
                    echo "<h3>✗ Connection Failed</h3>";
                    echo "<p><strong>Error:</strong> " . mysqli_connect_error() . "</p>";
                    echo "<h4>Troubleshooting:</h4>";
                    echo "<ul>";
                    echo "<li>Check if host address is correct</li>";
                    echo "<li>Verify username and password</li>";
                    echo "<li>Ensure database allows remote connections</li>";
                    echo "<li>Check firewall rules allow your IP</li>";
                    echo "<li>Verify the database name exists</li>";
                    echo "</ul>";
                    echo "</div>";
                }
            }
            
            if ($_POST['action'] == 'update_config') {
                $host = $_POST['db_host'];
                $user = $_POST['db_user'];
                $pass = $_POST['db_pass'];
                $name = $_POST['db_name'];
                $port = $_POST['db_port'];
                
                // Backup current config
                $current_config = file_get_contents('config/db.php');
                file_put_contents('config/db_backup_' . date('Y-m-d_H-i-s') . '.php', $current_config);
                
                // Create new config
                $new_config = "<?php
/**
 * Cloud Database Configuration
 * Updated: " . date('Y-m-d H:i:s') . "
 */

// Cloud Database Configuration
define('DB_HOST', '$host');
define('DB_USER', '$user');
define('DB_PASS', '$pass');
define('DB_NAME', '$name');
define('DB_PORT', '$port');

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
?>";
                
                if (file_put_contents('config/db.php', $new_config)) {
                    echo "<div class='alert alert-success'>";
                    echo "<h3>✓ Configuration Updated!</h3>";
                    echo "<p>Your application is now connected to the cloud database.</p>";
                    echo "<p><strong>Host:</strong> $host</p>";
                    echo "<p><strong>Database:</strong> $name</p>";
                    echo "</div>";
                    
                    echo "<div class='step'>";
                    echo "<h3>Final Step: Import Your Data</h3>";
                    echo "<p>Your backup file is ready at:</p>";
                    echo "<pre>database/backups/migration_backup_2026-03-05_10-52-49.sql</pre>";
                    echo "<p><strong>Import using MySQL Workbench:</strong></p>";
                    echo "<ol>";
                    echo "<li>Open MySQL Workbench</li>";
                    echo "<li>Connect to: <strong>$host</strong></li>";
                    echo "<li>Go to: Server → Data Import</li>";
                    echo "<li>Select: Import from Self-Contained File</li>";
                    echo "<li>Browse to the backup file above</li>";
                    echo "<li>Click: Start Import</li>";
                    echo "</ol>";
                    echo "<p><a href='test_cloud_connection.php' class='btn'>Test Final Connection</a></p>";
                    echo "<p><a href='admin/dashboard.php' class='btn btn-success'>Go to Dashboard</a></p>";
                    echo "</div>";
                } else {
                    echo "<div class='alert alert-error'>";
                    echo "<h3>✗ Failed to Update Configuration</h3>";
                    echo "<p>Could not write to config/db.php. Check file permissions.</p>";
                    echo "</div>";
                }
            }
        }
        ?>
        
        <div class="step">
            <h3>Step 1: Enter Your Cloud Database Credentials</h3>
            <p>Get these from your cloud provider (AWS, Google Cloud, Azure, DigitalOcean, etc.)</p>
        </div>
        
        <form method="POST">
            <input type="hidden" name="action" value="test_connection">
            
            <div class="form-group">
                <label>Database Host *</label>
                <input type="text" name="db_host" placeholder="e.g., mysql.example.com or 123.45.67.89" required>
                <div class="help-text">Your cloud MySQL server address</div>
            </div>
            
            <div class="form-group">
                <label>Port</label>
                <input type="text" name="db_port" value="3306" required>
                <div class="help-text">Usually 3306 for MySQL</div>
            </div>
            
            <div class="form-group">
                <label>Username *</label>
                <input type="text" name="db_user" placeholder="e.g., admin or root" required>
                <div class="help-text">Your cloud database username</div>
            </div>
            
            <div class="form-group">
                <label>Password *</label>
                <input type="password" name="db_pass" placeholder="Your database password" required>
                <div class="help-text">Your cloud database password</div>
            </div>
            
            <div class="form-group">
                <label>Database Name</label>
                <input type="text" name="db_name" value="pharmacy_db" required>
                <div class="help-text">Keep as pharmacy_db (create this database first in MySQL Workbench)</div>
            </div>
            
            <button type="submit" class="btn">🔍 Test Connection</button>
        </form>
        
        <div class="step" style="margin-top: 30px;">
            <h3>📋 Before You Start:</h3>
            <ol>
                <li>✓ Backup created: <code>migration_backup_2026-03-05_10-52-49.sql</code></li>
                <li>Create database <code>pharmacy_db</code> in MySQL Workbench</li>
                <li>Get your cloud database credentials</li>
                <li>Ensure firewall allows your IP address</li>
            </ol>
        </div>
        
        <div class="step">
            <h3>🆘 Need Help?</h3>
            <p><strong>Where to find credentials:</strong></p>
            <ul>
                <li><strong>AWS RDS:</strong> RDS Console → Databases → Endpoint & Port</li>
                <li><strong>Google Cloud SQL:</strong> SQL → Instance → Overview → Connection name</li>
                <li><strong>Azure:</strong> Azure Portal → MySQL servers → Connection strings</li>
                <li><strong>DigitalOcean:</strong> Databases → Connection Details</li>
            </ul>
        </div>
    </div>
</body>
</html>
