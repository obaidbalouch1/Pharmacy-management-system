<?php
include("../config/db.php");
$page_title = "Manage Users";
include("includes/header.php");

// Check if user is admin
if ($_SESSION['role'] != 'admin') {
    header("Location: dashboard.php");
    exit();
}

// Handle delete user
if (isset($_GET['delete'])) {
    $delete_id = intval($_GET['delete']);
    
    // Prevent deleting yourself
    if ($delete_id == $_SESSION['user_id']) {
        $_SESSION['error'] = "You cannot delete your own account!";
    } else {
        $delete_query = "DELETE FROM users WHERE id = $delete_id";
        if (mysqli_query($conn, $delete_query)) {
            $_SESSION['success'] = "User deleted successfully!";
        } else {
            $_SESSION['error'] = "Error deleting user: " . mysqli_error($conn);
        }
    }
    header("Location: users.php");
    exit();
}

// Handle status toggle
if (isset($_GET['toggle_status'])) {
    $user_id = intval($_GET['toggle_status']);
    
    // Prevent deactivating yourself
    if ($user_id == $_SESSION['user_id']) {
        $_SESSION['error'] = "You cannot deactivate your own account!";
    } else {
        $toggle_query = "UPDATE users SET status = IF(status = 'active', 'inactive', 'active') WHERE id = $user_id";
        if (mysqli_query($conn, $toggle_query)) {
            $_SESSION['success'] = "User status updated successfully!";
        } else {
            $_SESSION['error'] = "Error updating status: " . mysqli_error($conn);
        }
    }
    header("Location: users.php");
    exit();
}

// Get all users
$users = mysqli_query($conn, "SELECT * FROM users ORDER BY created_at DESC");

// Get statistics
$total_users = mysqli_num_rows($users);
$active_users = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as count FROM users WHERE status = 'active'"))['count'];
$inactive_users = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as count FROM users WHERE status = 'inactive'"))['count'];
$admin_count = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as count FROM users WHERE role = 'admin'"))['count'];
?>

<?php if (isset($_SESSION['success'])): ?>
    <div class="alert alert-success">✅ <?php echo $_SESSION['success']; unset($_SESSION['success']); ?></div>
<?php endif; ?>

<?php if (isset($_SESSION['error'])): ?>
    <div class="alert alert-danger">❌ <?php echo $_SESSION['error']; unset($_SESSION['error']); ?></div>
<?php endif; ?>

<!-- Statistics Cards -->
<div class="stats-grid" style="margin-bottom: 24px;">
    <div class="stat-card">
        <div class="stat-icon blue">👥</div>
        <div class="stat-details">
            <h4>Total Users</h4>
            <p><?php echo $total_users; ?></p>
        </div>
    </div>
    
    <div class="stat-card">
        <div class="stat-icon green">✅</div>
        <div class="stat-details">
            <h4>Active Users</h4>
            <p><?php echo $active_users; ?></p>
        </div>
    </div>
    
    <div class="stat-card">
        <div class="stat-icon red">❌</div>
        <div class="stat-details">
            <h4>Inactive Users</h4>
            <p><?php echo $inactive_users; ?></p>
        </div>
    </div>
    
    <div class="stat-card">
        <div class="stat-icon orange">🔑</div>
        <div class="stat-details">
            <h4>Administrators</h4>
            <p><?php echo $admin_count; ?></p>
        </div>
    </div>
</div>

<div class="card">
    <div class="card-header" style="display: flex; justify-content: space-between; align-items: center;">
        <h3>All Users</h3>
        <a href="add_user.php" class="btn btn-primary">+ Add New User</a>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table>
                <thead>
                    <tr>
                        <th>Avatar</th>
                        <th>Full Name</th>
                        <th>Username</th>
                        <th>Email</th>
                        <th>Phone</th>
                        <th>Role</th>
                        <th>Status</th>
                        <th>Last Login</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php 
                    mysqli_data_seek($users, 0);
                    while ($user = mysqli_fetch_assoc($users)): 
                    ?>
                    <tr>
                        <td>
                            <div class="user-avatar" style="width: 40px; height: 40px; border-radius: 50%; background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white; display: flex; align-items: center; justify-content: center; font-weight: bold;">
                                <?php echo strtoupper(substr($user['full_name'], 0, 1)); ?>
                            </div>
                        </td>
                        <td>
                            <strong><?php echo $user['full_name']; ?></strong>
                            <?php if ($user['id'] == $_SESSION['user_id']): ?>
                                <span class="badge badge-info" style="font-size: 10px; margin-left: 5px;">You</span>
                            <?php endif; ?>
                        </td>
                        <td>@<?php echo $user['username']; ?></td>
                        <td><?php echo $user['email'] ?? '<span style="color: #9ca3af;">Not set</span>'; ?></td>
                        <td><?php echo $user['phone'] ?? '<span style="color: #9ca3af;">Not set</span>'; ?></td>
                        <td>
                            <span class="badge badge-<?php 
                                echo $user['role'] == 'admin' ? 'danger' : 
                                    ($user['role'] == 'manager' ? 'warning' : 
                                    ($user['role'] == 'pharmacist' ? 'info' : 'secondary')); 
                            ?>">
                                <?php echo ucfirst($user['role']); ?>
                            </span>
                        </td>
                        <td>
                            <span class="badge badge-<?php echo $user['status'] == 'active' ? 'success' : 'danger'; ?>">
                                <?php echo ucfirst($user['status']); ?>
                            </span>
                        </td>
                        <td>
                            <?php 
                            if ($user['last_login']) {
                                echo date('d M Y, h:i A', strtotime($user['last_login']));
                            } else {
                                echo '<span style="color: #9ca3af;">Never</span>';
                            }
                            ?>
                        </td>
                        <td>
                            <div class="action-buttons">
                                <a href="edit_user.php?id=<?php echo $user['id']; ?>" class="btn btn-info btn-sm">Edit</a>
                                
                                <?php if ($user['id'] != $_SESSION['user_id']): ?>
                                    <a href="?toggle_status=<?php echo $user['id']; ?>" 
                                       class="btn btn-warning btn-sm"
                                       onclick="return confirm('Toggle user status?')">
                                        <?php echo $user['status'] == 'active' ? 'Deactivate' : 'Activate'; ?>
                                    </a>
                                    
                                    <a href="?delete=<?php echo $user['id']; ?>" 
                                       class="btn btn-danger btn-sm"
                                       onclick="return confirm('Are you sure you want to delete this user? This action cannot be undone!')">
                                        Delete
                                    </a>
                                <?php else: ?>
                                    <span class="text-muted" style="font-size: 12px;">Current User</span>
                                <?php endif; ?>
                            </div>
                        </td>
                    </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<?php include("includes/footer.php"); ?>
