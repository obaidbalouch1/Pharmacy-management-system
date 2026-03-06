<?php
include("../config/db.php");
$page_title = "Activity Logs";
include("includes/header.php");

// Only admin can access
if ($_SESSION['role'] != 'admin') {
    echo "<script>alert('Access denied!'); window.location='dashboard.php';</script>";
    exit();
}

// Filters
$user_filter = isset($_GET['user']) ? intval($_GET['user']) : 0;
$action_filter = isset($_GET['action']) ? mysqli_real_escape_string($conn, $_GET['action']) : '';
$date_from = isset($_GET['date_from']) ? $_GET['date_from'] : date('Y-m-d', strtotime('-7 days'));
$date_to = isset($_GET['date_to']) ? $_GET['date_to'] : date('Y-m-d');
$limit = isset($_GET['limit']) ? intval($_GET['limit']) : 50;

// Build query
$query = "SELECT al.*, u.full_name, u.username 
          FROM activity_logs al 
          LEFT JOIN users u ON al.user_id = u.id 
          WHERE DATE(al.created_at) BETWEEN '$date_from' AND '$date_to'";

if ($user_filter > 0) {
    $query .= " AND al.user_id = $user_filter";
}
if ($action_filter) {
    $query .= " AND al.action LIKE '%$action_filter%'";
}

$query .= " ORDER BY al.created_at DESC LIMIT $limit";
$logs = mysqli_query($conn, $query);

// Get users for filter
$users = mysqli_query($conn, "SELECT id, full_name, username FROM users ORDER BY full_name");

// Get statistics
$total_logs = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as count FROM activity_logs WHERE DATE(created_at) BETWEEN '$date_from' AND '$date_to'"))['count'];
$unique_users = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(DISTINCT user_id) as count FROM activity_logs WHERE DATE(created_at) BETWEEN '$date_from' AND '$date_to'"))['count'];
$today_logs = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as count FROM activity_logs WHERE DATE(created_at) = CURDATE()"))['count'];
?>

<!-- Statistics Cards -->
<div class="stats-grid" style="margin-bottom: 24px;">
    <div class="stat-card">
        <div class="stat-icon blue">📊</div>
        <div class="stat-details">
            <h4>Total Logs</h4>
            <p><?php echo number_format($total_logs); ?></p>
        </div>
    </div>
    
    <div class="stat-card">
        <div class="stat-icon green">👥</div>
        <div class="stat-details">
            <h4>Active Users</h4>
            <p><?php echo $unique_users; ?></p>
        </div>
    </div>
    
    <div class="stat-card">
        <div class="stat-icon orange">📅</div>
        <div class="stat-details">
            <h4>Today's Activity</h4>
            <p><?php echo number_format($today_logs); ?></p>
        </div>
    </div>
    
    <div class="stat-card">
        <div class="stat-icon red">📆</div>
        <div class="stat-details">
            <h4>Date Range</h4>
            <p><?php echo date('d M', strtotime($date_from)) . ' - ' . date('d M', strtotime($date_to)); ?></p>
        </div>
    </div>
</div>

