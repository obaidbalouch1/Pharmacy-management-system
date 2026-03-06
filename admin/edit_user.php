<?php
include("../config/db.php");
$page_title = "Edit User";
include("includes/header.php");

// Check if user is admin
if ($_SESSION['role'] != 'admin') {
    header("Location: dashboard.php");
    exit();
}

$user_id = isset($_GET['id']) ? intval($_GET['id']) : 0;

// Get user details
$user_query = mysqli_query($conn, "SELECT * FROM users WHERE id = $user_id");

if (mysqli_num_rows($user_query) == 0) {
    $_SESSION['error'] = "User not found!";
    header("Location: users.php");
    exit();
}

$user = mysqli_fetch_assoc($user_query);

// Handle form submission
if (isset($_POST['update_user'])) {
    $username = mysqli_real_escape_string($conn, $_POST['username']);
    $full_name = mysqli_real_escape_string($conn, $_POST['full_name']);
    $email = mysqli_real_escape_string($conn, $_POST['email']);
    $phone = mysqli_real_escape_string($conn, $_POST['phone']);
    $role = mysqli_real_escape_string($conn, $_POST['role']);
    $status = mysqli_real_escape_string($conn, $_POST['status']);
    
    // Validate inputs
    $errors = [];
    
    if (empty($username)) {
        $errors[] = "Username is required";
    }
    
    if (empty($full_name)) {
        $errors[] = "Full name is required";
    }
    
    // Check if username exists (excluding current user)
    $check_username = mysqli_query($conn, "SELECT id FROM users WHERE username = '$username' AND id != $user_id");
    if (mysqli_num_rows($check_username) > 0) {
        $errors[] = "Username already exists";
    }
    
    // Prevent changing own role or status
    if ($user_id == $_SESSION['user_id']) {
        if ($role != $user['role']) {
            $errors[] = "You cannot change your own role";
        }
        if ($status != $user['status']) {
            $errors[] = "You cannot change your own status";
        }
    }
    
    if (empty($errors)) {
        // Update user
        $update_query = "UPDATE users SET 
                        username = '$username',
                        full_name = '$full_name',
                        email = '$email',
                        phone = '$phone',
                        role = '$role',
                        status = '$status'
                        WHERE id = $user_id";
        
        if (mysqli_query($conn, $update_query)) {
            // Update session if editing own profile
            if ($user_id == $_SESSION['user_id']) {
                $_SESSION['username'] = $username;
                $_SESSION['full_name'] = $full_name;
            }
            
            $_SESSION['success'] = "User updated successfully!";
            header("Location: users.php");
            exit();
        } else {
            $_SESSION['error'] = "Error updating user: " . mysqli_error($conn);
        }
    } else {
        $_SESSION['error'] = implode("<br>", $errors);
    }
}

