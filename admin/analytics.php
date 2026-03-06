<?php
include("../config/db.php");
$page_title = "Sales Analytics";
include("includes/header.php");

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: ../login.php");
    exit();
}

// Date range filter
$date_from = isset($_GET['date_from']) ? $_GET['date_from'] : date('Y-m-01');
$date_to = isset($_GET['date_to']) ? $_GET['date_to'] : date('Y-m-d');

// Top selling medicines
$top_medicines = mysqli_query($conn, "SELECT m.medicine_name, m.generic_name, 
    SUM(si.quantity) as total_sold, 
    SUM(si.subtotal) as total_revenue,
    COUNT(DISTINCT si.sale_id) as times_sold
    FROM sale_items si 
    JOIN medicines m ON si.medicine_id = m.id 
    JOIN sales s ON si.sale_id = s.id 
    WHERE DATE(s.sale_date) BETWEEN '$date_from' AND '$date_to'
    GROUP BY si.medicine_id 
    ORDER BY total_sold DESC 
    LIMIT 15");

// Daily sales trend (Last 7 days)
$daily_sales = mysqli_query($conn, "SELECT DATE(sale_date) as sale_day, 
    COUNT(*) as total_orders,
    SUM(grand_total) as daily_revenue
    FROM sales 
    WHERE DATE(sale_date) >= DATE_SUB(CURDATE(), INTERVAL 7 DAY)
    GROUP BY DATE(sale_date)
    ORDER BY sale_date ASC");

// Category-wise sales
$category_sales = mysqli_query($conn, "SELECT c.category_name, 
    SUM(si.quantity) as total_sold,
    SUM(si.subtotal) as total_revenue
    FROM sale_items si 
    JOIN medicines m ON si.medicine_id = m.id 
    LEFT JOIN categories c ON m.category_id = c.id
    JOIN sales s ON si.sale_id = s.id 
    WHERE DATE(s.sale_date) BETWEEN '$date_from' AND '$date_to'
    GROUP BY m.category_id 
    ORDER BY total_sold DESC");

// Prepare chart data
$medicine_labels = [];
$medicine_data = [];
$medicine_revenue = [];

mysqli_data_seek($top_medicines, 0);
while ($med = mysqli_fetch_assoc($top_medicines)) {
    $medicine_labels[] = $med['medicine_name'];
    $medicine_data[] = $med['total_sold'];
    $medicine_revenue[] = $med['total_revenue'];
}

// Daily sales data
$daily_labels = [];
$daily_orders = [];
$daily_revenue_data = [];

while ($day = mysqli_fetch_assoc($daily_sales)) {
    $daily_labels[] = date('M d', strtotime($day['sale_day']));
    $daily_orders[] = $day['total_orders'];
    $daily_revenue_data[] = $day['daily_revenue'];
}

// Category data
$category_labels = [];
$category_data = [];
$category_colors = ['#667eea', '#764ba2', '#f093fb', '#4facfe', '#43e97b', '#fa709a', '#fee140', '#30cfd0', '#a8edea', '#fed6e3'];

while ($cat = mysqli_fetch_assoc($category_sales)) {
    $category_labels[] = $cat['category_name'] ?? 'Uncategorized';
    $category_data[] = $cat['total_sold'];
}

// Overall statistics
$total_revenue = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COALESCE(SUM(grand_total), 0) as revenue FROM sales WHERE DATE(sale_date) BETWEEN '$date_from' AND '$date_to'"))['revenue'];
$total_orders = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as count FROM sales WHERE DATE(sale_date) BETWEEN '$date_from' AND '$date_to'"))['count'];
$avg_order_value = $total_orders > 0 ? $total_revenue / $total_orders : 0;
$total_items_sold = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COALESCE(SUM(si.quantity), 0) as total FROM sale_items si JOIN sales s ON si.sale_id = s.id WHERE DATE(s.sale_date) BETWEEN '$date_from' AND '$date_to'"))['total'];
?>

<div class="card" style="margin-bottom: 24px;">
    <div class="card-body">
        <form method="GET" style="display: flex; gap: 15px; align-items: end;">
            <div class="form-group" style="margin: 0;">
                <label>From Date</label>
                <input type="date" name="date_from" class="form-control" value="<?php echo $date_from; ?>">
            </div>
            <div class="form-group" style="margin: 0;">
                <label>To Date</label>
                <input type="date" name="date_to" class="form-control" value="<?php echo $date_to; ?>">
            </div>
            <button type="submit" class="btn btn-primary">Filter</button>
            <a href="analytics.php" class="btn btn-secondary">Reset</a>
        </form>
    </div>
</div>

<!-- Statistics Cards -->
<div class="stats-grid">
    <div class="stat-card">
        <div class="stat-icon green">💰</div>
        <div class="stat-details">
            <h4>Total Revenue</h4>
            <p>Rs <?php echo number_format($total_revenue, 2); ?></p>
        </div>
    </div>
    
    <div class="stat-card">
        <div class="stat-icon blue">📦</div>
        <div class="stat-details">
            <h4>Total Orders</h4>
            <p><?php echo $total_orders; ?></p>
        </div>
    </div>
    
    <div class="stat-card">
        <div class="stat-icon orange">💵</div>
        <div class="stat-details">
            <h4>Avg Order Value</h4>
            <p>Rs <?php echo number_format($avg_order_value, 2); ?></p>
        </div>
    </div>
    
    <div class="stat-card">
        <div class="stat-icon red">📊</div>
        <div class="stat-details">
            <h4>Items Sold</h4>
            <p><?php echo $total_items_sold; ?></p>
        </div>
    </div>
</div>

<!-- Charts Row 1 -->
<div class="row" style="margin-bottom: 24px;">
    <div class="col-md-6">
        <div class="card">
            <div class="card-header">
                <h3>Top Selling Medicines</h3>
            </div>
            <div class="card-body">
                <canvas id="medicineChart" style="max-height: 350px;"></canvas>
            </div>
        </div>
    </div>
    <div class="col-md-6">
        <div class="card">
            <div class="card-header">
                <h3>Sales by Category</h3>
            </div>
            <div class="card-body">
                <canvas id="categoryChart" style="max-height: 350px;"></canvas>
            </div>
        </div>
    </div>
</div>

<!-- Charts Row 2 -->
<div class="row" style="margin-bottom: 24px;">
    <div class="col-md-6">
        <div class="card">
            <div class="card-header">
                <h3>Daily Sales Trend (Last 7 Days)</h3>
            </div>
            <div class="card-body">
                <canvas id="dailyChart" style="max-height: 300px;"></canvas>
            </div>
        </div>
    </div>
    <div class="col-md-6">
        <div class="card">
            <div class="card-header">
                <h3>Revenue by Medicine</h3>
            </div>
            <div class="card-body">
                <canvas id="revenueChart" style="max-height: 300px;"></canvas>
            </div>
        </div>
    </div>
</div>

<!-- Detailed Table -->
<div class="card">
    <div class="card-header">
        <h3>Detailed Sales Report</h3>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table>
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Medicine Name</th>
                        <th>Generic Name</th>
                        <th>Units Sold</th>
                        <th>Times Ordered</th>
                        <th>Total Revenue</th>
                        <th>Avg Price</th>
                    </tr>
                </thead>
                <tbody>
                    <?php 
                    mysqli_data_seek($top_medicines, 0);
                    $i = 1;
                    while ($med = mysqli_fetch_assoc($top_medicines)): 
                    ?>
                    <tr>
                        <td><?php echo $i++; ?></td>
                        <td><strong><?php echo $med['medicine_name']; ?></strong></td>
                        <td><?php echo $med['generic_name']; ?></td>
                        <td><?php echo $med['total_sold']; ?></td>
                        <td><?php echo $med['times_sold']; ?></td>
                        <td>Rs <?php echo number_format($med['total_revenue'], 2); ?></td>
                        <td>Rs <?php echo number_format($med['total_revenue'] / $med['total_sold'], 2); ?></td>
                    </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Chart.js Library -->
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
<script>
// Medicine Doughnut Chart
const medicineCtx = document.getElementById('medicineChart').getContext('2d');
new Chart(medicineCtx, {
    type: 'doughnut',
    data: {
        labels: <?php echo json_encode($medicine_labels); ?>,
        datasets: [{
            data: <?php echo json_encode($medicine_data); ?>,
            backgroundColor: [
                '#667eea', '#764ba2', '#f093fb', '#4facfe', '#43e97b',
                '#fa709a', '#fee140', '#30cfd0', '#a8edea', '#fed6e3',
                '#fbc2eb', '#a6c1ee', '#ffecd2', '#fcb69f', '#ff9a9e'
            ],
            borderWidth: 3,
            borderColor: '#fff',
            hoverOffset: 15
        }]
    },
    options: {
        responsive: true,
        maintainAspectRatio: true,
        plugins: {
            legend: {
                position: 'right',
                labels: {
                    padding: 12,
                    font: { size: 11 },
                    usePointStyle: true,
                    pointStyle: 'circle'
                }
            },
            tooltip: {
                callbacks: {
                    label: function(context) {
                        return context.label + ': ' + context.parsed + ' units';
                    }
                }
            }
        }
    }
});

// Category Pie Chart
const categoryCtx = document.getElementById('categoryChart').getContext('2d');
new Chart(categoryCtx, {
    type: 'pie',
    data: {
        labels: <?php echo json_encode($category_labels); ?>,
        datasets: [{
            data: <?php echo json_encode($category_data); ?>,
            backgroundColor: <?php echo json_encode($category_colors); ?>,
            borderWidth: 3,
            borderColor: '#fff',
            hoverOffset: 15
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
                    font: { size: 12 },
                    usePointStyle: true
                }
            }
        }
    }
});

// Daily Sales Line Chart
const dailyCtx = document.getElementById('dailyChart').getContext('2d');
new Chart(dailyCtx, {
    type: 'line',
    data: {
        labels: <?php echo json_encode($daily_labels); ?>,
        datasets: [{
            label: 'Orders',
            data: <?php echo json_encode($daily_orders); ?>,
            borderColor: '#667eea',
            backgroundColor: 'rgba(102, 126, 234, 0.1)',
            borderWidth: 3,
            fill: true,
            tension: 0.4,
            pointRadius: 5,
            pointHoverRadius: 7
        }]
    },
    options: {
        responsive: true,
        maintainAspectRatio: true,
        plugins: {
            legend: { display: false }
        },
        scales: {
            y: {
                beginAtZero: true,
                ticks: { precision: 0 }
            }
        }
    }
});

// Revenue Bar Chart
const revenueCtx = document.getElementById('revenueChart').getContext('2d');
new Chart(revenueCtx, {
    type: 'bar',
    data: {
        labels: <?php echo json_encode(array_slice($medicine_labels, 0, 10)); ?>,
        datasets: [{
            label: 'Revenue (Rs)',
            data: <?php echo json_encode(array_slice($medicine_revenue, 0, 10)); ?>,
            backgroundColor: 'rgba(102, 126, 234, 0.8)',
            borderColor: '#667eea',
            borderWidth: 2,
            borderRadius: 8
        }]
    },
    options: {
        responsive: true,
        maintainAspectRatio: true,
        plugins: {
            legend: { display: false }
        },
        scales: {
            y: {
                beginAtZero: true,
                ticks: {
                    callback: function(value) {
                        return 'Rs ' + value.toLocaleString();
                    }
                }
            }
        }
    }
});
</script>

<?php include("includes/footer.php"); ?>
