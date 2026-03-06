<?php
include("../config/db.php");
$page_title = "Return Sale";
include("includes/header.php");

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: ../login.php");
    exit();
}

$sale_id = isset($_GET['id']) ? intval($_GET['id']) : 0;

// Get sale details
$sale_query = mysqli_query($conn, "SELECT s.*, c.customer_name 
    FROM sales s 
    LEFT JOIN customers c ON s.customer_id = c.id 
    WHERE s.id = $sale_id");

if (mysqli_num_rows($sale_query) == 0) {
    echo "<script>alert('Sale not found!'); window.location='sales.php';</script>";
    exit();
}

$sale = mysqli_fetch_assoc($sale_query);

// Check if already returned
$return_check = mysqli_query($conn, "SELECT * FROM sale_returns WHERE sale_id = $sale_id AND status = 'completed'");
$already_returned = mysqli_num_rows($return_check) > 0;

// Get sale items
$items_query = mysqli_query($conn, "SELECT si.*, m.medicine_name, m.quantity as current_stock
    FROM sale_items si 
    JOIN medicines m ON si.medicine_id = m.id 
    WHERE si.sale_id = $sale_id");
?>

<div class="card">
    <div class="card-header">
        <h3>↩️ Return Sale / Refund</h3>
    </div>
    <div class="card-body">
        <?php if ($already_returned): ?>
            <div class="alert alert-warning">
                <strong>⚠️ This sale has already been returned!</strong>
                <p>You cannot process multiple returns for the same sale.</p>
                <a href="sales.php" class="btn btn-secondary">← Back to Sales</a>
            </div>
        <?php else: ?>
        
        <!-- Sale Information -->
        <div style="background: #f3f4f6; padding: 20px; border-radius: 8px; margin-bottom: 20px;">
            <h4>Sale Information</h4>
            <div class="row">
                <div class="col-md-3">
                    <strong>Invoice:</strong> <?php echo $sale['invoice_number']; ?>
                </div>
                <div class="col-md-3">
                    <strong>Customer:</strong> <?php echo $sale['customer_name'] ?? 'Walk-in'; ?>
                </div>
                <div class="col-md-3">
                    <strong>Date:</strong> <?php echo date('d M Y', strtotime($sale['sale_date'])); ?>
                </div>
                <div class="col-md-3">
                    <strong>Total:</strong> Rs <?php echo number_format($sale['grand_total'], 2); ?>
                </div>
            </div>
        </div>

        <form id="returnForm" method="POST" action="process_return.php">
            <input type="hidden" name="sale_id" value="<?php echo $sale_id; ?>">
            
            <h4>Select Items to Return</h4>
            <div class="table-responsive">
                <table>
                    <thead>
                        <tr>
                            <th>
                                <input type="checkbox" id="selectAll" onclick="toggleAll(this)">
                            </th>
                            <th>Medicine</th>
                            <th>Sold Qty</th>
                            <th>Unit Price</th>
                            <th>Return Qty</th>
                            <th>Refund Amount</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php 
                        $total_refund = 0;
                        while ($item = mysqli_fetch_assoc($items_query)): 
                        ?>
                        <tr>
                            <td>
                                <input type="checkbox" name="return_items[]" value="<?php echo $item['id']; ?>" 
                                       class="item-checkbox" onchange="calculateRefund()">
                            </td>
                            <td>
                                <strong><?php echo $item['medicine_name']; ?></strong>
                                <br><small>Current Stock: <?php echo $item['current_stock']; ?></small>
                            </td>
                            <td><?php echo $item['quantity']; ?></td>
                            <td>Rs <?php echo number_format($item['unit_price'], 2); ?></td>
                            <td>
                                <input type="number" name="return_qty_<?php echo $item['id']; ?>" 
                                       class="form-control form-control-sm return-qty" 
                                       min="1" max="<?php echo $item['quantity']; ?>" 
                                       value="<?php echo $item['quantity']; ?>" 
                                       style="width: 80px;"
                                       data-item-id="<?php echo $item['id']; ?>"
                                       data-unit-price="<?php echo $item['unit_price']; ?>"
                                       data-max-qty="<?php echo $item['quantity']; ?>"
                                       onchange="calculateRefund()">
                            </td>
                            <td>
                                <strong class="refund-amount" id="refund_<?php echo $item['id']; ?>">
                                    Rs <?php echo number_format($item['subtotal'], 2); ?>
                                </strong>
                            </td>
                        </tr>
                        <?php endwhile; ?>
                    </tbody>
                    <tfoot>
                        <tr style="background: #f3f4f6; font-weight: bold;">
                            <td colspan="5" class="text-right">Total Refund Amount:</td>
                            <td><h4 style="color: #dc2626; margin: 0;">Rs <span id="total_refund">0.00</span></h4></td>
                        </tr>
                    </tfoot>
                </table>
            </div>

            <div class="form-group" style="margin-top: 20px;">
                <label>Return Reason <span style="color: red;">*</span></label>
                <textarea name="return_reason" class="form-control" rows="3" required 
                          placeholder="Enter reason for return (e.g., damaged product, wrong medicine, customer request)"></textarea>
            </div>

            <div class="form-group">
                <label>Additional Notes</label>
                <textarea name="notes" class="form-control" rows="2" 
                          placeholder="Any additional notes about this return..."></textarea>
            </div>

            <div style="background: #fef3c7; border: 2px solid #f59e0b; padding: 15px; border-radius: 8px; margin: 20px 0;">
                <strong>⚠️ Important:</strong>
                <ul style="margin: 10px 0 0 0;">
                    <li>Returned items will be added back to inventory</li>
                    <li>Refund amount will be calculated based on selected items</li>
                    <li>This action cannot be undone</li>
                    <li>Original sale record will be marked as returned</li>
                </ul>
            </div>

            <div class="form-group">
                <button type="submit" class="btn btn-warning btn-lg" onclick="return confirm('Are you sure you want to process this return? Stock will be restored to inventory.')">
                    ↩️ Process Return & Refund
                </button>
                <a href="view_sale.php?id=<?php echo $sale_id; ?>" class="btn btn-secondary btn-lg">Cancel</a>
            </div>
        </form>
        <?php endif; ?>
    </div>
</div>

<script>
function toggleAll(checkbox) {
    const checkboxes = document.querySelectorAll('.item-checkbox');
    checkboxes.forEach(cb => {
        cb.checked = checkbox.checked;
    });
    calculateRefund();
}

function calculateRefund() {
    let totalRefund = 0;
    const checkboxes = document.querySelectorAll('.item-checkbox:checked');
    
    checkboxes.forEach(checkbox => {
        const itemId = checkbox.value;
        const qtyInput = document.querySelector(`input[name="return_qty_${itemId}"]`);
        const unitPrice = parseFloat(qtyInput.dataset.unitPrice);
        const qty = parseInt(qtyInput.value) || 0;
        const maxQty = parseInt(qtyInput.dataset.maxQty);
        
        // Validate quantity
        if (qty > maxQty) {
            qtyInput.value = maxQty;
            qty = maxQty;
        }
        if (qty < 1) {
            qtyInput.value = 1;
            qty = 1;
        }
        
        const refundAmount = unitPrice * qty;
        totalRefund += refundAmount;
        
        document.getElementById('refund_' + itemId).textContent = 'Rs ' + refundAmount.toFixed(2);
    });
    
    document.getElementById('total_refund').textContent = totalRefund.toFixed(2);
}

// Initialize on page load
document.addEventListener('DOMContentLoaded', function() {
    calculateRefund();
});

// Recalculate when quantity changes
document.querySelectorAll('.return-qty').forEach(input => {
    input.addEventListener('input', calculateRefund);
});
</script>

<style>
.alert {
    padding: 15px;
    border-radius: 8px;
    margin-bottom: 20px;
}
.alert-warning {
    background: #fef3c7;
    border: 1px solid #f59e0b;
    color: #92400e;
}
</style>

<?php include("includes/footer.php"); ?>
