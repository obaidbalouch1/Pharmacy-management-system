<?php
include("../config/db.php");

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: ../login.php");
    exit();
}

$date_from = isset($_GET['date_from']) ? $_GET['date_from'] : date('Y-m-01');
$date_to = isset($_GET['date_to']) ? $_GET['date_to'] : date('Y-m-d');

// Set headers for Excel download
header('Content-Type: application/vnd.ms-excel');
header('Content-Disposition: attachment; filename="Financial_Report_' . $date_from . '_to_' . $date_to . '.xls"');
header('Pragma: no-cache');
header('Expires: 0');

// Get data
$sales_query = mysqli_query($conn, "SELECT 
    COUNT(*) as total_transactions,
    COALESCE(SUM(grand_total), 0) as total_sales,
    COALESCE(SUM(subtotal), 0) as subtotal,
    COALESCE(SUM(tax_amount), 0) as total_tax,
    COALESCE(SUM(discount_amount), 0) as total_discount
    FROM sales 
    WHERE DATE(sale_date) BETWEEN '$date_from' AND '$date_to'");
$sales_data = mysqli_fetch_assoc($sales_query);

$returns_query = mysqli_query($conn, "SELECT 
    COUNT(*) as total_returns,
    COALESCE(SUM(return_amount), 0) as total_returned
    FROM sale_returns 
    WHERE DATE(return_date) BETWEEN '$date_from' AND '$date_to' 
    AND status = 'completed'");
$returns_data = mysqli_fetch_assoc($returns_query);

$profit_query = mysqli_query($conn, "SELECT 
    COALESCE(SUM(si.quantity * (si.unit_price - m.purchase_price)), 0) as total_profit
    FROM sale_items si
    JOIN sales s ON si.sale_id = s.id
    JOIN medicines m ON si.medicine_id = m.id
    WHERE DATE(s.sale_date) BETWEEN '$date_from' AND '$date_to'");
$profit_data = mysqli_fetch_assoc($profit_query);

$returned_profit_query = mysqli_query($conn, "SELECT 
    COALESCE(SUM(sri.quantity_returned * (sri.unit_price - m.purchase_price)), 0) as returned_profit
    FROM sale_return_items sri
    JOIN sale_returns sr ON sri.return_id = sr.id
    JOIN medicines m ON sri.medicine_id = m.id
    WHERE DATE(sr.return_date) BETWEEN '$date_from' AND '$date_to' 
    AND sr.status = 'completed'");
$returned_profit_data = mysqli_fetch_assoc($returned_profit_query);

$net_sales = $sales_data['total_sales'] - $returns_data['total_returned'];
$net_profit = $profit_data['total_profit'] - $returned_profit_data['returned_profit'];
$profit_margin = $net_sales > 0 ? ($net_profit / $net_sales) * 100 : 0;

// Output Excel
echo '<?xml version="1.0"?>';
echo '<Workbook xmlns="urn:schemas-microsoft-com:office:spreadsheet" xmlns:ss="urn:schemas-microsoft-com:office:spreadsheet">';
echo '<Worksheet ss:Name="Financial Report">';
echo '<Table>';

// Header
echo '<Row>';
echo '<Cell><Data ss:Type="String">FINANCIAL REPORT</Data></Cell>';
echo '</Row>';
echo '<Row>';
echo '<Cell><Data ss:Type="String">Period: ' . $date_from . ' to ' . $date_to . '</Data></Cell>';
echo '</Row>';
echo '<Row></Row>';

// Summary
echo '<Row>';
echo '<Cell><Data ss:Type="String">SUMMARY</Data></Cell>';
echo '</Row>';
echo '<Row>';
echo '<Cell><Data ss:Type="String">Total Sales</Data></Cell>';
echo '<Cell><Data ss:Type="Number">' . $sales_data['total_sales'] . '</Data></Cell>';
echo '</Row>';
echo '<Row>';
echo '<Cell><Data ss:Type="String">Total Returns</Data></Cell>';
echo '<Cell><Data ss:Type="Number">' . $returns_data['total_returned'] . '</Data></Cell>';
echo '</Row>';
echo '<Row>';
echo '<Cell><Data ss:Type="String">Net Sales</Data></Cell>';
echo '<Cell><Data ss:Type="Number">' . $net_sales . '</Data></Cell>';
echo '</Row>';
echo '<Row>';
echo '<Cell><Data ss:Type="String">Net Profit</Data></Cell>';
echo '<Cell><Data ss:Type="Number">' . $net_profit . '</Data></Cell>';
echo '</Row>';
echo '<Row>';
echo '<Cell><Data ss:Type="String">Profit Margin</Data></Cell>';
echo '<Cell><Data ss:Type="Number">' . $profit_margin . '</Data></Cell>';
echo '</Row>';
echo '<Row></Row>';

// Sales Breakdown
echo '<Row>';
echo '<Cell><Data ss:Type="String">SALES BREAKDOWN</Data></Cell>';
echo '</Row>';
echo '<Row>';
echo '<Cell><Data ss:Type="String">Subtotal</Data></Cell>';
echo '<Cell><Data ss:Type="Number">' . $sales_data['subtotal'] . '</Data></Cell>';
echo '</Row>';
echo '<Row>';
echo '<Cell><Data ss:Type="String">Tax Collected</Data></Cell>';
echo '<Cell><Data ss:Type="Number">' . $sales_data['total_tax'] . '</Data></Cell>';
echo '</Row>';
echo '<Row>';
echo '<Cell><Data ss:Type="String">Discounts Given</Data></Cell>';
echo '<Cell><Data ss:Type="Number">' . $sales_data['total_discount'] . '</Data></Cell>';
echo '</Row>';
echo '<Row>';
echo '<Cell><Data ss:Type="String">Total Transactions</Data></Cell>';
echo '<Cell><Data ss:Type="Number">' . $sales_data['total_transactions'] . '</Data></Cell>';
echo '</Row>';
echo '<Row>';
echo '<Cell><Data ss:Type="String">Total Returns</Data></Cell>';
echo '<Cell><Data ss:Type="Number">' . $returns_data['total_returns'] . '</Data></Cell>';
echo '</Row>';

echo '</Table>';
echo '</Worksheet>';
echo '</Workbook>';
?>
