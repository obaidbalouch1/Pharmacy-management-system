<?php
include("../config/db.php");
$page_title = "New Sale";
include("includes/header.php");

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: ../login.php");
    exit();
}

// Get all customers
$customers = mysqli_query($conn, "SELECT * FROM customers WHERE 1 ORDER BY customer_name");

// Get all available medicines
$medicines = mysqli_query($conn, "SELECT m.*, c.company_name 
    FROM medicines m 
    LEFT JOIN companies c ON m.company_id = c.id 
    WHERE m.status = 'available' AND m.quantity > 0 
    ORDER BY m.medicine_name");
?>

<div class="card">
    <div class="card-header">
        <h3>Create New Sale</h3>
    </div>
    <div class="card-body">
        <form id="saleForm" method="POST" action="process_sale.php">
            <div class="row">
                <div class="col-md-4">
                    <div class="form-group">
                        <label>Select Customer (Optional)</label>
                        <select name="customer_id" id="customer_id" class="form-control">
                            <option value="">-- Select Existing Customer --</option>
                            <?php while ($customer = mysqli_fetch_assoc($customers)): ?>
                                <option value="<?php echo $customer['id']; ?>"
                                        data-name="<?php echo htmlspecialchars($customer['customer_name']); ?>"
                                        data-phone="<?php echo htmlspecialchars($customer['phone']); ?>">
                                    <?php echo $customer['customer_name']; ?> - <?php echo $customer['phone']; ?>
                                </option>
                            <?php endwhile; ?>
                        </select>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="form-group">
                        <label>Or Enter Customer Name</label>
                        <input type="text" name="customer_name" id="customer_name" class="form-control" 
                               placeholder="Enter customer name for receipt...">
                        <small class="text-muted">Leave blank for "Walk-in Customer"</small>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="form-group">
                        <label>Payment Method</label>
                        <select name="payment_method" class="form-control" required>
                            <option value="cash">Cash</option>
                            <option value="card">Card</option>
                          
                            <option value="other">Other</option>
                        </select>
                    </div>
                </div>
            </div>

            <hr>
            <h4>Add Items</h4>
            
            <div class="row">
                <div class="col-md-8">
                    <div class="form-group">
                        <label>🔍 Search Medicine (Type to search or scan barcode)</label>
                        <input type="text" id="medicine_search" class="form-control" 
                               placeholder="Type medicine name, company, batch, or scan barcode..." 
                               autocomplete="off">
                        <div id="search_results" class="search-results"></div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="form-group">
                        <label>Or Select from Dropdown (Type to filter)</label>
                        <div class="searchable-dropdown">
                            <input type="text" id="dropdown_search" class="form-control dropdown-search-input" 
                                   placeholder="Type to filter medicines..." 
                                   autocomplete="off">
                            <div id="dropdown_list" class="dropdown-list" style="display: none;">
                                <div class="dropdown-item" data-value="">-- Select Medicine --</div>
                                <?php 
                                mysqli_data_seek($medicines, 0);
                                while ($medicine = mysqli_fetch_assoc($medicines)): 
                                ?>
                                    <div class="dropdown-item" 
                                         data-value="<?php echo $medicine['id']; ?>"
                                         data-name="<?php echo htmlspecialchars($medicine['medicine_name']); ?>"
                                         data-price="<?php echo $medicine['selling_price']; ?>"
                                         data-stock="<?php echo $medicine['quantity']; ?>"
                                         data-batch="<?php echo $medicine['batch_number']; ?>"
                                         data-barcode="<?php echo htmlspecialchars($medicine['barcode']); ?>"
                                         data-company="<?php echo htmlspecialchars($medicine['company_name']); ?>"
                                         data-searchtext="<?php echo strtolower($medicine['medicine_name'] . ' ' . $medicine['company_name'] . ' ' . $medicine['batch_number']); ?>">
                                        <div class="dropdown-item-name"><?php echo $medicine['medicine_name']; ?></div>
                                        <div class="dropdown-item-details">
                                            <?php echo $medicine['company_name']; ?> | Stock: <?php echo $medicine['quantity']; ?> | Rs <?php echo number_format($medicine['selling_price'], 2); ?>
                                        </div>
                                    </div>
                                <?php endwhile; ?>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="table-responsive">
                <table class="table" id="saleItemsTable">
                    <thead>
                        <tr>
                            <th>Medicine</th>
                            <th>Batch</th>
                            <th>Price</th>
                            <th>Quantity</th>
                            <th>Subtotal</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody id="saleItems">
                        <!-- Items will be added here dynamically -->
                    </tbody>
                </table>
            </div>

            <div class="row">
                <div class="col-md-8"></div>
                <div class="col-md-4">
                    <table class="table">
                        <tr>
                            <td><strong>Subtotal:</strong></td>
                            <td class="text-right">Rs <span id="subtotal">0.00</span></td>
                        </tr>
                        <tr>
                            <td>
                                <strong>Tax (%):</strong>
                                <input type="number" name="tax_percentage" id="tax_percentage" 
                                       class="form-control form-control-sm" value="0" min="0" max="100" step="0.01">
                            </td>
                            <td class="text-right">Rs <span id="tax_amount">0.00</span></td>
                        </tr>
                        <tr>
                            <td>
                                <strong>Discount (%):</strong>
                                <input type="number" name="discount_percentage" id="discount_percentage" 
                                       class="form-control form-control-sm" value="0" min="0" max="100" step="0.01">
                            </td>
                            <td class="text-right">Rs <span id="discount_amount">0.00</span></td>
                        </tr>
                        <tr style="border-top: 2px solid #333;">
                            <td><strong>Grand Total:</strong></td>
                            <td class="text-right"><h4 style="color: #2563eb; margin: 0;">Rs <span id="grand_total">0.00</span></h4></td>
                        </tr>
                        <tr>
                            <td>
                                <strong>Amount Paid:</strong>
                                <input type="number" name="amount_paid" id="amount_paid" 
                                       class="form-control form-control-sm" value="0" min="0" step="0.01" 
                                       placeholder="Enter amount">
                            </td>
                            <td class="text-right">Rs <span id="amount_paid_display">0.00</span></td>
                        </tr>
                        <tr style="background-color: #f0fdf4;">
                            <td><strong>Change to Return:</strong></td>
                            <td class="text-right"><h4 style="color: #10b981; margin: 0;">Rs <span id="change_amount">0.00</span></h4></td>
                        </tr>
                    </table>
                </div>
            </div>

            <div class="form-group">
                <label>Notes</label>
                <textarea name="notes" class="form-control" rows="2"></textarea>
            </div>

            <input type="hidden" name="subtotal" id="subtotal_input">
            <input type="hidden" name="tax_amount" id="tax_amount_input">
            <input type="hidden" name="discount_amount" id="discount_amount_input">
            <input type="hidden" name="grand_total" id="grand_total_input">
            <input type="hidden" name="change_amount" id="change_amount_input">
            <input type="hidden" name="items" id="items_input">

            <div class="form-group">
                <button type="submit" class="btn btn-success btn-lg">Complete Sale</button>
                <a href="sales.php" class="btn btn-secondary btn-lg">Cancel</a>
            </div>
        </form>
    </div>
</div>

<script>
let saleItems = [];
let medicinesData = [];

// Build medicines data array from select options
document.querySelectorAll('#medicine_select option').forEach(option => {
    if (option.value) {
        medicinesData.push({
            id: option.value,
            name: option.dataset.name,
            company: option.dataset.company,
            price: parseFloat(option.dataset.price),
            stock: parseInt(option.dataset.stock),
            batch: option.dataset.batch,
            barcode: option.dataset.barcode || '' // Add barcode support
        });
    }
});

// Medicine search functionality
const searchInput = document.getElementById('medicine_search');
const searchResults = document.getElementById('search_results');

searchInput.addEventListener('input', function() {
    const query = this.value.toLowerCase().trim();
    
    if (query.length < 2) {
        searchResults.innerHTML = '';
        searchResults.style.display = 'none';
        return;
    }
    
    // Filter medicines by name, company, batch, or BARCODE
    const filtered = medicinesData.filter(med => 
        med.name.toLowerCase().includes(query) ||
        (med.company && med.company.toLowerCase().includes(query)) ||
        (med.batch && med.batch.toLowerCase().includes(query)) ||
        (med.barcode && med.barcode.toLowerCase() === query) // Exact barcode match
    ).slice(0, 10); // Limit to 10 results
    
    // If exact barcode match found, auto-add it
    if (filtered.length === 1 && filtered[0].barcode && filtered[0].barcode.toLowerCase() === query) {
        addMedicineToCart(filtered[0]);
        searchInput.value = '';
        searchResults.innerHTML = '';
        searchResults.style.display = 'none';
        return;
    }
    
    if (filtered.length === 0) {
        searchResults.innerHTML = '<div class="search-item no-results">No medicines found</div>';
        searchResults.style.display = 'block';
        return;
    }
    
    // Display results
    searchResults.innerHTML = filtered.map(med => `
        <div class="search-item" onclick="addMedicineFromSearch('${med.id}')">
            <div class="search-item-name">${med.name}</div>
            <div class="search-item-details">
                ${med.company} | Stock: ${med.stock} | Rs ${med.price.toFixed(2)}
                ${med.barcode ? ' | Barcode: ' + med.barcode : ''}
            </div>
        </div>
    `).join('');
    searchResults.style.display = 'block';
});

// Handle Enter key for barcode scanners
searchInput.addEventListener('keypress', function(e) {
    if (e.key === 'Enter') {
        e.preventDefault();
        const query = this.value.toLowerCase().trim();
        
        // Try to find exact barcode match
        const exactMatch = medicinesData.find(med => 
            med.barcode && med.barcode.toLowerCase() === query
        );
        
        if (exactMatch) {
            addMedicineToCart(exactMatch);
            searchInput.value = '';
            searchResults.innerHTML = '';
            searchResults.style.display = 'none';
        } else {
            // If no exact match, try first search result
            const filtered = medicinesData.filter(med => 
                med.name.toLowerCase().includes(query) ||
                (med.company && med.company.toLowerCase().includes(query)) ||
                (med.batch && med.batch.toLowerCase().includes(query))
            );
            
            if (filtered.length > 0) {
                addMedicineToCart(filtered[0]);
                searchInput.value = '';
                searchResults.innerHTML = '';
                searchResults.style.display = 'none';
            }
        }
    }
});

// Close search results when clicking outside
document.addEventListener('click', function(e) {
    if (!searchInput.contains(e.target) && !searchResults.contains(e.target)) {
        searchResults.style.display = 'none';
    }
});

// Add medicine from search
function addMedicineFromSearch(medicineId) {
    const medicine = medicinesData.find(m => m.id === medicineId);
    if (!medicine) return;
    
    addMedicineToCart(medicine);
    searchInput.value = '';
    searchResults.innerHTML = '';
    searchResults.style.display = 'none';
}

// Add medicine to cart
function addMedicineToCart(medicine) {
    // Check if already added
    const existing = saleItems.find(item => item.id === medicine.id);
    if (existing) {
        alert('Medicine already added to the list!');
        return;
    }
    
    // Add to items array
    saleItems.push({
        id: medicine.id,
        name: medicine.name,
        batch: medicine.batch,
        price: medicine.price,
        quantity: 1,
        stock: medicine.stock
    });
    
    renderItems();
    calculateTotals();
}

// Dropdown search filter - Custom dropdown
const dropdownSearch = document.getElementById('dropdown_search');
const dropdownList = document.getElementById('dropdown_list');
const allDropdownItems = Array.from(document.querySelectorAll('.dropdown-item'));

// Customer dropdown auto-fill
document.getElementById('customer_id').addEventListener('change', function() {
    const selectedOption = this.options[this.selectedIndex];
    const customerNameInput = document.getElementById('customer_name');
    
    if (selectedOption.value) {
        // If a customer is selected, fill the name field
        customerNameInput.value = selectedOption.dataset.name || '';
        customerNameInput.readOnly = true;
        customerNameInput.style.backgroundColor = '#f3f4f6';
    } else {
        // If no customer selected, allow manual entry
        customerNameInput.value = '';
        customerNameInput.readOnly = false;
        customerNameInput.style.backgroundColor = '';
    }
});

// Show dropdown when clicking on search input
dropdownSearch.addEventListener('focus', function() {
    dropdownList.style.display = 'block';
    filterDropdownItems('');
});

// Filter dropdown items
dropdownSearch.addEventListener('input', function() {
    const searchTerm = this.value.toLowerCase().trim();
    filterDropdownItems(searchTerm);
});

function filterDropdownItems(searchTerm) {
    let visibleCount = 0;
    
    allDropdownItems.forEach(item => {
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
    if (visibleCount === 1) { // Only placeholder visible
        const noResults = document.createElement('div');
        noResults.className = 'dropdown-item no-results';
        noResults.textContent = 'No medicines found';
        noResults.style.color = '#9ca3af';
        noResults.style.textAlign = 'center';
        dropdownList.appendChild(noResults);
    } else {
        // Remove "no results" message if it exists
        const noResults = dropdownList.querySelector('.no-results');
        if (noResults) noResults.remove();
    }
}

// Handle dropdown item click
dropdownList.addEventListener('click', function(e) {
    const item = e.target.closest('.dropdown-item');
    if (!item || !item.dataset.value) {
        dropdownList.style.display = 'none';
        return;
    }
    
    const medicine = {
        id: item.dataset.value,
        name: item.dataset.name,
        company: item.dataset.company,
        price: parseFloat(item.dataset.price),
        stock: parseInt(item.dataset.stock),
        batch: item.dataset.batch
    };
    
    addMedicineToCart(medicine);
    
    // Reset dropdown
    dropdownSearch.value = '';
    dropdownList.style.display = 'none';
    filterDropdownItems('');
});

// Close dropdown when clicking outside
document.addEventListener('click', function(e) {
    if (!e.target.closest('.searchable-dropdown')) {
        dropdownList.style.display = 'none';
    }
});

function renderItems() {
    const tbody = document.getElementById('saleItems');
    tbody.innerHTML = '';
    
    saleItems.forEach((item, index) => {
        const row = document.createElement('tr');
        row.innerHTML = `
            <td>${item.name}</td>
            <td>${item.batch || 'N/A'}</td>
            <td>Rs ${item.price.toFixed(2)}</td>
            <td>
                <input type="number" class="form-control form-control-sm" 
                       value="${item.quantity}" min="1" max="${item.stock}" 
                       onchange="updateQuantity(${index}, this.value)" style="width: 80px;">
                <small class="text-muted">Max: ${item.stock}</small>
            </td>
            <td>Rs ${(item.price * item.quantity).toFixed(2)}</td>
            <td>
                <button type="button" class="btn btn-danger btn-sm" onclick="removeItem(${index})">Remove</button>
            </td>
        `;
        tbody.appendChild(row);
    });
}

function updateQuantity(index, quantity) {
    quantity = parseInt(quantity);
    if (quantity < 1) quantity = 1;
    if (quantity > saleItems[index].stock) {
        alert('Quantity exceeds available stock!');
        quantity = saleItems[index].stock;
    }
    saleItems[index].quantity = quantity;
    renderItems();
    calculateTotals();
}

function removeItem(index) {
    saleItems.splice(index, 1);
    renderItems();
    calculateTotals();
}

function calculateTotals() {
    const subtotal = saleItems.reduce((sum, item) => sum + (item.price * item.quantity), 0);
    const taxPercentage = parseFloat(document.getElementById('tax_percentage').value) || 0;
    const discountPercentage = parseFloat(document.getElementById('discount_percentage').value) || 0;
    
    const taxAmount = (subtotal * taxPercentage) / 100;
    const discountAmount = (subtotal * discountPercentage) / 100;
    const grandTotal = subtotal + taxAmount - discountAmount;
    
    document.getElementById('subtotal').textContent = subtotal.toFixed(2);
    document.getElementById('tax_amount').textContent = taxAmount.toFixed(2);
    document.getElementById('discount_amount').textContent = discountAmount.toFixed(2);
    document.getElementById('grand_total').textContent = grandTotal.toFixed(2);
    
    document.getElementById('subtotal_input').value = subtotal.toFixed(2);
    document.getElementById('tax_amount_input').value = taxAmount.toFixed(2);
    document.getElementById('discount_amount_input').value = discountAmount.toFixed(2);
    document.getElementById('grand_total_input').value = grandTotal.toFixed(2);
    
    // Calculate change
    calculateChange();
}

function calculateChange() {
    const grandTotal = parseFloat(document.getElementById('grand_total_input').value) || 0;
    const amountPaid = parseFloat(document.getElementById('amount_paid').value) || 0;
    const change = amountPaid - grandTotal;
    
    document.getElementById('amount_paid_display').textContent = amountPaid.toFixed(2);
    document.getElementById('change_amount').textContent = change >= 0 ? change.toFixed(2) : '0.00';
    document.getElementById('change_amount_input').value = change >= 0 ? change.toFixed(2) : '0.00';
    
    // Highlight if insufficient payment
    const changeRow = document.getElementById('change_amount').parentElement.parentElement;
    if (amountPaid > 0 && amountPaid < grandTotal) {
        changeRow.style.backgroundColor = '#fee2e2';
        document.getElementById('change_amount').style.color = '#dc2626';
    } else if (change > 0) {
        changeRow.style.backgroundColor = '#f0fdf4';
        document.getElementById('change_amount').style.color = '#10b981';
    } else {
        changeRow.style.backgroundColor = '#f9fafb';
        document.getElementById('change_amount').style.color = '#6b7280';
    }
}

document.getElementById('tax_percentage').addEventListener('input', calculateTotals);
document.getElementById('discount_percentage').addEventListener('input', calculateTotals);
document.getElementById('amount_paid').addEventListener('input', calculateChange);

document.getElementById('saleForm').addEventListener('submit', function(e) {
    if (saleItems.length === 0) {
        e.preventDefault();
        alert('Please add at least one item to the sale!');
        return false;
    }
    
    const grandTotal = parseFloat(document.getElementById('grand_total_input').value) || 0;
    const amountPaid = parseFloat(document.getElementById('amount_paid').value) || 0;
    
    if (amountPaid > 0 && amountPaid < grandTotal) {
        e.preventDefault();
        alert('Amount paid is less than the grand total! Please enter sufficient amount.');
        return false;
    }
    
    document.getElementById('items_input').value = JSON.stringify(saleItems);
});
</script>

<style>
.search-results {
    position: absolute;
    z-index: 1000;
    background: white;
    border: 1px solid #d1d5db;
    border-radius: 8px;
    box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06);
    max-height: 400px;
    overflow-y: auto;
    width: calc(100% - 30px);
    margin-top: 5px;
    display: none;
}

.search-item {
    padding: 12px 16px;
    cursor: pointer;
    border-bottom: 1px solid #f3f4f6;
    transition: background-color 0.2s;
}

.search-item:hover {
    background-color: #f9fafb;
}

.search-item:last-child {
    border-bottom: none;
}

.search-item-name {
    font-weight: 600;
    color: #111827;
    margin-bottom: 4px;
}

.search-item-details {
    font-size: 13px;
    color: #6b7280;
}

.search-item.no-results {
    text-align: center;
    color: #9ca3af;
    cursor: default;
}

.search-item.no-results:hover {
    background-color: white;
}

/* Highlight search input when focused */
#medicine_search:focus {
    border-color: #3b82f6;
    box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.1);
    outline: none;
}

