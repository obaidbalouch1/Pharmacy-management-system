<?php
include("../config/db.php");
$page_title = "Store Settings";
include("includes/header.php");

// Only admin can access
if ($_SESSION['role'] != 'admin') {
    echo "<script>alert('Access denied! Only admins can access settings.'); window.location='dashboard.php';</script>";
    exit();
}

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['save_settings'])) {
    $settings = [
        'store_name' => mysqli_real_escape_string($conn, $_POST['store_name']),
        'store_address' => mysqli_real_escape_string($conn, $_POST['store_address']),
        'store_phone' => mysqli_real_escape_string($conn, $_POST['store_phone']),
        'store_email' => mysqli_real_escape_string($conn, $_POST['store_email']),
        'store_gst' => mysqli_real_escape_string($conn, $_POST['store_gst']),
        'store_tagline' => mysqli_real_escape_string($conn, $_POST['store_tagline']),
        'receipt_footer' => mysqli_real_escape_string($conn, $_POST['receipt_footer'])
    ];
    
    $success = true;
    foreach ($settings as $key => $value) {
        $query = "INSERT INTO settings (setting_key, setting_value) VALUES ('$key', '$value') 
                  ON DUPLICATE KEY UPDATE setting_value = '$value'";
        if (!mysqli_query($conn, $query)) {
            $success = false;
            break;
        }
    }
    
    if ($success) {
        $_SESSION['success'] = "Settings updated successfully!";
        
        // Log activity
        $user_id = $_SESSION['user_id'];
        $ip = $_SERVER['REMOTE_ADDR'];
        mysqli_query($conn, "INSERT INTO activity_logs (user_id, action, details, ip_address) 
            VALUES ($user_id, 'Settings Updated', 'Store settings updated', '$ip')");
    } else {
        $_SESSION['error'] = "Failed to update settings!";
    }
    
    header("Location: settings.php");
    exit();
}

// Get current settings
$settings_query = mysqli_query($conn, "SELECT setting_key, setting_value FROM settings");
$settings = [];
while ($row = mysqli_fetch_assoc($settings_query)) {
    $settings[$row['setting_key']] = $row['setting_value'];
}

// Default values if not set
$defaults = [
    'store_name' => 'AsadsPharma',
    'store_address' => '123 Medical Street',
    'store_phone' => '+92 334 0540325',
    'store_email' => 'info@pharmacy.com',
    'store_gst' => '29XXXXX1234X1ZX',
    'store_tagline' => 'Management System',
    'receipt_footer' => 'Thank you for your business!'
];

foreach ($defaults as $key => $value) {
    if (!isset($settings[$key])) {
        $settings[$key] = $value;
    }
}
?>

<?php if (isset($_SESSION['success'])): ?>
<div class="alert alert-success">
    <?php 
    echo $_SESSION['success']; 
    unset($_SESSION['success']);
    ?>
</div>
<?php endif; ?>

<?php if (isset($_SESSION['error'])): ?>
<div class="alert alert-danger">
    <?php 
    echo $_SESSION['error']; 
    unset($_SESSION['error']);
    ?>
</div>
<?php endif; ?>

<div class="card">
    <div class="card-header">
        <h3>⚙️ Store Settings</h3>
    </div>
    <div class="card-body">
        <form method="POST">
            <div class="row">
                <div class="col-md-6">
                    <div class="form-group">
                        <label>Store Name <span style="color: red;">*</span></label>
                        <input type="text" name="store_name" class="form-control" 
                               value="<?php echo htmlspecialchars($settings['store_name']); ?>" required>
                        <small class="text-muted">This will appear on receipts and invoices</small>
                    </div>
                </div>
                
                <div class="col-md-6">
                    <div class="form-group">
                        <label>Store Tagline</label>
                        <input type="text" name="store_tagline" class="form-control" 
                               value="<?php echo htmlspecialchars($settings['store_tagline']); ?>">
                        <small class="text-muted">Subtitle below store name</small>
                    </div>
                </div>
            </div>
            
            <div class="row">
                <div class="col-md-12">
                    <div class="form-group">
                        <label>Store Address <span style="color: red;">*</span></label>
                        <textarea name="store_address" class="form-control" rows="2" required><?php echo htmlspecialchars($settings['store_address']); ?></textarea>
                    </div>
                </div>
            </div>
            
            <div class="row">
                <div class="col-md-4">
                    <div class="form-group">
                        <label>Phone Number <span style="color: red;">*</span></label>
                        <input type="text" name="store_phone" class="form-control" 
                               value="<?php echo htmlspecialchars($settings['store_phone']); ?>" required>
                    </div>
                </div>
                
                <div class="col-md-4">
                    <div class="form-group">
                        <label>Email Address</label>
                        <input type="email" name="store_email" class="form-control" 
                               value="<?php echo htmlspecialchars($settings['store_email']); ?>">
                    </div>
                </div>
                
                <div class="col-md-4">
                    <div class="form-group">
                        <label>GST/Tax Number</label>
                        <input type="text" name="store_gst" class="form-control" 
                               value="<?php echo htmlspecialchars($settings['store_gst']); ?>">
                    </div>
                </div>
            </div>
            
            <div class="row">
                <div class="col-md-12">
                    <div class="form-group">
                        <label>Receipt Footer Message</label>
                        <textarea name="receipt_footer" class="form-control" rows="2"><?php echo htmlspecialchars($settings['receipt_footer']); ?></textarea>
                        <small class="text-muted">Message displayed at the bottom of receipts</small>
                    </div>
                </div>
            </div>
            
            <div class="form-group">
                <button type="submit" name="save_settings" class="btn btn-success btn-lg">
                    💾 Save Settings
                </button>
                <a href="dashboard.php" class="btn btn-secondary btn-lg">Cancel</a>
            </div>
        </form>
    </div>
</div>

<!-- Preview Section -->
<div class="card" style="margin-top: 24px;">
    <div class="card-header">
        <h3>📄 Receipt Preview</h3>
    </div>
    <div class="card-body">
        <div style="max-width: 80mm; margin: 0 auto; padding: 20px; border: 2px dashed #d1d5db; font-family: 'Courier New', monospace; font-size: 12px;">
            <div style="text-align: center; border-bottom: 1px dashed #000; padding-bottom: 10px; margin-bottom: 10px;">
                <h2 style="margin: 0; font-size: 18px;"><?php echo htmlspecialchars($settings['store_name']); ?></h2>
                <p style="margin: 5px 0; font-size: 11px;"><?php echo htmlspecialchars($settings['store_tagline']); ?></p>
                <p style="margin: 2px 0; font-size: 11px;"><?php echo nl2br(htmlspecialchars($settings['store_address'])); ?></p>
                <p style="margin: 2px 0; font-size: 11px;">Tel: <?php echo htmlspecialchars($settings['store_phone']); ?></p>
                <?php if (!empty($settings['store_gst'])): ?>
                <p style="margin: 2px 0; font-size: 11px;">GST: <?php echo htmlspecialchars($settings['store_gst']); ?></p>
                <?php endif; ?>
            </div>
            
            <div style="text-align: center; margin: 15px 0;">
                <strong>SALES RECEIPT</strong>
            </div>
            
            <div style="margin: 10px 0; font-size: 11px;">
                <div>Invoice: INV-20260304-0001</div>
                <div>Date: <?php echo date('d/m/Y h:i A'); ?></div>
                <div>Customer: Sample Customer</div>
            </div>
            
            <div style="border-top: 1px dashed #000; border-bottom: 1px dashed #000; padding: 10px 0; margin: 10px 0;">
                <div style="display: flex; justify-content: space-between; margin-bottom: 5px;">
                    <span>Sample Medicine x 2</span>
                    <span>Rs 200.00</span>
                </div>
                <div style="font-size: 10px; color: #666;">Batch: ABC123</div>
            </div>
            
            <div style="text-align: right; margin: 10px 0; font-size: 11px;">
                <div>Subtotal: Rs 200.00</div>
                <div style="font-weight: bold; font-size: 14px; margin-top: 5px;">TOTAL: Rs 200.00</div>
            </div>
            
            <div style="text-align: center; border-top: 1px dashed #000; padding-top: 10px; margin-top: 15px; font-size: 11px;">
                <p style="font-weight: bold; margin: 5px 0;"><?php echo htmlspecialchars($settings['receipt_footer']); ?></p>
                <p style="margin: 5px 0;">Please visit again</p>
                <p style="font-size: 10px; color: #666; margin: 5px 0;">Powered by <?php echo htmlspecialchars($settings['store_name']); ?> System</p>
            </div>
        </div>
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
