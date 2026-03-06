<?php
include("../config/db.php");
$page_title = "Add New User";
include("includes/header.php");

// Check if user is admin
if ($_SESSION['role'] != 'admin') {
    header("Location: dashboard.php");
    exit();
}

// Handle form submission
if (isset($_POST['add_user'])) {
    $username = mysqli_real_escape_string($conn, $_POST['username']);
    $password = $_POST['password'];
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
    
    if (empty($password)) {
        $errors[] = "Password is required";
    } elseif (strlen($password) < 6) {
        $errors[] = "Password must be at least 6 characters";
    }
    
    if (empty($full_name)) {
        $errors[] = "Full name is required";
    }
    
    // Check if username exists
    $check_username = mysqli_query($conn, "SELECT id FROM users WHERE username = '$username'");
    if (mysqli_num_rows($check_username) > 0) {
        $errors[] = "Username already exists";
    }
    
    if (empty($errors)) {
        // Hash password
        $hashed_password = password_hash($password, PASSWORD_DEFAULT);
        
        // Insert user
        $insert_query = "INSERT INTO users (username, password, full_name, email, phone, role, status) 
                        VALUES ('$username', '$hashed_password', '$full_name', '$email', '$phone', '$role', '$status')";
        
        if (mysqli_query($conn, $insert_query)) {
            $_SESSION['success'] = "User added successfully!";
            header("Location: users.php");
            exit();
        } else {
            $_SESSION['error'] = "Error adding user: " . mysqli_error($conn);
        }
    } else {
        $_SESSION['error'] = implode("<br>", $errors);
    }
}
?>

<?php if (isset($_SESSION['error'])): ?>
    <div class="alert alert-danger">❌ <?php echo $_SESSION['error']; unset($_SESSION['error']); ?></div>
<?php endif; ?>

<div class="card">
    <div class="card-header">
        <h3>Add New User</h3>
    </div>
    <div class="card-body">
        <form method="POST">
            <div class="row">
                <div class="col-md-6">
                    <div class="form-group">
                        <label>Username <span style="color: red;">*</span></label>
                        <input type="text" name="username" class="form-control" 
                               placeholder="Enter username" required>
                        <small class="text-muted">Username must be unique</small>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-group">
                        <label>Password <span style="color: red;">*</span></label>
                        <input type="password" name="password" class="form-control" 
                               placeholder="Enter password (min 6 characters)" required>
                        <small class="text-muted">Minimum 6 characters</small>
                    </div>
                </div>
            </div>
            
            <div class="form-group">
                <label>Full Name <span style="color: red;">*</span></label>
                <input type="text" name="full_name" class="form-control" 
                       placeholder="Enter full name" required>
            </div>
            
            <div class="row">
                <div class="col-md-6">
                    <div class="form-group">
                        <label>Email</label>
                        <input type="email" name="email" class="form-control" 
                               placeholder="Enter email address">
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-group">
                        <label>Phone</label>
                        <input type="text" name="phone" class="form-control" 
                               placeholder="Enter phone number">
                    </div>
                </div>
            </div>
            
            <div class="row">
                <div class="col-md-6">
                    <div class="form-group">
                        <label>Role <span style="color: red;">*</span></label>
                        <select name="role" class="form-control" required>
                            <option value="cashier">Cashier</option>
                            <option value="pharmacist">Pharmacist</option>
                            <option value="manager">Manager</option>
                            <option value="admin">Administrator</option>
                        </select>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-group">
                        <label>Status <span style="color: red;">*</span></label>
                        <select name="status" class="form-control" required>
                            <option value="active">Active</option>
                            <option value="inactive">Inactive</option>
                        </select>
                    </div>
                </div>
            </div>
            
            <div class="form-group">
                <button type="submit" name="add_user" class="btn btn-success btn-lg">
                    ✅ Add User
                </button>
                <a href="users.php" class="btn btn-secondary btn-lg">Cancel</a>
            </div>
        </form>
    </div>
</div>

<?php include("includes/footer.php"); ?>
