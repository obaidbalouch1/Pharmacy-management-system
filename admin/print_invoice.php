<?php
include("../config/db.php");
include("../config/settings.php");

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: ../login.php");
    exit();
}

// Get store settings
$store = get_all_settings($conn);

$sale_id = isset($_GET['id']) ? intval($_GET['id']) : 0;

// Get sale details
$sale_query = mysqli_query($conn, "SELECT s.*, c.customer_name as customer_db_name, c.phone, c.address, u.full_name as cashier 
    FROM sales s 
    LEFT JOIN customers c ON s.customer_id = c.id 
    LEFT JOIN users u ON s.user_id = u.id 
    WHERE s.id = $sale_id");

if (mysqli_num_rows($sale_query) == 0) {
    die("Sale not found!");
}

$sale = mysqli_fetch_assoc($sale_query);

// Get sale items
$items_query = mysqli_query($conn, "SELECT si.*, m.medicine_name, m.generic_name, c.company_name 
    FROM sale_items si 
    JOIN medicines m ON si.medicine_id = m.id 
    LEFT JOIN companies c ON m.company_id = c.id 
    WHERE si.sale_id = $sale_id");
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Invoice - <?php echo $sale['invoice_number']; ?></title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: 'Courier New', monospace;
            font-size: 12px;
            line-height: 1.4;
            padding: 0;
            margin: 0;
        }
        
        /* Thermal printer paper size (80mm width) */
        @page {
            size: 80mm auto;
            margin: 0;
        }
        
        .receipt {
            width: 80mm;
            padding: 5mm;
            margin: 0 auto;
            background: white;
        }
        
        .center {
            text-align: center;
        }
        
        .left {
            text-align: left;
        }
        
        .right {
            text-align: right;
        }
        
        .header {
            text-align: center;
            margin-bottom: 10px;
            border-bottom: 1px dashed #000;
            padding-bottom: 10px;
        }
        
        .header h1 {
            font-size: 18px;
            font-weight: bold;
            margin-bottom: 5px;
        }
        
        .header p {
            font-size: 11px;
            margin: 2px 0;
        }
        
        .section {
            margin: 10px 0;
            border-bottom: 1px dashed #000;
            padding-bottom: 10px;
        }
        
        .section-title {
            font-weight: bold;
            font-size: 13px;
            margin-bottom: 5px;
        }
        
        .info-row {
            display: flex;
            justify-content: space-between;
            margin: 3px 0;
            font-size: 11px;
        }
        
        .info-label {
            font-weight: bold;
        }
        
        .items-table {
            width: 100%;
            margin: 10px 0;
            font-size: 11px;
        }
        
        .items-header {
            font-weight: bold;
            border-bottom: 1px solid #000;
            padding-bottom: 5px;
            margin-bottom: 5px;
        }
        
        .item-row {
            margin: 5px 0;
            padding: 3px 0;
        }
        
        .item-name {
            font-weight: bold;
            margin-bottom: 2px;
        }
        
        .item-details {
            display: flex;
            justify-content: space-between;
            font-size: 10px;
        }
        
        .totals {
            margin-top: 10px;
            border-top: 1px solid #000;
            padding-top: 5px;
        }
        
        .total-row {
            display: flex;
            justify-content: space-between;
            margin: 3px 0;
            font-size: 11px;
        }
        
        .grand-total {
            font-weight: bold;
            font-size: 14px;
            border-top: 1px solid #000;
            border-bottom: 1px solid #000;
            padding: 5px 0;
            margin: 5px 0;
        }
        
        .payment-info {
            background: #f0f0f0;
            padding: 5px;
            margin: 5px 0;
            border: 1px solid #000;
        }
        
        .footer {
            text-align: center;
            margin-top: 15px;
            font-size: 11px;
            border-top: 1px dashed #000;
            padding-top: 10px;
        }
        
        .footer p {
            margin: 3px 0;
        }
        
        .bold {
            font-weight: bold;
        }
        
        .large {
            font-size: 14px;
        }
        
        .small {
            font-size: 10px;
        }
        
        .barcode {
            text-align: center;
            font-family: 'Libre Barcode 128', cursive;
            font-size: 40px;
            margin: 10px 0;
        }
        
        /* Print styles */
        @media print {
            body {
                width: 80mm;
            }
            .receipt {
                width: 80mm;
                padding: 2mm;
            }
            .no-print {
                display: none !important;
            }
            /* Remove any margins for thermal printer */
            @page {
                margin: 0;
            }
        }
        
        /* Button styles */
        .no-print {
            text-align: center;
            padding: 20px;
            background: #f0f0f0;
        }
        
        .btn {
            padding: 10px 20px;
            font-size: 14px;
            cursor: pointer;
            border: none;
            border-radius: 5px;
            margin: 0 5px;
        }
        
        .btn-print {
            background: #4CAF50;
            color: white;
        }
        
        .btn-close {
            background: #f44336;
            color: white;
        }
    </style>
</head>
<body>
    <div class="no-print">
        <button class="btn btn-print" onclick="window.print()">🖨️ Print Receipt</button>
        <button class="btn btn-close" onclick="window.close()">❌ Close</button>
    </div>

    <div class="receipt">
        <!-- Header -->
        <div class="header">
            <h1><?php echo htmlspecialchars($store['store_name']); ?></h1>
            <p><?php echo htmlspecialchars($store['store_tagline']); ?></p>
            <p><?php echo nl2br(htmlspecialchars($store['store_address'])); ?></p>
            <p>Tel: <?php echo htmlspecialchars($store['store_phone']); ?></p>
            <?php if (!empty($store['store_gst'])): ?>
            <p>GST: <?php echo htmlspecialchars($store['store_gst']); ?></p>
            <?php endif; ?>
        </div>

        <!-- Invoice Info -->
        <div class="section">
            <div class="center bold large">SALES RECEIPT</div>
            <div class="info-row">
                <span class="info-label">Invoice:</span>
                <span><?php echo $sale['invoice_number']; ?></span>
            </div>
            <div class="info-row">
                <span class="info-label">Date:</span>
                <span><?php echo date('d/m/Y h:i A', strtotime($sale['sale_date'])); ?></span>
            </div>
            <div class="info-row">
                <span class="info-label">Cashier:</span>
                <span><?php echo $sale['cashier']; ?></span>
            </div>
        </div>

        <!-- Customer Info -->
        <div class="section">
            <div class="section-title">CUSTOMER</div>
            <?php 
            // Priority: custom customer_name > database customer name > Walk-in Customer
            $display_name = '';
            if (!empty($sale['customer_name'])) {
                $display_name = $sale['customer_name'];
            } elseif (!empty($sale['customer_db_name'])) {
                $display_name = $sale['customer_db_name'];
            } else {
                $display_name = 'Walk-in Customer';
            }
            ?>
            
            <div class="info-row">
                <span class="info-label">Name:</span>
                <span><?php echo htmlspecialchars($display_name); ?></span>
            </div>
            
            <?php if (!empty($sale['phone'])): ?>
            <div class="info-row">
                <span class="info-label">Phone:</span>
                <span><?php echo $sale['phone']; ?></span>
            </div>
            <?php endif; ?>
        </div>

        <!-- Items -->
        <div class="section">
            <div class="items-header">
                <div style="display: flex; justify-content: space-between;">
                    <span>ITEM</span>
                    <span>AMOUNT</span>
                </div>
            </div>
            
            <?php 
            mysqli_data_seek($items_query, 0);
            while ($item = mysqli_fetch_assoc($items_query)): 
            ?>
            <div class="item-row">
                <div class="item-name"><?php echo $item['medicine_name']; ?></div>
                <div class="item-details">
                    <span><?php echo $item['quantity']; ?> x Rs <?php echo number_format($item['unit_price'], 2); ?></span>
                    <span class="bold">Rs <?php echo number_format($item['subtotal'], 2); ?></span>
                </div>
                <?php if ($item['batch_number']): ?>
                <div class="small">Batch: <?php echo $item['batch_number']; ?></div>
                <?php endif; ?>
            </div>
            <?php endwhile; ?>
        </div>

        <!-- Totals -->
        <div class="totals">
            <div class="total-row">
                <span>Subtotal:</span>
                <span>Rs <?php echo number_format($sale['subtotal'], 2); ?></span>
            </div>
            
            <?php if ($sale['tax_amount'] > 0): ?>
            <div class="total-row">
                <span>Tax (<?php echo $sale['tax_percentage']; ?>%):</span>
                <span>Rs <?php echo number_format($sale['tax_amount'], 2); ?></span>
            </div>
            <?php endif; ?>
            
            <?php if ($sale['discount_amount'] > 0): ?>
            <div class="total-row">
                <span>Discount (<?php echo $sale['discount_percentage']; ?>%):</span>
                <span>-Rs <?php echo number_format($sale['discount_amount'], 2); ?></span>
            </div>
            <?php endif; ?>
            
            <div class="total-row grand-total">
                <span>TOTAL:</span>
                <span>Rs <?php echo number_format($sale['grand_total'], 2); ?></span>
            </div>
        </div>

        <!-- Payment Info -->
        <?php if ($sale['amount_paid'] > 0): ?>
        <div class="payment-info">
            <div class="total-row">
                <span class="bold">Paid (<?php echo ucfirst($sale['payment_method']); ?>):</span>
                <span class="bold">Rs <?php echo number_format($sale['amount_paid'], 2); ?></span>
            </div>
            <?php if ($sale['change_amount'] > 0): ?>
            <div class="total-row">
                <span class="bold">Change:</span>
                <span class="bold">Rs <?php echo number_format($sale['change_amount'], 2); ?></span>
            </div>
            <?php endif; ?>
        </div>
        <?php endif; ?>

        <!-- Notes -->
        <?php if ($sale['notes']): ?>
        <div class="section">
            <div class="section-title">NOTES</div>
            <div class="small"><?php echo nl2br(htmlspecialchars($sale['notes'])); ?></div>
        </div>
        <?php endif; ?>

        <!-- Barcode (Invoice Number) -->
        <div class="center" style="margin: 10px 0;">
            <div class="small">Invoice: <?php echo $sale['invoice_number']; ?></div>
        </div>

        <!-- Footer -->
        <div class="footer">
            <p class="bold"><?php echo strtoupper(htmlspecialchars($store['receipt_footer'])); ?></p>
            <p>Please visit again</p>
            <p class="small">----------------------------</p>
            <p class="small">Powered by <?php echo htmlspecialchars($store['store_name']); ?> System</p>
            <p class="small">This is a computer generated receipt</p>
        </div>
    </div>

    <script>
        // Auto print on load (optional - uncomment to enable)
        // window.onload = function() { 
        //     window.print(); 
        //     // Auto close after print (optional)
        //     // setTimeout(function(){ window.close(); }, 1000);
        // }
    </script>
</body>
</html>
