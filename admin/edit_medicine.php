<?php
include("../config/db.php");
$page_title = "Edit Medicine";
include("includes/header.php");

$success = "";
$error = "";

// Get medicine ID
if (!isset($_GET['id'])) {
    header("Location: medicines.php");
    exit();
}

$medicine_id = intval($_GET['id']);

// Handle form submission
if (isset($_POST['submit'])) {
    $medicine_name = mysqli_real_escape_string($conn, $_POST['medicine_name']);
    $generic_name = mysqli_real_escape_string($conn, $_POST['generic_name']);
    $category_id = !empty($_POST['category_id']) ? intval($_POST['category_id']) : 'NULL';
    $company_id = !empty($_POST['company_id']) ? intval($_POST['company_id']) : 'NULL';
    $batch_number = !empty($_POST['batch_number']) ? "'" . mysqli_real_escape_string($conn, $_POST['batch_number']) . "'" : 'NULL';
    $barcode = !empty($_POST['barcode']) ? "'" . mysqli_real_escape_string($conn, $_POST['barcode']) . "'" : 'NULL';
    $manufacturing_date = !empty($_POST['manufacturing_date']) ? "'" . $_POST['manufacturing_date'] . "'" : 'NULL';
    $expiry_date = !empty($_POST['expiry_date']) ? "'" . $_POST['expiry_date'] . "'" : 'NULL';
    $purchase_price = floatval($_POST['purchase_price']);
    $selling_price = floatval($_POST['selling_price']);
    $mrp = !empty($_POST['mrp']) ? floatval($_POST['mrp']) : 'NULL';
    $quantity = intval($_POST['quantity']);
    $reorder_level = intval($_POST['reorder_level']);
    $rack_location = !empty($_POST['rack_location']) ? "'" . mysqli_real_escape_string($conn, $_POST['rack_location']) . "'" : 'NULL';
    $description = !empty($_POST['description']) ? "'" . mysqli_real_escape_string($conn, $_POST['description']) . "'" : 'NULL';
    $status = mysqli_real_escape_string($conn, $_POST['status']);
    
    $query = "UPDATE medicines SET 
              medicine_name = '$medicine_name',
              generic_name = '$generic_name',
              category_id = $category_id,
              company_id = $company_id,
              batch_number = $batch_number,
              barcode = $barcode,
              manufacturing_date = $manufacturing_date,
              expiry_date = $expiry_date,
              purchase_price = $purchase_price,
              selling_price = $selling_price,
              mrp = $mrp,
              quantity = $quantity,
              reorder_level = $reorder_level,
              rack_location = $rack_location,
              description = $description,
              status = '$status'
              WHERE id = $medicine_id";
    
    if (mysqli_query($conn, $query)) {
        // Log activity
        include("includes/log_activity.php");
        $user_id = $_SESSION['user_id'];
        $details = "Updated medicine: $medicine_name (ID: $medicine_id), Quantity: $quantity";
        log_activity($conn, $user_id, 'Medicine Updated', 'medicines', $medicine_id, $details);
        
        $_SESSION['success'] = "Medicine updated successfully! New quantity: $quantity";
        
        // Redirect to prevent form resubmission
        header("Location: edit_medicine.php?id=$medicine_id");
        exit();
    } else {
        $error = "Error: " . mysqli_error($conn);
    }
} else {
    // Get medicine data
    $medicine_query = mysqli_query($conn, "SELECT * FROM medicines WHERE id = $medicine_id");
    
    if (mysqli_num_rows($medicine_query) == 0) {
        echo "<script>alert('Medicine not found!'); window.location='medicines.php';</script>";
        exit();
    }
    
    $medicine = mysqli_fetch_assoc($medicine_query);
}

// Get categories and companies
$categories = mysqli_query($conn, "SELECT * FROM categories ORDER BY category_name");
$companies = mysqli_query($conn, "SELECT * FROM companies WHERE status = 'active' ORDER BY company_name");
?>

<?php if (isset($_SESSION['success'])): ?>
    <div class="alert alert-success">✅ <?php echo $_SESSION['success']; unset($_SESSION['success']); ?></div>
<?php endif; ?>

<?php if ($success): ?>
    <div class="alert alert-success">✅ <?php echo $success; ?></div>
<?php endif; ?>

<?php if ($error): ?>
    <div class="alert alert-danger">❌ <?php echo $error; ?></div>
<?php endif; ?>

