<?php
include("../config/db.php");
$page_title = "Dashboard";
include("includes/header.php");

// Get statistics
$total_medicines = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as count FROM medicines"))['count'];
$total_sales = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as count FROM sales WHERE DATE(sale_date) = CURDATE()"))['count'];
$total_customers = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as count FROM customers"))['count'];
$expiring_soon = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as count FROM medicines WHERE expiry_date <= DATE_ADD(CURDATE(), INTERVAL 30 DAY) AND expiry_date >= CURDATE()"))['count'];

// Today's revenue
$today_revenue = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COALESCE(SUM(grand_total), 0) as revenue FROM sales WHERE DATE(sale_date) = CURDATE()"))['revenue'];

// Low stock items
$low_stock = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as count FROM medicines WHERE quantity <= reorder_level"))['count'];

// Pagination for recent sales
$page = isset($_GET['page']) ? intval($_GET['page']) : 1;
$records_per_page = 10;
$offset = ($page - 1) * $records_per_page;

// Count total today's sales
$total_today_sales = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as count FROM sales WHERE DATE(sale_date) = CURDATE()"))['count'];
$total_pages = ceil($total_today_sales / $records_per_page);

// Recent sales - Today's sales only with pagination
$recent_sales = mysqli_query($conn, "SELECT s.*, c.customer_name FROM sales s LEFT JOIN customers c ON s.customer_id = c.id WHERE DATE(s.sale_date) = CURDATE() ORDER BY s.sale_date DESC LIMIT $records_per_page OFFSET $offset");

