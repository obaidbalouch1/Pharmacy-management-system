<?php
include("../config/db.php");
$page_title = "Backup & Restore";
include("includes/header.php");

// Only admin can access
if ($_SESSION['role'] != 'admin') {
    echo "<script>alert('Access denied!'); window.location='dashboard.php';</script>";
    exit();
}

// Handle backup
if (isset($_POST['create_backup'])) {
    $backup_file = 'pharmacy_backup_' . date('Y-m-d_H-i-s') . '.sql';
    $backup_path = '../database/backups/';
    
    // Create backups directory if not exists
    if (!file_exists($backup_path)) {
        mkdir($backup_path, 0777, true);
    }
    
    // Method 1: Try PHP-based backup (more reliable)
    try {
        $tables = [];
        $result = mysqli_query($conn, "SHOW TABLES");
        while ($row = mysqli_fetch_row($result)) {
            $tables[] = $row[0];
        }
        
        $sql_dump = "-- Pharmacy Management System Database Backup\n";
        $sql_dump .= "-- Generated: " . date('Y-m-d H:i:s') . "\n";
        $sql_dump .= "-- Database: pharmacy_db\n\n";
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
        
        // Write to file
        if (file_put_contents($backup_path . $backup_file, $sql_dump)) {
            $file_size = filesize($backup_path . $backup_file);
            $success_msg = "Backup created successfully: $backup_file (" . number_format($file_size / 1024, 2) . " KB)";
            
            // Log activity
            $user_id = $_SESSION['user_id'];
            $ip = $_SERVER['REMOTE_ADDR'];
            mysqli_query($conn, "INSERT INTO activity_logs (user_id, action, details, ip_address) 
                VALUES ($user_id, 'Database Backup Created', 'Backup file: $backup_file', '$ip')");
        } else {
            $error_msg = "Failed to write backup file. Check directory permissions.";
        }
    } catch (Exception $e) {
        $error_msg = "Backup failed: " . $e->getMessage();
    }
}

// Handle restore
if (isset($_POST['restore_backup']) && isset($_FILES['backup_file'])) {
    $file = $_FILES['backup_file'];
    
    if ($file['error'] == 0 && pathinfo($file['name'], PATHINFO_EXTENSION) == 'sql') {
        $temp_file = $file['tmp_name'];
        
        // Read SQL file
        $sql = file_get_contents($temp_file);
        
        if ($sql) {
            // Disable foreign key checks
            mysqli_query($conn, "SET FOREIGN_KEY_CHECKS=0");
            
            // Split SQL into individual queries
            $queries = array_filter(array_map('trim', explode(';', $sql)));
            
            $success_count = 0;
            $error_count = 0;
            
            foreach ($queries as $query) {
                if (!empty($query) && $query != '--') {
                    if (mysqli_query($conn, $query)) {
                        $success_count++;
                    } else {
                        $error_count++;
                    }
                }
            }
            
            // Re-enable foreign key checks
            mysqli_query($conn, "SET FOREIGN_KEY_CHECKS=1");
            
            if ($error_count == 0) {
                $success_msg = "Database restored successfully! ($success_count queries executed)";
                
                // Log activity
                $user_id = $_SESSION['user_id'];
                $ip = $_SERVER['REMOTE_ADDR'];
                mysqli_query($conn, "INSERT INTO activity_logs (user_id, action, details, ip_address) 
                    VALUES ($user_id, 'Database Restored', 'Restored from: {$file['name']}', '$ip')");
            } else {
                $error_msg = "Restore completed with errors. Success: $success_count, Errors: $error_count";
            }
        } else {
            $error_msg = "Failed to read backup file.";
        }
    } else {
        $error_msg = "Invalid file. Please upload a .sql file.";
    }
}

// Handle delete backup
if (isset($_GET['delete'])) {
    $file = basename($_GET['delete']);
    $file_path = '../database/backups/' . $file;
    if (file_exists($file_path)) {
        if (unlink($file_path)) {
            $success_msg = "Backup deleted successfully!";
            
            // Log activity
            $user_id = $_SESSION['user_id'];
            $ip = $_SERVER['REMOTE_ADDR'];
            mysqli_query($conn, "INSERT INTO activity_logs (user_id, action, details, ip_address) 
                VALUES ($user_id, 'Backup Deleted', 'Deleted file: $file', '$ip')");
        } else {
            $error_msg = "Failed to delete backup file.";
        }
    } else {
        $error_msg = "Backup file not found.";
    }
}

// Get existing backups
$backup_path = '../database/backups/';
$backups = [];
if (file_exists($backup_path)) {
    $files = scandir($backup_path);
    foreach ($files as $file) {
        if (pathinfo($file, PATHINFO_EXTENSION) == 'sql') {
            $backups[] = [
                'name' => $file,
                'size' => filesize($backup_path . $file),
                'date' => filemtime($backup_path . $file)
            ];
        }
    }
    // Sort by date descending
    usort($backups, function($a, $b) {
        return $b['date'] - $a['date'];
    });
}
?>

<?php if (isset($success_msg)): ?>
<div class="alert alert-success"><?php echo $success_msg; ?></div>
<?php endif; ?>

<?php if (isset($error_msg)): ?>
<div class="alert alert-danger"><?php echo $error_msg; ?></div>
<?php endif; ?>

<div class="row">
    <!-- Create Backup -->
    <div class="col-md-6">
        <div class="card">
            <div class="card-header">
                <h3>💾 Create Backup</h3>
            </div>
            <div class="card-body">
                <p>Create a complete backup of your pharmacy database. This includes all medicines, sales, purchases, customers, and system data.</p>
                
                <form method="POST">
                    <button type="submit" name="create_backup" class="btn btn-success" onclick="return confirm('Create database backup now?')">
                        📦 Create Backup Now
                    </button>
                </form>
                
                <div style="margin-top: 20px; padding: 15px; background: #f3f4f6; border-radius: 8px;">
                    <strong>⚠️ Important Notes:</strong>
                    <ul style="margin-top: 10px; margin-bottom: 0;">
                        <li>Backups are stored in database/backups/ folder</li>
                        <li>Download backups to external storage regularly</li>
                        <li>Keep backups secure and confidential</li>
                        <li>Test restore process periodically</li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Restore Backup -->
    <div class="col-md-6">
        <div class="card">
            <div class="card-header">
                <h3>♻️ Restore Backup</h3>
            </div>
            <div class="card-body">
                <p>Restore your database from a previous backup file. This will replace all current data.</p>
                
                <form method="POST" enctype="multipart/form-data">
                    <div class="form-group">
                        <label>Select Backup File (.sql)</label>
                        <input type="file" name="backup_file" class="form-control" accept=".sql" required>
                    </div>
                    
                    <button type="submit" name="restore_backup" class="btn btn-warning" onclick="return confirm('⚠️ WARNING: This will replace ALL current data with the backup. Are you absolutely sure?')">
                        ♻️ Restore Database
                    </button>
                </form>
                
                <div style="margin-top: 20px; padding: 15px; background: #fef3c7; border-radius: 8px;">
                    <strong>⚠️ WARNING:</strong>
                    <ul style="margin-top: 10px; margin-bottom: 0;">
                        <li>This will REPLACE all current data</li>
                        <li>Create a backup before restoring</li>
                        <li>All users will be logged out</li>
                        <li>This action cannot be undone</li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Existing Backups -->
<div class="card" style="margin-top: 24px;">
    <div class="card-header">
        <h3>📂 Existing Backups</h3>
    </div>
    <div class="card-body">
        <?php if (count($backups) > 0): ?>
        <div class="table-responsive">
            <table>
                <thead>
                    <tr>
                        <th>Backup File</th>
                        <th>Size</th>
                        <th>Created Date</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($backups as $backup): ?>
                    <tr>
                        <td><strong><?php echo $backup['name']; ?></strong></td>
                        <td><?php echo number_format($backup['size'] / 1024, 2); ?> KB</td>
                        <td><?php echo date('d M Y, h:i A', $backup['date']); ?></td>
                        <td>
                            <div class="action-buttons">
                                <a href="download_backup.php?file=<?php echo urlencode($backup['name']); ?>" class="btn btn-sm btn-info">
                                    📥 Download
                                </a>
                                <a href="?delete=<?php echo urlencode($backup['name']); ?>" class="btn btn-sm btn-danger" onclick="return confirm('Delete this backup?')">
                                    🗑️ Delete
                                </a>
                            </div>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
        <?php else: ?>
        <div style="text-align: center; padding: 40px; color: #6b7280;">
            <div style="font-size: 48px; margin-bottom: 16px;">📂</div>
            <h4 style="color: #374151; margin-bottom: 8px;">No Backups Found</h4>
            <p>Create your first backup to get started.</p>
        </div>
        <?php endif; ?>
    </div>
</div>

<style>
.alert {
    padding: 15px;
    margin-bottom: 20px;
    border-radius: 8px;
    font-weight: 500;
}
.alert-success {
    background: #d1fae5;
    color: #065f46;
    border: 1px solid #10b981;
}
.alert-danger {
    background: #fee2e2;
    color: #991b1b;
    border: 1px solid #ef4444;
}
</style>

<?php include("includes/footer.php"); ?>