<div class="card">
    <div class="card-header">
        <h3>Edit Medicine - <?php echo htmlspecialchars($medicine['medicine_name']); ?></h3>
    </div>
    <div class="card-body">
        <form method="POST" action="">
            <div class="form-row">
                <div class="form-group">
                    <label>Medicine Name *</label>
                    <input type="text" name="medicine_name" class="form-control" value="<?php echo htmlspecialchars($medicine['medicine_name']); ?>" required>
                </div>
                <div class="form-group">
                    <label>Generic Name</label>
                    <input type="text" name="generic_name" class="form-control" value="<?php echo htmlspecialchars($medicine['generic_name']); ?>">
                </div>
            </div>
            
            <div class="form-row">
                <div class="form-group">
                    <label>Category</label>
                    <select name="category_id" class="form-control">
                        <option value="">Select Category</option>
                        <?php while ($cat = mysqli_fetch_assoc($categories)): ?>
                            <option value="<?php echo $cat['id']; ?>" <?php echo $cat['id'] == $medicine['category_id'] ? 'selected' : ''; ?>>
                                <?php echo $cat['category_name']; ?>
                            </option>
                        <?php endwhile; ?>
                    </select>
                </div>
                <div class="form-group">
                    <label>Company (Type to search)</label>
                    <div class="searchable-dropdown-company">
                        <input type="text" id="company_search" class="form-control" 
                               placeholder="Type to search companies..." 
                               autocomplete="off"
                               value="<?php 
                                   if ($medicine['company_id']) {
                                       $comp_result = mysqli_query($conn, "SELECT company_name FROM companies WHERE id = " . $medicine['company_id']);
                                       if ($comp_row = mysqli_fetch_assoc($comp_result)) {
                                           echo htmlspecialchars($comp_row['company_name']);
                                       }
                                   }
                               ?>">
                        <input type="hidden" name="company_id" id="company_id_hidden" value="<?php echo $medicine['company_id']; ?>">
                        <div id="company_dropdown" class="dropdown-list-company" style="display: none;">
                            <div class="dropdown-item-company" data-value="">-- Select Company --</div>
                            <?php 
                            mysqli_data_seek($companies, 0);
                            while ($comp = mysqli_fetch_assoc($companies)): 
                            ?>
                                <div class="dropdown-item-company" 
                                     data-value="<?php echo $comp['id']; ?>"
                                     data-name="<?php echo htmlspecialchars($comp['company_name']); ?>"
                                     data-searchtext="<?php echo strtolower($comp['company_name']); ?>">
                                    <?php echo htmlspecialchars($comp['company_name']); ?>
                                </div>
                            <?php endwhile; ?>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="form-row">
                <div class="form-group">
                    <label>Batch Number</label>
                    <input type="text" name="batch_number" class="form-control" value="<?php echo htmlspecialchars($medicine['batch_number']); ?>">
                </div>
                <div class="form-group">
                    <label>Barcode</label>
                    <input type="text" name="barcode" class="form-control" value="<?php echo htmlspecialchars($medicine['barcode']); ?>">
                </div>
            </div>
            
            <div class="form-row">
                <div class="form-group">
                    <label>Manufacturing Date</label>
                    <input type="date" name="manufacturing_date" class="form-control" value="<?php echo $medicine['manufacturing_date']; ?>">
                </div>
                <div class="form-group">
                    <label>Expiry Date</label>
                    <input type="date" name="expiry_date" class="form-control" value="<?php echo $medicine['expiry_date']; ?>">
                </div>
            </div>
            
            <div class="form-row">
                <div class="form-group">
                    <label>Purchase Price *</label>
                    <input type="number" step="0.01" name="purchase_price" class="form-control" value="<?php echo $medicine['purchase_price']; ?>" required>
                </div>
                <div class="form-group">
                    <label>Selling Price *</label>
                    <input type="number" step="0.01" name="selling_price" class="form-control" value="<?php echo $medicine['selling_price']; ?>" required>
                </div>
                <div class="form-group">
                    <label>MRP</label>
                    <input type="number" step="0.01" name="mrp" class="form-control" value="<?php echo $medicine['mrp']; ?>">
                </div>
            </div>
            
            <div class="form-row">
                <div class="form-group">
                    <label>Quantity * <span style="color: #10b981; font-weight: bold;">(Editable)</span></label>
                    <input type="number" name="quantity" class="form-control" value="<?php echo $medicine['quantity']; ?>" required style="border: 2px solid #10b981;">
                    <small class="text-muted">Current stock: <?php echo $medicine['quantity']; ?> units</small>
                </div>
                <div class="form-group">
                    <label>Reorder Level</label>
                    <input type="number" name="reorder_level" class="form-control" value="<?php echo $medicine['reorder_level']; ?>">
                    <small class="text-muted">Alert when stock falls below this level</small>
                </div>
                <div class="form-group">
                    <label>Rack Location</label>
                    <input type="text" name="rack_location" class="form-control" value="<?php echo htmlspecialchars($medicine['rack_location']); ?>">
                </div>
            </div>
            
            <div class="form-row">
                <div class="form-group">
                    <label>Status *</label>
                    <select name="status" class="form-control" required>
                        <option value="available" <?php echo $medicine['status'] == 'available' ? 'selected' : ''; ?>>Available</option>
                        <option value="out_of_stock" <?php echo $medicine['status'] == 'out_of_stock' ? 'selected' : ''; ?>>Out of Stock</option>
                        <option value="expired" <?php echo $medicine['status'] == 'expired' ? 'selected' : ''; ?>>Expired</option>
                    </select>
                </div>
            </div>
            
            <div class="form-group">
                <label>Description</label>
                <textarea name="description" class="form-control" rows="3"><?php echo htmlspecialchars($medicine['description']); ?></textarea>
            </div>
            
            <div style="display: flex; gap: 10px; margin-top: 20px;">
                <button type="submit" name="submit" class="btn btn-success">💾 Update Medicine</button>
                <a href="medicines.php" class="btn btn-secondary">← Back to List</a>
            </div>
        </form>
    </div>
