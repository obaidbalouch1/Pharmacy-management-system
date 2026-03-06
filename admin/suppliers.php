<?php
include("../config/db.php");
$page_title = "Suppliers Management";
include("includes/header.php");

$success = "";

// Handle add
if (isset($_POST['submit'])) {
    $supplier_name = mysqli_real_escape_string($conn, $_POST['supplier_name']);
    $contact_person = mysqli_real_escape_string($conn, $_POST['contact_person']);
    $phone = mysqli_real_escape_string($conn, $_POST['phone']);
    $email = mysqli_real_escape_string($conn, $_POST['email']);
    $address = mysqli_real_escape_string($conn, $_POST['address']);
    
    $query = "INSERT INTO suppliers (supplier_name, contact_person, phone, email, address) 
              VALUES ('$supplier_name', '$contact_person', '$phone', '$email', '$address')";
    
    if (mysqli_query($conn, $query)) {
        $success = "Supplier added successfully!";
    }
}

// Handle delete
if (isset($_GET['delete'])) {
    mysqli_query($conn, "DELETE FROM suppliers WHERE id = " . intval($_GET['delete']));
    $success = "Supplier deleted!";
}

$suppliers = mysqli_query($conn, "SELECT * FROM suppliers ORDER BY supplier_name");
?>

<?php if ($success): ?>
    <div class="alert alert-success">✅ <?php echo $success; ?></div>
<?php endif; ?>

<div class="card">
    <div class="card-header">
        <h3>Add New Supplier</h3>
    </div>
    <div class="card-body">
        <form method="POST">
            <div class="form-row">
                <div class="form-group">
                    <label>Supplier Name *</label>
                    <input type="text" name="supplier_name" class="form-control" required>
                </div>
                <div class="form-group">
                    <label>Contact Person</label>
                    <input type="text" name="contact_person" class="form-control">
                </div>
            </div>
            <div class="form-row">
                <div class="form-group">
                    <label>Phone</label>
                    <input type="text" name="phone" class="form-control">
                </div>
                <div class="form-group">
                    <label>Email</label>
                    <input type="email" name="email" class="form-control">
                </div>
            </div>
            <div class="form-group">
                <label>Address</label>
                <textarea name="address" class="form-control" rows="2"></textarea>
            </div>
            <button type="submit" name="submit" class="btn btn-primary">💾 Save Supplier</button>
        </form>
    </div>
</div>

<div class="card">
    <div class="card-header">
        <h3>All Suppliers</h3>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table>
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Supplier Name</th>
                        <th>Contact Person</th>
                        <th>Phone</th>
                        <th>Email</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while ($supplier = mysqli_fetch_assoc($suppliers)): ?>
                    <tr>
                        <td><?php echo $supplier['id']; ?></td>
                        <td><strong><?php echo $supplier['supplier_name']; ?></strong></td>
                        <td><?php echo $supplier['contact_person']; ?></td>
                        <td><?php echo $supplier['phone']; ?></td>
                        <td><?php echo $supplier['email']; ?></td>
                        <td>
                            <a href="?delete=<?php echo $supplier['id']; ?>" class="btn btn-danger btn-sm" onclick="return confirm('Delete?')">Delete</a>
                        </td>
                    </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<?php include("includes/footer.php"); ?>
