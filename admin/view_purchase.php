<?php
include("../config/db.php");
$page_title = "View Purchase";
include("includes/header.php");

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: ../login.php");
    exit();
}

$purchase_id = isset($_GET['id']) ? intval($_GET['id']) : 0;

// Get purchase details
$purchase_query = mysqli_query($conn, "SELECT p.*, s.supplier_name, s.phone, s.address, s.email, u.full_name as added_by 
    FROM purchases p 
    LEFT JOIN suppliers s ON p.supplier_id = s.id 
    LEFT JOIN users u ON p.user_id = u.id 
    WHERE p.id = $purchase_id");

if (mysqli_num_rows($purchase_query) == 0) {
    echo "<div class='alert alert-danger'>Purchase not found!</div>";
    include("includes/footer.php");
    exit();
}

$purchase = mysqli_fetch_assoc($purchase_query);

// Get purchase items
$items_query = mysqli_query($conn, "SELECT pi.*, m.medicine_name, m.generic_name, c.company_name 
    FROM purchase_items pi 
    JOIN medicines m ON pi.medicine_id = m.id 
    LEFT JOIN companies c ON m.company_id = c.id 
    WHERE pi.purchase_id = $purchase_id");
?>

<div class="card">
    <div class="card-header" style="display: flex; justify-content: space-between; align-items: center;">
        <h3>Purchase Details - <?php echo $purchase['purchase_number']; ?></h3>
        <div>
            <a href="purchases.php" class="btn btn-secondary">Back to Purchases</a>
        </div>
    </div>
    <div class="card-body">
        <div class="row">
            <div class="col-md-6">
                <h4>Purchase Information</h4>
                <table class="table table-borderless">
                    <tr>
                        <td><strong>Purchase Number:</strong></td>
                        <td><?php echo $purchase['purchase_number']; ?></td>
                    </tr>
                    <tr>
                        <td><strong>Purchase Date:</strong></td>
                        <td><?php echo date('d M Y', strtotime($purchase['purchase_date'])); ?></td>
                    </tr>
                    <tr>
                        <td><strong>Added By:</strong></td>
                        <td><?php echo $purchase['added_by']; ?></td>
                    </tr>
                    <tr>
                        <td><strong>Payment Status:</strong></td>
                        <td>
                            <span class="badge badge-<?php echo $purchase['payment_status'] == 'paid' ? 'success' : 'warning'; ?>">
                                <?php echo ucfirst($purchase['payment_status']); ?>
                            </span>
                        </td>
                    </tr>
                    <tr>
                        <td><strong>Created At:</strong></td>
                        <td><?php echo date('d M Y, h:i A', strtotime($purchase['created_at'])); ?></td>
                    </tr>
                </table>
            </div>
            <div class="col-md-6">
                <h4>Supplier Information</h4>
                <table class="table table-borderless">
                    <tr>
                        <td><strong>Name:</strong></td>
                        <td><?php echo $purchase['supplier_name']; ?></td>
                    </tr>
                    <tr>
                        <td><strong>Phone:</strong></td>
                        <td><?php echo $purchase['phone']; ?></td>
                    </tr>
                    <tr>
                        <td><strong>Email:</strong></td>
                        <td><?php echo $purchase['email'] ?? 'N/A'; ?></td>
                    </tr>
                    <tr>
                        <td><strong>Address:</strong></td>
                        <td><?php echo $purchase['address'] ?? 'N/A'; ?></td>
                    </tr>
                </table>
            </div>
        </div>

        <hr>

        <h4>Items Purchased</h4>
        <div class="table-responsive">
            <table class="table">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Medicine Name</th>
                        <th>Company</th>
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
                <?php if ($purchase['notes']): ?>
                <div>
                    <strong>Notes:</strong>
                    <p><?php echo nl2br(htmlspecialchars($purchase['notes'])); ?></p>
                </div>
                <?php endif; ?>
            </div>
            <div class="col-md-4">
                <table class="table">
                    <tr>
                        <td><strong>Total Amount:</strong></td>
                        <td class="text-right">Rs <?php echo number_format($purchase['total_amount'], 2); ?></td>
                    </tr>
                    <tr>
                        <td><strong>Tax Amount:</strong></td>
                        <td class="text-right">Rs <?php echo number_format($purchase['tax_amount'], 2); ?></td>
                    </tr>
                    <tr style="border-top: 2px solid #333;">
                        <td><strong>Grand Total:</strong></td>
                        <td class="text-right"><h4 style="color: #2563eb;">Rs <?php echo number_format($purchase['grand_total'], 2); ?></h4></td>
                    </tr>
                </table>
            </div>
        </div>
    </div>
</div>

<?php include("includes/footer.php"); ?>
