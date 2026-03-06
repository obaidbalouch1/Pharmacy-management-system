<?php
include("../config/db.php");
$page_title = "New Purchase";
include("includes/header.php");

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: ../login.php");
    exit();
}

// Get all suppliers
$suppliers = mysqli_query($conn, "SELECT * FROM suppliers WHERE status = 'active' ORDER BY supplier_name");

// Get all medicines
$medicines = mysqli_query($conn, "SELECT m.*, c.company_name 
    FROM medicines m 
    LEFT JOIN companies c ON m.company_id = c.id 
    ORDER BY m.medicine_name");
?>

<div class="card">
    <div class="card-header">
        <h3>Create New Purchase</h3>
    </div>
    <div class="card-body">
        <form id="purchaseForm" method="POST" action="process_purchase.php">
            <div class="row">
                <div class="col-md-6">
                    <div class="form-group">
                        <label>Supplier <span style="color: red;">*</span></label>
                        <select name="supplier_id" id="supplier_id" class="form-control" required>
                            <option value="">-- Select Supplier --</option>
                            <?php while ($supplier = mysqli_fetch_assoc($suppliers)): ?>
                                <option value="<?php echo $supplier['id']; ?>">
                                    <?php echo $supplier['supplier_name']; ?> - <?php echo $supplier['phone']; ?>
                                </option>
                            <?php endwhile; ?>
                        </select>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-group">
                        <label>Purchase Date <span style="color: red;">*</span></label>
                        <input type="date" name="purchase_date" class="form-control" 
                               value="<?php echo date('Y-m-d'); ?>" required>
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-md-6">
                    <div class="form-group">
                        <label>Payment Status</label>
                        <select name="payment_status" class="form-control" required>
                            <option value="paid">Paid</option>
                            <option value="pending">Pending</option>
                            <option value="partial">Partial</option>
                        </select>
                    </div>
                </div>
            </div>

            <hr>
            <h4>Add Items</h4>
            
            <div class="form-group">
                <label>Search Medicine</label>
                <select id="medicine_select" class="form-control">
                    <option value="">-- Select Medicine --</option>
                    <?php 
                    mysqli_data_seek($medicines, 0);
                    while ($medicine = mysqli_fetch_assoc($medicines)): 
                    ?>
                        <option value="<?php echo $medicine['id']; ?>" 
                                data-name="<?php echo htmlspecialchars($medicine['medicine_name']); ?>"
                                data-price="<?php echo $medicine['purchase_price']; ?>"
                                data-batch="<?php echo $medicine['batch_number']; ?>">
                            <?php echo $medicine['medicine_name']; ?> 
                            (<?php echo $medicine['company_name']; ?>) - 
                            Purchase Price: Rs <?php echo number_format($medicine['purchase_price'], 2); ?>
                        </option>
                    <?php endwhile; ?>
                </select>
            </div>

            <div class="table-responsive">
                <table class="table" id="purchaseItemsTable">
                    <thead>
                        <tr>
                            <th>Medicine</th>
                            <th>Batch Number</th>
                            <th>Unit Price</th>
                            <th>Quantity</th>
                            <th>Subtotal</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody id="purchaseItems">
                        <!-- Items will be added here dynamically -->
                    </tbody>
                </table>
            </div>

            <div class="row">
                <div class="col-md-8"></div>
                <div class="col-md-4">
                    <table class="table">
                        <tr>
                            <td><strong>Total Amount:</strong></td>
                            <td class="text-right">Rs <span id="total_amount">0.00</span></td>
                        </tr>
                        <tr>
                            <td>
                                <strong>Tax (%):</strong>
                                <input type="number" name="tax_percentage" id="tax_percentage" 
                                       class="form-control form-control-sm" value="0" min="0" max="100" step="0.01">
                            </td>
                            <td class="text-right">Rs <span id="tax_amount">0.00</span></td>
                        </tr>
                        <tr style="border-top: 2px solid #333;">
                            <td><strong>Grand Total:</strong></td>
                            <td class="text-right"><h4 style="color: #2563eb; margin: 0;">Rs <span id="grand_total">0.00</span></h4></td>
                        </tr>
                    </table>
                </div>
            </div>

            <div class="form-group">
                <label>Notes</label>
                <textarea name="notes" class="form-control" rows="2"></textarea>
            </div>

            <input type="hidden" name="total_amount" id="total_amount_input">
            <input type="hidden" name="tax_amount" id="tax_amount_input">
            <input type="hidden" name="grand_total" id="grand_total_input">
            <input type="hidden" name="items" id="items_input">

            <div class="form-group">
                <button type="submit" class="btn btn-success btn-lg">Complete Purchase</button>
                <a href="purchases.php" class="btn btn-secondary btn-lg">Cancel</a>
            </div>
        </form>
    </div>
</div>

<script>
let purchaseItems = [];

document.getElementById('medicine_select').addEventListener('change', function() {
    const select = this;
    const option = select.options[select.selectedIndex];
    
    if (!option.value) return;
    
    const medicineId = option.value;
    const medicineName = option.dataset.name;
    const price = parseFloat(option.dataset.price);
    const batch = option.dataset.batch;
    
    // Check if already added
    const existing = purchaseItems.find(item => item.id === medicineId);
    if (existing) {
        alert('Medicine already added to the list!');
        select.value = '';
        return;
    }
    
    // Add to items array
    purchaseItems.push({
        id: medicineId,
        name: medicineName,
        batch: batch || '',
        price: price,
        quantity: 1
    });
    
    renderItems();
    calculateTotals();
    select.value = '';
});

function renderItems() {
    const tbody = document.getElementById('purchaseItems');
    tbody.innerHTML = '';
    
    purchaseItems.forEach((item, index) => {
        const row = document.createElement('tr');
        row.innerHTML = `
            <td>${item.name}</td>
            <td>
                <input type="text" class="form-control form-control-sm" 
                       value="${item.batch}" 
                       onchange="updateBatch(${index}, this.value)" 
                       placeholder="Enter batch" style="width: 120px;">
            </td>
            <td>
                <input type="number" class="form-control form-control-sm" 
                       value="${item.price}" min="0" step="0.01"
                       onchange="updatePrice(${index}, this.value)" style="width: 100px;">
            </td>
            <td>
                <input type="number" class="form-control form-control-sm" 
                       value="${item.quantity}" min="1"
                       onchange="updateQuantity(${index}, this.value)" style="width: 80px;">
            </td>
            <td>Rs ${(item.price * item.quantity).toFixed(2)}</td>
            <td>
                <button type="button" class="btn btn-danger btn-sm" onclick="removeItem(${index})">Remove</button>
            </td>
        `;
        tbody.appendChild(row);
    });
}

function updateBatch(index, batch) {
    purchaseItems[index].batch = batch;
}

function updatePrice(index, price) {
    price = parseFloat(price);
    if (price < 0) price = 0;
    purchaseItems[index].price = price;
    renderItems();
    calculateTotals();
}

function updateQuantity(index, quantity) {
    quantity = parseInt(quantity);
    if (quantity < 1) quantity = 1;
    purchaseItems[index].quantity = quantity;
    renderItems();
    calculateTotals();
}

function removeItem(index) {
    purchaseItems.splice(index, 1);
    renderItems();
    calculateTotals();
}

function calculateTotals() {
    const totalAmount = purchaseItems.reduce((sum, item) => sum + (item.price * item.quantity), 0);
    const taxPercentage = parseFloat(document.getElementById('tax_percentage').value) || 0;
    
    const taxAmount = (totalAmount * taxPercentage) / 100;
    const grandTotal = totalAmount + taxAmount;
    
    document.getElementById('total_amount').textContent = totalAmount.toFixed(2);
    document.getElementById('tax_amount').textContent = taxAmount.toFixed(2);
    document.getElementById('grand_total').textContent = grandTotal.toFixed(2);
    
    document.getElementById('total_amount_input').value = totalAmount.toFixed(2);
    document.getElementById('tax_amount_input').value = taxAmount.toFixed(2);
    document.getElementById('grand_total_input').value = grandTotal.toFixed(2);
}

document.getElementById('tax_percentage').addEventListener('input', calculateTotals);

document.getElementById('purchaseForm').addEventListener('submit', function(e) {
    if (purchaseItems.length === 0) {
        e.preventDefault();
        alert('Please add at least one item to the purchase!');
        return false;
    }
    
    document.getElementById('items_input').value = JSON.stringify(purchaseItems);
});
</script>

<?php include("includes/footer.php"); ?>
