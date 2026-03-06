<?php
include("../config/db.php");
$page_title = "View Sale";
include("includes/header.php");

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: ../login.php");
    exit();
}

$sale_id = isset($_GET['id']) ? intval($_GET['id']) : 0;

// Get sale details
$sale_query = mysqli_query($conn, "SELECT s.*, c.customer_name, c.phone, c.address, u.full_name as cashier 
    FROM sales s 
    LEFT JOIN customers c ON s.customer_id = c.id 
    LEFT JOIN users u ON s.user_id = u.id 
    WHERE s.id = $sale_id");

if (mysqli_num_rows($sale_query) == 0) {
    echo "<div class='alert alert-danger'>Sale not found!</div>";
    include("includes/footer.php");
    exit();
}

$sale = mysqli_fetch_assoc($sale_query);

// Get sale items
$items_query = mysqli_query($conn, "SELECT si.*, m.medicine_name, m.generic_name, c.company_name 
    FROM sale_items si 
    JOIN medicines m ON si.medicine_id = m.id 
    LEFT JOIN companies c ON m.company_id = c.id 
    WHERE si.sale_id = $sale_id");
?>

<div class="card">
    <div class="card-header" style="display: flex; justify-content: space-between; align-items: center;">
        <h3>Sale Details - <?php echo $sale['invoice_number']; ?></h3>
        <div>
            <a href="print_invoice.php?id=<?php echo $sale_id; ?>" class="btn btn-success" target="_blank">
                Print Invoice
            </a>
            <a href="sales.php" class="btn btn-secondary">Back to Sales</a>
        </div>
    </div>
    <div class="card-body">
        <div class="row">
            <div class="col-md-6">
                <h4>Sale Information</h4>
                <table class="table table-borderless">
                    <tr>
                        <td><strong>Invoice Number:</strong></td>
                        <td><?php echo $sale['invoice_number']; ?></td>
                    </tr>
                    <tr>
                        <td><strong>Date:</strong></td>
                        <td><?php echo date('d M Y, h:i A', strtotime($sale['sale_date'])); ?></td>
                    </tr>
                    <tr>
                        <td><strong>Cashier:</strong></td>
                        <td><?php echo $sale['cashier']; ?></td>
                    </tr>
                    <tr>
                        <td><strong>Payment Method:</strong></td>
                        <td><?php echo ucfirst($sale['payment_method']); ?></td>
                    </tr>
                    <tr>
                        <td><strong>Payment Status:</strong></td>
                        <td>
                            <span class="badge badge-<?php echo $sale['payment_status'] == 'paid' ? 'success' : 'warning'; ?>">
                                <?php echo ucfirst($sale['payment_status']); ?>
                            </span>
                        </td>
                    </tr>
                </table>
            </div>
            <div class="col-md-6">
                <h4>Customer Information</h4>
                <?php if ($sale['customer_name']): ?>
                <table class="table table-borderless">
                    <tr>
                        <td><strong>Name:</strong></td>
                        <td><?php echo $sale['customer_name']; ?></td>
                    </tr>
                    <tr>
                        <td><strong>Phone:</strong></td>
                        <td><?php echo $sale['phone']; ?></td>
                    </tr>
                    <tr>
                        <td><strong>Address:</strong></td>
                        <td><?php echo $sale['address'] ?? 'N/A'; ?></td>
                    </tr>
                </table>
                <?php else: ?>
                <p class="text-muted">Walk-in Customer</p>
                <?php endif; ?>
            </div>
        </div>

        <hr>

        <h4>Items</h4>
        <div class="table-responsive">
            <table class="table">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Medicine Name</th>
                        <th>Company</th>
                        <th>Batch</th>
                        <th>Unit Price</th>
                        <th>Quantity</th>
                        <th>Subtotal</th>
                    </tr>
                </thead>
                <tbody>
                    <?php 
                    $i = 1;
                    while ($item = mysqli_fetch_assoc($items_query)): 
                    ?>
                    <tr>
                        <td><?php echo $i++; ?></td>
                        <td>
                            <strong><?php echo $item['medicine_name']; ?></strong><br>
                            <small class="text-muted"><?php echo $item['generic_name']; ?></small>
                        </td>
                        <td><?php echo $item['company_name']; ?></td>
                        <td><?php echo $item['batch_number'] ?? 'N/A'; ?></td>
                        <td>Rs <?php echo number_format($item['unit_price'], 2); ?></td>
                        <td><?php echo $item['quantity']; ?></td>
                        <td>Rs <?php echo number_format($item['subtotal'], 2); ?></td>
                    </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        </div>

        <div class="row">
            <div class="col-md-8">
                <?php if ($sale['notes']): ?>
                <div>
                    <strong>Notes:</strong>
                    <p><?php echo nl2br(htmlspecialchars($sale['notes'])); ?></p>
                </div>
                <?php endif; ?>
            </div>
            <div class="col-md-4">
                <table class="table">
                    <tr>
                        <td><strong>Subtotal:</strong></td>
                        <td class="text-right">Rs <?php echo number_format($sale['subtotal'], 2); ?></td>
                    </tr>
                    <tr>
                        <td><strong>Tax (<?php echo $sale['tax_percentage']; ?>%):</strong></td>
                        <td class="text-right">Rs <?php echo number_format($sale['tax_amount'], 2); ?></td>
                    </tr>
                    <tr>
                        <td><strong>Discount (<?php echo $sale['discount_percentage']; ?>%):</strong></td>
                        <td class="text-right">-Rs <?php echo number_format($sale['discount_amount'], 2); ?></td>
                    </tr>
                    <tr style="border-top: 2px solid #333;">
                        <td><strong>Grand Total:</strong></td>
                        <td class="text-right"><h4 style="color: #2563eb;">Rs <?php echo number_format($sale['grand_total'], 2); ?></h4></td>
                    </tr>
                    <?php if ($sale['amount_paid'] > 0): ?>
                    <tr>
                        <td><strong>Amount Paid:</strong></td>
                        <td class="text-right">Rs <?php echo number_format($sale['amount_paid'], 2); ?></td>
                    </tr>
                    <tr style="background-color: #f0fdf4;">
                        <td><strong>Change Returned:</strong></td>
                        <td class="text-right"><strong style="color: #10b981;">Rs <?php echo number_format($sale['change_amount'], 2); ?></strong></td>
                    </tr>
                    <?php endif; ?>
                </table>
            </div>
        </div>
    </div>
</div>

<?php include("includes/footer.php"); ?>
