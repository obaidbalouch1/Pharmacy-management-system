<?php
include("../config/db.php");
$page_title = "View Return";
include("includes/header.php");

$return_id = isset($_GET['id']) ? intval($_GET['id']) : 0;

// Get return details
$return_query = mysqli_query($conn, "SELECT sr.*, s.invoice_number, s.sale_date, c.customer_name, u.full_name as processed_by_name
    FROM sale_returns sr
    JOIN sales s ON sr.sale_id = s.id
    LEFT JOIN customers c ON s.customer_id = c.id
    LEFT JOIN users u ON sr.processed_by = u.id
    WHERE sr.id = $return_id");

if (mysqli_num_rows($return_query) == 0) {
    echo "<script>alert('Return not found!'); window.location='sales.php';</script>";
    exit();
}

$return = mysqli_fetch_assoc($return_query);

// Get return items
$items_query = mysqli_query($conn, "SELECT sri.*, m.medicine_name
    FROM sale_return_items sri
    JOIN medicines m ON sri.medicine_id = m.id
    WHERE sri.return_id = $return_id");
?>

<?php if (isset($_SESSION['success'])): ?>
<div class="alert alert-success">
    <?php echo $_SESSION['success']; unset($_SESSION['success']); ?>
</div>
<?php endif; ?>

<div class="card">
    <div class="card-header" style="display: flex; justify-content: space-between; align-items: center;">
        <h3>↩️ Return Details</h3>
        <div>
            <a href="view_sale.php?id=<?php echo $return['sale_id']; ?>" class="btn btn-info">View Original Sale</a>
            <a href="sales.php" class="btn btn-secondary">Back to Sales</a>
        </div>
    </div>
    <div class="card-body">
        <!-- Return Information -->
        <div class="row" style="margin-bottom: 30px;">
            <div class="col-md-6">
                <div style="background: #f3f4f6; padding: 20px; border-radius: 8px;">
                    <h4>Return Information</h4>
                    <table style="width: 100%;">
                        <tr>
                            <td><strong>Return ID:</strong></td>
                            <td>#<?php echo $return_id; ?></td>
                        </tr>
                        <tr>
                            <td><strong>Original Invoice:</strong></td>
                            <td><?php echo $return['invoice_number']; ?></td>
                        </tr>
                        <tr>
                            <td><strong>Return Date:</strong></td>
                            <td><?php echo date('d M Y, h:i A', strtotime($return['return_date'])); ?></td>
                        </tr>
                        <tr>
                            <td><strong>Processed By:</strong></td>
                            <td><?php echo $return['processed_by_name']; ?></td>
                        </tr>
                        <tr>
                            <td><strong>Status:</strong></td>
                            <td>
                                <span class="badge badge-<?php echo $return['status'] == 'completed' ? 'success' : 'warning'; ?>">
                                    <?php echo ucfirst($return['status']); ?>
                                </span>
                            </td>
                        </tr>
                    </table>
                </div>
            </div>
            <div class="col-md-6">
                <div style="background: #fee2e2; padding: 20px; border-radius: 8px;">
                    <h4>Refund Information</h4>
                    <table style="width: 100%;">
                        <tr>
                            <td><strong>Customer:</strong></td>
                            <td><?php echo $return['customer_name'] ?? 'Walk-in'; ?></td>
                        </tr>
                        <tr>
                            <td><strong>Original Sale Date:</strong></td>
                            <td><?php echo date('d M Y', strtotime($return['sale_date'])); ?></td>
                        </tr>
                        <tr>
                            <td><strong>Refund Amount:</strong></td>
                            <td><h3 style="color: #dc2626; margin: 10px 0;">Rs <?php echo number_format($return['return_amount'], 2); ?></h3></td>
                        </tr>
                    </table>
                </div>
            </div>
        </div>

        <!-- Return Reason -->
        <div style="background: #fef3c7; padding: 15px; border-radius: 8px; margin-bottom: 20px;">
            <strong>Return Reason:</strong>
            <p style="margin: 10px 0 0 0;"><?php echo nl2br(htmlspecialchars($return['return_reason'])); ?></p>
        </div>

        <?php if (!empty($return['notes'])): ?>
        <div style="background: #f3f4f6; padding: 15px; border-radius: 8px; margin-bottom: 20px;">
            <strong>Additional Notes:</strong>
            <p style="margin: 10px 0 0 0;"><?php echo nl2br(htmlspecialchars($return['notes'])); ?></p>
        </div>
        <?php endif; ?>

        <!-- Returned Items -->
        <h4>Returned Items</h4>
        <div class="table-responsive">
            <table>
                <thead>
                    <tr>
                        <th>Medicine</th>
                        <th>Quantity Returned</th>
                        <th>Unit Price</th>
                        <th>Refund Amount</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while ($item = mysqli_fetch_assoc($items_query)): ?>
                    <tr>
                        <td><strong><?php echo $item['medicine_name']; ?></strong></td>
                        <td><?php echo $item['quantity_returned']; ?></td>
                        <td>Rs <?php echo number_format($item['unit_price'], 2); ?></td>
                        <td><strong>Rs <?php echo number_format($item['subtotal'], 2); ?></strong></td>
                    </tr>
                    <?php endwhile; ?>
                </tbody>
                <tfoot>
                    <tr style="background: #fee2e2; font-weight: bold;">
                        <td colspan="3" class="text-right">Total Refund:</td>
                        <td><h4 style="color: #dc2626; margin: 0;">Rs <?php echo number_format($return['return_amount'], 2); ?></h4></td>
                    </tr>
                </tfoot>
            </table>
        </div>

        <div style="background: #d1fae5; border: 2px solid #10b981; padding: 15px; border-radius: 8px; margin-top: 20px;">
            <strong>✓ Stock Restored:</strong> All returned items have been added back to inventory.
        </div>
    </div>
</div>

<style>
.alert {
    padding: 15px;
    border-radius: 8px;
    margin-bottom: 20px;
}
.alert-success {
    background: #d1fae5;
    border: 1px solid #10b981;
    color: #065f46;
}
</style>

<?php include("includes/footer.php"); ?>
