<?php
include("../config/db.php");
$page_title = "Medicines Management";
include("includes/header.php");

// Handle delete
if (isset($_GET['delete'])) {
    $id = intval($_GET['delete']);
    mysqli_query($conn, "DELETE FROM medicines WHERE id = $id");
    echo "<script>alert('Medicine deleted successfully!'); window.location='medicines.php';</script>";
}

// Search and filter
$search = isset($_GET['search']) ? mysqli_real_escape_string($conn, $_GET['search']) : '';
$category_filter = isset($_GET['category']) ? intval($_GET['category']) : 0;
$status_filter = isset($_GET['status']) ? mysqli_real_escape_string($conn, $_GET['status']) : '';

// Build query
$query = "SELECT m.*, c.company_name, cat.category_name FROM medicines m 
    LEFT JOIN companies c ON m.company_id = c.id 
    LEFT JOIN categories cat ON m.category_id = cat.id WHERE 1=1";

if ($search) {
    $query .= " AND (m.medicine_name LIKE '%$search%' OR m.generic_name LIKE '%$search%' OR m.batch_number LIKE '%$search%')";
}
if ($category_filter > 0) {
    $query .= " AND m.category_id = $category_filter";
}
if ($status_filter) {
    $query .= " AND m.status = '$status_filter'";
}

$query .= " ORDER BY m.id DESC";
$medicines = mysqli_query($conn, $query);

// Get categories for filter
$categories = mysqli_query($conn, "SELECT * FROM categories ORDER BY category_name");
?>

<div class="card">
    <div class="card-header" style="display: flex; justify-content: space-between; align-items: center;">
        <h3>All Medicines</h3>
        <a href="add_medicine.php" class="btn btn-primary">+ Add New Medicine</a>
    </div>
    <div class="card-body">
        <!-- Search and Filter Form -->
        <form method="GET" style="margin-bottom: 20px;">
            <div class="row">
                <div class="col-md-4">
                    <div class="form-group">
                        <input type="text" name="search" class="form-control" placeholder="Search by name, generic, or batch..." value="<?php echo htmlspecialchars($search); ?>">
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="form-group">
                        <select name="category" class="form-control">
                            <option value="0">All Categories</option>
                            <?php while ($cat = mysqli_fetch_assoc($categories)): ?>
                                <option value="<?php echo $cat['id']; ?>" <?php echo $category_filter == $cat['id'] ? 'selected' : ''; ?>>
                                    <?php echo $cat['category_name']; ?>
                                </option>
                            <?php endwhile; ?>
                        </select>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="form-group">
                        <select name="status" class="form-control">
                            <option value="">All Status</option>
                            <option value="available" <?php echo $status_filter == 'available' ? 'selected' : ''; ?>>Available</option>
                            <option value="out_of_stock" <?php echo $status_filter == 'out_of_stock' ? 'selected' : ''; ?>>Out of Stock</option>
                            <option value="expired" <?php echo $status_filter == 'expired' ? 'selected' : ''; ?>>Expired</option>
                        </select>
                    </div>
                </div>
                <div class="col-md-2">
                    <button type="submit" class="btn btn-primary" style="width: 100%;">🔍 Search</button>
                </div>
            </div>
            <?php if ($search || $category_filter || $status_filter): ?>
            <div style="margin-top: 10px;">
                <a href="medicines.php" class="btn btn-secondary btn-sm">Clear Filters</a>
            </div>
            <?php endif; ?>
        </form>
        
        <div class="table-responsive">
            <table>
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Medicine Name</th>
                        <th>Generic Name</th>
                        <th>Category</th>
                        <th>Company</th>
                        <th>Batch</th>
                        <th>Expiry</th>
                        <th>Stock</th>
                        <th>Price</th>
                        <th>Status</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while ($med = mysqli_fetch_assoc($medicines)): ?>
                    <tr>
                        <td><?php echo $med['id']; ?></td>
                        <td><strong><?php echo $med['medicine_name']; ?></strong></td>
                        <td><?php echo $med['generic_name']; ?></td>
                        <td><?php echo $med['category_name']; ?></td>
                        <td><?php echo $med['company_name']; ?></td>
                        <td><?php echo $med['batch_number']; ?></td>
                        <td><?php echo date('d M Y', strtotime($med['expiry_date'])); ?></td>
                        <td>
                            <?php if ($med['quantity'] <= $med['reorder_level']): ?>
                                <span class="badge badge-danger"><?php echo $med['quantity']; ?></span>
                            <?php else: ?>
                                <?php echo $med['quantity']; ?>
                            <?php endif; ?>
                        </td>
                        <td>Rs <?php echo number_format($med['selling_price'], 2); ?></td>
                        <td>
                            <?php if ($med['status'] == 'available'): ?>
                                <span class="badge badge-success">Available</span>
                            <?php elseif ($med['status'] == 'expired'): ?>
                                <span class="badge badge-danger">Expired</span>
                            <?php else: ?>
                                <span class="badge badge-warning">Out of Stock</span>
                            <?php endif; ?>
                        </td>
                        <td>
                            <div class="action-buttons">
                                <a href="edit_medicine.php?id=<?php echo $med['id']; ?>" class="btn btn-info btn-sm">Edit</a>
                                <a href="?delete=<?php echo $med['id']; ?>" class="btn btn-danger btn-sm" onclick="return confirm('Are you sure?')">Delete</a>
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
