<?php
include("../config/db.php");
$page_title = "Sales Management";
include("includes/header.php");

// Search and filter
$search = isset($_GET['search']) ? mysqli_real_escape_string($conn, $_GET['search']) : '';
$date_from = isset($_GET['date_from']) ? $_GET['date_from'] : '';
$date_to = isset($_GET['date_to']) ? $_GET['date_to'] : '';
$payment_status = isset($_GET['payment_status']) ? mysqli_real_escape_string($conn, $_GET['payment_status']) : '';

// Pagination
$page = isset($_GET['page']) ? intval($_GET['page']) : 1;
$records_per_page = 15;
$offset = ($page - 1) * $records_per_page;

// Build query for counting
$count_query = "SELECT COUNT(*) as total FROM sales s 
    LEFT JOIN customers c ON s.customer_id = c.id 
    LEFT JOIN users u ON s.user_id = u.id WHERE 1=1";

if ($search) {
    $count_query .= " AND (s.invoice_number LIKE '%$search%' OR c.customer_name LIKE '%$search%')";
}
if ($date_from) {
    $count_query .= " AND DATE(s.sale_date) >= '$date_from'";
}
if ($date_to) {
    $count_query .= " AND DATE(s.sale_date) <= '$date_to'";
}
if ($payment_status) {
    $count_query .= " AND s.payment_status = '$payment_status'";
}

$total_records = mysqli_fetch_assoc(mysqli_query($conn, $count_query))['total'];
$total_pages = ceil($total_records / $records_per_page);

// Build query for data
$query = "SELECT s.*, c.customer_name, u.full_name as cashier FROM sales s 
    LEFT JOIN customers c ON s.customer_id = c.id 
    LEFT JOIN users u ON s.user_id = u.id WHERE 1=1";

if ($search) {
    $query .= " AND (s.invoice_number LIKE '%$search%' OR c.customer_name LIKE '%$search%')";
}
if ($date_from) {
    $query .= " AND DATE(s.sale_date) >= '$date_from'";
}
if ($date_to) {
    $query .= " AND DATE(s.sale_date) <= '$date_to'";
}
if ($payment_status) {
    $query .= " AND s.payment_status = '$payment_status'";
}

$query .= " ORDER BY s.sale_date DESC LIMIT $records_per_page OFFSET $offset";
$sales = mysqli_query($conn, $query);

// Get statistics
$total_sales = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as count, SUM(grand_total) as total FROM sales"));
?>

