<?php
include("../config/db.php");

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    echo json_encode(['success' => false, 'message' => 'Unauthorized']);
    exit();
}

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $user_id = $_SESSION['user_id'];
    $sales_data = json_decode($_POST['sales_data'], true);
    
    if (empty($sales_data)) {
        echo json_encode(['success' => false, 'message' => 'No sales data provided']);
        exit();
    }
    
    $successful_sales = [];
    $failed_sales = [];
    $total_processed = 0;
    
    // Process each sale
    foreach ($sales_data as $sale_id => $sale) {
        // Start transaction for each sale
        mysqli_begin_transaction($conn);
        
        try {
            // Get form data for this sale
            $customer_id = !empty($_POST['customer_' . $sale_id]) ? intval($_POST['customer_' . $sale_id]) : NULL;
            $customer_name = !empty($_POST['customer_name_' . $sale_id]) ? mysqli_real_escape_string($conn, trim($_POST['customer_name_' . $sale_id])) : '';
            $payment_method = mysqli_real_escape_string($conn, $_POST['payment_method_' . $sale_id]);
            
            $subtotal = floatval($sale['subtotal']);
            $tax_percentage = floatval($sale['tax_percentage']);
            $tax_amount = floatval($sale['tax_amount']);
            $discount_percentage = floatval($sale['discount_percentage']);
            $discount_amount = floatval($sale['discount_amount']);
            $grand_total = floatval($sale['grand_total']);
            $amount_paid = isset($sale['amount_paid']) ? floatval($sale['amount_paid']) : 0;
            $change_amount = isset($sale['change_amount']) ? floatval($sale['change_amount']) : 0;
            $items = $sale['items'];
            
            // Validate items
            if (empty($items)) {
                throw new Exception("No items in sale");
            }
            
            // Generate invoice number
            $invoice_number = 'INV-' . date('Ymd') . '-' . str_pad(rand(1, 9999), 4, '0', STR_PAD_LEFT);
            
            // Check if invoice number exists
            $check = mysqli_query($conn, "SELECT id FROM sales WHERE invoice_number = '$invoice_number'");
            $attempts = 0;
            while (mysqli_num_rows($check) > 0 && $attempts < 10) {
                $invoice_number = 'INV-' . date('Ymd') . '-' . str_pad(rand(1, 9999), 4, '0', STR_PAD_LEFT);
                $check = mysqli_query($conn, "SELECT id FROM sales WHERE invoice_number = '$invoice_number'");
                $attempts++;
            }
            
            if ($attempts >= 10) {
                throw new Exception("Could not generate unique invoice number");
            }
            
            // Insert sale
            $customer_id_sql = $customer_id ? $customer_id : 'NULL';
            $customer_name_sql = !empty($customer_name) ? "'" . $customer_name . "'" : 'NULL';
            $sql = "INSERT INTO sales (invoice_number, customer_id, customer_name, user_id, subtotal, tax_percentage, tax_amount, 
                    discount_percentage, discount_amount, grand_total, payment_method, payment_status, amount_paid, change_amount, notes) 
                    VALUES ('$invoice_number', $customer_id_sql, $customer_name_sql, $user_id, $subtotal, $tax_percentage, $tax_amount, 
                    $discount_percentage, $discount_amount, $grand_total, '$payment_method', 'paid', $amount_paid, $change_amount, 'Bulk sale entry')";
            
            if (!mysqli_query($conn, $sql)) {
                throw new Exception("Error creating sale: " . mysqli_error($conn));
            }
            
            $sale_db_id = mysqli_insert_id($conn);
            
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
                
                if (!$stock_row) {
                    throw new Exception("Medicine ID $medicine_id not found");
                }
                
                if ($stock_row['quantity'] < $quantity) {
                    throw new Exception("Insufficient stock for medicine ID: $medicine_id");
                }
                
                // Insert sale item
                $sql = "INSERT INTO sale_items (sale_id, medicine_id, batch_number, quantity, unit_price, subtotal) 
                        VALUES ($sale_db_id, $medicine_id, '$batch', $quantity, $unit_price, $item_subtotal)";
                
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
            $details = "Invoice: $invoice_number, Total: Rs " . number_format($grand_total, 2) . ", Items: " . count($items) . " (Bulk Entry)";
            log_activity($conn, $user_id, 'Sale Created', 'sales', $sale_db_id, $details);
            
            $successful_sales[] = $invoice_number;
            $total_processed++;
            
        } catch (Exception $e) {
            // Rollback transaction
            mysqli_rollback($conn);
            $failed_sales[] = [
                'sale_id' => $sale_id,
                'error' => $e->getMessage()
            ];
        }
    }
    
    // Prepare response
    if (count($failed_sales) == 0) {
        echo json_encode([
            'success' => true,
            'message' => "Successfully processed $total_processed sales! Invoice numbers: " . implode(', ', $successful_sales)
        ]);
    } else if (count($successful_sales) > 0) {
        $error_details = array_map(function($f) {
            return $f['sale_id'] . ': ' . $f['error'];
        }, $failed_sales);
        
        echo json_encode([
            'success' => true,
            'message' => "Processed $total_processed sales successfully. " . count($failed_sales) . " failed: " . implode('; ', $error_details)
        ]);
    } else {
        $error_details = array_map(function($f) {
            return $f['sale_id'] . ': ' . $f['error'];
        }, $failed_sales);
        
        echo json_encode([
            'success' => false,
            'message' => "All sales failed: " . implode('; ', $error_details)
        ]);
    }
    
} else {
    echo json_encode(['success' => false, 'message' => 'Invalid request method']);
}
?>