/* Make the form group relative for absolute positioning */
.form-group {
    position: relative;
}

/* Searchable Dropdown Styles */
.searchable-dropdown {
    position: relative;
}

.dropdown-search-input {
    cursor: pointer;
}

.dropdown-search-input:focus {
    border-color: #3b82f6;
    box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.1);
    outline: none;
}

.dropdown-list {
    position: absolute;
    top: 100%;
    left: 0;
    right: 0;
    z-index: 1000;
    background: white;
    border: 1px solid #d1d5db;
    border-radius: 8px;
    box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1), 0 4px 6px -2px rgba(0, 0, 0, 0.05);
    max-height: 350px;
    overflow-y: auto;
    margin-top: 4px;
}

.dropdown-item {
    padding: 12px 16px;
    cursor: pointer;
    border-bottom: 1px solid #f3f4f6;
    transition: background-color 0.2s;
}

.dropdown-item:hover {
    background-color: #f0f9ff;
}

.dropdown-item:last-child {
    border-bottom: none;
}

.dropdown-item[data-value=""] {
    font-weight: 600;
    color: #6b7280;
    background-color: #f9fafb;
}

.dropdown-item[data-value=""]:hover {
    background-color: #f3f4f6;
}

.dropdown-item-name {
    font-weight: 600;
    color: #111827;
    margin-bottom: 4px;
    font-size: 14px;
}

.dropdown-item-details {
    font-size: 12px;
    color: #6b7280;
}

/* Scrollbar styling for dropdown */
.dropdown-list::-webkit-scrollbar {
    width: 8px;
}

.dropdown-list::-webkit-scrollbar-track {
    background: #f1f1f1;
    border-radius: 4px;
}

.dropdown-list::-webkit-scrollbar-thumb {
    background: #cbd5e1;
    border-radius: 4px;
}

.dropdown-list::-webkit-scrollbar-thumb:hover {
    background: #94a3b8;
}
</style>

<?php include("includes/footer.php"); ?>