<div class="card">
    <div class="card-header">
        <h3>🔍 Activity Logs</h3>
    </div>
    <div class="card-body">
        <!-- Filters -->
        <form method="GET" style="margin-bottom: 20px;">
            <div class="row">
                <div class="col-md-3">
                    <div class="form-group">
                        <label>User</label>
                        <select name="user" class="form-control">
                            <option value="0">All Users</option>
                            <?php while ($user = mysqli_fetch_assoc($users)): ?>
                                <option value="<?php echo $user['id']; ?>" <?php echo $user_filter == $user['id'] ? 'selected' : ''; ?>>
                                    <?php echo $user['full_name']; ?>
                                </option>
                            <?php endwhile; ?>
                        </select>
                    </div>
                </div>
                <div class="col-md-2">
                    <div class="form-group">
                        <label>Action</label>
                        <input type="text" name="action" class="form-control" placeholder="Search action..." value="<?php echo htmlspecialchars($action_filter); ?>">
                    </div>
                </div>
                <div class="col-md-2">
                    <div class="form-group">
                        <label>From Date</label>
                        <input type="date" name="date_from" class="form-control" value="<?php echo $date_from; ?>">
                    </div>
                </div>
                <div class="col-md-2">
                    <div class="form-group">
                        <label>To Date</label>
                        <input type="date" name="date_to" class="form-control" value="<?php echo $date_to; ?>">
                    </div>
                </div>
                <div class="col-md-2">
                    <div class="form-group">
                        <label>Limit</label>
                        <select name="limit" class="form-control">
                            <option value="50" <?php echo $limit == 50 ? 'selected' : ''; ?>>50 records</option>
                            <option value="100" <?php echo $limit == 100 ? 'selected' : ''; ?>>100 records</option>
                            <option value="200" <?php echo $limit == 200 ? 'selected' : ''; ?>>200 records</option>
                            <option value="500" <?php echo $limit == 500 ? 'selected' : ''; ?>>500 records</option>
                        </select>
                    </div>
                </div>
                <div class="col-md-1" style="display: flex; align-items: flex-end;">
                    <div class="form-group" style="width: 100%;">
                        <button type="submit" class="btn btn-primary" style="width: 100%;">Filter</button>
                    </div>
                </div>
            </div>
            <?php if ($user_filter || $action_filter): ?>
            <div style="margin-top: 10px;">
                <a href="activity_logs.php" class="btn btn-secondary btn-sm">Clear Filters</a>
            </div>
            <?php endif; ?>
        </form>
        
        <!-- Logs Table -->
        <?php if (mysqli_num_rows($logs) > 0): ?>
        <div class="table-responsive">
            <table>
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Date & Time</th>
                        <th>User</th>
                        <th>Action</th>
                        <th>Table</th>
                        <th>Record ID</th>
                        <th>IP Address</th>
                        <th>Details</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while ($log = mysqli_fetch_assoc($logs)): ?>
                    <tr>
                        <td><?php echo $log['id']; ?></td>
                        <td><?php echo date('d M Y, h:i A', strtotime($log['created_at'])); ?></td>
                        <td>
                            <strong><?php echo $log['full_name'] ?? 'System'; ?></strong>
                            <br><small class="text-muted"><?php echo $log['username'] ?? 'N/A'; ?></small>
                        </td>
                        <td>
                            <?php
                            $action_class = 'badge-info';
                            if (strpos($log['action'], 'delete') !== false) $action_class = 'badge-danger';
                            elseif (strpos($log['action'], 'create') !== false || strpos($log['action'], 'add') !== false) $action_class = 'badge-success';
                            elseif (strpos($log['action'], 'update') !== false || strpos($log['action'], 'edit') !== false) $action_class = 'badge-warning';
                            elseif (strpos($log['action'], 'login') !== false) $action_class = 'badge-primary';
                            ?>
                            <span class="badge <?php echo $action_class; ?>"><?php echo $log['action']; ?></span>
                        </td>
                        <td><?php echo $log['table_name'] ?? '-'; ?></td>
                        <td><?php echo $log['record_id'] ?? '-'; ?></td>
                        <td><?php echo $log['ip_address'] ?? '-'; ?></td>
                        <td>
                            <?php if ($log['details']): ?>
                                <button class="btn btn-sm btn-info" onclick="showDetails(<?php echo $log['id']; ?>)">View</button>
                                <div id="details-<?php echo $log['id']; ?>" style="display: none;">
                                    <?php echo htmlspecialchars($log['details']); ?>
                                </div>
                            <?php else: ?>
                                -
                            <?php endif; ?>
                        </td>
                    </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        </div>
        <?php else: ?>
        <div style="text-align: center; padding: 40px; color: #6b7280;">
            <div style="font-size: 48px; margin-bottom: 16px;">📋</div>
            <h4 style="color: #374151; margin-bottom: 8px;">No Activity Logs Found</h4>
            <p>No activity logs match your filter criteria.</p>
        </div>
        <?php endif; ?>
    </div>
</div>

<script>
function showDetails(id) {
    const details = document.getElementById('details-' + id).textContent;
    alert('Log Details:\n\n' + details);
}
</script>

<style>
.badge {
    padding: 4px 8px;
    border-radius: 4px;
    font-size: 11px;
    font-weight: 600;
    text-transform: uppercase;
}
.badge-primary { background: #3b82f6; color: white; }
.badge-success { background: #10b981; color: white; }
.badge-warning { background: #f59e0b; color: white; }
.badge-danger { background: #ef4444; color: white; }
.badge-info { background: #6366f1; color: white; }
</style>

<?php include("includes/footer.php"); ?>
