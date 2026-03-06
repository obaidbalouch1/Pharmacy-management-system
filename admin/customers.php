<?php
include("../config/db.php");
$page_title = "Customers Management";
include("includes/header.php");

$success = "";

if (isset($_POST['submit'])) {
    $customer_name = mysqli_real_escape_string($conn, $_POST['customer_name']);
    $phone = mysqli_real_escape_string($conn, $_POST['phone']);
    $email = mysqli_real_escape_string($conn, $_POST['email']);
    $address = mysqli_real_escape_string($conn, $_POST['address']);
    
    $query = "INSERT INTO customers (customer_name, phone, email, address) 
              VALUES ('$customer_name', '$phone', '$email', '$address')";
    
    if (mysqli_query($conn, $query)) {
        $success = "Customer added successfully!";
    }
}

if (isset($_GET['delete'])) {
    mysqli_query($conn, "DELETE FROM customers WHERE id = " . intval($_GET['delete']));
    $success = "Customer deleted!";
}

$customers = mysqli_query($conn, "SELECT * FROM customers ORDER BY customer_name");
?>

<?php if ($success): ?>
    <div class="alert alert-success">✅ <?php echo $success; ?></div>
<?php endif; ?>

<div class="card">
    <div class="card-header">
        <h3>Add New Customer</h3>
    </div>
    <div class="card-body">
        <form method="POST">
            <div class="form-row">
                <div class="form-group">
                    <label>Customer Name *</label>
                    <input type="text" name="customer_name" class="form-control" required>
                </div>
                <div class="form-group">
                    <label>Phone</label>
                    <input type="text" name="phone" class="form-control">
                </div>
            </div>
            <div class="form-row">
                <div class="form-group">
                    <label>Email</label>
                    <input type="email" name="email" class="form-control">
                </div>
                <div class="form-group">
                    <label>Address</label>
                    <input type="text" name="address" class="form-control">
                </div>
            </div>
            <button type="submit" name="submit" class="btn btn-primary">💾 Save Customer</button>
        </form>
    </div>
</div>

<div class="card">
    <div class="card-header">
        <h3>All Customers</h3>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table>
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Customer Name</th>
                        <th>Phone</th>
                        <th>Email</th>
                        <th>Address</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while ($customer = mysqli_fetch_assoc($customers)): ?>
                    <tr>
                        <td><?php echo $customer['id']; ?></td>
                        <td><strong><?php echo $customer['customer_name']; ?></strong></td>
                        <td><?php echo $customer['phone']; ?></td>
                        <td><?php echo $customer['email']; ?></td>
                        <td><?php echo $customer['address']; ?></td>
                        <td>
                            <a href="?delete=<?php echo $customer['id']; ?>" class="btn btn-danger btn-sm" onclick="return confirm('Delete?')">Delete</a>
                        </td>
                    </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<?php include("includes/footer.php"); ?>
