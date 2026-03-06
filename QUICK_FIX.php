<?php
/**
 * QUICK PASSWORD FIX
 * 
 * INSTRUCTIONS:
 * 1. Copy this file to: C:\xampp\htdocs\pharmacy\QUICK_FIX.php
 * 2. Open browser: http://localhost/pharmacy/QUICK_FIX.php
 * 3. Follow the instructions on screen
 * 4. DELETE this file after use!
 */

// Database connection
$host = "localhost";
$user = "root";
$password = "";
$database = "pharmacy_db";

$conn = mysqli_connect($host, $user, $password, $database);

if (!$conn) {
    die("<h2 style='color: red;'>❌ Cannot connect to database!</h2><p>Error: " . mysqli_connect_error() . "</p><p>Make sure XAMPP MySQL is running and database 'pharmacy_db' exists.</p>");
}

// Check if users table exists
$table_check = mysqli_query($conn, "SHOW TABLES LIKE 'users'");
if (mysqli_num_rows($table_check) == 0) {
    die("<h2 style='color: red;'>❌ Users table not found!</h2><p>Please import the database schema first (database/schema.sql)</p>");
}

// Delete existing admin
mysqli_query($conn, "DELETE FROM users WHERE username = 'admin'");

// Create new admin with correct password
$username = 'admin';
$password_plain = 'admin123';
$password_hash = password_hash($password_plain, PASSWORD_DEFAULT);

$query = "INSERT INTO users (username, password, full_name, email, role, status) 
          VALUES ('$username', '$password_hash', 'System Administrator', 'admin@pharmacy.com', 'admin', 'active')";

if (mysqli_query($conn, $query)) {
    // Verify it was created
    $verify = mysqli_query($conn, "SELECT * FROM users WHERE username = 'admin'");
    $user = mysqli_fetch_assoc($verify);
    
    echo "<!DOCTYPE html>
    <html>
    <head>
        <title>Password Reset - Success</title>
        <style>
            body {
                font-family: 'Segoe UI', Arial, sans-serif;
                background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
                padding: 50px;
                margin: 0;
            }
            .container {
                max-width: 600px;
                margin: 0 auto;
                background: white;
                padding: 40px;
                border-radius: 20px;
                box-shadow: 0 20px 60px rgba(0,0,0,0.3);
            }
            h1 {
                color: #10b981;
                margin-bottom: 10px;
            }
            .success-box {
                background: #f0fdf4;
                border-left: 5px solid #10b981;
                padding: 20px;
                margin: 20px 0;
                border-radius: 8px;
            }
            .credentials {
                background: #eff6ff;
                border-left: 5px solid #2563eb;
                padding: 20px;
                margin: 20px 0;
                border-radius: 8px;
            }
            .credentials p {
                margin: 10px 0;
                font-size: 16px;
            }
            .credentials strong {
                color: #1e40af;
            }
            .warning {
                background: #fef3c7;
                border-left: 5px solid #f59e0b;
                padding: 20px;
                margin: 20px 0;
                border-radius: 8px;
                color: #92400e;
            }
            .btn {
                display: inline-block;
                padding: 15px 30px;
                background: #2563eb;
                color: white;
                text-decoration: none;
                border-radius: 10px;
                font-weight: 600;
                margin-top: 20px;
                transition: all 0.3s;
            }
            .btn:hover {
                background: #1e40af;
                transform: translateY(-2px);
                box-shadow: 0 5px 15px rgba(37, 99, 235, 0.3);
            }
            .info {
                background: #f9fafb;
                padding: 15px;
                border-radius: 8px;
                margin: 20px 0;
                font-size: 14px;
                color: #6b7280;
            }
        </style>
    </head>
    <body>
        <div class='container'>
            <h1>✅ Admin Password Reset Successful!</h1>
            
            <div class='success-box'>
                <p><strong>✓</strong> Admin user has been created successfully</p>
                <p><strong>✓</strong> Password has been hashed securely</p>
                <p><strong>✓</strong> You can now login to the system</p>
            </div>
            
            <div class='credentials'>
                <h3 style='margin-top: 0; color: #1e40af;'>🔐 Login Credentials</h3>
                <p><strong>Username:</strong> admin</p>
                <p><strong>Password:</strong> admin123</p>
            </div>
            
            <a href='login.php' class='btn'>🚀 Go to Login Page</a>
            
            <div class='warning'>
                <h3 style='margin-top: 0;'>⚠️ IMPORTANT - Security Warning</h3>
                <p><strong>DELETE THIS FILE IMMEDIATELY!</strong></p>
                <p>For security reasons, delete <code>QUICK_FIX.php</code> from your server after successful login.</p>
            </div>
            
            <div class='info'>
                <strong>Technical Details:</strong><br>
                User ID: {$user['id']}<br>
                Password Hash: " . substr($user['password'], 0, 30) . "...<br>
                Role: {$user['role']}<br>
                Status: {$user['status']}
            </div>
        </div>
    </body>
    </html>";
} else {
    echo "<!DOCTYPE html>
    <html>
    <head>
        <title>Error</title>
        <style>
            body { font-family: Arial; padding: 50px; background: #fee; }
            .error { background: white; padding: 30px; border-radius: 10px; max-width: 600px; margin: 0 auto; border-left: 5px solid #ef4444; }
        </style>
    </head>
    <body>
        <div class='error'>
            <h2 style='color: #dc2626;'>❌ Error Creating Admin User</h2>
            <p><strong>Error:</strong> " . mysqli_error($conn) . "</p>
            <p>Please check your database configuration and try again.</p>
        </div>
    </body>
    </html>";
}

mysqli_close($conn);
?>