<div class="card">
    <div class="card-header" style="display: flex; justify-content: space-between; align-items: center;">
        <h3>All Sales (<?php echo $total_records; ?> records)</h3>
        <div style="display: flex; gap: 10px;">
            <a href="bulk_sales.php" class="btn btn-info" style="min-width: 140px;">+ Bulk Sales</a>
            <a href="new_sale.php" class="btn btn-primary" style="min-width: 140px;">+ New Sale</a>
        </div>
    </div>
    <div class="card-body">
        <!-- Search and Filter Form -->
        <form method="GET" style="margin-bottom: 20px;">
            <div class="row">
                <div class="col-md-3">
                    <div class="form-group">
                        <input type="text" name="search" class="form-control" placeholder="Search invoice or customer..." value="<?php echo htmlspecialchars($search); ?>">
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
                <a href="sales.php" class="btn btn-secondary btn-sm">Clear Filters</a>
            </div>
            <?php endif; ?>
        </form>
        
        <div class="table-responsive">
            <table>
                <thead>
                    <tr>
                        <th>Invoice #</th>
                        <th>Customer</th>
                        <th>Date</th>
                        <th>Subtotal</th>
                        <th>Tax</th>
                        <th>Discount</th>
                        <th>Grand Total</th>
                        <th>Payment</th>
                        <th>Cashier</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while ($sale = mysqli_fetch_assoc($sales)): ?>
                    <tr>
                        <td><strong><?php echo $sale['invoice_number']; ?></strong></td>
                        <td><?php echo $sale['customer_name'] ?? 'Walk-in Customer'; ?></td>
                        <td><?php echo date('d M Y, h:i A', strtotime($sale['sale_date'])); ?></td>
                        <td>Rs <?php echo number_format($sale['subtotal'], 2); ?></td>
                        <td>Rs <?php echo number_format($sale['tax_amount'], 2); ?></td>
                        <td>Rs <?php echo number_format($sale['discount_amount'], 2); ?></td>
                        <td><strong>Rs <?php echo number_format($sale['grand_total'], 2); ?></strong></td>
                        <td>
                            <span class="badge badge-<?php echo $sale['payment_status'] == 'paid' ? 'success' : 'warning'; ?>">
                                <?php echo ucfirst($sale['payment_status']); ?>
                            </span>
                        </td>
                        <td><?php echo $sale['cashier']; ?></td>
                        <td>
                            <div class="action-buttons">
                                <a href="view_sale.php?id=<?php echo $sale['id']; ?>" class="btn btn-info btn-sm">View</a>
                                <a href="print_invoice.php?id=<?php echo $sale['id']; ?>" class="btn btn-success btn-sm" target="_blank">Print</a>
                            </div>
                        </td>
                    </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        </div>
        
        <?php if ($total_pages > 1): ?>
        <div class="pagination-container">
            <div class="pagination-info">
                Showing <?php echo $offset + 1; ?> to <?php echo min($offset + $records_per_page, $total_records); ?> of <?php echo $total_records; ?> sales
            </div>
            <div class="pagination">
                <?php if ($page > 1): ?>
                    <a href="?page=1<?php echo $search ? '&search=' . urlencode($search) : ''; ?><?php echo $date_from ? '&date_from=' . $date_from : ''; ?><?php echo $date_to ? '&date_to=' . $date_to : ''; ?><?php echo $payment_status ? '&payment_status=' . $payment_status : ''; ?>" class="pagination-btn">First</a>
                    <a href="?page=<?php echo $page - 1; ?><?php echo $search ? '&search=' . urlencode($search) : ''; ?><?php echo $date_from ? '&date_from=' . $date_from : ''; ?><?php echo $date_to ? '&date_to=' . $date_to : ''; ?><?php echo $payment_status ? '&payment_status=' . $payment_status : ''; ?>" class="pagination-btn">Previous</a>
                <?php endif; ?>
                
                <?php
                $start_page = max(1, $page - 2);
                $end_page = min($total_pages, $page + 2);
                
                for ($i = $start_page; $i <= $end_page; $i++):
                ?>
                    <a href="?page=<?php echo $i; ?><?php echo $search ? '&search=' . urlencode($search) : ''; ?><?php echo $date_from ? '&date_from=' . $date_from : ''; ?><?php echo $date_to ? '&date_to=' . $date_to : ''; ?><?php echo $payment_status ? '&payment_status=' . $payment_status : ''; ?>" class="pagination-btn <?php echo $i == $page ? 'active' : ''; ?>">
                        <?php echo $i; ?>
                    </a>
                <?php endfor; ?>
                
                <?php if ($page < $total_pages): ?>
                    <a href="?page=<?php echo $page + 1; ?><?php echo $search ? '&search=' . urlencode($search) : ''; ?><?php echo $date_from ? '&date_from=' . $date_from : ''; ?><?php echo $date_to ? '&date_to=' . $date_to : ''; ?><?php echo $payment_status ? '&payment_status=' . $payment_status : ''; ?>" class="pagination-btn">Next</a>
                    <a href="?page=<?php echo $total_pages; ?><?php echo $search ? '&search=' . urlencode($search) : ''; ?><?php echo $date_from ? '&date_from=' . $date_from : ''; ?><?php echo $date_to ? '&date_to=' . $date_to : ''; ?><?php echo $payment_status ? '&payment_status=' . $payment_status : ''; ?>" class="pagination-btn">Last</a>
                <?php endif; ?>
            </div>
        </div>
        <?php endif; ?>
    </div>
</div>

<?php include("includes/footer.php"); ?>
