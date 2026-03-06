<?php
include("../config/db.php");
$page_title = "Expiry Alerts";
include("includes/header.php");

// Get medicines expiring in next 30 days
$expiring_soon = mysqli_query($conn, "SELECT m.*, c.company_name FROM medicines m 
    LEFT JOIN companies c ON m.company_id = c.id 
    WHERE m.expiry_date <= DATE_ADD(CURDATE(), INTERVAL 30 DAY) 
    AND m.expiry_date >= CURDATE() 
    ORDER BY m.expiry_date ASC");

// Get expired medicines
$expired = mysqli_query($conn, "SELECT m.*, c.company_name FROM medicines m 
    LEFT JOIN companies c ON m.company_id = c.id 
    WHERE m.expiry_date < CURDATE() 
    ORDER BY m.expiry_date DESC");
?>

<div class="alert alert-warning">
    ⚠️ Medicines expiring within 30 days require immediate attention!
</div>

<div class="card">
    <div class="card-header">
        <h3>Medicines Expiring Soon (Next 30 Days)</h3>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table>
                <thead>
                    <tr>
                        <th>Medicine Name</th>
                        <th>Generic Name</th>
                        <th>Company</th>
                        <th>Batch</th>
                        <th>Expiry Date</th>
                        <th>Days Left</th>
                        <th>Stock</th>
                        <th>Value</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while ($med = mysqli_fetch_assoc($expiring_soon)): 
                        $days_left = floor((strtotime($med['expiry_date']) - time()) / (60 * 60 * 24));
                    ?>
                    <tr>
                        <td><strong><?php echo $med['medicine_name']; ?></strong></td>
                        <td><?php echo $med['generic_name']; ?></td>
                        <td><?php echo $med['company_name']; ?></td>
                        <td><?php echo $med['batch_number']; ?></td>
                        <td><?php echo date('d M Y', strtotime($med['expiry_date'])); ?></td>
                        <td>
                            <span class="badge badge-warning"><?php echo $days_left; ?> days</span>
                        </td>
                        <td><?php echo $med['quantity']; ?></td>
                        <td>Rs <?php echo number_format($med['quantity'] * $med['purchase_price'], 2); ?></td>
                    </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<div class="card">
    <div class="card-header">
        <h3>Expired Medicines</h3>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table>
                <thead>
                    <tr>
                        <th>Medicine Name</th>
                        <th>Generic Name</th>
                        <th>Company</th>
                        <th>Batch</th>
                        <th>Expiry Date</th>
                        <th>Stock</th>
                        <th>Loss Value</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while ($med = mysqli_fetch_assoc($expired)): ?>
                    <tr>
                        <td><strong><?php echo $med['medicine_name']; ?></strong></td>
                        <td><?php echo $med['generic_name']; ?></td>
                        <td><?php echo $med['company_name']; ?></td>
                        <td><?php echo $med['batch_number']; ?></td>
                        <td><?php echo date('d M Y', strtotime($med['expiry_date'])); ?></td>
                        <td><span class="badge badge-danger"><?php echo $med['quantity']; ?></span></td>
                        <td>Rs <?php echo number_format($med['quantity'] * $med['purchase_price'], 2); ?></td>
                        <td>
                            <a href="medicines.php?delete=<?php echo $med['id']; ?>" class="btn btn-danger btn-sm" onclick="return confirm('Remove this expired medicine?')">Remove</a>
                        </td>
                    </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<?php include("includes/footer.php"); ?>
