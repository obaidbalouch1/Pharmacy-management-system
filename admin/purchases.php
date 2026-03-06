<?php
include("../config/db.php");
$page_title = "Purchase Management";
include("includes/header.php");

// Search and filter
$search = isset($_GET['search']) ? mysqli_real_escape_string($conn, $_GET['search']) : '';
$date_from = isset($_GET['date_from']) ? $_GET['date_from'] : '';
$date_to = isset($_GET['date_to']) ? $_GET['date_to'] : '';
$payment_status = isset($_GET['payment_status']) ? mysqli_real_escape_string($conn, $_GET['payment_status']) : '';

// Build query
$query = "SELECT p.*, s.supplier_name, u.full_name FROM purchases p 
    LEFT JOIN suppliers s ON p.supplier_id = s.id 
    LEFT JOIN users u ON p.user_id = u.id WHERE 1=1";

if ($search) {
    $query .= " AND (p.purchase_number LIKE '%$search%' OR s.supplier_name LIKE '%$search%')";
}
if ($date_from) {
    $query .= " AND p.purchase_date >= '$date_from'";
}
if ($date_to) {
    $query .= " AND p.purchase_date <= '$date_to'";
}
if ($payment_status) {
    $query .= " AND p.payment_status = '$payment_status'";
}

$query .= " ORDER BY p.purchase_date DESC";
$purchases = mysqli_query($conn, $query);
?>

<div class="card">
    <div class="card-header" style="display: flex; justify-content: space-between; align-items: center;">
        <h3>All Purchases (<?php echo mysqli_num_rows($purchases); ?> records)</h3>
        <a href="new_purchase.php" class="btn btn-primary">+ New Purchase</a>
    </div>
    <div class="card-body">
        <!-- Search and Filter Form -->
        <form method="GET" style="margin-bottom: 20px;">
            <div class="row">
                <div class="col-md-3">
                    <div class="form-group">
                        <input type="text" name="search" class="form-control" placeholder="Search purchase or supplier..." value="<?php echo htmlspecialchars($search); ?>">
                    </div>
                </div>
                <div class="col-md-2">
                    <div class="form-group">
                        <input type="date" name="date_from" class="form-control" placeholder="From Date" value="<?php echo $date_from; ?>">
                    </div>
                </div>
                <div class="col-md-2">
                    <div class="form-group">
                        <input type="date" name="date_to" class="form-control" placeholder="To Date" value="<?php echo $date_to; ?>">
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="form-group">
                        <select name="payment_status" class="form-control">
                            <option value="">All Payment Status</option>
                            <option value="paid" <?php echo $payment_status == 'paid' ? 'selected' : ''; ?>>Paid</option>
                            <option value="pending" <?php echo $payment_status == 'pending' ? 'selected' : ''; ?>>Pending</option>
                            <option value="partial" <?php echo $payment_status == 'partial' ? 'selected' : ''; ?>>Partial</option>
                        </select>
                    </div>
                </div>
                <div class="col-md-2">
                    <button type="submit" class="btn btn-primary" style="width: 100%;">🔍 Search</button>
                </div>
            </div>
            <?php if ($search || $date_from || $date_to || $payment_status): ?>
            <div style="margin-top: 10px;">
                <a href="purchases.php" class="btn btn-secondary btn-sm">Clear Filters</a>
            </div>
            <?php endif; ?>
        </form>
        
        <div class="table-responsive">
            <table>
                <thead>
                    <tr>
                        <th>Purchase #</th>
                        <th>Supplier</th>
                        <th>Date</th>
                        <th>Total Amount</th>
                        <th>Tax</th>
                        <th>Grand Total</th>
                        <th>Status</th>
                        <th>Added By</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while ($purchase = mysqli_fetch_assoc($purchases)): ?>
                    <tr>
                        <td><strong><?php echo $purchase['purchase_number']; ?></strong></td>
                        <td><?php echo $purchase['supplier_name']; ?></td>
                        <td><?php echo date('d M Y', strtotime($purchase['purchase_date'])); ?></td>
                        <td>Rs <?php echo number_format($purchase['total_amount'], 2); ?></td>
                        <td>Rs <?php echo number_format($purchase['tax_amount'], 2); ?></td>
                        <td><strong>Rs <?php echo number_format($purchase['grand_total'], 2); ?></strong></td>
                        <td>
                            <span class="badge badge-<?php echo $purchase['payment_status'] == 'paid' ? 'success' : 'warning'; ?>">
                                <?php echo ucfirst($purchase['payment_status']); ?>
                            </span>
                        </td>
                        <td><?php echo $purchase['full_name']; ?></td>
                        <td>
                            <div class="action-buttons">
                                <a href="view_purchase.php?id=<?php echo $purchase['id']; ?>" class="btn btn-info btn-sm">View</a>
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
