<?php
include("../config/db.php");

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: ../login.php");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $customer_id = !empty($_POST['customer_id']) ? $_POST['customer_id'] : NULL;
    $customer_name = !empty($_POST['customer_name']) ? mysqli_real_escape_string($conn, trim($_POST['customer_name'])) : '';
    $user_id = $_SESSION['user_id'];
    $payment_method = mysqli_real_escape_string($conn, $_POST['payment_method']);
    $subtotal = floatval($_POST['subtotal']);
    $tax_percentage = floatval($_POST['tax_percentage']);
    $tax_amount = floatval($_POST['tax_amount']);
    $discount_percentage = floatval($_POST['discount_percentage']);
    $discount_amount = floatval($_POST['discount_amount']);
    $grand_total = floatval($_POST['grand_total']);
    $amount_paid = floatval($_POST['amount_paid']);
    $change_amount = floatval($_POST['change_amount']);
    $notes = mysqli_real_escape_string($conn, $_POST['notes']);
    $items = json_decode($_POST['items'], true);
    
    // Validate items
    if (empty($items)) {
        $_SESSION['error'] = "No items in the sale!";
        header("Location: new_sale.php");
        exit();
    }
    
    // Start transaction
    mysqli_begin_transaction($conn);
    
    try {
        // Generate invoice number
        $invoice_number = 'INV-' . date('Ymd') . '-' . str_pad(rand(1, 9999), 4, '0', STR_PAD_LEFT);
        
        // Check if invoice number exists
        $check = mysqli_query($conn, "SELECT id FROM sales WHERE invoice_number = '$invoice_number'");
        while (mysqli_num_rows($check) > 0) {
            $invoice_number = 'INV-' . date('Ymd') . '-' . str_pad(rand(1, 9999), 4, '0', STR_PAD_LEFT);
            $check = mysqli_query($conn, "SELECT id FROM sales WHERE invoice_number = '$invoice_number'");
        }
        
        // Insert sale
        $customer_id_sql = $customer_id ? $customer_id : 'NULL';
        $customer_name_sql = !empty($customer_name) ? "'" . $customer_name . "'" : 'NULL';
        $sql = "INSERT INTO sales (invoice_number, customer_id, customer_name, user_id, subtotal, tax_percentage, tax_amount, 
                discount_percentage, discount_amount, grand_total, payment_method, payment_status, amount_paid, change_amount, notes) 
                VALUES ('$invoice_number', $customer_id_sql, $customer_name_sql, $user_id, $subtotal, $tax_percentage, $tax_amount, 
                $discount_percentage, $discount_amount, $grand_total, '$payment_method', 'paid', $amount_paid, $change_amount, '$notes')";
        
        if (!mysqli_query($conn, $sql)) {
            throw new Exception("Error creating sale: " . mysqli_error($conn));
        }
        
        $sale_id = mysqli_insert_id($conn);
        
        // Insert sale items and update stock
        foreach ($items as $item) {
            $medicine_id = intval($item['id']);
            $quantity = intval($item['quantity']);
            $unit_price = floatval($item['price']);
            $batch = mysqli_real_escape_string($conn, $item['batch']);
            $item_subtotal = $quantity * $unit_price;
            
            // Check stock availability
            $stock_check = mysqli_query($conn, "SELECT quantity FROM medicines WHERE id = $medicine_id");
            $stock_row = mysqli_fetch_assoc($stock_check);
            
            if ($stock_row['quantity'] < $quantity) {
                throw new Exception("Insufficient stock for medicine ID: $medicine_id");
            }
            
            // Insert sale item
            $sql = "INSERT INTO sale_items (sale_id, medicine_id, batch_number, quantity, unit_price, subtotal) 
                    VALUES ($sale_id, $medicine_id, '$batch', $quantity, $unit_price, $item_subtotal)";
            
            if (!mysqli_query($conn, $sql)) {
                throw new Exception("Error adding sale item: " . mysqli_error($conn));
            }
            
            // Update medicine stock
            $sql = "UPDATE medicines SET quantity = quantity - $quantity WHERE id = $medicine_id";
            if (!mysqli_query($conn, $sql)) {
                throw new Exception("Error updating stock: " . mysqli_error($conn));
            }
            
            // Update status if out of stock
            mysqli_query($conn, "UPDATE medicines SET status = 'out_of_stock' WHERE id = $medicine_id AND quantity = 0");
        }
        
        // Commit transaction
        mysqli_commit($conn);
        
        // Log activity
        include("includes/log_activity.php");
        $details = "Invoice: $invoice_number, Total: Rs " . number_format($grand_total, 2) . ", Items: " . count($items);
        log_activity($conn, $user_id, 'Sale Created', 'sales', $sale_id, $details);
        
        $_SESSION['success'] = "Sale completed successfully! Invoice: $invoice_number";
        header("Location: view_sale.php?id=$sale_id");
        exit();
        
    } catch (Exception $e) {
        // Rollback transaction
        mysqli_rollback($conn);
        $_SESSION['error'] = $e->getMessage();
        header("Location: new_sale.php");
        exit();
    }
} else {
    header("Location: new_sale.php");
    exit();
}
?>
