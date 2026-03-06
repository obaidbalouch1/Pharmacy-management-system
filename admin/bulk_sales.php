<?php
include("../config/db.php");
$page_title = "Bulk Sales Entry";
include("includes/header.php");

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: ../login.php");
    exit();
}

// Get all customers
$customers = mysqli_query($conn, "SELECT * FROM customers ORDER BY customer_name");

// Get all available medicines
$medicines = mysqli_query($conn, "SELECT m.*, c.company_name 
    FROM medicines m 
    LEFT JOIN companies c ON m.company_id = c.id 
    WHERE m.status = 'available' AND m.quantity > 0 
    ORDER BY m.medicine_name");
?>

<div class="card">
    <div class="card-header">
        <h3>Bulk Sales Entry - Add Multiple Sales at Once</h3>
    </div>
    <div class="card-body">
        <div class="alert alert-info">
            <strong>💡 Tip:</strong> You can add multiple sales transactions below. 
            <ul style="margin: 10px 0 0 0;">
                <li><strong>Process This Sale</strong> - Process individual sale immediately</li>
                <li><strong>Process All Sales</strong> - Process all sales at once at the bottom</li>
            </ul>
        </div>

        <form id="bulkSalesForm" method="POST" action="process_bulk_sales.php">
            <div id="salesContainer">
                <!-- Sales will be added here -->
            </div>

            <div style="margin: 20px 0;">
                <button type="button" class="btn btn-primary" onclick="addNewSale()">+ Add Another Sale</button>
                <button type="submit" class="btn btn-success btn-lg" style="margin-left: 10px;">Process All Sales</button>
                <a href="sales.php" class="btn btn-secondary">Cancel</a>
            </div>
        </form>
    </div>
</div>

<!-- Medicine Data for JavaScript -->
<script>
const medicinesData = [
    <?php 
    mysqli_data_seek($medicines, 0);
    $medicine_array = [];
    while ($medicine = mysqli_fetch_assoc($medicines)) {
        $medicine_array[] = json_encode([
            'id' => $medicine['id'],
            'name' => $medicine['medicine_name'],
            'company' => $medicine['company_name'],
            'price' => floatval($medicine['selling_price']),
            'stock' => intval($medicine['quantity']),
            'batch' => $medicine['batch_number'],
            'barcode' => $medicine['barcode']
        ]);
    }
    echo implode(",\n    ", $medicine_array);
    ?>
];

const customersData = [
    <?php 
    mysqli_data_seek($customers, 0);
    $customer_array = [];
    while ($customer = mysqli_fetch_assoc($customers)) {
        $customer_array[] = json_encode([
            'id' => $customer['id'],
            'name' => $customer['customer_name'],
            'phone' => $customer['phone']
        ]);
    }
    echo implode(",\n    ", $customer_array);
    ?>
];

let saleCounter = 0;
let salesData = {};

// Debug: Check if data is loaded
console.log('Medicines loaded:', medicinesData.length);
console.log('Customers loaded:', customersData.length);

if (medicinesData.length === 0) {
    alert('⚠️ No medicines available! Please add medicines with stock before creating sales.');
}

function addNewSale() {
    try {
        saleCounter++;
        const saleId = 'sale_' + saleCounter;
        
        salesData[saleId] = {
            items: [],
            subtotal: 0,
            tax_percentage: 0,
            discount_percentage: 0
        };
        
        const saleHtml = `
        <div class="sale-block" id="${saleId}" style="border: 2px solid #e5e7eb; padding: 20px; margin-bottom: 20px; border-radius: 10px; background: #f9fafb;">
            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 15px;">
                <h4 style="margin: 0;">Sale #${saleCounter}</h4>
                <div>
                    <button type="button" class="btn btn-success btn-sm" onclick="processSingleSale('${saleId}')" style="margin-right: 10px;">
                        ✓ Process This Sale
                    </button>
                    <button type="button" class="btn btn-danger btn-sm" onclick="removeSale('${saleId}')">Remove Sale</button>
                </div>
            </div>
            
            <div class="row">
                <div class="col-md-3">
                    <div class="form-group">
                        <label>Select Customer (Optional)</label>
                        <select name="customer_${saleId}" id="customer_${saleId}" class="form-control" onchange="handleCustomerSelect('${saleId}')">
                            <option value="">-- Select Existing Customer --</option>
                            ${customersData.map(c => `<option value="${c.id}" data-name="${c.name}">${c.name} - ${c.phone}</option>`).join('')}
                        </select>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="form-group">
                        <label>Or Enter Customer Name</label>
                        <input type="text" name="customer_name_${saleId}" id="customer_name_${saleId}" class="form-control" 
                               placeholder="Enter customer name for receipt...">
                        <small class="text-muted">Leave blank for "Walk-in Customer"</small>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="form-group">
                        <label>Payment Method</label>
                        <select name="payment_method_${saleId}" class="form-control" required>
                            <option value="cash">Cash</option>
                            <option value="card">Card</option>
                            <option value="upi">UPI</option>
                            <option value="other">Other</option>
                        </select>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="form-group">
                        <label>🔍 Search & Add Medicine (or scan barcode)</label>
                        <input type="text" class="medicine-search form-control" 
                               placeholder="Type medicine name or scan barcode..." 
                               autocomplete="off"
                               onfocus="showSearchResults('${saleId}', this)"
                               oninput="searchMedicines('${saleId}', this.value)"
                               onkeypress="handleBarcodeEnter(event, '${saleId}', this)">
                        <div id="search_results_${saleId}" class="search-results-bulk"></div>
                    </div>
                </div>
            </div>
            
            <div class="row">
                <div class="col-md-12">
                    <div class="form-group">
                        <label>Or Select from Dropdown (Type to filter)</label>
                        <div class="searchable-dropdown-bulk" data-sale-id="${saleId}">
                            <input type="text" class="form-control dropdown-search-bulk" 
                                   placeholder="Type to filter medicines..." 
                                   autocomplete="off"
                                   data-sale-id="${saleId}">
                            <div class="dropdown-list-bulk" data-sale-id="${saleId}" style="display: none;">
                                <div class="dropdown-item-bulk" data-value="">-- Select Medicine --</div>
                                ${medicinesData.map(m => `
                                    <div class="dropdown-item-bulk" 
                                         data-value="${m.id}" 
                                         data-price="${m.price}" 
                                         data-stock="${m.stock}" 
                                         data-batch="${m.batch || ''}" 
                                         data-name="${m.name}"
                                         data-searchtext="${m.name.toLowerCase()} ${(m.company || '').toLowerCase()} ${(m.batch || '').toLowerCase()}">
                                        <div class="dropdown-item-name">${m.name}</div>
                                        <div class="dropdown-item-details">${m.company || 'N/A'} | Stock: ${m.stock} | Rs ${m.price.toFixed(2)}</div>
                                    </div>
                                `).join('')}
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="table-responsive">
                <table class="table">
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
                    <tbody id="items_${saleId}">
                        <tr>
                            <td colspan="6" class="text-center text-muted">No items added yet</td>
                        </tr>
                    </tbody>
                </table>
            </div>
            
            <div class="row">
                <div class="col-md-8"></div>
                <div class="col-md-4">
                    <table class="table table-borderless">
                        <tr>
                            <td><strong>Subtotal:</strong></td>
                            <td class="text-right">Rs <span id="subtotal_${saleId}">0.00</span></td>
                        </tr>
                        <tr>
                            <td>
                                <strong>Tax (%):</strong>
                                <input type="number" class="form-control form-control-sm" value="0" min="0" max="100" step="0.01" onchange="calculateSaleTotals('${saleId}')">
                            </td>
                            <td class="text-right">Rs <span id="tax_${saleId}">0.00</span></td>
                        </tr>
                        <tr>
                            <td>
                                <strong>Discount (%):</strong>
                                <input type="number" class="form-control form-control-sm" value="0" min="0" max="100" step="0.01" onchange="calculateSaleTotals('${saleId}')">
                            </td>
                            <td class="text-right">Rs <span id="discount_${saleId}">0.00</span></td>
                        </tr>
                        <tr style="border-top: 2px solid #333;">
                            <td><strong>Grand Total:</strong></td>
                            <td class="text-right"><h4 style="color: #2563eb; margin: 0;">Rs <span id="total_${saleId}">0.00</span></h4></td>
                        </tr>
                        <tr>
                            <td>
                                <strong>Amount Paid:</strong>
                                <input type="number" class="form-control form-control-sm" id="amount_paid_${saleId}" value="0" min="0" step="0.01" placeholder="Enter amount" onchange="calculateSaleChange('${saleId}')">
                            </td>
                            <td class="text-right">Rs <span id="amount_paid_display_${saleId}">0.00</span></td>
                        </tr>
                        <tr style="background-color: #f0fdf4;">
                            <td><strong>Change to Return:</strong></td>
                            <td class="text-right"><h4 style="color: #10b981; margin: 0;">Rs <span id="change_${saleId}">0.00</span></h4></td>
                        </tr>
                    </table>
                </div>
            </div>
            
            <input type="hidden" name="sale_data_${saleId}" id="sale_data_${saleId}">
        </div>
    `;
    
    document.getElementById('salesContainer').insertAdjacentHTML('beforeend', saleHtml);
    } catch (error) {
        console.error('Error in addNewSale:', error);
        alert('Error adding new sale: ' + error.message);
    }
}

function addMedicineToSale(saleId, selectElement) {
    const option = selectElement.options[selectElement.selectedIndex];
    if (!option.value) return;
    
    const medicineId = option.value;
    const medicineName = option.dataset.name;
    const price = parseFloat(option.dataset.price);
    const stock = parseInt(option.dataset.stock);
    const batch = option.dataset.batch;
    
    // Check if already added
    const existing = salesData[saleId].items.find(item => item.id === medicineId);
    if (existing) {
        alert('Medicine already added to this sale!');
        selectElement.value = '';
        return;
    }
    
    // Add to items
    salesData[saleId].items.push({
        id: medicineId,
        name: medicineName,
        batch: batch,
        price: price,
        quantity: 1,
        stock: stock
    });
    
    renderSaleItems(saleId);
    calculateSaleTotals(saleId);
    selectElement.value = '';
}

function renderSaleItems(saleId) {
    const tbody = document.getElementById('items_' + saleId);
    const items = salesData[saleId].items;
    
    if (items.length === 0) {
        tbody.innerHTML = '<tr><td colspan="6" class="text-center text-muted">No items added yet</td></tr>';
        return;
    }
    
    tbody.innerHTML = '';
    items.forEach((item, index) => {
        const row = document.createElement('tr');
        row.innerHTML = `
            <td>${item.name}</td>
            <td>${item.batch || 'N/A'}</td>
            <td>Rs ${item.price.toFixed(2)}</td>
            <td>
                <input type="number" class="form-control form-control-sm" 
                       value="${item.quantity}" min="1" max="${item.stock}" 
                       onchange="updateSaleItemQuantity('${saleId}', ${index}, this.value)" 
                       style="width: 80px;">
                <small class="text-muted">Max: ${item.stock}</small>
            </td>
            <td>Rs ${(item.price * item.quantity).toFixed(2)}</td>
            <td>
                <button type="button" class="btn btn-danger btn-sm" onclick="removeSaleItem('${saleId}', ${index})">Remove</button>
            </td>
        `;
        tbody.appendChild(row);
    });
}

function updateSaleItemQuantity(saleId, index, quantity) {
    quantity = parseInt(quantity);
    if (quantity < 1) quantity = 1;
    if (quantity > salesData[saleId].items[index].stock) {
        alert('Quantity exceeds available stock!');
        quantity = salesData[saleId].items[index].stock;
    }
    salesData[saleId].items[index].quantity = quantity;
    renderSaleItems(saleId);
    calculateSaleTotals(saleId);
}

function removeSaleItem(saleId, index) {
    salesData[saleId].items.splice(index, 1);
    renderSaleItems(saleId);
    calculateSaleTotals(saleId);
}

function calculateSaleTotals(saleId) {
    const items = salesData[saleId].items;
    const subtotal = items.reduce((sum, item) => sum + (item.price * item.quantity), 0);
    
    const saleBlock = document.getElementById(saleId);
    const taxInput = saleBlock.querySelectorAll('input[type="number"]')[0];
    const discountInput = saleBlock.querySelectorAll('input[type="number"]')[1];
    
    const taxPercentage = parseFloat(taxInput.value) || 0;
    const discountPercentage = parseFloat(discountInput.value) || 0;
    
    const taxAmount = (subtotal * taxPercentage) / 100;
    const discountAmount = (subtotal * discountPercentage) / 100;
    const grandTotal = subtotal + taxAmount - discountAmount;
    
    document.getElementById('subtotal_' + saleId).textContent = subtotal.toFixed(2);
    document.getElementById('tax_' + saleId).textContent = taxAmount.toFixed(2);
    document.getElementById('discount_' + saleId).textContent = discountAmount.toFixed(2);
    document.getElementById('total_' + saleId).textContent = grandTotal.toFixed(2);
    
    salesData[saleId].subtotal = subtotal;
    salesData[saleId].tax_percentage = taxPercentage;
    salesData[saleId].tax_amount = taxAmount;
    salesData[saleId].discount_percentage = discountPercentage;
    salesData[saleId].discount_amount = discountAmount;
    salesData[saleId].grand_total = grandTotal;
    
    // Recalculate change
    calculateSaleChange(saleId);
}

function calculateSaleChange(saleId) {
    const grandTotal = salesData[saleId].grand_total || 0;
    const amountPaidInput = document.getElementById('amount_paid_' + saleId);
    const amountPaid = parseFloat(amountPaidInput.value) || 0;
    const change = amountPaid - grandTotal;
    
    document.getElementById('amount_paid_display_' + saleId).textContent = amountPaid.toFixed(2);
    document.getElementById('change_' + saleId).textContent = change >= 0 ? change.toFixed(2) : '0.00';
    
    // Store in salesData
    salesData[saleId].amount_paid = amountPaid;
    salesData[saleId].change_amount = change >= 0 ? change : 0;
    
    // Highlight if insufficient payment
    const changeRow = document.getElementById('change_' + saleId).parentElement.parentElement;
    if (amountPaid > 0 && amountPaid < grandTotal) {
        changeRow.style.backgroundColor = '#fee2e2';
        document.getElementById('change_' + saleId).style.color = '#dc2626';
    } else if (change > 0) {
        changeRow.style.backgroundColor = '#f0fdf4';
        document.getElementById('change_' + saleId).style.color = '#10b981';
    } else {
        changeRow.style.backgroundColor = '#f9fafb';
        document.getElementById('change_' + saleId).style.color = '#6b7280';
    }
}

function removeSale(saleId) {
    if (confirm('Are you sure you want to remove this sale?')) {
        document.getElementById(saleId).remove();
        delete salesData[saleId];
    }
}

// Process single sale
function processSingleSale(saleId) {
    // Validate sale
    if (!salesData[saleId] || salesData[saleId].items.length === 0) {
        alert('Please add items to this sale first!');
        return;
    }
    
    const amountPaid = salesData[saleId].amount_paid || 0;
    const grandTotal = salesData[saleId].grand_total || 0;
    
    if (amountPaid > 0 && amountPaid < grandTotal) {
        alert('Amount paid is less than the grand total!');
        return;
    }
    
    if (!confirm('Process this sale now?')) {
        return;
    }
    
    // Get form data for this sale
    const saleBlock = document.getElementById(saleId);
    const customerSelect = saleBlock.querySelector('select[name^="customer_"]');
    const paymentMethodSelect = saleBlock.querySelector('select[name^="payment_method_"]');
    
    const singleSaleData = {
        [saleId]: salesData[saleId]
    };
    
    const formData = new FormData();
    formData.append('sales_data', JSON.stringify(singleSaleData));
    formData.append('customer_' + saleId, customerSelect.value);
    formData.append('payment_method_' + saleId, paymentMethodSelect.value);
    formData.append('single_sale', 'true');
    
    // Show processing message
    const processBtn = saleBlock.querySelector('.btn-success');
    const originalText = processBtn.innerHTML;
    processBtn.disabled = true;
    processBtn.innerHTML = '⏳ Processing...';
    
    // Submit via fetch
    fetch('process_bulk_sales.php', {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            alert(data.message);
            // Remove the processed sale from the page
            document.getElementById(saleId).remove();
            delete salesData[saleId];
            
            // If no more sales, redirect to sales page
            if (Object.keys(salesData).length === 0) {
                window.location.href = 'sales.php';
            }
        } else {
            alert('Error: ' + data.message);
            processBtn.disabled = false;
            processBtn.innerHTML = originalText;
        }
    })
    .catch(error => {
        alert('Error processing sale: ' + error);
        processBtn.disabled = false;
        processBtn.innerHTML = originalText;
    });
}

