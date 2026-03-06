<?php
/**
 * Free Cloud Database Setup Guide
 */
?>
<!DOCTYPE html>
<html>
<head>
    <title>Free Cloud Database Setup</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            max-width: 1200px;
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
        h1 {
            color: #2563eb;
            border-bottom: 3px solid #2563eb;
            padding-bottom: 10px;
        }
        .option {
            background: #f9fafb;
            border: 2px solid #e5e7eb;
            border-radius: 8px;
            padding: 20px;
            margin: 20px 0;
        }
        .option:hover {
            border-color: #2563eb;
            box-shadow: 0 4px 6px rgba(37, 99, 235, 0.1);
        }
        .option h2 {
            color: #1f2937;
            margin-top: 0;
        }
        .badge {
            display: inline-block;
            padding: 5px 10px;
            border-radius: 5px;
            font-size: 12px;
            font-weight: bold;
            margin-left: 10px;
        }
        .badge-free {
            background: #10b981;
            color: white;
        }
        .badge-trial {
            background: #f59e0b;
            color: white;
        }
        .steps {
            background: white;
            border-left: 4px solid #2563eb;
            padding: 15px;
            margin: 15px 0;
        }
        .steps ol {
            margin: 10px 0;
            padding-left: 20px;
        }
        .steps li {
            margin: 8px 0;
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
        .btn:hover {
            background: #1d4ed8;
        }
        .btn-success {
            background: #10b981;
        }
        .btn-success:hover {
            background: #059669;
        }
        .credentials-form {
            background: #fef3c7;
            border: 2px solid #f59e0b;
            border-radius: 8px;
            padding: 20px;
            margin: 20px 0;
        }
        .form-group {
            margin: 15px 0;
        }
        .form-group label {
            display: block;
            font-weight: bold;
            margin-bottom: 5px;
        }
        .form-group input {
            width: 100%;
            padding: 10px;
            border: 1px solid #d1d5db;
            border-radius: 5px;
            font-size: 14px;
        }
        .alert {
            padding: 15px;
            border-radius: 8px;
            margin: 20px 0;
        }
        .alert-info {
            background: #dbeafe;
            border: 1px solid #3b82f6;
            color: #1e40af;
        }
        .alert-warning {
            background: #fef3c7;
            border: 1px solid #f59e0b;
            color: #92400e;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>🌐 Free Cloud Database Setup</h1>
        <p>Choose a free cloud MySQL provider and set up your pharmacy database in the cloud!</p>

        <div class="alert alert-info">
            <strong>📋 What You'll Get:</strong>
            <ul>
                <li>✓ Free cloud MySQL database</li>
                <li>✓ Accessible from anywhere</li>
                <li>✓ Automatic backups (on some providers)</li>
                <li>✓ No credit card required (for free tiers)</li>
            </ul>
        </div>

        <!-- Option 1: db4free.net -->
        <div class="option">
            <h2>1. db4free.net <span class="badge badge-free">100% FREE</span></h2>
            <p><strong>Best for:</strong> Testing and learning (completely free, no credit card)</p>
            
            <div class="steps">
                <strong>Setup Steps:</strong>
                <ol>
                    <li>Go to: <a href="https://www.db4free.net/" target="_blank">https://www.db4free.net/</a></li>
                    <li>Click "Sign Up" (top right)</li>
                    <li>Fill in the form:
                        <ul>
                            <li>Database Name: <code>pharmacy_db</code> (or any name)</li>
                            <li>Username: Choose a username</li>
                            <li>Password: Create a strong password</li>
                            <li>Email: Your email</li>
                        </ul>
                    </li>
                    <li>Check your email and confirm</li>
                    <li>Your credentials:
                        <ul>
                            <li>Host: <code>db4free.net</code></li>
                            <li>Port: <code>3306</code></li>
                            <li>Database: Your chosen name</li>
                        </ul>
                    </li>
                </ol>
            </div>
            <a href="https://www.db4free.net/" target="_blank" class="btn">Go to db4free.net</a>
        </div>

        <!-- Option 2: FreeSQLDatabase -->
        <div class="option">
            <h2>2. FreeSQLDatabase.com <span class="badge badge-free">FREE</span></h2>
            <p><strong>Best for:</strong> Quick setup (instant database creation)</p>
            
            <div class="steps">
                <strong>Setup Steps:</strong>
                <ol>
                    <li>Go to: <a href="https://www.freesqldatabase.com/" target="_blank">https://www.freesqldatabase.com/</a></li>
                    <li>Click "Sign Up"</li>
                    <li>Fill in registration form</li>
                    <li>Create new database</li>
                    <li>Get instant credentials (shown on screen)</li>
                    <li>Save your credentials:
                        <ul>
                            <li>Server/Host</li>
                            <li>Database Name</li>
                            <li>Username</li>
                            <li>Password</li>
                        </ul>
                    </li>
                </ol>
            </div>
            <a href="https://www.freesqldatabase.com/" target="_blank" class="btn">Go to FreeSQLDatabase</a>
        </div>

        <!-- Option 3: Clever Cloud -->
        <div class="option">
            <h2>3. Clever Cloud <span class="badge badge-free">FREE TIER</span></h2>
            <p><strong>Best for:</strong> Production use (reliable, professional)</p>
            
            <div class="steps">
                <strong>Setup Steps:</strong>
                <ol>
                    <li>Go to: <a href="https://www.clever-cloud.com/" target="_blank">https://www.clever-cloud.com/</a></li>
                    <li>Sign up for free account</li>
                    <li>Create new application</li>
                    <li>Add MySQL add-on (choose free tier: 256MB)</li>
                    <li>Get credentials from dashboard</li>
                    <li>Note: Includes automatic backups!</li>
                </ol>
            </div>
            <a href="https://www.clever-cloud.com/" target="_blank" class="btn">Go to Clever Cloud</a>
        </div>

        <!-- Option 4: Aiven -->
        <div class="option">
            <h2>4. Aiven <span class="badge badge-trial">FREE TRIAL</span></h2>
            <p><strong>Best for:</strong> Professional features (30-day free trial, then paid)</p>
            
            <div class="steps">
                <strong>Setup Steps:</strong>
                <ol>
                    <li>Go to: <a href="https://aiven.io/" target="_blank">https://aiven.io/</a></li>
                    <li>Sign up for free trial (no credit card for trial)</li>
                    <li>Create MySQL service</li>
                    <li>Choose free tier or trial</li>
                    <li>Get connection details from dashboard</li>
                    <li>Includes SSL, backups, monitoring</li>
                </ol>
            </div>
            <a href="https://aiven.io/" target="_blank" class="btn">Go to Aiven</a>
        </div>

        <hr style="margin: 40px 0;">

        <!-- Credentials Form -->
        <div class="credentials-form">
            <h2>📝 After You Get Your Credentials</h2>
            <p>Once you've signed up and received your database credentials, enter them here:</p>
            
            <form method="POST" action="apply_cloud_credentials.php">
                <div class="form-group">
                    <label>Database Host/Server:</label>
                    <input type="text" name="db_host" placeholder="e.g., db4free.net or mysql.example.com" required>
                </div>
                
                <div class="form-group">
                    <label>Port:</label>
                    <input type="text" name="db_port" value="3306" required>
                </div>
                
                <div class="form-group">
                    <label>Database Name:</label>
                    <input type="text" name="db_name" placeholder="e.g., pharmacy_db" required>
                </div>
                
                <div class="form-group">
                    <label>Username:</label>
                    <input type="text" name="db_user" placeholder="Your database username" required>
                </div>
                
                <div class="form-group">
                    <label>Password:</label>
                    <input type="password" name="db_pass" placeholder="Your database password" required>
                </div>
                
                <button type="submit" class="btn btn-success">✓ Apply Credentials & Import Data</button>
            </form>
        </div>

        <div class="alert alert-warning">
            <strong>⚠️ Important:</strong>
            <ul>
                <li>Save your credentials in a safe place</li>
                <li>Don't share your password</li>
                <li>Free databases may have limitations (storage, connections)</li>
                <li>For production, consider paid plans for better performance</li>
            </ul>
        </div>

        <hr>
        <p><a href="admin/dashboard.php">← Back to Dashboard</a></p>
    </div>
</body>
</html>
