<?php
include("../config/db.php");
$page_title = "Financial Reports";
include("includes/header.php");

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: ../login.php");
    exit();
}

// Date filter
$date_from = isset($_GET['date_from']) ? $_GET['date_from'] : date('Y-m-01');
$date_to = isset($_GET['date_to']) ? $_GET['date_to'] : date('Y-m-d');

// Total Sales
$sales_query = mysqli_query($conn, "SELECT 
    COUNT(*) as total_transactions,
    COALESCE(SUM(grand_total), 0) as total_sales,
    COALESCE(SUM(subtotal), 0) as subtotal,
    COALESCE(SUM(tax_amount), 0) as total_tax,
    COALESCE(SUM(discount_amount), 0) as total_discount
    FROM sales 
    WHERE DATE(sale_date) BETWEEN '$date_from' AND '$date_to'");
$sales_data = mysqli_fetch_assoc($sales_query);

// Total Returns
$returns_query = mysqli_query($conn, "SELECT 
    COUNT(*) as total_returns,
    COALESCE(SUM(return_amount), 0) as total_returned
    FROM sale_returns 
    WHERE DATE(return_date) BETWEEN '$date_from' AND '$date_to' 
    AND status = 'completed'");
$returns_data = mysqli_fetch_assoc($returns_query);

// Calculate Profit (Sales - Purchase Cost)
$profit_query = mysqli_query($conn, "SELECT 
    COALESCE(SUM(si.quantity * (si.unit_price - m.purchase_price)), 0) as total_profit
    FROM sale_items si
    JOIN sales s ON si.sale_id = s.id
    JOIN medicines m ON si.medicine_id = m.id
    WHERE DATE(s.sale_date) BETWEEN '$date_from' AND '$date_to'");
$profit_data = mysqli_fetch_assoc($profit_query);

// Deduct returned profit
$returned_profit_query = mysqli_query($conn, "SELECT 
    COALESCE(SUM(sri.quantity_returned * (sri.unit_price - m.purchase_price)), 0) as returned_profit
    FROM sale_return_items sri
    JOIN sale_returns sr ON sri.return_id = sr.id
    JOIN medicines m ON sri.medicine_id = m.id
    WHERE DATE(sr.return_date) BETWEEN '$date_from' AND '$date_to' 
    AND sr.status = 'completed'");
$returned_profit_data = mysqli_fetch_assoc($returned_profit_query);

// Net calculations
$net_sales = $sales_data['total_sales'] - $returns_data['total_returned'];
$net_profit = $profit_data['total_profit'] - $returned_profit_data['returned_profit'];
$profit_margin = $net_sales > 0 ? ($net_profit / $net_sales) * 100 : 0;

// Payment methods breakdown
$payment_methods = mysqli_query($conn, "SELECT 
    payment_method,
    COUNT(*) as count,
    SUM(grand_total) as total
    FROM sales 
    WHERE DATE(sale_date) BETWEEN '$date_from' AND '$date_to'
    GROUP BY payment_method");

// Top selling medicines
$top_medicines = mysqli_query($conn, "SELECT 
    m.medicine_name,
    SUM(si.quantity) as total_sold,
    SUM(si.subtotal) as revenue,
    SUM(si.quantity * (si.unit_price - m.purchase_price)) as profit
    FROM sale_items si
    JOIN sales s ON si.sale_id = s.id
    JOIN medicines m ON si.medicine_id = m.id
    WHERE DATE(s.sale_date) BETWEEN '$date_from' AND '$date_to'
    GROUP BY si.medicine_id
    ORDER BY total_sold DESC
    LIMIT 10");

// Daily sales trend
$daily_sales = mysqli_query($conn, "SELECT 
    DATE(sale_date) as date,
    COUNT(*) as transactions,
    SUM(grand_total) as total
    FROM sales 
    WHERE DATE(sale_date) BETWEEN '$date_from' AND '$date_to'
    GROUP BY DATE(sale_date)
    ORDER BY date DESC
    LIMIT 30");
?>

<div class="card">
    <div class="card-header">
        <h3>📊 Financial Reports</h3>
    </div>
    <div class="card-body">
        <!-- Date Filter -->
        <form method="GET" style="margin-bottom: 30px;">
            <div class="row">
                <div class="col-md-4">
                    <div class="form-group">
                        <label>From Date</label>
                        <input type="date" name="date_from" class="form-control" value="<?php echo $date_from; ?>" required>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="form-group">
                        <label>To Date</label>
                        <input type="date" name="date_to" class="form-control" value="<?php echo $date_to; ?>" required>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="form-group">
                        <label>&nbsp;</label>
                        <button type="submit" class="btn btn-primary" style="width: 100%;">📅 Apply Filter</button>
                    </div>
                </div>
            </div>
        </form>

        <h4>Period: <?php echo date('d M Y', strtotime($date_from)); ?> to <?php echo date('d M Y', strtotime($date_to)); ?></h4>

        <!-- Key Metrics -->
        <div class="stats-grid" style="margin: 30px 0;">
            <div class="stat-card">
                <div class="stat-icon blue">💰</div>
                <div class="stat-details">
                    <h4>Total Sales</h4>
                    <p>Rs <?php echo number_format($sales_data['total_sales'], 2); ?></p>
                    <small><?php echo $sales_data['total_transactions']; ?> transactions</small>
                </div>
            </div>
            
            <div class="stat-card">
                <div class="stat-icon red">↩️</div>
                <div class="stat-details">
                    <h4>Total Returns</h4>
                    <p>Rs <?php echo number_format($returns_data['total_returned'], 2); ?></p>
                    <small><?php echo $returns_data['total_returns']; ?> returns</small>
                </div>
            </div>
            
            <div class="stat-card">
                <div class="stat-icon green">💵</div>
                <div class="stat-details">
                    <h4>Net Sales</h4>
                    <p>Rs <?php echo number_format($net_sales, 2); ?></p>
                    <small>After returns</small>
                </div>
            </div>
            
            <div class="stat-card">
                <div class="stat-icon orange">📈</div>
                <div class="stat-details">
                    <h4>Net Profit</h4>
                    <p>Rs <?php echo number_format($net_profit, 2); ?></p>
                    <small><?php echo number_format($profit_margin, 1); ?>% margin</small>
                </div>
            </div>
        </div>

        <!-- Detailed Breakdown -->
        <div class="row" style="margin-top: 30px;">
            <!-- Sales Breakdown -->
            <div class="col-md-6">
                <div class="card">
                    <div class="card-header">
                        <h4>Sales Breakdown</h4>
                    </div>
                    <div class="card-body">
                        <table style="width: 100%;">
                            <tr>
                                <td><strong>Subtotal:</strong></td>
                                <td class="text-right">Rs <?php echo number_format($sales_data['subtotal'], 2); ?></td>
                            </tr>
                            <tr>
                                <td><strong>Tax Collected:</strong></td>
                                <td class="text-right">Rs <?php echo number_format($sales_data['total_tax'], 2); ?></td>
                            </tr>
                            <tr>
                                <td><strong>Discounts Given:</strong></td>
                                <td class="text-right" style="color: #dc2626;">-Rs <?php echo number_format($sales_data['total_discount'], 2); ?></td>
                            </tr>
                            <tr style="border-top: 2px solid #333;">
                                <td><strong>Gross Sales:</strong></td>
                                <td class="text-right"><strong>Rs <?php echo number_format($sales_data['total_sales'], 2); ?></strong></td>
                            </tr>
                            <tr>
                                <td><strong>Returns/Refunds:</strong></td>
                                <td class="text-right" style="color: #dc2626;">-Rs <?php echo number_format($returns_data['total_returned'], 2); ?></td>
                            </tr>
                            <tr style="border-top: 2px solid #333; background: #f0fdf4;">
                                <td><strong>Net Sales:</strong></td>
                                <td class="text-right"><strong style="color: #10b981;">Rs <?php echo number_format($net_sales, 2); ?></strong></td>
                            </tr>
                        </table>
                    </div>
                </div>
            </div>

            <!-- Profit Breakdown -->
            <div class="col-md-6">
                <div class="card">
                    <div class="card-header">
                        <h4>Profit Analysis</h4>
                    </div>
                    <div class="card-body">
                        <table style="width: 100%;">
                            <tr>
                                <td><strong>Gross Profit:</strong></td>
                                <td class="text-right">Rs <?php echo number_format($profit_data['total_profit'], 2); ?></td>
                            </tr>
                            <tr>
                                <td><strong>Returned Profit Loss:</strong></td>
                                <td class="text-right" style="color: #dc2626;">-Rs <?php echo number_format($returned_profit_data['returned_profit'], 2); ?></td>
                            </tr>
                            <tr style="border-top: 2px solid #333; background: #f0fdf4;">
                                <td><strong>Net Profit:</strong></td>
                                <td class="text-right"><strong style="color: #10b981;">Rs <?php echo number_format($net_profit, 2); ?></strong></td>
                            </tr>
                            <tr>
                                <td><strong>Profit Margin:</strong></td>
                                <td class="text-right"><strong><?php echo number_format($profit_margin, 2); ?>%</strong></td>
                            </tr>
                            <tr>
                                <td colspan="2">&nbsp;</td>
                            </tr>
                            <tr>
                                <td><strong>Total Transactions:</strong></td>
                                <td class="text-right"><?php echo $sales_data['total_transactions']; ?></td>
                            </tr>
                            <tr>
                                <td><strong>Average Sale:</strong></td>
                                <td class="text-right">Rs <?php echo $sales_data['total_transactions'] > 0 ? number_format($sales_data['total_sales'] / $sales_data['total_transactions'], 2) : '0.00'; ?></td>
                            </tr>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <!-- Payment Methods -->
        <div class="card" style="margin-top: 30px;">
            <div class="card-header">
                <h4>Payment Methods Breakdown</h4>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table>
                        <thead>
                            <tr>
                                <th>Payment Method</th>
                                <th>Transactions</th>
                                <th>Total Amount</th>
                                <th>Percentage</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php 
                            mysqli_data_seek($payment_methods, 0);
                            while ($method = mysqli_fetch_assoc($payment_methods)): 
                                $percentage = $sales_data['total_sales'] > 0 ? ($method['total'] / $sales_data['total_sales']) * 100 : 0;
                            ?>
                            <tr>
                                <td><strong><?php echo ucfirst($method['payment_method']); ?></strong></td>
                                <td><?php echo $method['count']; ?></td>
                                <td>Rs <?php echo number_format($method['total'], 2); ?></td>
                                <td>
                                    <div style="display: flex; align-items: center;">
                                        <div style="width: 100px; height: 20px; background: #e5e7eb; border-radius: 10px; margin-right: 10px;">
                                            <div style="width: <?php echo $percentage; ?>%; height: 100%; background: #2563eb; border-radius: 10px;"></div>
                                        </div>
                                        <span><?php echo number_format($percentage, 1); ?>%</span>
                                    </div>
                                </td>
                            </tr>
                            <?php endwhile; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- Top Selling Medicines -->
        <div class="card" style="margin-top: 30px;">
            <div class="card-header">
                <h4>Top 10 Selling Medicines</h4>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table>
                        <thead>
                            <tr>
                                <th>Rank</th>
                                <th>Medicine</th>
                                <th>Quantity Sold</th>
                                <th>Revenue</th>
                                <th>Profit</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php 
                            $rank = 1;
                            while ($medicine = mysqli_fetch_assoc($top_medicines)): 
                            ?>
                            <tr>
                                <td><strong>#<?php echo $rank++; ?></strong></td>
                                <td><?php echo $medicine['medicine_name']; ?></td>
                                <td><?php echo $medicine['total_sold']; ?> units</td>
                                <td>Rs <?php echo number_format($medicine['revenue'], 2); ?></td>
                                <td style="color: #10b981;"><strong>Rs <?php echo number_format($medicine['profit'], 2); ?></strong></td>
                            </tr>
                            <?php endwhile; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- Daily Sales Trend -->
        <div class="card" style="margin-top: 30px;">
            <div class="card-header">
                <h4>Daily Sales Trend (Last 30 Days)</h4>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table>
                        <thead>
                            <tr>
                                <th>Date</th>
                                <th>Transactions</th>
                                <th>Total Sales</th>
                                <th>Average Sale</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php while ($day = mysqli_fetch_assoc($daily_sales)): ?>
                            <tr>
                                <td><strong><?php echo date('d M Y (D)', strtotime($day['date'])); ?></strong></td>
                                <td><?php echo $day['transactions']; ?></td>
                                <td>Rs <?php echo number_format($day['total'], 2); ?></td>
                                <td>Rs <?php echo number_format($day['total'] / $day['transactions'], 2); ?></td>
                            </tr>
                            <?php endwhile; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- Export Options -->
        <div style="margin-top: 30px; text-align: center;">
            <a href="export_financial_report.php?date_from=<?php echo $date_from; ?>&date_to=<?php echo $date_to; ?>" 
               class="btn btn-success" target="_blank">
                📥 Export to Excel
            </a>
            <button onclick="window.print()" class="btn btn-primary">
                🖨️ Print Report
            </button>
        </div>
    </div>
</div>

<style>
@media print {
    .sidebar, .btn, form { display: none !important; }
    .card { box-shadow: none; border: 1px solid #ddd; }
}
</style>

<?php include("includes/footer.php"); ?>