document.getElementById('bulkSalesForm').addEventListener('submit', function(e) {
    e.preventDefault();
    
    const activeSales = Object.keys(salesData);
    if (activeSales.length === 0) {
        alert('Please add at least one sale!');
        return false;
    }
    
    // Validate each sale has items and sufficient payment
    for (let saleId of activeSales) {
        if (salesData[saleId].items.length === 0) {
            alert('Sale #' + saleId.replace('sale_', '') + ' has no items!');
            return false;
        }
        
        const amountPaid = salesData[saleId].amount_paid || 0;
        const grandTotal = salesData[saleId].grand_total || 0;
        
        if (amountPaid > 0 && amountPaid < grandTotal) {
            alert('Sale #' + saleId.replace('sale_', '') + ': Amount paid is less than the grand total!');
            return false;
        }
    }
    
    // Prepare data for submission
    const formData = new FormData(this);
    formData.append('sales_data', JSON.stringify(salesData));
    
    // Submit via fetch
    fetch('process_bulk_sales.php', {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            alert(data.message);
            window.location.href = 'sales.php';
        } else {
            alert('Error: ' + data.message);
        }
    })
    .catch(error => {
        alert('Error processing sales: ' + error);
    });
});

// Add first sale on load
addNewSale();

// Handle customer selection in bulk sales
function handleCustomerSelect(saleId) {
    const customerSelect = document.getElementById('customer_' + saleId);
    const customerNameInput = document.getElementById('customer_name_' + saleId);
    const selectedOption = customerSelect.options[customerSelect.selectedIndex];
    
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
}

