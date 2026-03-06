<?php
include("../config/db.php");
$page_title = "Companies Management";
include("includes/header.php");

$success = "";
$error = "";

// Handle add/edit
if (isset($_POST['submit'])) {
    $company_name = mysqli_real_escape_string($conn, $_POST['company_name']);
    $contact_person = mysqli_real_escape_string($conn, $_POST['contact_person']);
    $phone = mysqli_real_escape_string($conn, $_POST['phone']);
    $email = mysqli_real_escape_string($conn, $_POST['email']);
    $address = mysqli_real_escape_string($conn, $_POST['address']);
    $city = mysqli_real_escape_string($conn, $_POST['city']);
    
    if (isset($_POST['id']) && $_POST['id']) {
        $id = intval($_POST['id']);
        $query = "UPDATE companies SET company_name='$company_name', contact_person='$contact_person', 
                  phone='$phone', email='$email', address='$address', city='$city' WHERE id=$id";
    } else {
        $query = "INSERT INTO companies (company_name, contact_person, phone, email, address, city) 
                  VALUES ('$company_name', '$contact_person', '$phone', '$email', '$address', '$city')";
    }
    
    if (mysqli_query($conn, $query)) {
        $success = "Company saved successfully!";
    } else {
        $error = "Error: " . mysqli_error($conn);
    }
}

// Handle delete
if (isset($_GET['delete'])) {
    $id = intval($_GET['delete']);
    mysqli_query($conn, "DELETE FROM companies WHERE id = $id");
    $success = "Company deleted successfully!";
}

// Get all companies
$companies = mysqli_query($conn, "SELECT * FROM companies ORDER BY company_name");
?>

<?php if ($success): ?>
    <div class="alert alert-success">✅ <?php echo $success; ?></div>
<?php endif; ?>

<div class="card">
    <div class="card-header">
        <h3>Add New Company</h3>
    </div>
    <div class="card-body">
        <form method="POST" action="">
            <div class="form-row">
                <div class="form-group">
                    <label>Company Name *</label>
                    <input type="text" name="company_name" class="form-control" required>
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
                <div class="form-group">
                    <label>City</label>
                    <input type="text" name="city" class="form-control">
                </div>
            </div>
            <div class="form-group">
                <label>Address</label>
                <textarea name="address" class="form-control" rows="2"></textarea>
            </div>
            <button type="submit" name="submit" class="btn btn-primary">💾 Save Company</button>
        </form>
    </div>
</div>

<div class="card">
    <div class="card-header">
        <h3>All Companies</h3>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table>
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Company Name</th>
                        <th>Contact Person</th>
                        <th>Phone</th>
                        <th>Email</th>
                        <th>City</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while ($company = mysqli_fetch_assoc($companies)): ?>
                    <tr>
                        <td><?php echo $company['id']; ?></td>
                        <td><strong><?php echo $company['company_name']; ?></strong></td>
                        <td><?php echo $company['contact_person']; ?></td>
                        <td><?php echo $company['phone']; ?></td>
                        <td><?php echo $company['email']; ?></td>
                        <td><?php echo $company['city']; ?></td>
                        <td>
                            <a href="?delete=<?php echo $company['id']; ?>" class="btn btn-danger btn-sm" onclick="return confirm('Delete this company?')">Delete</a>
                        </td>
                    </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<?php include("includes/footer.php"); ?>
