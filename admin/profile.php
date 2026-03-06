<?php
include("../config/db.php");
$page_title = "My Profile";
include("includes/header.php");

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: ../login.php");
    exit();
}

$user_id = $_SESSION['user_id'];

// Get user details
$user_query = mysqli_query($conn, "SELECT * FROM users WHERE id = $user_id");
$user = mysqli_fetch_assoc($user_query);

// Handle profile update
if (isset($_POST['update_profile'])) {
    $full_name = mysqli_real_escape_string($conn, $_POST['full_name']);
    $email = mysqli_real_escape_string($conn, $_POST['email']);
    $phone = mysqli_real_escape_string($conn, $_POST['phone']);
    
    $update_query = "UPDATE users SET 
                     full_name = '$full_name',
                     email = '$email',
                     phone = '$phone'
                     WHERE id = $user_id";
    
    if (mysqli_query($conn, $update_query)) {
        $_SESSION['full_name'] = $full_name;
        $_SESSION['success'] = "Profile updated successfully!";
        header("Location: profile.php");
        exit();
    } else {
        $_SESSION['error'] = "Error updating profile: " . mysqli_error($conn);
    }
}

// Handle username change
if (isset($_POST['change_username'])) {
    $new_username = mysqli_real_escape_string($conn, $_POST['new_username']);
    $current_password = $_POST['current_password'];
    
    // Verify current password
    if (password_verify($current_password, $user['password'])) {
        // Check if username already exists
        $check_username = mysqli_query($conn, "SELECT id FROM users WHERE username = '$new_username' AND id != $user_id");
        
        if (mysqli_num_rows($check_username) > 0) {
            $_SESSION['error'] = "Username already taken!";
        } else {
            $update_query = "UPDATE users SET username = '$new_username' WHERE id = $user_id";
            
            if (mysqli_query($conn, $update_query)) {
                $_SESSION['username'] = $new_username;
                $_SESSION['success'] = "Username changed successfully!";
                header("Location: profile.php");
                exit();
            } else {
                $_SESSION['error'] = "Error changing username: " . mysqli_error($conn);
            }
        }
    } else {
        $_SESSION['error'] = "Current password is incorrect!";
    }
}

// Handle password change
if (isset($_POST['change_password'])) {
    $current_password = $_POST['current_password_pass'];
    $new_password = $_POST['new_password'];
    $confirm_password = $_POST['confirm_password'];
    
    // Verify current password
    if (password_verify($current_password, $user['password'])) {
        // Check if new passwords match
        if ($new_password === $confirm_password) {
            // Check password strength
            if (strlen($new_password) >= 6) {
                $hashed_password = password_hash($new_password, PASSWORD_DEFAULT);
                $update_query = "UPDATE users SET password = '$hashed_password' WHERE id = $user_id";
                
                if (mysqli_query($conn, $update_query)) {
                    $_SESSION['success'] = "Password changed successfully!";
                    header("Location: profile.php");
                    exit();
                } else {
                    $_SESSION['error'] = "Error changing password: " . mysqli_error($conn);
                }
            } else {
                $_SESSION['error'] = "Password must be at least 6 characters long!";
            }
        } else {
            $_SESSION['error'] = "New passwords do not match!";
        }
    } else {
        $_SESSION['error'] = "Current password is incorrect!";
    }
}

// Refresh user data
$user_query = mysqli_query($conn, "SELECT * FROM users WHERE id = $user_id");
$user = mysqli_fetch_assoc($user_query);
?>

<?php if (isset($_SESSION['success'])): ?>
    <div class="alert alert-success">✅ <?php echo $_SESSION['success']; unset($_SESSION['success']); ?></div>
<?php endif; ?>

<?php if (isset($_SESSION['error'])): ?>
    <div class="alert alert-danger">❌ <?php echo $_SESSION['error']; unset($_SESSION['error']); ?></div>
<?php endif; ?>

