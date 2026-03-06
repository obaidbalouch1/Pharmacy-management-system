<?php
include("../config/db.php");

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: ../login.php");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $sale_id = intval($_POST['sale_id']);
    $return_items = isset($_POST['return_items']) ? $_POST['return_items'] : [];
    $return_reason = mysqli_real_escape_string($conn, $_POST['return_reason']);
    $notes = mysqli_real_escape_string($conn, $_POST['notes']);
    $user_id = $_SESSION['user_id'];
    
    if (empty($return_items)) {
        $_SESSION['error'] = "Please select at least one item to return!";
        header("Location: return_sale.php?id=$sale_id");
        exit();
    }
    
    // Start transaction
    mysqli_begin_transaction($conn);
    
    try {
        // Calculate total refund
        $total_refund = 0;
        $return_data = [];
        
        foreach ($return_items as $item_id) {
            $item_id = intval($item_id);
            $return_qty = intval($_POST['return_qty_' . $item_id]);
            
            // Get item details
            $item_query = mysqli_query($conn, "SELECT si.*, m.quantity as current_stock 
                FROM sale_items si 
                JOIN medicines m ON si.medicine_id = m.id 
                WHERE si.id = $item_id");
            
            if (mysqli_num_rows($item_query) == 0) {
                throw new Exception("Item not found!");
            }
            
            $item = mysqli_fetch_assoc($item_query);
            
            // Validate quantity
            if ($return_qty > $item['quantity']) {
                throw new Exception("Return quantity cannot exceed sold quantity!");
            }
            
            $refund_amount = $item['unit_price'] * $return_qty;
            $total_refund += $refund_amount;
            
            $return_data[] = [
                'item_id' => $item_id,
                'medicine_id' => $item['medicine_id'],
                'quantity' => $return_qty,
                'unit_price' => $item['unit_price'],
                'subtotal' => $refund_amount
            ];
        }
        
        // Insert return record
        $sql = "INSERT INTO sale_returns (sale_id, return_amount, return_reason, processed_by, notes, status) 
                VALUES ($sale_id, $total_refund, '$return_reason', $user_id, '$notes', 'completed')";
        
        if (!mysqli_query($conn, $sql)) {
            throw new Exception("Error creating return record: " . mysqli_error($conn));
        }
        
        $return_id = mysqli_insert_id($conn);
        
        // Insert return items and restore stock
        foreach ($return_data as $data) {
            // Insert return item
            $sql = "INSERT INTO sale_return_items (return_id, sale_item_id, medicine_id, quantity_returned, unit_price, subtotal) 
                    VALUES ($return_id, {$data['item_id']}, {$data['medicine_id']}, {$data['quantity']}, {$data['unit_price']}, {$data['subtotal']})";
            
            if (!mysqli_query($conn, $sql)) {
                throw new Exception("Error adding return item: " . mysqli_error($conn));
            }
            
            // Restore stock to inventory
            $sql = "UPDATE medicines SET quantity = quantity + {$data['quantity']} WHERE id = {$data['medicine_id']}";
            if (!mysqli_query($conn, $sql)) {
                throw new Exception("Error restoring stock: " . mysqli_error($conn));
            }
            
            // Update medicine status if it was out of stock
            mysqli_query($conn, "UPDATE medicines SET status = 'available' WHERE id = {$data['medicine_id']} AND quantity > 0");
        }
        
        // Update sale status (optional - mark as returned)
        mysqli_query($conn, "UPDATE sales SET notes = CONCAT(notes, '\n[RETURNED: Rs $total_refund on " . date('Y-m-d H:i:s') . "]') WHERE id = $sale_id");
        
        // Commit transaction
        mysqli_commit($conn);
        
        // Log activity
        include("includes/log_activity.php");
        $sale_info = mysqli_fetch_assoc(mysqli_query($conn, "SELECT invoice_number FROM sales WHERE id = $sale_id"));
        $details = "Invoice: {$sale_info['invoice_number']}, Refund: Rs " . number_format($total_refund, 2) . ", Items: " . count($return_data);
        log_activity($conn, $user_id, 'Sale Returned', 'sale_returns', $return_id, $details);
        
        $_SESSION['success'] = "Return processed successfully! Refund amount: Rs " . number_format($total_refund, 2) . ". Stock has been restored to inventory.";
        header("Location: view_return.php?id=$return_id");
        exit();
        
    } catch (Exception $e) {
        // Rollback transaction
        mysqli_rollback($conn);
        $_SESSION['error'] = $e->getMessage();
        header("Location: return_sale.php?id=$sale_id");
        exit();
    }
} else {
    header("Location: sales.php");
    exit();
}
?>
