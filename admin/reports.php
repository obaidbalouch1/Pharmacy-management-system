<?php
include("../config/db.php");
$page_title = "Reports";
include("includes/header.php");

// Date filters
$from_date = isset($_GET['from']) ? $_GET['from'] : date('Y-m-01');
$to_date = isset($_GET['to']) ? $_GET['to'] : date('Y-m-d');
$report_type = isset($_GET['report_type']) ? $_GET['report_type'] : 'sales';

// Sales Report
$sales_report = mysqli_query($conn, "SELECT DATE(sale_date) as date, COUNT(*) as total_sales, 
    SUM(grand_total) as revenue, SUM(discount_amount) as total_discount, SUM(tax_amount) as total_tax
    FROM sales WHERE DATE(sale_date) BETWEEN '$from_date' AND '$to_date' 
    GROUP BY DATE(sale_date) ORDER BY date DESC");

// Top Selling Medicines
$top_medicines = mysqli_query($conn, "SELECT m.medicine_name, m.generic_name, c.company_name,
    SUM(si.quantity) as total_sold, SUM(si.subtotal) as total_revenue,
    COUNT(DISTINCT si.sale_id) as times_sold
    FROM sale_items si 
    JOIN medicines m ON si.medicine_id = m.id 
    LEFT JOIN companies c ON m.company_id = c.id
    JOIN sales s ON si.sale_id = s.id 
    WHERE DATE(s.sale_date) BETWEEN '$from_date' AND '$to_date'
    GROUP BY si.medicine_id 
    ORDER BY total_sold DESC 
    LIMIT 20");

// Purchase Report
$purchase_report = mysqli_query($conn, "SELECT DATE(purchase_date) as date, COUNT(*) as total_purchases,
    SUM(grand_total) as total_amount, s.supplier_name
    FROM purchases p
    LEFT JOIN suppliers s ON p.supplier_id = s.id
    WHERE DATE(purchase_date) BETWEEN '$from_date' AND '$to_date'
    GROUP BY DATE(purchase_date)
    ORDER BY date DESC");

// Customer Report
$customer_report = mysqli_query($conn, "SELECT c.customer_name, c.phone,
    COUNT(s.id) as total_orders, SUM(s.grand_total) as total_spent
    FROM customers c
    JOIN sales s ON c.id = s.customer_id
    WHERE DATE(s.sale_date) BETWEEN '$from_date' AND '$to_date'
    GROUP BY c.id
    ORDER BY total_spent DESC
    LIMIT 20");

// Profit calculation
$profit_query = mysqli_query($conn, "SELECT 
    SUM(si.subtotal) as total_revenue,
    SUM(si.quantity * m.purchase_price) as total_cost
    FROM sale_items si 
    JOIN medicines m ON si.medicine_id = m.id 
    JOIN sales s ON si.sale_id = s.id 
    WHERE DATE(s.sale_date) BETWEEN '$from_date' AND '$to_date'");
$profit_data = mysqli_fetch_assoc($profit_query);
$total_revenue = $profit_data['total_revenue'] ?? 0;
$total_cost = $profit_data['total_cost'] ?? 0;
$total_profit = $total_revenue - $total_cost;

// Overall statistics
$total_sales = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as count, SUM(grand_total) as total FROM sales WHERE DATE(sale_date) BETWEEN '$from_date' AND '$to_date'"));
$total_purchases = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as count, SUM(grand_total) as total FROM purchases WHERE DATE(purchase_date) BETWEEN '$from_date' AND '$to_date'"));
?>

<div class="card" style="margin-bottom: 24px;">
    <div class="card-header" style="display: flex; justify-content: space-between; align-items: center;">
        <h3>Reports & Analytics</h3>
        <div>
            <button onclick="window.print()" class="btn btn-info">🖨️ Print Report</button>
            <a href="export_report.php?from=<?php echo $from_date; ?>&to=<?php echo $to_date; ?>&type=<?php echo $report_type; ?>" 
               class="btn btn-success">📥 Download Excel</a>
        </div>
    </div>
    <div class="card-body">
        <form method="GET" style="margin-bottom: 20px;">
            <div class="row">
                <div class="col-md-3">
                    <div class="form-group">
                        <label>Report Type</label>
                        <select name="report_type" class="form-control">
                            <option value="sales" <?php echo $report_type == 'sales' ? 'selected' : ''; ?>>Sales Report</option>
                            <option value="medicines" <?php echo $report_type == 'medicines' ? 'selected' : ''; ?>>Top Medicines</option>
                            <option value="purchases" <?php echo $report_type == 'purchases' ? 'selected' : ''; ?>>Purchase Report</option>
                            <option value="customers" <?php echo $report_type == 'customers' ? 'selected' : ''; ?>>Customer Report</option>
                        </select>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="form-group">
                        <label>From Date</label>
                        <input type="date" name="from" class="form-control" value="<?php echo $from_date; ?>">
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="form-group">
                        <label>To Date</label>
                        <input type="date" name="to" class="form-control" value="<?php echo $to_date; ?>">
                    </div>
                </div>
                <div class="col-md-3" style="display: flex; align-items: flex-end;">
                    <div class="form-group" style="width: 100%;">
                        <button type="submit" class="btn btn-primary" style="width: 100%;">Generate Report</button>
                    </div>
                </div>
            </div>
        </form>
        
        <!-- Statistics Cards -->
        <div class="stats-grid" style="margin-bottom: 30px;">
            <div class="stat-card">
                <div class="stat-icon green">💰</div>
                <div class="stat-details">
                    <h4>Total Revenue</h4>
                    <p>Rs <?php echo number_format($total_sales['total'] ?? 0, 2); ?></p>
                </div>
            </div>
            
            <div class="stat-card">
                <div class="stat-icon blue">📊</div>
                <div class="stat-details">
                    <h4>Total Profit</h4>
                    <p>Rs <?php echo number_format($total_profit, 2); ?></p>
                </div>
            </div>
            
            <div class="stat-card">
                <div class="stat-icon orange">🛒</div>
                <div class="stat-details">
                    <h4>Total Sales</h4>
                    <p><?php echo $total_sales['count'] ?? 0; ?></p>
                </div>
            </div>
            
            <div class="stat-card">
                <div class="stat-icon red">📦</div>
                <div class="stat-details">
                    <h4>Total Purchases</h4>
                    <p><?php echo $total_purchases['count'] ?? 0; ?></p>
                </div>
            </div>
        </div>
        
        <!-- Report Content -->
        <?php if ($report_type == 'sales'): ?>
        <h4>Daily Sales Report (<?php echo date('d M Y', strtotime($from_date)); ?> to <?php echo date('d M Y', strtotime($to_date)); ?>)</h4>
        <div class="table-responsive">
            <table>
                <thead>
                    <tr>
                        <th>Date</th>
                        <th>Total Sales</th>
                        <th>Revenue</th>
                        <th>Tax</th>
                        <th>Discount</th>
                        <th>Net Revenue</th>
                    </tr>
                </thead>
                <tbody>
                    <?php 
                    mysqli_data_seek($sales_report, 0);
                    $total_revenue_sum = 0;
                    $total_sales_count = 0;
                    $total_tax_sum = 0;
                    $total_discount_sum = 0;
                    while ($report = mysqli_fetch_assoc($sales_report)): 
                        $total_revenue_sum += $report['revenue'];
                        $total_sales_count += $report['total_sales'];
                        $total_tax_sum += $report['total_tax'];
                        $total_discount_sum += $report['total_discount'];
                    ?>
                    <tr>
                        <td><?php echo date('d M Y', strtotime($report['date'])); ?></td>
                        <td><?php echo $report['total_sales']; ?></td>
                        <td>Rs <?php echo number_format($report['revenue'], 2); ?></td>
                        <td>Rs <?php echo number_format($report['total_tax'], 2); ?></td>
                        <td>Rs <?php echo number_format($report['total_discount'], 2); ?></td>
                        <td><strong>Rs <?php echo number_format($report['revenue'] - $report['total_discount'], 2); ?></strong></td>
                    </tr>
                    <?php endwhile; ?>
                    <tr style="background: #f3f4f6; font-weight: bold;">
                        <td>TOTAL</td>
                        <td><?php echo $total_sales_count; ?></td>
                        <td>Rs <?php echo number_format($total_revenue_sum, 2); ?></td>
                        <td>Rs <?php echo number_format($total_tax_sum, 2); ?></td>
                        <td>Rs <?php echo number_format($total_discount_sum, 2); ?></td>
                        <td>Rs <?php echo number_format($total_revenue_sum - $total_discount_sum, 2); ?></td>
                    </tr>
                </tbody>
            </table>
        </div>
        
        <?php elseif ($report_type == 'medicines'): ?>
        <h4>Top Selling Medicines (<?php echo date('d M Y', strtotime($from_date)); ?> to <?php echo date('d M Y', strtotime($to_date)); ?>)</h4>
        <div class="table-responsive">
            <table>
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Medicine Name</th>
                        <th>Generic Name</th>
                        <th>Company</th>
                        <th>Units Sold</th>
                        <th>Times Ordered</th>
                        <th>Total Revenue</th>
                    </tr>
                </thead>
                <tbody>
                    <?php 
                    $i = 1;
                    while ($med = mysqli_fetch_assoc($top_medicines)): 
                    ?>
                    <tr>
                        <td><?php echo $i++; ?></td>
                        <td><strong><?php echo $med['medicine_name']; ?></strong></td>
                        <td><?php echo $med['generic_name']; ?></td>
                        <td><?php echo $med['company_name']; ?></td>
                        <td><?php echo $med['total_sold']; ?></td>
                        <td><?php echo $med['times_sold']; ?></td>
                        <td>Rs <?php echo number_format($med['total_revenue'], 2); ?></td>
                    </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        </div>
        
        <?php elseif ($report_type == 'purchases'): ?>
        <h4>Purchase Report (<?php echo date('d M Y', strtotime($from_date)); ?> to <?php echo date('d M Y', strtotime($to_date)); ?>)</h4>
        <div class="table-responsive">
            <table>
                <thead>
                    <tr>
                        <th>Date</th>
                        <th>Supplier</th>
                        <th>Total Purchases</th>
                        <th>Total Amount</th>
                    </tr>
                </thead>
                <tbody>
                    <?php 
                    $total_purchase_amount = 0;
                    $total_purchase_count = 0;
                    while ($purchase = mysqli_fetch_assoc($purchase_report)): 
                        $total_purchase_amount += $purchase['total_amount'];
                        $total_purchase_count += $purchase['total_purchases'];
                    ?>
                    <tr>
                        <td><?php echo date('d M Y', strtotime($purchase['date'])); ?></td>
                        <td><?php echo $purchase['supplier_name'] ?? 'N/A'; ?></td>
                        <td><?php echo $purchase['total_purchases']; ?></td>
                        <td>Rs <?php echo number_format($purchase['total_amount'], 2); ?></td>
                    </tr>
                    <?php endwhile; ?>
                    <tr style="background: #f3f4f6; font-weight: bold;">
                        <td colspan="2">TOTAL</td>
                        <td><?php echo $total_purchase_count; ?></td>
                        <td>Rs <?php echo number_format($total_purchase_amount, 2); ?></td>
                    </tr>
                </tbody>
            </table>
        </div>
        
        <?php elseif ($report_type == 'customers'): ?>
        <h4>Top Customers (<?php echo date('d M Y', strtotime($from_date)); ?> to <?php echo date('d M Y', strtotime($to_date)); ?>)</h4>
        <div class="table-responsive">
            <table>
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Customer Name</th>
                        <th>Phone</th>
                        <th>Total Orders</th>
                        <th>Total Spent</th>
                        <th>Avg Order Value</th>
                    </tr>
                </thead>
                <tbody>
                    <?php 
                    $i = 1;
                    while ($customer = mysqli_fetch_assoc($customer_report)): 
                        $avg_order = $customer['total_spent'] / $customer['total_orders'];
                    ?>
                    <tr>
                        <td><?php echo $i++; ?></td>
                        <td><strong><?php echo $customer['customer_name']; ?></strong></td>
                        <td><?php echo $customer['phone']; ?></td>
                        <td><?php echo $customer['total_orders']; ?></td>
                        <td>Rs <?php echo number_format($customer['total_spent'], 2); ?></td>
                        <td>Rs <?php echo number_format($avg_order, 2); ?></td>
                    </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        </div>
        <?php endif; ?>
    </div>
</div>

<style>
@media print {
    .no-print, .sidebar, .top-bar, .btn, button, .form-group {
        display: none !important;
    }
    .main-content {
        margin-left: 0 !important;
    }
    .card {
        box-shadow: none !important;
        border: 1px solid #000 !important;
    }
}
</style>

<?php include("includes/footer.php"); ?>