<div class="row" style="margin-bottom: 24px;">
    <div class="col-md-4">
        <div class="card">
            <div class="card-header">
                <h3>Profile Information</h3>
            </div>
            <div class="card-body" style="text-align: center;">
                <div class="profile-avatar" style="width: 120px; height: 120px; border-radius: 50%; background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white; display: flex; align-items: center; justify-content: center; font-size: 48px; font-weight: bold; margin: 0 auto 20px;">
                    <?php echo strtoupper(substr($user['full_name'], 0, 1)); ?>
                </div>
                <h3 style="margin-bottom: 5px;"><?php echo $user['full_name']; ?></h3>
                <p style="color: #6b7280; margin-bottom: 5px;">@<?php echo $user['username']; ?></p>
                <span class="badge badge-info" style="font-size: 12px;"><?php echo ucfirst($user['role']); ?></span>
                
                <div style="margin-top: 20px; padding-top: 20px; border-top: 1px solid #e5e7eb; text-align: left;">
                    <p style="margin: 10px 0; font-size: 14px;">
                        <strong>Email:</strong><br>
                        <span style="color: #6b7280;"><?php echo $user['email'] ?? 'Not set'; ?></span>
                    </p>
                    <p style="margin: 10px 0; font-size: 14px;">
                        <strong>Phone:</strong><br>
                        <span style="color: #6b7280;"><?php echo $user['phone'] ?? 'Not set'; ?></span>
                    </p>
                    <p style="margin: 10px 0; font-size: 14px;">
                        <strong>Status:</strong><br>
                        <span class="badge badge-success"><?php echo ucfirst($user['status']); ?></span>
                    </p>
                    <p style="margin: 10px 0; font-size: 14px;">
                        <strong>Member Since:</strong><br>
                        <span style="color: #6b7280;"><?php echo date('d M Y', strtotime($user['created_at'])); ?></span>
                    </p>
                    <?php if ($user['last_login']): ?>
                    <p style="margin: 10px 0; font-size: 14px;">
                        <strong>Last Login:</strong><br>
                        <span style="color: #6b7280;"><?php echo date('d M Y, h:i A', strtotime($user['last_login'])); ?></span>
                    </p>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
    
    <div class="col-md-8">
        <!-- Update Profile -->
        <div class="card" style="margin-bottom: 24px;">
            <div class="card-header">
                <h3>Update Profile Information</h3>
            </div>
            <div class="card-body">
                <form method="POST">
                    <div class="form-group">
                        <label>Full Name <span style="color: red;">*</span></label>
                        <input type="text" name="full_name" class="form-control" 
                               value="<?php echo $user['full_name']; ?>" required>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Email</label>
                                <input type="email" name="email" class="form-control" 
                                       value="<?php echo $user['email']; ?>">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Phone</label>
                                <input type="text" name="phone" class="form-control" 
                                       value="<?php echo $user['phone']; ?>">
                            </div>
                        </div>
                    </div>
                    <button type="submit" name="update_profile" class="btn btn-primary">
                        💾 Update Profile
                    </button>
                </form>
            </div>
        </div>
        
        <!-- Change Username -->
        <div class="card" style="margin-bottom: 24px;">
            <div class="card-header">
                <h3>Change Username</h3>
            </div>
            <div class="card-body">
                <form method="POST">
                    <div class="form-group">
                        <label>Current Username</label>
                        <input type="text" class="form-control" value="<?php echo $user['username']; ?>" disabled>
                    </div>
                    <div class="form-group">
                        <label>New Username <span style="color: red;">*</span></label>
                        <input type="text" name="new_username" class="form-control" 
                               placeholder="Enter new username" required>
                        <small class="text-muted">Username must be unique</small>
                    </div>
                    <div class="form-group">
                        <label>Current Password <span style="color: red;">*</span></label>
                        <input type="password" name="current_password" class="form-control" 
                               placeholder="Enter current password to confirm" required>
                    </div>
                    <button type="submit" name="change_username" class="btn btn-warning">
                        🔄 Change Username
                    </button>
                </form>
            </div>
        </div>
        
        <!-- Change Password -->
        <div class="card">
            <div class="card-header">
                <h3>Change Password</h3>
            </div>
            <div class="card-body">
                <form method="POST" id="passwordForm">
                    <div class="form-group">
                        <label>Current Password <span style="color: red;">*</span></label>
                        <input type="password" name="current_password_pass" class="form-control" 
                               placeholder="Enter current password" required>
                    </div>
                    <div class="form-group">
                        <label>New Password <span style="color: red;">*</span></label>
                        <input type="password" name="new_password" id="new_password" class="form-control" 
                               placeholder="Enter new password (min 6 characters)" required>
                        <small class="text-muted">Password must be at least 6 characters long</small>
                    </div>
                    <div class="form-group">
                        <label>Confirm New Password <span style="color: red;">*</span></label>
                        <input type="password" name="confirm_password" id="confirm_password" class="form-control" 
                               placeholder="Re-enter new password" required>
                        <small id="password-match" style="display: none;"></small>
                    </div>
                    <button type="submit" name="change_password" class="btn btn-danger">
                        🔒 Change Password
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
// Password match validation
document.getElementById('confirm_password').addEventListener('input', function() {
    const newPassword = document.getElementById('new_password').value;
    const confirmPassword = this.value;
    const matchIndicator = document.getElementById('password-match');
    
    if (confirmPassword.length > 0) {
        matchIndicator.style.display = 'block';
        if (newPassword === confirmPassword) {
            matchIndicator.textContent = '✅ Passwords match';
            matchIndicator.style.color = '#10b981';
        } else {
            matchIndicator.textContent = '❌ Passwords do not match';
            matchIndicator.style.color = '#ef4444';
        }
    } else {
        matchIndicator.style.display = 'none';
    }
});

// Form validation
document.getElementById('passwordForm').addEventListener('submit', function(e) {
    const newPassword = document.getElementById('new_password').value;
    const confirmPassword = document.getElementById('confirm_password').value;
    
    if (newPassword !== confirmPassword) {
        e.preventDefault();
        alert('Passwords do not match!');
        return false;
    }
    
    if (newPassword.length < 6) {
        e.preventDefault();
        alert('Password must be at least 6 characters long!');
        return false;
    }
});
</script>

<?php include("includes/footer.php"); ?>