</div>

<style>
.alert {
    padding: 15px;
    margin-bottom: 20px;
    border-radius: 8px;
    font-weight: 500;
}
.alert-success {
    background: #d1fae5;
    color: #065f46;
    border: 1px solid #10b981;
}
.alert-danger {
    background: #fee2e2;
    color: #991b1b;
    border: 1px solid #ef4444;
}
</style>

<script>
// Company searchable dropdown
const companySearch = document.getElementById('company_search');
const companyDropdown = document.getElementById('company_dropdown');
const companyHidden = document.getElementById('company_id_hidden');
const allCompanyItems = Array.from(document.querySelectorAll('.dropdown-item-company'));

// Show dropdown when clicking on search input
companySearch.addEventListener('focus', function() {
    companyDropdown.style.display = 'block';
    filterCompanyItems('');
});

// Filter company items
companySearch.addEventListener('input', function() {
    const searchTerm = this.value.toLowerCase().trim();
    filterCompanyItems(searchTerm);
});

function filterCompanyItems(searchTerm) {
    let visibleCount = 0;
    
    allCompanyItems.forEach(item => {
        if (!item.dataset.value) {
            // Always show placeholder
            item.style.display = 'block';
            visibleCount++;
        } else {
            const searchText = item.dataset.searchtext || '';
            if (searchTerm === '' || searchText.includes(searchTerm)) {
                item.style.display = 'block';
                visibleCount++;
            } else {
                item.style.display = 'none';
            }
        }
    });
    
    // Show "no results" if nothing found
    const existingNoResults = companyDropdown.querySelector('.no-results-company');
    if (existingNoResults) existingNoResults.remove();
    
    if (visibleCount === 1) { // Only placeholder visible
        const noResults = document.createElement('div');
        noResults.className = 'dropdown-item-company no-results-company';
        noResults.textContent = 'No companies found';
        noResults.style.color = '#9ca3af';
        noResults.style.textAlign = 'center';
        noResults.style.cursor = 'default';
        companyDropdown.appendChild(noResults);
    }
}

// Handle company item click
companyDropdown.addEventListener('click', function(e) {
    const item = e.target.closest('.dropdown-item-company');
    if (!item || item.classList.contains('no-results-company')) {
        return;
    }
    
    if (!item.dataset.value) {
        // Clicked on placeholder
        companySearch.value = '';
        companyHidden.value = '';
        companyDropdown.style.display = 'none';
        return;
    }
    
    // Set the selected company
    companySearch.value = item.dataset.name;
    companyHidden.value = item.dataset.value;
    companyDropdown.style.display = 'none';
});

// Close dropdown when clicking outside
document.addEventListener('click', function(e) {
    if (!e.target.closest('.searchable-dropdown-company')) {
        companyDropdown.style.display = 'none';
    }
});
</script>

<style>
.searchable-dropdown-company {
    position: relative;
}

.dropdown-list-company {
    position: absolute;
    top: 100%;
    left: 0;
    right: 0;
    z-index: 1000;
    background: white;
    border: 1px solid #d1d5db;
    border-radius: 8px;
    box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1), 0 4px 6px -2px rgba(0, 0, 0, 0.05);
    max-height: 300px;
    overflow-y: auto;
    margin-top: 4px;
}

.dropdown-item-company {
    padding: 12px 16px;
    cursor: pointer;
    border-bottom: 1px solid #f3f4f6;
    transition: background-color 0.2s;
    font-size: 14px;
    color: #111827;
}

.dropdown-item-company:hover {
    background-color: #f0f9ff;
}

.dropdown-item-company:last-child {
    border-bottom: none;
}

.dropdown-item-company[data-value=""] {
    font-weight: 600;
    color: #6b7280;
    background-color: #f9fafb;
}

.dropdown-item-company[data-value=""]:hover {
    background-color: #f3f4f6;
}

#company_search:focus {
    border-color: #3b82f6;
    box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.1);
    outline: none;
}

/* Scrollbar styling */
.dropdown-list-company::-webkit-scrollbar {
    width: 8px;
}

.dropdown-list-company::-webkit-scrollbar-track {
    background: #f1f1f1;
    border-radius: 4px;
}

.dropdown-list-company::-webkit-scrollbar-thumb {
    background: #cbd5e1;
    border-radius: 4px;
}

.dropdown-list-company::-webkit-scrollbar-thumb:hover {
    background: #94a3b8;
}
</style>

<?php include("includes/footer.php"); ?>