// Top selling medicines (Last 30 days)
$top_medicines = mysqli_query($conn, "SELECT m.medicine_name, m.generic_name, 
    SUM(si.quantity) as total_sold, 
    SUM(si.subtotal) as total_revenue,
    COUNT(DISTINCT si.sale_id) as times_sold
    FROM sale_items si 
    JOIN medicines m ON si.medicine_id = m.id 
    JOIN sales s ON si.sale_id = s.id 
    WHERE s.sale_date >= DATE_SUB(CURDATE(), INTERVAL 30 DAY)
    GROUP BY si.medicine_id 
    ORDER BY total_sold DESC 
    LIMIT 10");

// Prepare data for chart
$chart_data = [];
$chart_labels = [];
$chart_colors = ['#667eea', '#764ba2', '#f093fb', '#4facfe', '#43e97b', '#fa709a', '#fee140', '#30cfd0', '#a8edea', '#fed6e3'];
$color_index = 0;

mysqli_data_seek($top_medicines, 0);
while ($med = mysqli_fetch_assoc($top_medicines)) {
    $chart_labels[] = $med['medicine_name'];
    $chart_data[] = $med['total_sold'];
    $color_index++;
}
?>

<div class="stats-grid">
    <div class="stat-card">
        <div class="stat-icon blue">💊</div>
        <div class="stat-details">
            <h4>Total Medicines</h4>
            <p><?php echo $total_medicines; ?></p>
        </div>
    </div>
    
    <div class="stat-card">
        <div class="stat-icon green">💰</div>
        <div class="stat-details">
            <h4>Today's Revenue</h4>
            <p>Rs <?php echo number_format($today_revenue, 2); ?></p>
        </div>
    </div>
    
    <div class="stat-card">
        <div class="stat-icon orange">⚠️</div>
        <div class="stat-details">
            <h4>Expiring Soon</h4>
            <p><?php echo $expiring_soon; ?></p>
        </div>
    </div>
    
    <div class="stat-card">
        <div class="stat-icon red">📉</div>
        <div class="stat-details">
            <h4>Low Stock Items</h4>
            <p><?php echo $low_stock; ?></p>
        </div>
    </div>
</div>

<!-- Sales Analytics Section -->
<div class="row" style="margin-bottom: 24px;">
    <div class="col-md-6">
        <div class="card">
            <div class="card-header">
                <h3>Top Selling Medicines (Last 30 Days)</h3>
            </div>
            <div class="card-body">
                <canvas id="salesChart" style="max-height: 400px;"></canvas>
            </div>
        </div>
    </div>
    <div class="col-md-6">
        <div class="card">
            <div class="card-header">
                <h3>Sales Breakdown</h3>
            </div>
            <div class="card-body">
                <div class="sales-list">
                    <?php 
                    mysqli_data_seek($top_medicines, 0);
                    $index = 0;
                    while ($med = mysqli_fetch_assoc($top_medicines)): 
                    ?>
                    <div class="sales-item">
                        <div class="sales-item-header">
                            <div class="sales-item-color" style="background: <?php echo $chart_colors[$index % count($chart_colors)]; ?>;"></div>
                            <div class="sales-item-info">
                                <strong><?php echo $med['medicine_name']; ?></strong>
                                <small class="text-muted"><?php echo $med['generic_name']; ?></small>
                            </div>
                        </div>
                        <div class="sales-item-stats">
                            <div class="stat-item">
                                <span class="stat-label">Sold</span>
                                <span class="stat-value"><?php echo $med['total_sold']; ?> units</span>
                            </div>
                            <div class="stat-item">
                                <span class="stat-label">Revenue</span>
                                <span class="stat-value">Rs <?php echo number_format($med['total_revenue'], 2); ?></span>
                            </div>
                            <div class="stat-item">
                                <span class="stat-label">Orders</span>
                                <span class="stat-value"><?php echo $med['times_sold']; ?></span>
                            </div>
                        </div>
                    </div>
                    <?php 
                    $index++;
                    endwhile; 
                    ?>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="card">
    <div class="card-header" style="display: flex; justify-content: space-between; align-items: center;">
        <h3>Today's Sales (<?php echo date('d M Y'); ?>)</h3>
        <span class="badge badge-primary" style="font-size: 14px; padding: 8px 16px;">
            Total: <?php echo $total_today_sales; ?> Sales
        </span>
    </div>
    <div class="card-body">
        <?php if ($total_today_sales > 0): ?>
        <div class="table-responsive">
            <table>
                <thead>
                    <tr>
                        <th>Invoice #</th>
                        <th>Customer</th>
                        <th>Time</th>
                        <th>Amount</th>
                        <th>Paid</th>
                        <th>Change</th>
                        <th>Status</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php 
                    while ($sale = mysqli_fetch_assoc($recent_sales)): 
                    ?>
                    <tr>
                        <td><strong><?php echo $sale['invoice_number']; ?></strong></td>
                        <td><?php echo $sale['customer_name'] ?? 'Walk-in'; ?></td>
                        <td><?php echo date('h:i A', strtotime($sale['sale_date'])); ?></td>
                        <td><strong>Rs <?php echo number_format($sale['grand_total'], 2); ?></strong></td>
                        <td>Rs <?php echo number_format($sale['amount_paid'], 2); ?></td>
                        <td>Rs <?php echo number_format($sale['change_amount'], 2); ?></td>
                        <td><span class="badge badge-success"><?php echo ucfirst($sale['payment_status']); ?></span></td>
                        <td>
                            <div class="action-buttons">
                                <a href="view_sale.php?id=<?php echo $sale['id']; ?>" class="btn btn-sm btn-info">View</a>
                                <a href="print_invoice.php?id=<?php echo $sale['id']; ?>" class="btn btn-sm btn-success" target="_blank">Print</a>
                            </div>
                        </td>
                    </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        </div>
        
        <?php if ($total_pages > 1): ?>
        <div class="pagination-container">
            <div class="pagination-info">
                Showing <?php echo $offset + 1; ?> to <?php echo min($offset + $records_per_page, $total_today_sales); ?> of <?php echo $total_today_sales; ?> sales
            </div>
            <div class="pagination">
                <?php if ($page > 1): ?>
                    <a href="?page=1" class="pagination-btn">First</a>
                    <a href="?page=<?php echo $page - 1; ?>" class="pagination-btn">Previous</a>
                <?php endif; ?>
                
                <?php
                $start_page = max(1, $page - 2);
                $end_page = min($total_pages, $page + 2);
                
                for ($i = $start_page; $i <= $end_page; $i++):
                ?>
                    <a href="?page=<?php echo $i; ?>" class="pagination-btn <?php echo $i == $page ? 'active' : ''; ?>">
                        <?php echo $i; ?>
                    </a>
                <?php endfor; ?>
                
                <?php if ($page < $total_pages): ?>
                    <a href="?page=<?php echo $page + 1; ?>" class="pagination-btn">Next</a>
                    <a href="?page=<?php echo $total_pages; ?>" class="pagination-btn">Last</a>
                <?php endif; ?>
            </div>
        </div>
        <?php endif; ?>
        
        <?php else: ?>
        <div style="text-align: center; padding: 40px; color: #6b7280;">
            <div style="font-size: 48px; margin-bottom: 16px;">📊</div>
            <h4 style="color: #374151; margin-bottom: 8px;">No Sales Today</h4>
            <p>No sales have been recorded today yet.</p>
            <a href="new_sale.php" class="btn btn-primary" style="margin-top: 16px;">Create New Sale</a>
        </div>
        <?php endif; ?>
    </div>
</div>

<!-- Chart.js Library -->
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
<script>
// Sales Chart
const ctx = document.getElementById('salesChart').getContext('2d');
const salesChart = new Chart(ctx, {
    type: 'doughnut',
    data: {
        labels: <?php echo json_encode($chart_labels); ?>,
        datasets: [{
            label: 'Units Sold',
            data: <?php echo json_encode($chart_data); ?>,
            backgroundColor: [
                '#667eea',
                '#764ba2',
                '#f093fb',
                '#4facfe',
                '#43e97b',
                '#fa709a',
                '#fee140',
                '#30cfd0',
                '#a8edea',
                '#fed6e3'
            ],
            borderWidth: 3,
            borderColor: '#fff',
            hoverOffset: 10
        }]
    },
    options: {
        responsive: true,
        maintainAspectRatio: true,
        plugins: {
            legend: {
                position: 'bottom',
                labels: {
                    padding: 15,
                    font: {
                        size: 12
                    },
                    usePointStyle: true,
                    pointStyle: 'circle'
                }
            },
            tooltip: {
                backgroundColor: 'rgba(0, 0, 0, 0.8)',
                padding: 12,
                titleFont: {
                    size: 14
                },
                bodyFont: {
                    size: 13
                },
                callbacks: {
                    label: function(context) {
                        let label = context.label || '';
                        if (label) {
                            label += ': ';
                        }
                        label += context.parsed + ' units';
                        return label;
                    }
                }
            }
        },
        animation: {
            animateRotate: true,
            animateScale: true
        }
    }
});
</script>

<style>
.sales-list {
    max-height: 400px;
    overflow-y: auto;
}

.sales-item {
    padding: 15px;
    border-bottom: 1px solid #e5e7eb;
    transition: background-color 0.3s ease;
}

.sales-item:hover {
    background-color: #f9fafb;
}

.sales-item:last-child {
    border-bottom: none;
}

.sales-item-header {
    display: flex;
    align-items: center;
    gap: 12px;
    margin-bottom: 10px;
}

.sales-item-color {
    width: 12px;
    height: 12px;
    border-radius: 50%;
    flex-shrink: 0;
}

.sales-item-info {
    flex: 1;
}

.sales-item-info strong {
    display: block;
    color: #111827;
    font-size: 14px;
    margin-bottom: 2px;
}

.sales-item-info small {
    font-size: 12px;
}

.sales-item-stats {
    display: flex;
    gap: 20px;
    margin-left: 24px;
}

.stat-item {
    display: flex;
    flex-direction: column;
}

.stat-label {
    font-size: 11px;
    color: #6b7280;
    text-transform: uppercase;
    letter-spacing: 0.5px;
}

.stat-value {
    font-size: 13px;
    font-weight: 600;
    color: #111827;
    margin-top: 2px;
}
</style>

<?php include("includes/footer.php"); ?>
