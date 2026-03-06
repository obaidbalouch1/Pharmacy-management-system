<?php
include("../config/db.php");

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: ../login.php");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $supplier_id = intval($_POST['supplier_id']);
    $user_id = $_SESSION['user_id'];
    $purchase_date = mysqli_real_escape_string($conn, $_POST['purchase_date']);
    $payment_status = mysqli_real_escape_string($conn, $_POST['payment_status']);
    $total_amount = floatval($_POST['total_amount']);
    $tax_amount = floatval($_POST['tax_amount']);
    $grand_total = floatval($_POST['grand_total']);
    $notes = mysqli_real_escape_string($conn, $_POST['notes']);
    $items = json_decode($_POST['items'], true);
    
    // Validate items
    if (empty($items)) {
        $_SESSION['error'] = "No items in the purchase!";
        header("Location: new_purchase.php");
        exit();
    }
    
    // Validate supplier
    if ($supplier_id == 0) {
        $_SESSION['error'] = "Please select a supplier!";
        header("Location: new_purchase.php");
        exit();
    }
    
    // Start transaction
    mysqli_begin_transaction($conn);
    
    try {
        // Generate purchase number
        $purchase_number = 'PUR-' . date('Ymd') . '-' . str_pad(rand(1, 9999), 4, '0', STR_PAD_LEFT);
        
        // Check if purchase number exists
        $check = mysqli_query($conn, "SELECT id FROM purchases WHERE purchase_number = '$purchase_number'");
        while (mysqli_num_rows($check) > 0) {
            $purchase_number = 'PUR-' . date('Ymd') . '-' . str_pad(rand(1, 9999), 4, '0', STR_PAD_LEFT);
            $check = mysqli_query($conn, "SELECT id FROM purchases WHERE purchase_number = '$purchase_number'");
        }
        
        // Insert purchase
        $sql = "INSERT INTO purchases (purchase_number, supplier_id, user_id, purchase_date, total_amount, 
                tax_amount, grand_total, payment_status, notes) 
                VALUES ('$purchase_number', $supplier_id, $user_id, '$purchase_date', $total_amount, 
                $tax_amount, $grand_total, '$payment_status', '$notes')";
        
        if (!mysqli_query($conn, $sql)) {
            throw new Exception("Error creating purchase: " . mysqli_error($conn));
        }
        
        $purchase_id = mysqli_insert_id($conn);
        
        // Insert purchase items and update stock
        foreach ($items as $item) {
            $medicine_id = intval($item['id']);
            $quantity = intval($item['quantity']);
            $unit_price = floatval($item['price']);
            $batch = mysqli_real_escape_string($conn, $item['batch']);
            $item_subtotal = $quantity * $unit_price;
            
            // Insert purchase item
            $sql = "INSERT INTO purchase_items (purchase_id, medicine_id, quantity, unit_price, subtotal) 
                    VALUES ($purchase_id, $medicine_id, $quantity, $unit_price, $item_subtotal)";
            
            if (!mysqli_query($conn, $sql)) {
                throw new Exception("Error adding purchase item: " . mysqli_error($conn));
            }
            
            // Update medicine stock and batch
            $sql = "UPDATE medicines SET 
                    quantity = quantity + $quantity,
                    purchase_price = $unit_price,
                    status = 'available'";
            
            if (!empty($batch)) {
                $sql .= ", batch_number = '$batch'";
            }
            
            $sql .= " WHERE id = $medicine_id";
            
            if (!mysqli_query($conn, $sql)) {
                throw new Exception("Error updating stock: " . mysqli_error($conn));
            }
        }
        
        // Commit transaction
        mysqli_commit($conn);
        
        // Log activity
        include("includes/log_activity.php");
        $details = "Purchase #: $purchase_number, Total: Rs " . number_format($grand_total, 2) . ", Items: " . count($items);
        log_activity($conn, $user_id, 'Purchase Created', 'purchases', $purchase_id, $details);
        
        $_SESSION['success'] = "Purchase completed successfully! Purchase #: $purchase_number";
        header("Location: view_purchase.php?id=$purchase_id");
        exit();
        
    } catch (Exception $e) {
        // Rollback transaction
        mysqli_rollback($conn);
        $_SESSION['error'] = $e->getMessage();
        header("Location: new_purchase.php");
        exit();
    }
} else {
    header("Location: new_purchase.php");
    exit();
}
?>