// Medicine search functionality for bulk sales
function searchMedicines(saleId, query) {
    query = query.toLowerCase().trim();
    const resultsDiv = document.getElementById('search_results_' + saleId);
    
    if (query.length < 2) {
        resultsDiv.innerHTML = '';
        resultsDiv.style.display = 'none';
        return;
    }
    
    // Filter medicines by name, company, batch, or BARCODE
    const filtered = medicinesData.filter(med => 
        med.name.toLowerCase().includes(query) ||
        (med.company && med.company.toLowerCase().includes(query)) ||
        (med.batch && med.batch.toLowerCase().includes(query)) ||
        (med.barcode && med.barcode.toLowerCase() === query) // Exact barcode match
    ).slice(0, 10);
    
    // If exact barcode match found, auto-add it
    if (filtered.length === 1 && filtered[0].barcode && filtered[0].barcode.toLowerCase() === query) {
        addMedicineFromSearchBulk(saleId, filtered[0].id);
        return;
    }
    
    if (filtered.length === 0) {
        resultsDiv.innerHTML = '<div class="search-item-bulk no-results">No medicines found</div>';
        resultsDiv.style.display = 'block';
        return;
    }
    
    resultsDiv.innerHTML = filtered.map(med => `
        <div class="search-item-bulk" onclick="addMedicineFromSearchBulk('${saleId}', '${med.id}')">
            <div class="search-item-name">${med.name}</div>
            <div class="search-item-details">
                ${med.company} | Stock: ${med.stock} | Rs ${med.price.toFixed(2)}
                ${med.barcode ? ' | Barcode: ' + med.barcode : ''}
            </div>
        </div>
    `).join('');
    resultsDiv.style.display = 'block';
}

