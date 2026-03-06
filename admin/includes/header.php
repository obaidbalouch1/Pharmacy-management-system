<?php
if (!isset($_SESSION['user_id'])) {
    header("Location: ../login.php");
    exit();
}

// Load settings helper
include_once("../config/settings.php");
$store_settings = get_all_settings($conn);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo isset($page_title) ? $page_title : 'Dashboard'; ?> - Pharmacy System</title>
    <link rel="stylesheet" href="../css/style.css">
</head>
<body>
    <div class="dashboard-wrapper">
        <!-- Sidebar -->
        <aside class="sidebar">
            <div class="sidebar-header">
                <h2>🏥 <?php echo htmlspecialchars($store_settings['store_name']); ?></h2>
                <p><?php echo htmlspecialchars($store_settings['store_tagline']); ?></p>
            </div>
            <nav class="sidebar-menu">
                <a href="dashboard.php" class="menu-item <?php echo basename($_SERVER['PHP_SELF']) == 'dashboard.php' ? 'active' : ''; ?>">
                    📊 Dashboard
                </a>
                <a href="medicines.php" class="menu-item <?php echo basename($_SERVER['PHP_SELF']) == 'medicines.php' ? 'active' : ''; ?>">
                    💊 Medicines
                </a>
                <a href="sales.php" class="menu-item <?php echo basename($_SERVER['PHP_SELF']) == 'sales.php' ? 'active' : ''; ?>">
                    🛒 Sales
                </a>
                <a href="search_invoice.php" class="menu-item <?php echo basename($_SERVER['PHP_SELF']) == 'search_invoice.php' ? 'active' : ''; ?>">
                    🔍 Search Invoice
                </a>
                <a href="analytics.php" class="menu-item <?php echo basename($_SERVER['PHP_SELF']) == 'analytics.php' ? 'active' : ''; ?>">
                    📊 Analytics
                </a>
                <a href="financial_reports.php" class="menu-item <?php echo basename($_SERVER['PHP_SELF']) == 'financial_reports.php' ? 'active' : ''; ?>">
                    💵 Financial Reports
                </a>
                <a href="purchases.php" class="menu-item <?php echo basename($_SERVER['PHP_SELF']) == 'purchases.php' ? 'active' : ''; ?>">
                    📦 Purchases
                </a>
                <a href="customers.php" class="menu-item <?php echo basename($_SERVER['PHP_SELF']) == 'customers.php' ? 'active' : ''; ?>">
                    👥 Customers
                </a>
                <a href="companies.php" class="menu-item <?php echo basename($_SERVER['PHP_SELF']) == 'companies.php' ? 'active' : ''; ?>">
                    🏢 Companies
                </a>
                <a href="suppliers.php" class="menu-item <?php echo basename($_SERVER['PHP_SELF']) == 'suppliers.php' ? 'active' : ''; ?>">
                    🚚 Suppliers
                </a>
                <a href="reports.php" class="menu-item <?php echo basename($_SERVER['PHP_SELF']) == 'reports.php' ? 'active' : ''; ?>">
                    📈 Reports
                </a>
                <a href="expiry_alert.php" class="menu-item <?php echo basename($_SERVER['PHP_SELF']) == 'expiry_alert.php' ? 'active' : ''; ?>">
                    ⚠️ Expiry Alerts
                </a>
                <a href="profile.php" class="menu-item <?php echo basename($_SERVER['PHP_SELF']) == 'profile.php' ? 'active' : ''; ?>">
                    👤 My Profile
                </a>
                <?php if ($_SESSION['role'] == 'admin'): ?>
                <a href="users.php" class="menu-item <?php echo basename($_SERVER['PHP_SELF']) == 'users.php' ? 'active' : ''; ?>">
                    👥 Manage Users
                </a>
                <a href="activity_logs.php" class="menu-item <?php echo basename($_SERVER['PHP_SELF']) == 'activity_logs.php' ? 'active' : ''; ?>">
                    📋 Activity Logs
                </a>
                <a href="backup_restore.php" class="menu-item <?php echo basename($_SERVER['PHP_SELF']) == 'backup_restore.php' ? 'active' : ''; ?>">
                    💾 Backup & Restore
                </a>
                <a href="settings.php" class="menu-item <?php echo basename($_SERVER['PHP_SELF']) == 'settings.php' ? 'active' : ''; ?>">
                    ⚙️ Store Settings
                </a>
                <?php endif; ?>
                <a href="../logout.php" class="menu-item">
                    🚪 Logout
                </a>
            </nav>
        </aside>

        <!-- Main Content -->
        <main class="main-content">
            <div class="top-bar">
                <div class="page-title">
                    <h1><?php echo isset($page_title) ? $page_title : 'Dashboard'; ?></h1>
                </div>
                <div class="user-info" style="position: relative;">
                    <a href="profile.php" style="text-decoration: none; color: inherit; display: flex; align-items: center; gap: 15px;">
                        <div class="user-avatar">
                            <?php echo strtoupper(substr($_SESSION['full_name'], 0, 1)); ?>
                        </div>
                        <div>
                            <strong><?php echo $_SESSION['full_name']; ?></strong>
                            <br><small style="color: #6b7280;"><?php echo ucfirst($_SESSION['role']); ?></small>
                        </div>
                    </a>
                </div>
            </div>
