<?php
include("../config/db.php");
$page_title = "Search Invoice";
include("includes/header.php");

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: ../login.php");
    exit();
}

$search_query = isset($_GET['search']) ? mysqli_real_escape_string($conn, $_GET['search']) : '';
$results = [];

if (!empty($search_query)) {
    $query = "SELECT s.*, c.customer_name, u.full_name as cashier 
              FROM sales s 
              LEFT JOIN customers c ON s.customer_id = c.id 
              LEFT JOIN users u ON s.user_id = u.id 
              WHERE s.invoice_number LIKE '%$search_query%' 
              OR c.customer_name LIKE '%$search_query%'
              OR s.customer_name LIKE '%$search_query%'
              ORDER BY s.sale_date DESC 
              LIMIT 50";
    
    $result = mysqli_query($conn, $query);
    while ($row = mysqli_fetch_assoc($result)) {
        $results[] = $row;
    }
}
?>

<div class="card">
    <div class="card-header">
        <h3>🔍 Search Invoice</h3>
    </div>
    <div class="card-body">
        <form method="GET" style="margin-bottom: 30px;">
            <div class="row">
                <div class="col-md-8">
                    <div class="form-group">
                        <input type="text" name="search" class="form-control" 
                               placeholder="Search by Invoice Number or Customer Name..." 
                               value="<?php echo htmlspecialchars($search_query); ?>" 
                               autofocus required>
                    </div>
                </div>
                <div class="col-md-4">
                    <button type="submit" class="btn btn-primary" style="width: 100%;">
                        🔍 Search
                    </button>
                </div>
            </div>
        </form>

        <?php if (!empty($search_query)): ?>
            <?php if (count($results) > 0): ?>
                <h4>Found <?php echo count($results); ?> invoice(s)</h4>
                <div class="table-responsive">
                    <table>
                        <thead>
                            <tr>
                                <th>Invoice #</th>
                                <th>Customer</th>
                                <th>Date</th>
                                <th>Amount</th>
                                <th>Payment</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($results as $sale): ?>
                            <tr>
                                <td><strong><?php echo $sale['invoice_number']; ?></strong></td>
                                <td><?php echo $sale['customer_name'] ?? 'Walk-in'; ?></td>
                                <td><?php echo date('d M Y, h:i A', strtotime($sale['sale_date'])); ?></td>
                                <td><strong>Rs <?php echo number_format($sale['grand_total'], 2); ?></strong></td>
                                <td>
                                    <span class="badge badge-<?php echo $sale['payment_status'] == 'paid' ? 'success' : 'warning'; ?>">
                                        <?php echo ucfirst($sale['payment_status']); ?>
                                    </span>
                                </td>
                                <td>
                                    <div class="action-buttons">
                                        <a href="view_sale.php?id=<?php echo $sale['id']; ?>" class="btn btn-sm btn-info">View</a>
                                        <a href="print_invoice.php?id=<?php echo $sale['id']; ?>" class="btn btn-sm btn-success" target="_blank">Print</a>
                                        <a href="return_sale.php?id=<?php echo $sale['id']; ?>" class="btn btn-sm btn-warning">Return</a>
                                    </div>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            <?php else: ?>
                <div style="text-align: center; padding: 40px; color: #6b7280;">
                    <div style="font-size: 48px; margin-bottom: 16px;">🔍</div>
                    <h4 style="color: #374151; margin-bottom: 8px;">No Results Found</h4>
                    <p>No invoices found matching "<?php echo htmlspecialchars($search_query); ?>"</p>
                    <p>Try searching by:</p>
                    <ul style="list-style: none; padding: 0;">
                        <li>• Invoice number (e.g., INV-20260304-0001)</li>
                        <li>• Customer name</li>
                        <li>• Part of invoice number</li>
                    </ul>
                </div>
            <?php endif; ?>
        <?php else: ?>
            <div style="text-align: center; padding: 40px; color: #6b7280;">
                <div style="font-size: 48px; margin-bottom: 16px;">🔍</div>
                <h4 style="color: #374151; margin-bottom: 8px;">Search for an Invoice</h4>
                <p>Enter an invoice number or customer name to search</p>
            </div>
        <?php endif; ?>
    </div>
</div>

<?php include("includes/footer.php"); ?>