function showSearchResults(saleId, input) {
    if (input.value.length >= 2) {
        searchMedicines(saleId, input.value);
    }
}

function addMedicineFromSearchBulk(saleId, medicineId) {
    const medicine = medicinesData.find(m => m.id === medicineId);
    if (!medicine) return;
    
    // Check if already added
    const existing = salesData[saleId].items.find(item => item.id === medicineId);
    if (existing) {
        alert('Medicine already added to this sale!');
        return;
    }
    
    // Add to items
    salesData[saleId].items.push({
        id: medicineId,
        name: medicine.name,
        batch: medicine.batch,
        price: medicine.price,
        quantity: 1,
        stock: medicine.stock
    });
    
    renderSaleItems(saleId);
    calculateSaleTotals(saleId);
    
    // Clear search
    const searchInput = document.querySelector(`#${saleId} .medicine-search`);
    if (searchInput) {
        searchInput.value = '';
    }
    const resultsDiv = document.getElementById('search_results_' + saleId);
    resultsDiv.innerHTML = '';
    resultsDiv.style.display = 'none';
}

// Handle Enter key for barcode scanners in bulk sales
function handleBarcodeEnter(event, saleId, input) {
    if (event.key === 'Enter') {
        event.preventDefault();
        const query = input.value.toLowerCase().trim();
        
        // Try to find exact barcode match
        const exactMatch = medicinesData.find(med => 
            med.barcode && med.barcode.toLowerCase() === query
        );
        
        if (exactMatch) {
            addMedicineFromSearchBulk(saleId, exactMatch.id);
        } else {
            // If no exact match, try first search result
            const filtered = medicinesData.filter(med => 
                med.name.toLowerCase().includes(query) ||
                (med.company && med.company.toLowerCase().includes(query)) ||
                (med.batch && med.batch.toLowerCase().includes(query))
            );
            
            if (filtered.length > 0) {
                addMedicineFromSearchBulk(saleId, filtered[0].id);
            }
        }
    }
}