// Handle password reset
if (isset($_POST['reset_password'])) {
    $new_password = $_POST['new_password'];
    $confirm_password = $_POST['confirm_password'];
    
    $errors = [];
    
    if (empty($new_password)) {
        $errors[] = "Password is required";
    } elseif (strlen($new_password) < 6) {
        $errors[] = "Password must be at least 6 characters";
    }
    
    if ($new_password !== $confirm_password) {
        $errors[] = "Passwords do not match";
    }
    
    if (empty($errors)) {
        $hashed_password = password_hash($new_password, PASSWORD_DEFAULT);
        $update_query = "UPDATE users SET password = '$hashed_password' WHERE id = $user_id";
        
        if (mysqli_query($conn, $update_query)) {
            $_SESSION['success'] = "Password reset successfully!";
            header("Location: edit_user.php?id=$user_id");
            exit();
        } else {
            $_SESSION['error'] = "Error resetting password: " . mysqli_error($conn);
        }
    } else {
        $_SESSION['error'] = implode("<br>", $errors);
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

<div class="row">
    <div class="col-md-4">
        <div class="card">
            <div class="card-header">
                <h3>User Information</h3>
            </div>
            <div class="card-body" style="text-align: center;">
                <div class="profile-avatar" style="width: 100px; height: 100px; border-radius: 50%; background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white; display: flex; align-items: center; justify-content: center; font-size: 40px; font-weight: bold; margin: 0 auto 15px;">
                    <?php echo strtoupper(substr($user['full_name'], 0, 1)); ?>
                </div>
                <h3 style="margin-bottom: 5px;"><?php echo $user['full_name']; ?></h3>
                <p style="color: #6b7280; margin-bottom: 5px;">@<?php echo $user['username']; ?></p>
                <span class="badge badge-<?php 
                    echo $user['role'] == 'admin' ? 'danger' : 
                        ($user['role'] == 'manager' ? 'warning' : 
                        ($user['role'] == 'pharmacist' ? 'info' : 'secondary')); 
                ?>" style="font-size: 12px;">
                    <?php echo ucfirst($user['role']); ?>
                </span>
                
                <div style="margin-top: 20px; padding-top: 20px; border-top: 1px solid #e5e7eb; text-align: left;">
                    <p style="margin: 10px 0; font-size: 14px;">
                        <strong>Status:</strong><br>
                        <span class="badge badge-<?php echo $user['status'] == 'active' ? 'success' : 'danger'; ?>">
                            <?php echo ucfirst($user['status']); ?>
                        </span>
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
        <!-- Edit User Form -->
        <div class="card" style="margin-bottom: 24px;">
            <div class="card-header">
                <h3>Edit User Details</h3>
            </div>
            <div class="card-body">
                <form method="POST">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Username <span style="color: red;">*</span></label>
                                <input type="text" name="username" class="form-control" 
                                       value="<?php echo $user['username']; ?>" required>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Full Name <span style="color: red;">*</span></label>
                                <input type="text" name="full_name" class="form-control" 
                                       value="<?php echo $user['full_name']; ?>" required>
                            </div>
                        </div>
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
                    
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Role <span style="color: red;">*</span></label>
                                <select name="role" class="form-control" required 
                                        <?php echo $user_id == $_SESSION['user_id'] ? 'disabled' : ''; ?>>
                                    <option value="cashier" <?php echo $user['role'] == 'cashier' ? 'selected' : ''; ?>>Cashier</option>
                                    <option value="pharmacist" <?php echo $user['role'] == 'pharmacist' ? 'selected' : ''; ?>>Pharmacist</option>
                                    <option value="manager" <?php echo $user['role'] == 'manager' ? 'selected' : ''; ?>>Manager</option>
                                    <option value="admin" <?php echo $user['role'] == 'admin' ? 'selected' : ''; ?>>Administrator</option>
                                </select>
                                <?php if ($user_id == $_SESSION['user_id']): ?>
                                    <small class="text-muted">You cannot change your own role</small>
                                    <input type="hidden" name="role" value="<?php echo $user['role']; ?>">
                                <?php endif; ?>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Status <span style="color: red;">*</span></label>
                                <select name="status" class="form-control" required
                                        <?php echo $user_id == $_SESSION['user_id'] ? 'disabled' : ''; ?>>
                                    <option value="active" <?php echo $user['status'] == 'active' ? 'selected' : ''; ?>>Active</option>
                                    <option value="inactive" <?php echo $user['status'] == 'inactive' ? 'selected' : ''; ?>>Inactive</option>
                                </select>
                                <?php if ($user_id == $_SESSION['user_id']): ?>
                                    <small class="text-muted">You cannot change your own status</small>
                                    <input type="hidden" name="status" value="<?php echo $user['status']; ?>">
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                    
                    <div class="form-group">
                        <button type="submit" name="update_user" class="btn btn-primary btn-lg">
                            💾 Update User
                        </button>
                        <a href="users.php" class="btn btn-secondary btn-lg">Back to Users</a>
                    </div>
                </form>
            </div>
        </div>
        
        <!-- Reset Password Form -->
        <div class="card">
            <div class="card-header">
                <h3>Reset Password</h3>
            </div>
            <div class="card-body">
                <form method="POST" id="passwordForm">
                    <div class="form-group">
                        <label>New Password <span style="color: red;">*</span></label>
                        <input type="password" name="new_password" id="new_password" class="form-control" 
                               placeholder="Enter new password (min 6 characters)" required>
                    </div>
                    <div class="form-group">
                        <label>Confirm Password <span style="color: red;">*</span></label>
                        <input type="password" name="confirm_password" id="confirm_password" class="form-control" 
                               placeholder="Re-enter new password" required>
                        <small id="password-match" style="display: none;"></small>
                    </div>
                    <button type="submit" name="reset_password" class="btn btn-danger">
                        🔒 Reset Password
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