// Close search results when clicking outside
document.addEventListener('click', function(e) {
    if (!e.target.classList.contains('medicine-search') && 
        !e.target.closest('.search-results-bulk')) {
        document.querySelectorAll('.search-results-bulk').forEach(div => {
            div.style.display = 'none';
        });
    }
    
    // Close dropdown lists
    if (!e.target.closest('.searchable-dropdown-bulk')) {
        document.querySelectorAll('.dropdown-list-bulk').forEach(div => {
            div.style.display = 'none';
        });
    }
});

// Dropdown functionality for bulk sales
document.addEventListener('focus', function(e) {
    if (e.target.classList.contains('dropdown-search-bulk')) {
        const saleId = e.target.dataset.saleId;
        const dropdownList = document.querySelector(`.dropdown-list-bulk[data-sale-id="${saleId}"]`);
        if (dropdownList) {
            dropdownList.style.display = 'block';
            filterBulkDropdown(saleId, '');
        }
    }
}, true);

document.addEventListener('input', function(e) {
    if (e.target.classList.contains('dropdown-search-bulk')) {
        const saleId = e.target.dataset.saleId;
        const searchTerm = e.target.value.toLowerCase().trim();
        filterBulkDropdown(saleId, searchTerm);
    }
});

function filterBulkDropdown(saleId, searchTerm) {
    const dropdownList = document.querySelector(`.dropdown-list-bulk[data-sale-id="${saleId}"]`);
    if (!dropdownList) return;
    
    const items = dropdownList.querySelectorAll('.dropdown-item-bulk');
    let visibleCount = 0;
    
    items.forEach(item => {
        if (!item.dataset.value) {
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
    
    // Remove existing "no results" message
    const existingNoResults = dropdownList.querySelector('.no-results-bulk');
    if (existingNoResults) existingNoResults.remove();
    
    // Show "no results" if nothing found
    if (visibleCount === 1) {
        const noResults = document.createElement('div');
        noResults.className = 'dropdown-item-bulk no-results-bulk';
        noResults.textContent = 'No medicines found';
        noResults.style.color = '#9ca3af';
        noResults.style.textAlign = 'center';
        dropdownList.appendChild(noResults);
    }
}

// Handle dropdown item click in bulk sales
document.addEventListener('click', function(e) {
    const item = e.target.closest('.dropdown-item-bulk');
    if (!item) return;
    
    const dropdownList = item.closest('.dropdown-list-bulk');
    if (!dropdownList) return;
    
    const saleId = dropdownList.dataset.saleId;
    
    if (!item.dataset.value) {
        dropdownList.style.display = 'none';
        return;
    }
    
    const medicine = {
        id: item.dataset.value,
        name: item.dataset.name,
        price: parseFloat(item.dataset.price),
        stock: parseInt(item.dataset.stock),
        batch: item.dataset.batch
    };
    
    // Check if already added
    const existing = salesData[saleId].items.find(i => i.id === medicine.id);
    if (existing) {
        alert('Medicine already added to this sale!');
        dropdownList.style.display = 'none';
        return;
    }
    
    // Add to items
    salesData[saleId].items.push({
        id: medicine.id,
        name: medicine.name,
        batch: medicine.batch,
        price: medicine.price,
        quantity: 1,
        stock: medicine.stock
    });
    
    renderSaleItems(saleId);
    calculateSaleTotals(saleId);
    
    // Reset dropdown
    const searchInput = document.querySelector(`.dropdown-search-bulk[data-sale-id="${saleId}"]`);
    if (searchInput) {
        searchInput.value = '';
    }
    dropdownList.style.display = 'none';
    filterBulkDropdown(saleId, '');
});</script>

<style>
.sale-block {
    animation: slideIn 0.3s ease;
}

@keyframes slideIn {
    from {
        opacity: 0;
        transform: translateY(-20px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}

.search-results-bulk {
    position: absolute;
    z-index: 1000;
    background: white;
    border: 1px solid #d1d5db;
    border-radius: 8px;
    box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06);
    max-height: 300px;
    overflow-y: auto;
    width: calc(100% - 30px);
    margin-top: 5px;
    display: none;
}

.search-item-bulk {
    padding: 10px 14px;
    cursor: pointer;
    border-bottom: 1px solid #f3f4f6;
    transition: background-color 0.2s;
}

.search-item-bulk:hover {
    background-color: #f9fafb;
}

.search-item-bulk:last-child {
    border-bottom: none;
}

.search-item-name {
    font-weight: 600;
    color: #111827;
    margin-bottom: 3px;
    font-size: 14px;
}

.search-item-details {
    font-size: 12px;
    color: #6b7280;
}

.search-item-bulk.no-results {
    text-align: center;
    color: #9ca3af;
    cursor: default;
}

.search-item-bulk.no-results:hover {
    background-color: white;
}

.medicine-search:focus {
    border-color: #3b82f6;
    box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.1);
    outline: none;
}

/* Searchable Dropdown for Bulk Sales */
.searchable-dropdown-bulk {
    position: relative;
}

.dropdown-search-bulk {
    cursor: pointer;
}

.dropdown-search-bulk:focus {
    border-color: #3b82f6;
    box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.1);
    outline: none;
}

.dropdown-list-bulk {
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

.dropdown-item-bulk {
    padding: 12px 16px;
    cursor: pointer;
    border-bottom: 1px solid #f3f4f6;
    transition: background-color 0.2s;
}

.dropdown-item-bulk:hover {
    background-color: #f0f9ff;
}

.dropdown-item-bulk:last-child {
    border-bottom: none;
}

.dropdown-item-bulk[data-value=""] {
    font-weight: 600;
    color: #6b7280;
    background-color: #f9fafb;
}

.dropdown-item-bulk[data-value=""]:hover {
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

/* Scrollbar styling */
.dropdown-list-bulk::-webkit-scrollbar {
    width: 8px;
}

.dropdown-list-bulk::-webkit-scrollbar-track {
    background: #f1f1f1;
    border-radius: 4px;
}

.dropdown-list-bulk::-webkit-scrollbar-thumb {
    background: #cbd5e1;
    border-radius: 4px;
}

.dropdown-list-bulk::-webkit-scrollbar-thumb:hover {
    background: #94a3b8;
}
</style>

<?php include("includes/footer.php"); ?>
